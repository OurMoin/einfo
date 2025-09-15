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

                        

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                            @error('name')
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