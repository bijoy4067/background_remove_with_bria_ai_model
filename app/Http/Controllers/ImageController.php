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
        // Use GD driver instead of ImageMagick
        Transformers::setup()->setImageDriver(\Codewithkyrian\Transformers\Utils\ImageDriver::GD);

        $inputPath = storage_path('app/public/' . $path);
        $outputPath = storage_path('app/public/processed/' . $fileName);

        // Ensure the processed directory exists
        Storage::disk('public')->makeDirectory('processed');

        $url = Storage::disk('public')->path($path);

        $model = AutoModel::fromPretrained('briaai/RMBG-1.4');
        $processor = AutoProcessor::fromPretrained('briaai/RMBG-1.4');

        $image = Image::read($url);

        ['pixel_values' => $pixelValues] = $processor($image);

        ['output' => $output] = $model(['input' => $pixelValues]);

        $mask = Image::fromTensor($output[0]->multiply(255))
            ->resize($image->width(), $image->height());

        $maskedName = pathinfo($path, PATHINFO_FILENAME) . '_masked';
        $maskedPath = "processed/$maskedName.png";  // Changed from "images" to "processed"

        // Ensure the directory exists
        Storage::disk('public')->makeDirectory(dirname($maskedPath));

        $maskedImage = $image->applyMask($mask);
        $maskedImage->save(Storage::disk('public')->path($maskedPath));

        return $maskedPath;
    }
}
