<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AnimalController extends Controller
{
    public function index(Request $request)
    {
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['name', 'species', 'created_at'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at';
        }

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $animals = Animal::orderBy($sortColumn, $sortDirection)->get();

        return view('animals.index', compact('animals'));
    }

    public function show(Animal $animal)
    {
        return view('animals.show', compact('animal'));
    }

    public function create()
    {
        return view('animals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $this->resizeAndStoreImage($request->file('photo'));
        }

        Animal::create([
            'name' => $validated['name'],
            'species' => $validated['species'],
            'breed' => $validated['breed'],
            'description' => $validated['description'],
            'photo_path' => $path,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('animals.index')->with('success', 'Animal created successfully.');
    }

    public function edit(Animal $animal)
    {
        return view('animals.edit', compact('animal'));
    }

    public function update(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo) {
            if ($animal->photo_path) {
                Storage::delete($animal->photo_path);
                $validated['photo_path'] = null;
            }
        }
        // Handle new photo upload (only if not removing photo)
        elseif ($request->hasFile('photo')) {
            if ($animal->photo_path) {
                Storage::delete($animal->photo_path);
            }
            $path = $this->resizeAndStoreImage($request->file('photo'));
            $validated['photo_path'] = $path;
        }

        // Remove the remove_photo field from validated data before updating
        unset($validated['remove_photo']);

        $animal->update($validated);
        return redirect()->route('animals.index')->with('success', 'Animal updated successfully.');
    }

    public function destroy(Animal $animal)
    {
        if ($animal->photo_path) {
            Storage::delete($animal->photo_path);
        }
        $animal->delete();
        return redirect()->route('animals.index')->with('success', 'Animal deleted successfully.');
    }

    /**
     * Resize and save uploaded image
     */
    private function resizeAndStoreImage($uploadedFile, $maxWidth = 800, $maxHeight = 600)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            // Fallback: just store the original image using Storage facade
            return Storage::disk('public')->put('animals', $uploadedFile);
        }

        // Generate a unique filename
        $filename = time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        
        // Get image info
        $imageInfo = getimagesize($uploadedFile->getPathname());
        if (!$imageInfo) {
            // If can't get image info, store original
            return Storage::disk('public')->put('animals', $uploadedFile);
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // If image is already smaller than max dimensions, store original
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return Storage::disk('public')->put('animals', $uploadedFile);
        }

        // Calculate new dimensions while maintaining aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // Create image resource based on file type
        $sourceImage = null;
        try {
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($uploadedFile->getPathname());
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($uploadedFile->getPathname());
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($uploadedFile->getPathname());
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = imagecreatefromwebp($uploadedFile->getPathname());
                    } else {
                        return Storage::disk('public')->put('animals', $uploadedFile);
                    }
                    break;
                default:
                    // If unsupported format, store original
                    return Storage::disk('public')->put('animals', $uploadedFile);
            }
        } catch (Exception $e) {
            // If image creation fails, store original
            return Storage::disk('public')->put('animals', $uploadedFile);
        }

        if (!$sourceImage) {
            return Storage::disk('public')->put('animals', $uploadedFile);
        }

        // Create new image with desired dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize the image
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Save the resized image
        $storagePath = storage_path('app/public/animals/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }

        $success = false;
        try {
            switch ($mimeType) {
                case 'image/jpeg':
                    $success = imagejpeg($resizedImage, $storagePath, 90); // 90% quality
                    break;
                case 'image/png':
                    $success = imagepng($resizedImage, $storagePath, 8); // PNG compression level 8
                    break;
                case 'image/gif':
                    $success = imagegif($resizedImage, $storagePath);
                    break;
                case 'image/webp':
                    if (function_exists('imagewebp')) {
                        $success = imagewebp($resizedImage, $storagePath, 90);
                    }
                    break;
            }
        } catch (Exception $e) {
            $success = false;
        }

        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        if ($success) {
            return 'animals/' . $filename;
        } else {
            // If resize failed, store original
            return Storage::disk('public')->put('animals', $uploadedFile);
        }
    }
}