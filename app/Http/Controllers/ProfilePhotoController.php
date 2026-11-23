<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    /**
     * Display an employee's private profile photo.
     */
    public function employee(Employee $employee)
    {
        // Admins can view all employee photos.
        // Employees can view only their own photo.
        Gate::authorize('view', $employee);

        $profilePhoto = $employee->getAttribute('profile_photo');

        return $this->servePrivatePhoto(
            \is_string($profilePhoto) ? $profilePhoto : null,
            'employee-photos/'
        );
    }

    /**
     * Display a standalone user's private profile photo.
     */
    public function user(User $user)
    {
        // UserPolicy allows only administrators.
        Gate::authorize('view', $user);

        $profilePhoto = $user->getAttribute('profile_photo');

        return $this->servePrivatePhoto(
            \is_string($profilePhoto) ? $profilePhoto : null,
            'user-profile-photos/'
        );
    }

    /**
     * Serve a validated image from private storage.
     */
    private function servePrivatePhoto(?string $path, string $allowedDirectory)
    {
        abort_if(
            empty($path) ||
            ! str_starts_with($path, $allowedDirectory),
            404
        );

        $filename = substr($path, \strlen($allowedDirectory));

        // Prevent directory traversal and unexpected filenames.
        abort_unless(
            preg_match('/\A[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)\z/i', $filename),
            404
        );

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('private_photos');

        abort_unless($disk->exists($path), 404);

        $mimeType = $disk->mimeType($path);

        abort_unless(
            \in_array($mimeType, [
                'image/jpeg',
                'image/png',
                'image/webp',
            ], true),
            404
        );

        $fileContents = file_get_contents($disk->path($path));

        abort_unless($fileContents !== false, 404);

        return response()->stream(function () use ($fileContents) {
            echo $fileContents;
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) strlen($fileContents),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
