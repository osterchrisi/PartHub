<div class="row justify-content-center mb-5">
    <div class="col-md-8">
        <div class="card card-whatis shadow-sm rounded">
            <div class="card-body text-center {{ $bgClass }}">
                <h2 class="display-4 mb-4">{{ $title }}</h2>
                <img src="{{ $imageSrc }}" class="img-fluid mb-4" alt="{{ $imageAlt }}">
                <p class="lead">{{ $content }}</p>
                @if (isset($listItems))
                    <ul class="list-group lead text-start">
                        @foreach ($listItems as $item)
                            <li class="list-group-item active">{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
                @isset($ctaText)
                    <a href="{{ $ctaLink }}" class="btn btn-lg btn-outline-light ml-2 cta-btn mt-5">{{ $ctaText }}</a>
                @endisset
            </div>
        </div>
    </div>
</div>
