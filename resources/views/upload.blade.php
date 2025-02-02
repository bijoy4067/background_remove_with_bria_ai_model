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
                    <div class="loading mt-3" id="loading">
                        <div class="alert alert-info">
                            Processing image... Please wait...
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function() {
            document.getElementById('loading').classList.add('active');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>