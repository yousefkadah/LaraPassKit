<?php

namespace App\Services;

use App\Models\MediaLibraryAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MediaLibraryService
{
    protected string $imagesDisk;

    protected string $imagesPath;

    public function __construct()
    {
        $this->imagesDisk = (string) config('passkit.storage.images_disk');
        $this->imagesPath = (string) config('passkit.storage.images_path');
    }

    public function store(UploadedFile $file, ?string $slot, int $userId): MediaLibraryAsset
    {
        [$sourceImage, $width, $height, $mime] = $this->loadImage($file);

        $slotSegment = $slot ?: 'unsorted';
        $path = $this->imagesPath.'/'.$userId.'/library/'.$slotSegment.'/'.Str::uuid().'.png';
        $this->storePng($sourceImage, $path);
        imagedestroy($sourceImage);

        $disk = Storage::disk($this->imagesDisk);
        $sizeBytes = (int) $disk->size($path);

        return MediaLibraryAsset::create([
            'owner_user_id' => $userId,
            'source' => 'user',
            'slot' => $slot,
            'path' => $path,
            'url' => $disk->url($path),
            'width' => $width,
            'height' => $height,
            'mime' => $mime,
            'size_bytes' => $sizeBytes,
        ]);
    }

    public function delete(MediaLibraryAsset $asset): void
    {
        Storage::disk($this->imagesDisk)->delete($asset->path);
        $asset->delete();
    }

    /**
     * @return array{0: \GdImage, 1: int, 2: int, 3: string}
     */
    protected function loadImage(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException('Invalid image upload.');
        }

        $info = getimagesize($path);
        if ($info === false) {
            throw new RuntimeException('Unable to read image metadata.');
        }

        [$width, $height] = $info;
        $mime = (string) Arr::get($info, 'mime', '');

        return [$this->createImageResource($path, $mime), $width, $height, $mime];
    }

    protected function createImageResource(string $path, string $mime): \GdImage
    {
        $resource = match ($mime) {
            'image/png' => imagecreatefrompng($path),
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/webp' => $this->createWebpResource($path),
            default => throw new RuntimeException('Unsupported image format.'),
        };

        if ($resource === false) {
            throw new RuntimeException('Unable to read the uploaded image.');
        }

        return $resource;
    }

    protected function createWebpResource(string $path): \GdImage
    {
        if (! function_exists('imagecreatefromwebp')) {
            throw new RuntimeException('WEBP images are not supported by this server.');
        }

        return imagecreatefromwebp($path);
    }

    protected function storePng(\GdImage $image, string $path): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'media_');
        if ($tempPath === false) {
            throw new RuntimeException('Unable to create temp file for image export.');
        }

        if (! imagepng($image, $tempPath)) {
            @unlink($tempPath);
            throw new RuntimeException('Failed to write PNG image.');
        }

        $content = file_get_contents($tempPath);
        @unlink($tempPath);

        if ($content === false) {
            throw new RuntimeException('Failed to read PNG image content.');
        }

        if (! Storage::disk($this->imagesDisk)->put($path, $content)) {
            throw new RuntimeException('Failed to store PNG image.');
        }
    }
}
