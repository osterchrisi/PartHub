<div @class(['row hero-section rounded-3 mb-5', $divExtraClass ?? ''])
    style="background-image: url('{{ $backgroundImage }}'); background-size: cover; background-position: center;">
    <div class="col-md-8">
        <h1 class="display-1 mb-4 {{ $titleExtraClass ?? '' }}">{{ $title }}</h1>
        <p class="lead mb-0 bg-dark text-white p-2 rounded d-inline-block p-hero">{{ $subtitle }}</p><br>
        <a href="{{ $firstButtonRoute }}" class="btn btn-lg btn-light gradient-hero-button extra-shadow fs-2 mt-4 me-sm-2" id="demo-hero">{{ $firstButtonText }}</a>
        <a href="{{ $secondButtonRoute }}" class="btn btn-lg gradient-hero-button btn-light extra-shadow fs-2 mt-4 ms-sm-2">{{ $secondButtonText }}</a>
    </div>
</div>
