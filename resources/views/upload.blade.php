<!DOCTYPE html>
<html>
<head>
    <title>Remove Image Background</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .loading {
            display: none;
        }
        .loading.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Remove Image Background</h2>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                        @if(config('app.debug'))
                            <br>
                            <small>{{ session('debug_error') }}</small>
                        @endif
                    </div>
                @endif
                
                <form action="{{ route('remove.background') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Remove Background</button>
                    <button type="button" class="btn btn-success d-none" id="downloadBtn">Download Processed Image</button>
                    <div class="loading mt-3" id="loading">
                        <div class="alert alert-info">
                            Processing image... Please wait...
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- show error if download file not found -->
                <div class="alert alert-danger d-none" id="errorMessage"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('loading').classList.add('active');
            document.getElementById('submitBtn').disabled = true;
            
            const formData = new FormData(this);
            fetch('{{ route('remove.background') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('downloadBtn').classList.remove('d-none');
            document.getElementById('submitBtn').classList.add('d-none'); // Hide submit button
            document.getElementById('downloadBtn').addEventListener('click', function() {
                fetch(data.downloadUrl)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = data.fileName;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        this.classList.add('d-none');
                        document.getElementById('submitBtn').classList.remove('d-none'); // Show submit button again
                    })
                    .catch(error => {
                        document.getElementById('errorMessage').textContent = error.error;
                        document.getElementById('errorMessage').classList.remove('d-none');
                    });
            });
        }
    })
    .catch(error => {
        document.getElementById('errorMessage').textContent = error.error || 'An error occurred while processing the image.';
        document.getElementById('errorMessage').classList.remove('d-none');
    })
    .finally(() => {
        document.getElementById('loading').classList.remove('active');
        document.getElementById('submitBtn').disabled = false;
    });
        });
    </script>
</body>
</html>