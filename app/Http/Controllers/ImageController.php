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
        $originalFileName = $image->getClientOriginalName();

        $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('uploads', $fileName, 'public');

        // Process the image for background removal
        $processedImagePath = $this->processImage($path, $fileName, $originalFileName);
        
        // Delete the original uploaded file
        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => true,
            'fileName' => $processedImagePath,
            'downloadUrl' => route('download.image', ['fileName' => $processedImagePath])
        ]);
    }

    public function downloadImage($fileName)
    {
        $processedPath = 'processed/' . $fileName;
        $fullPath = storage_path('app/public/' . $processedPath);
        
        if (!file_exists($fullPath)) {
            return response()->json([
                'error' => 'Processed file not found. Please try processing the image again.'
            ], 404);
        }
        
        $imageContent = file_get_contents($fullPath);
        
        // Delete the processed file after download
        Storage::disk('public')->delete($processedPath);

        $downloadFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . env('APP_URL') . '.png';
        
        return response($imageContent)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $downloadFileName . '"');
    }

    private function processImage($path, $fileName, $originalFileName)
    {
        $inputPath = storage_path('app/public/' . $path);
        $outputPath = storage_path('app/public/processed/' . $fileName);

        // Ensure the processed directory exists
        Storage::disk('public')->makeDirectory('processed');

        // Use virtual environment's Python with correct path
        $venvPath = base_path('venv/bin/');
        $command = "cd {$venvPath} && ./python rembg i \"{$inputPath}\" \"{$outputPath}\" 2>&1";

        $output = shell_exec($command);

        // delete input file after processing
        Storage::disk('public')->delete($inputPath);

        // rename output file to original file name
        $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '-my-site.com.png';
        $newOutputPath = storage_path('app/public/processed/' . $newFileName);
        rename($outputPath, $newOutputPath);
        
        if (!file_exists($newOutputPath)) {
            throw new \Exception('Failed to process image: ' . $output);
        }

        return $newFileName;
    }
}
