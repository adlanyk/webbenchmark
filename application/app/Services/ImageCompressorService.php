<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageCompressorService
{
    public static function compressAndStore(
        TemporaryUploadedFile $file,
        string $directory,
        int $maxDimension = 1280,
        int $quality = 75
    ): string {
        $realPath = $file->getRealPath();

        // 1. Validasi MIME biner asli (anti file berbahaya yang diganti ekstensi)
        $imageInfo = @getimagesize($realPath);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if ($imageInfo === false || ! in_array($imageInfo['mime'], $allowedMimes, true)) {
            throw new \RuntimeException('File bukan gambar valid (hanya mendukung JPG, PNG, atau WebP).');
        }

        [$origWidth, $origHeight] = $imageInfo;

        // 2. Proteksi memori server dari gambar beresolusi ekstrem
        if ($origWidth > 8000 || $origHeight > 8000) {
            throw new \RuntimeException('Resolusi gambar melebihi batas maksimal 8000x8000 piksel.');
        }

        $rawContent = file_get_contents($realPath);
        $source = @imagecreatefromstring($rawContent);
        unset($rawContent);

        if ($source === false) {
            throw new \RuntimeException('Gagal memproses file gambar.');
        }

        // 3. Perbaiki rotasi otomatis foto kamera HP berdasarkan EXIF
        if ($imageInfo['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($realPath);
            $orientation = $exif['Orientation'] ?? 1;

            $source = match ($orientation) {
                3 => imagerotate($source, 180, 0),
                6 => imagerotate($source, -90, 0),
                8 => imagerotate($source, 90, 0),
                default => $source,
            };

            $origWidth = imagesx($source);
            $origHeight = imagesy($source);
        }

        // 4. Resize proporsional di server (fallback jika browser tidak me-resize)
        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight);
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($source);
            $source = $resized;
        }

        // 5. Re-encode menjadi JPEG bersih (menghapus metadata EXIF/GPS & payload tersembunyi)
        ob_start();
        imagejpeg($source, null, $quality);
        $compressedBinary = ob_get_clean();
        imagedestroy($source);

        // 6. Simpan dengan nama ULID acak di disk public
        $filename = trim($directory, '/') . '/' . Str::ulid() . '.jpg';
        Storage::disk('public')->put($filename, $compressedBinary);

        return $filename;
    }
}