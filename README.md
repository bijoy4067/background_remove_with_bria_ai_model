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

create a python app evnironment

# Create virtual environment
python3 -m venv venv
/opt/alt/python39/bin/python3.9 -m venv venv
/opt/alt/python39/bin/python3.9 -m venv venv --system-site-packages


# Activate virtual environment
source venv/bin/activate

# Upgrade pip first
pip install --upgrade pip

# Install onnxruntime first
pip install --no-cache-dir onnxruntime

# Install rembg and dependencies
pip install "rembg[cpu,cli]"
rembg i -m birefnet-general-lite "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" 2>&1

TMPDIR=$HOME/ffmpeg_tmp PATH="$HOME/bin:$PATH" PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" ./configure --prefix="$HOME/ffmpeg_build" --pkg-config-flags="--static" --extra-cflags="-I$HOME/ffmpeg_build/include" --extra-ldflags="-L$HOME/ffmpeg_build/lib" --extra-libs="-lpthread -lm" --bindir="$HOME/bin" --enable-gpl --enable-gnutls --enable-libaom --enable-libass --enable-libfdk-aac --enable-libfreetype --enable-libmp3lame --enable-libopus --enable-libsvtav1 --enable-libdav1d --enable-libvorbis --enable-libvpx --enable-libx264 --enable-libx265 --enable-nonfree && PATH="$HOME/bin:$PATH" make && make install && hash -r
wget https://bootstrap.pypa.io/pip/get-pip.py

/opt/alt/python39/bin/python3.9 get-pip.py --user
# pip install onnxruntime-cpu
cd /home/devesnns/virtualenv/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/3.9/bin && ./python rembg i "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/1daa57ac-bdae-4297-bb7c-61f0275eb708.png" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/1daa57ac-bdae-4297-bb7c-61f0275eb708.png" 2>&1

./rembg i -m u2net_custom -x '{"model_path": "~/.u2net/u2netp.onnx"}' "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/59a2c58f-878e-4278-a306-29e139946ba0.jpeg"

./rembg i -m u2net_custom -x '{"model_path": "~/.u2net/u2netp.onnx"}' "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" 2>&1

# Deactivate when done
deactivate
```
source /home/devesnns/virtualenv/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/3.9/bin/activate && cd /home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model

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
cd /home/devesnns/virtualenv/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/3.9/bin && ./python3 rembg i "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" 2>&1
## Usage

./python rembg i -m u2netp "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/uploads/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" "/home/devesnns/location-api.developer-bijoy.com/background_remove_with_bria_ai_model/storage/app/public/processed/59a2c58f-878e-4278-a306-29e139946ba0.jpeg" 2>&1

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