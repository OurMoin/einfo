@extends("frontend.master")
@section('main-content')

<style>


/* প্রতিটি section এর উপরে offset */
.grid-section {
    scroll-margin-top: 80px; /* এখানে 80px হচ্ছে header এর height */
}

</style>

<div class="mt-4">
   
    <section class="grid-section mb-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title fw-bold text-dark text-center mb-4">Categories</h2>
                </div>
            </div>
            
            <div class="row g-3 g-md-4">
                @php
                    $universalCategories = \App\Models\Category::where('cat_type', 'universal')->where('parent_cat_id', null)->get();
                @endphp
                
                @foreach($universalCategories as $category)
                <div class="col-4">
                    <!-- anchor link using slug -->
                    <a href="#{{ $category->slug }}">
                        <div class="card shadow-sm border-0">
                            <img src="{{ $category->image ?? asset('profile-image/no-image.jpeg') }}" class="card-img-top" alt="{{ $category->category_name }}">
                            <div class="card-body p-2">
                                <h5 class="card-title mb-0">{{ $category->category_name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @foreach($universalCategories as $universalCategory)
    @php
        $profileCategories = \App\Models\Category::where('parent_cat_id', $universalCategory->id)->where('cat_type', 'profile')->get();
        $productCategories = \App\Models\Category::where('parent_cat_id', $universalCategory->id)->whereIn('cat_type', ['product', 'service'])->get();
    @endphp
    
    @if($productCategories->count() > 0)
    <section class="grid-section mb-4" id="{{ $universalCategory->slug }}">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="section-title fw-bold text-dark text-center">{{ $universalCategory->category_name }}</h2>
                </div>
                
                @if($profileCategories->count() > 0)
                <div class="col-12 text-center">
                    @foreach($profileCategories as $index => $profileCat)
                        <a href="{{ route('products.category', $profileCat->slug) }}">{{ $profileCat->category_name }}</a>
                        @if($index < $profileCategories->count() - 1) | @endif
                    @endforeach
                </div>
                @endif
            </div>
            
            <div class="row g-3 g-md-4">
                @foreach($productCategories as $productCat)
                <div class="col-4">
                    <a href="{{ route('products.category', $productCat->slug) }}">
                        <div class="card shadow-sm border-0">
                            <img src="{{ $productCat->image ?? asset('profile-image/no-image.jpeg') }}" class="card-img-top" alt="{{ $productCat->category_name }}">
                            <div class="card-body p-2">
                                <h5 class="card-title mb-0">{{ $productCat->category_name }}</h5>                                                   
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach

</div>
@endsection
