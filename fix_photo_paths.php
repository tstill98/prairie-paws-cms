<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update the photo paths
$animals = DB::table('animals')->whereNotNull('photo_path')->get();

foreach ($animals as $animal) {
    $newPath = str_replace('public/', '', $animal->photo_path);
    DB::table('animals')->where('id', $animal->id)->update(['photo_path' => $newPath]);
    echo "Updated animal {$animal->id}: {$animal->photo_path} -> {$newPath}\n";
}

echo "Done!\n";
