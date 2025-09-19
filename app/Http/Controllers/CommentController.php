<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Animal;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = new Comment([
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);
        $animal->comments()->save($comment);

        return redirect()->route('animals.show', $animal)->with('success', 'Comment added!');
    }
}
