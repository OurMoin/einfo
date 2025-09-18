@extends('frontend.master')
@section('main-content')
<div class="py-4">
     <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Job Title</th>
                                    <th>Category</th>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Area</th>
                                    <th>Verified</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        @if($user->image)
                                            <img src="{{ asset('profile-image/' . $user->image) }}" alt="{{ $user->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('profile-image/default.png') }}" alt="Default" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->role }}</span>
                                    </td>
                                    <td>{{ $user->job_title ?? 'N/A' }}</td>
                                    <td>{{ $user->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $user->country->name ?? 'N/A' }}</td>
                                    <td>{{ $user->city->name ?? 'N/A' }}</td>
                                    <td>{{ $user->area ?? 'N/A' }}</td>
                                    <td>{{ $user->email_verified ?? 'N/A' }}</td>
                                    <td>
                                        <a href="/{{ $user->username }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View Profile
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                   
                </div>
</div>
@endsection