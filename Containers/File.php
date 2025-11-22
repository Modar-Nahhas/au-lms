<?php

namespace LMS_Website\Containers;

use Exception;

abstract class File
{

    public static function getFilePath(string $fileName, ?string $filePath = null): string
    {

        $filePath = $filePath ?? Env::getValue('FILES_PATH');
        return DIRECTORY_SEPARATOR . "$filePath" . DIRECTORY_SEPARATOR . "$fileName";
    }

    /**
     * Save an uploaded image to a directory.
     *
     * @param array $file The $_FILES['coverImage'] array
     * @param ?string $targetDirectory Absolute path to the save location (NOT URL)
     * @param int $maxSize Max size in bytes (default 2MB)
     *
     * @return string|null  Saved filename, or null on failure
     *
     * @throws Exception
     */
    public static function saveUploadedImage(array $file, ?string $targetDirectory = null, int $maxSize = 2_000_000): ?string
    {
        if ($targetDirectory === null) {
            $targetDirectory = DIRECTORY_SEPARATOR . Env::getValue('FILES_PATH');
        }
        // No file uploaded
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // optional, depending on your logic
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Image upload failed: " . $file['error']);
        }

        // Validate size
        if ($file['size'] > $maxSize) {
            throw new Exception("Image is too large. Max size is " . round($maxSize / 1024 / 1024, 2) . "MB");
        }

        // Allowed extensions
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid file type. Allowed: JPG, JPEG, PNG.");
        }

        // Ensure directory exists
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        // Generate a safe unique filename
        $filename = uniqid("book_", true) . "." . $ext;

        // Final absolute path
        $savePath = __DIR__ . "/../" . rtrim($targetDirectory, '/') . '/' . $filename;

//        var_dump($savePath);die;
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            throw new Exception("Error saving uploaded image.");
        }

        return $filename;
    }

    public static function deleteImage(?string $filename, ?string $targetDirectory = null): bool
    {
        // Prevent deletion of default image
        if ($filename === Env::getValue('DEFAULT_BOOK_IMAGE')) {
            return true;
        }
        if ($targetDirectory === null) {
            $targetDirectory = DIRECTORY_SEPARATOR . Env::getValue('FILES_PATH');
        }
        // No filename? Nothing to delete.
        if (empty($filename)) {
            return false;
        }

        // Full path to image folder (adjust if needed)
        $path = __DIR__ . "/../" . rtrim($targetDirectory, '/') . '/' . $filename;

        // Check file exists and is a real file
        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }

        return false;
    }

}