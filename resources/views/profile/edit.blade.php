@extends("frontend.master")
@section('main-content')
<!-- Page Header -->
<div class="container mt-4">
<div class="">
@php
    $passwordError = $errors->userDeletion->first('password');
@endphp
@if($passwordError)
    <div class="text-danger mt-2 mb-4">
        {{ $passwordError }}
    </div>
@endif

    <!-- Update Profile Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Update Profile Information</h5>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf

                <!-- Profile Image 
                <div class="mb-3">
                    <label for="image" class="form-label">Profile Image</label>
                    @if(auth()->user()->image)
                        <div class="mb-3">
                            <img src="{{ asset('profile-image/' . (Auth::user()->image ?? 'default.png')) }}" alt="Current Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            <p class="text-muted small mt-1">Current profile image</p>
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <small class="text-muted">Upload a new profile image (optional)</small>
                    @error('image')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>-->


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

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Username -->
                <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" 
           name="username" 
           id="username" 
           class="form-control @error('username') is-invalid @enderror" 
           value="{{ old('username', auth()->user()->username) }}" 
           placeholder="Enter your username (minimum 4 characters)"
           minlength="4">
    <small class="form-text text-muted">Username must be at least 4 characters long and can contain letters, numbers, dashes and underscores.</small>
    @error('username')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

               <!-- Job Title or Business Category with Suggestions -->
<div class="mb-3">
    <label for="job_title" class="form-label">Job Title or Business Category</label>
    <div style="position: relative;">
        <input type="text" 
               name="job_title" 
               id="job_title" 
               class="form-control @error('job_title') is-invalid @enderror" 
               value="{{ old('job_title', auth()->user()->job_title ?? (auth()->user()->category ? auth()->user()->category->category_name : '')) }}" 
               placeholder="e.g. Software Engineer, Restaurant Owner, etc."
               autocomplete="off">
        <input type="hidden" id="category_id" name="category_id" value="{{ old('category_id', auth()->user()->category_id) }}">
        <div id="job_suggestions" 
             style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000; display: none;">
        </div>
    </div>
    @error('job_title')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
// Job title categories data from backend (only profile type categories)
const jobCategories = @json($categories ?? []);
const jobTitleInput = document.getElementById('job_title');
const categoryIdInput = document.getElementById('category_id');
const jobSuggestionsDiv = document.getElementById('job_suggestions');
let filteredJobCategories = [];

function showJobSuggestions(searchTerm) {
    if (searchTerm.length === 0) {
        jobSuggestionsDiv.style.display = 'none';
        return;
    }

    filteredJobCategories = jobCategories.filter(category =>
        category.category_name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (filteredJobCategories.length === 0) {
        jobSuggestionsDiv.innerHTML = '<div style="padding: 10px 15px; color: #6c757d;">No matching categories found. You can enter your custom job title!</div>';
        jobSuggestionsDiv.style.display = 'block';
        return;
    }

    const suggestionsHtml = filteredJobCategories.map(category => `
        <div style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0;"
             onclick="selectJobCategory(${category.id}, '${category.category_name}')"
             onmouseover="this.style.backgroundColor='#f8f9fa'"
             onmouseout="this.style.backgroundColor='white'">
            ${category.category_name}
        </div>
    `).join('');

    jobSuggestionsDiv.innerHTML = suggestionsHtml;
    jobSuggestionsDiv.style.display = 'block';
}

function selectJobCategory(id, name) {
    jobTitleInput.value = name;
    categoryIdInput.value = id;
    jobSuggestionsDiv.style.display = 'none';
}

jobTitleInput.addEventListener('input', function() {
    const searchValue = this.value.trim();
    
    if (searchValue.length > 0) {
        showJobSuggestions(searchValue);
        
        // Check if typed value exactly matches any existing category
        const exactMatch = jobCategories.find(category => 
            category.category_name.toLowerCase() === searchValue.toLowerCase()
        );
        
        if (exactMatch) {
            categoryIdInput.value = exactMatch.id; // Set existing category ID
        } else {
            categoryIdInput.value = ''; // Clear category_id for custom job title
        }
    } else {
        jobSuggestionsDiv.style.display = 'none';
        categoryIdInput.value = '';
    }
});

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#job_title') && !e.target.closest('#job_suggestions')) {
        jobSuggestionsDiv.style.display = 'none';
    }
});
</script>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    
                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-muted small">
                                Your email address is unverified.
                                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-decoration-underline small">
                                        Click here to re-send the verification email.
                                    </button>
                                </form>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="text-success small mt-1">
                                    A new verification link has been sent to your email address.
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Country Dropdown -->
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <select id="country" name="country_id" class="form-select @error('country_id') is-invalid @enderror">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', auth()->user()->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- City Dropdown -->
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <select id="city" name="city_id" class="form-select @error('city_id') is-invalid @enderror">
                        <option value="">Select City</option>
                        @if(auth()->user()->city_id && isset($cities))
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', auth()->user()->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('city_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Area -->
                <div class="mb-3">
                    <label for="area" class="form-label">Area</label>
                    <input type="text" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', auth()->user()->area) }}" placeholder="Enter your area/locality">
                    @error('area')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
                
                @if (session('status') === 'profile-updated')
                    <span class="text-success ms-2">Profile updated successfully!</span>
                @endif
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Update Password</h5>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <!-- New Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                    @error('password_confirmation')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-warning">Update Password</button>
                
                @if (session('status') === 'password-updated')
                    <span class="text-success ms-2">Password updated successfully!</span>
                @endif
            </form>
        </div>
    </div>

    <!-- Delete User -->
    <div class="card mb-4 border-danger">
        <div class="card-body">
            @include("profile.partials.delete-user-form")
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('country');
        const citySelect = document.getElementById('city');

        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            
            if(countryId) {
                fetch(`/get-cities/${countryId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    data.forEach(function(city) {
                        citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                citySelect.innerHTML = '<option value="">Select City</option>';
            }
        });
    });
</script>
</div>
@endsection