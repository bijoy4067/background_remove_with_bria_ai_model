```markdown:/Users/bijoykarmokar/public_html/remove_background/README.md
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Background Removal Application

A Laravel application that removes backgrounds from images using rembg.

## Setup Instructions

1. Install Python 3 and pip:
```bash
brew install python3
```

2. Set up the environment:
```bash
# Navigate to project directory
cd /Users/bijoykarmokar/public_html/remove_background

# Create virtual environment
python3 -m venv venv

# Activate virtual environment
source venv/bin/activate

# Install rembg and dependencies
pip install "rembg[cpu,cli]"

# pip install onnxruntime-cpu

# Deactivate when done
deactivate
```

3. Configure permissions and Laravel:
```bash
# Set permissions
chmod -R 755 venv

# Install Laravel dependencies
composer install

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

## Usage

1. Access the application through your web browser
2. Upload an image (supported formats: JPEG, PNG, JPG)
3. The processed image will be downloaded automatically with the background removed

## Notes

- Maximum upload size: 5MB
- Supported image formats: JPEG, PNG, JPG
- Processed images are automatically deleted after download

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
```