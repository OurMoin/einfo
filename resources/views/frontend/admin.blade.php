@extends('frontend.master')
@section('main-content')
<div class="py-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <!-- <th>Image</th> -->
                        <th>Name</th>                                  
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
                            <a href="/{{ $user->username }}">
                                {{ $user->name }} <span class="badge bg-primary">{{ $user->role }}</span>
                            </a>
                        </td>
                        <td>{{ $user->job_title ?? 'N/A' }}</td>
                        <td>{{ $user->category->category_name ?? 'N/A' }}</td>
                        <td>{{ $user->country->name ?? 'N/A' }}</td>
                        <td>{{ $user->city->name ?? 'N/A' }}</td>
                        <td>{{ $user->area ?? 'N/A' }}</td>
                        <td>{{ $user->email_verified ?? 'N/A' }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection