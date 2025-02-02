<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Codewithkyrian\Transformers\Transformers;
use Codewithkyrian\Transformers\Models\Auto\AutoModel;
use Codewithkyrian\Transformers\Processors\AutoProcessor;
use Codewithkyrian\Transformers\Utils\Image;

class ImageController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function removeBackground(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $image = $request->file('image');
        $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('uploads', $fileName, 'public');

        // Process the image for background removal
        $processedPath = $this->processImage($path, $fileName);
        $imageContent = file_get_contents(storage_path('app/public/' . $processedPath));

        // Delete the original uploaded file
        Storage::disk('public')->delete($path);
        // Delete the processed file after download
        Storage::disk('public')->delete($processedPath);

        // Return file download response
        return response($imageContent)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="removed_background_' . $fileName . '"');
    }

    private function processImage($path, $fileName)
    {
        $inputPath = storage_path('app/public/' . $path);
        $outputPath = storage_path('app/public/processed/' . $fileName);

        // Ensure the processed directory exists
        Storage::disk('public')->makeDirectory('processed');

        // Use virtual environment's Python with correct path
        $venvPath = base_path('venv/bin/');
        $command = "cd {$venvPath} && ./python rembg i \"{$inputPath}\" \"{$outputPath}\" 2>&1";

        $output = shell_exec($command);
        
        if (!file_exists($outputPath)) {
            throw new \Exception('Failed to process image: ' . $output);
        }

        return 'processed/' . $fileName;
    }
}
