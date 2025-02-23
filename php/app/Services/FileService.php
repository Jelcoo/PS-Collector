<?php

namespace App\Services;

class FileService
{
    public function saveBase64($base64String, $filename, $outputDir) {
        if (preg_match('/^data:(.*?);base64,/', $base64String, $matches)) {
            $mimeType = $matches[1];
            $base64String = preg_replace('/^data:(.*?);base64,/', '', $base64String);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'application/pdf' => 'pdf',
                'text/plain' => 'txt'
            ];

            $extension = isset($extensions[$mimeType]) ? $extensions[$mimeType] : 'bin';
        } else {
            $extension = 'bin';
        }

        $outputFile = rtrim($outputDir, '/') . '/' . $filename . '.' . $extension;

        $decodedData = base64_decode($base64String);
        if ($decodedData === false) {
            return false;
        }

        if (file_put_contents($outputFile, $decodedData) !== false) {
            return $outputFile;
        }

        return false;
    }
}
