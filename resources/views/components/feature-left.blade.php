<div class="row mb-5">
    <div class="col-12 {{ $bgClass ?? 'bg-light' }}">
        <div class="row py-5 align-items-center">
            <!-- Feature Image on the left -->
            <div class="col-lg-6 text-center">
                <img src="{{ $imageSrc }}" alt="{{ $imageAlt }}" class="img-fluid {{ $imageClass ?? 'rounded w-75' }}">
            </div>
            <!-- Feature Text on the right -->
            <div class="col-lg-6">
                <h3 class="display-5 my-4 mx-5">{{ $tagline }}</h3>
                <p class="lead mx-5">{{ $description }}</p>
            </div>
        </div>
    </div>
</div>
