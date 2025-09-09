@extends("frontend.master")
@section('main-content')
<!-- Page Header -->
<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Register</h4>

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                          <div class="mb-3">
                                <label for="image" class="form-label">Profile Image</label>
                                
                                <!-- Image Preview Container -->
                                <div class="mb-3" id="imagePreviewContainer">
                                    <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}" 
                                         alt="Current Profile" 
                                         class="rounded-circle" 
                                         id="profileImagePreview"
                                         style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #dee2e6;">
                                    <p class="text-muted small mt-1" id="imageStatus">Current profile image</p>
                                </div>
                                
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       class="form-control" 
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <small class="text-muted">Upload a new profile image (optional)</small>
                                
                                <!-- Error display -->
                                <div class="text-danger mt-1" id="imageError" style="display: none;"></div>
                            </div>


                 <script>
        function previewImage(input) {
            const file = input.files[0];
            const preview = document.getElementById('profileImagePreview');
            const status = document.getElementById('imageStatus');
            const errorDiv = document.getElementById('imageError');
            
            // Clear any previous errors
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    errorDiv.textContent = 'Please select a valid image file (JPEG, PNG, GIF, WebP)';
                    errorDiv.style.display = 'block';
                    input.value = ''; // Clear the input
                    return;
                }
                
                // Validate file size (2MB limit)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    errorDiv.textContent = 'Image size must be less than 2MB';
                    errorDiv.style.display = 'block';
                    input.value = ''; // Clear the input
                    return;
                }
                
                // Create FileReader to preview image
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    status.textContent = 'New image selected: ' + file.name;
                    status.classList.remove('text-muted');
                    status.classList.add('text-success');
                    
                    // Add a subtle animation
                    preview.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        preview.style.transition = 'transform 0.2s ease';
                        preview.style.transform = 'scale(1)';
                    }, 50);
                };
                
                reader.onerror = function() {
                    errorDiv.textContent = 'Error reading the image file';
                    errorDiv.style.display = 'block';
                    input.value = ''; // Clear the input
                };
                
                reader.readAsDataURL(file);
            } else {
                // Reset to default if no file selected
                preview.src = 'https://via.placeholder.com/80x80/6c757d/ffffff?text=Default';
                status.textContent = 'Current profile image';
                status.classList.remove('text-success');
                status.classList.add('text-muted');
            }
        }
        
        // Optional: Add drag and drop functionality
        const imageInput = document.getElementById('image');
        const imageContainer = document.getElementById('imagePreviewContainer');
        
        imageContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            imageContainer.style.backgroundColor = '#f8f9fa';
            imageContainer.style.border = '2px dashed #007bff';
        });
        
        imageContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            imageContainer.style.backgroundColor = '';
            imageContainer.style.border = '';
        });
        
        imageContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            imageContainer.style.backgroundColor = '';
            imageContainer.style.border = '';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                previewImage(imageInput);
            }
        });
    </script>

                        <!-- Image Upload 
                        <div class="mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>-->

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Title or Business Category -->
                        <div class="mb-3">
                            <label for="job_title" class="form-label">Job Title or Business Category</label>
                            <input id="job_title" type="text" class="form-control @error('job_title') is-invalid @enderror" name="job_title" value="{{ old('job_title') }}" placeholder="e.g. Software Engineer, Restaurant Owner, etc.">
                            @error('job_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>                    

                        <!-- Country Dropdown -->
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select id="country" name="country_id" class="form-select @error('country') is-invalid @enderror" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City Dropdown -->
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <select id="city" name="city_id" class="form-select @error('city') is-invalid @enderror" required>
                                <option value="">Select City</option>
                            </select>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <input id="area" type="text" class="form-control @error('area') is-invalid @enderror" name="area" value="{{ old('area') }}" placeholder="Enter your area/locality">
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                          <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="text-decoration-underline">Already registered?</a>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#country').change(function() {
            var country_id = $(this).val();
            if(country_id) {
                $.ajax({
                    url: '/get-cities/'+country_id, // route to get cities
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('#city').empty();
                        $('#city').append('<option value="">Select City</option>');
                        $.each(data, function(key, value) {
                            $('#city').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                    }
                });
            } else {
                $('#city').empty();
                $('#city').append('<option value="">Select City</option>');
            }
        });
    });
</script>


@endsection