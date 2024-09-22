<div class="row justify-content-center mb-5">
    <div class="col ">
        <div class="card card-whatis shadow-sm rounded">
            <div class="card-body text-center {{ $bgClass }}"
                @if (isset($opacity)) style="--bs-bg-opacity: .75;" @endif>
                <h2 class="display-4 mb-4">{{ $title }}</h2>
                @if ($imageSrc)
                    <img src="{{ $imageSrc }}"
                        class="img-fluid mb-4 @if (isset($imageExtraClass)) {{ $imageExtraClass }} @endif"
                        alt="{{ $imageAlt }}">
                @endif
                @if (isset($content))
                    <p class="lead">{{ $content }}</p>
                @else
                    {{ $slot }} <!-- Allows you to pass complex HTML content -->
                @endif
                @if (isset($listItems))
                    <ul class="list-group lead text-start">
                        @foreach ($listItems as $item)
                            <li class="list-group-item active">{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
                @isset($ctaText)
                    <a href="{{ $ctaLink }}"
                        class="btn btn-lg btn-outline-light ml-2 cta-btn mt-5">{{ $ctaText }}</a>
                @endisset
            </div>
        </div>
    </div>
</div>
