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
        $totalCategories = $universalCategories->count();
        
        // Cards per row: 3 for sm, 4 for lg
        $cardsPerRowSm = 3;
        $cardsPerRowLg = 4;
        
        // Calculate complete rows
        $completeRowsSm = intval($totalCategories / $cardsPerRowSm);
        $completeRowsLg = intval($totalCategories / $cardsPerRowLg);
        
        // Calculate remaining cards in last row
        $remainingSm = $totalCategories % $cardsPerRowSm;
        $remainingLg = $totalCategories % $cardsPerRowLg;
        
        // Show "See More" only if the last row is incomplete
        $needSeeMoreSm = $remainingSm > 0;
        $needSeeMoreLg = $remainingLg > 0;
        
        // Position where "See More" should appear (last card of last complete row)
        $seeMorePositionSm = $needSeeMoreSm ? ($completeRowsSm * $cardsPerRowSm) - 1 : -1;
        $seeMorePositionLg = $needSeeMoreLg ? ($completeRowsLg * $cardsPerRowLg) - 1 : -1;
    @endphp

    @foreach($universalCategories as $index => $category)
        @php
            // Hide categories after see more position
            $hiddenOnSm = $needSeeMoreSm && $index >= $seeMorePositionSm;
            $hiddenOnLg = $needSeeMoreLg && $index >= $seeMorePositionLg;
        @endphp

        {{-- Regular category card --}}
        <div class="col-4 col-sm-4 col-lg-3 text-center mb-3 category-card 
            @if($hiddenOnSm) category-hidden-sm @endif 
            @if($hiddenOnLg) category-hidden-lg @endif"
            data-index="{{ $index }}">
            <a href="#{{ $category->slug }}" class="text-decoration-none">                  
                <div class="mx-auto mb-2" style="width: 80px; height: 80px; overflow: hidden;">
                    <img src="{{ $category->image ?? asset('profile-image/no-image.jpeg') }}" 
                        alt="{{ $category->category_name }}" 
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <span style="display: block; font-size: 14px; color: #000;">{{ $category->category_name }}</span>
                </div>
            </a>
        </div>

        {{-- Show "See More" button at the right position for SM --}}
        @if($needSeeMoreSm && $index == $seeMorePositionSm)
            <div class="col-4 col-sm-4 d-lg-none text-center mb-3 toggle-btn-sm" id="toggleBtnSm">
                <a href="javascript:void(0);" class="text-decoration-none" onclick="toggleCategoriesSm()">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                        <span style="font-size: 14px; color: #888;" id="toggleTextSm">See More</span>
                    </div>
                </a>
            </div>
        @endif

        {{-- Show "See More" button at the right position for LG --}}
        @if($needSeeMoreLg && $index == $seeMorePositionLg)
            <div class="d-none d-lg-block col-lg-3 text-center mb-3 toggle-btn-lg" id="toggleBtnLg">
                <a href="javascript:void(0);" class="text-decoration-none" onclick="toggleCategoriesLg()">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f5f5f5; border-radius: 8px; cursor: pointer;">
                        <span style="font-size: 14px; color: #888;" id="toggleTextLg">See More</span>
                    </div>
                </a>
            </div>
        @endif
    @endforeach
</div>

<style>
    /* Hide on small screens */
    @media (max-width: 991px) {
        .category-hidden-sm {
            display: none !important;
        }
    }
    
    /* Hide on large screens */
    @media (min-width: 992px) {
        .category-hidden-lg {
            display: none !important;
        }
    }
    
    /* Ensure flexbox order works */
    .row {
        display: flex;
        flex-wrap: wrap;
    }
</style>

<script>
    let expandedSm = false;
    let expandedLg = false;

    function toggleCategoriesSm() {
        expandedSm = !expandedSm;
        const toggleBtn = document.getElementById('toggleBtnSm');
        const toggleText = document.getElementById('toggleTextSm');
        
        if (expandedSm) {
            // Show hidden categories
            document.querySelectorAll('.category-hidden-sm').forEach(function(card) {
                card.classList.remove('category-hidden-sm');
                card.style.display = 'block';
            });
            
            // Change button text to "See Less"
            toggleText.textContent = 'See Less';
            
            // Move button to the end
            const row = toggleBtn.parentElement;
            row.appendChild(toggleBtn);
        } else {
            // Hide categories again
            document.querySelectorAll('.category-card').forEach(function(card) {
                const index = parseInt(card.getAttribute('data-index'));
                if (index >= {{ $seeMorePositionSm }}) {
                    card.classList.add('category-hidden-sm');
                }
            });
            
            // Change button text back to "See More"
            toggleText.textContent = 'See More';
            
            // Move button back to original position
            const row = toggleBtn.parentElement;
            const cards = row.querySelectorAll('.category-card');
            let insertPosition = null;
            
            cards.forEach(function(card) {
                const index = parseInt(card.getAttribute('data-index'));
                if (index === {{ $seeMorePositionSm }}) {
                    insertPosition = card;
                }
            });
            
            if (insertPosition && insertPosition.nextSibling) {
                row.insertBefore(toggleBtn, insertPosition.nextSibling);
            }
        }
    }
    
    function toggleCategoriesLg() {
        expandedLg = !expandedLg;
        const toggleBtn = document.getElementById('toggleBtnLg');
        const toggleText = document.getElementById('toggleTextLg');
        
        if (expandedLg) {
            // Show hidden categories
            document.querySelectorAll('.category-hidden-lg').forEach(function(card) {
                card.classList.remove('category-hidden-lg');
                card.style.display = 'block';
            });
            
            // Change button text to "See Less"
            toggleText.textContent = 'See Less';
            
            // Move button to the end
            const row = toggleBtn.parentElement;
            row.appendChild(toggleBtn);
        } else {
            // Hide categories again
            document.querySelectorAll('.category-card').forEach(function(card) {
                const index = parseInt(card.getAttribute('data-index'));
                if (index >= {{ $seeMorePositionLg }}) {
                    card.classList.add('category-hidden-lg');
                }
            });
            
            // Change button text back to "See More"
            toggleText.textContent = 'See More';
            
            // Move button back to original position
            const row = toggleBtn.parentElement;
            const cards = row.querySelectorAll('.category-card');
            let insertPosition = null;
            
            cards.forEach(function(card) {
                const index = parseInt(card.getAttribute('data-index'));
                if (index === {{ $seeMorePositionLg }}) {
                    insertPosition = card;
                }
            });
            
            if (insertPosition && insertPosition.nextSibling) {
                row.insertBefore(toggleBtn, insertPosition.nextSibling);
            }
        }
    }
</script>
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

                <!-- <div class="col-4">
                    <a href="{{ route('products.category', $productCat->slug) }}">
                        <div class="card shadow-sm border-0">
                            <img src="{{ $productCat->image ?? asset('profile-image/no-image.jpeg') }}" class="card-img-top" alt="{{ $productCat->category_name }}">
                            <div class="card-body p-2">
                                <h5 class="card-title mb-0">{{ $productCat->category_name }}</h5>                                                   
                            </div>
                        </div>
                    </a>
                </div> -->

                <div class="col-4 col-sm-4 col-lg-3 text-center mb-3">
                    <a href="{{ route('products.category', $productCat->slug) }}" class="text-decoration-none">
                        <div class="mx-auto mb-2" style="width: 80px; height: 80px; overflow: hidden;">
                            <img src="{{ $productCat->image ?? asset('profile-image/no-image.jpeg') }}" 
                                alt="{{ $productCat->category_name }}" 
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <span style="display: block; font-size: 14px; color: #000;">{{ $productCat->category_name }}</span>
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
