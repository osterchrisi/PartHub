<div @class(['row hero-section rounded-2 mb-5', $divExtraClass ?? ''])
    style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $backgroundImage }}'); background-size: cover; background-blend-mode: darken; background-position: center center;
">
    <div class="col-md-8">
        @isset($slot)
            <div class="mb-4">
                {{ $slot }}
            </div>
        @endisset

        <h1 class="display-1 mb-4 {{ $titleExtraClass ?? '' }}">{{ $title }}</h1>
        <p class="lead mb-0 signup-gradient-background text-white p-2 rounded d-inline-block p-hero">{{ $subtitle }}</p><br>
        <a href="{{ $firstButtonRoute }}" class="btn btn-lg btn-light gradient-hero-button extra-shadow fs-2 mt-4 me-sm-2" id="demo-hero">{{ $firstButtonText }}</a>
        <a href="{{ $secondButtonRoute }}" class="btn btn-lg gradient-hero-button btn-light extra-shadow fs-2 mt-4 ms-sm-2">{{ $secondButtonText }}</a>
    </div>
</div>
