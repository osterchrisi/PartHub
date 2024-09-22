<div class="row mb-5">
    <div class="col-12 {{ $bgClass ?? 'bg-light' }}">
        <div class="row py-5 align-items-center">
            <div class="col-lg-6 order-lg-2 text-center">
                <img src="{{ $imageSrc }}" alt="{{ $imageAlt }}" class="img-fluid {{ $imageClass ?? 'rounded w-75' }}">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="display-4 my-4 mx-5">{{ $title }}</h2>
                <p class="lead mx-5">{!! $content !!}</p>
            </div>
        </div>
    </div>
</div>
