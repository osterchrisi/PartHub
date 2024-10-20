<div @class(['row hero-section rounded-3 mb-5', $divExtraClass ?? ''])
    style="background-image: url('{{ $backgroundImage }}'); background-size: cover; background-position: center;">
    <div class="col-md-8">
        <h1 class="display-1 mb-4">{{ $title }}</h1>
        <p class="lead mb-4 bg-dark text-white p-2 rounded d-inline-block p-hero">{{ $subtitle }}</p><br>
        <a href="{{ $demoRoute }}" class="btn btn-lg btn-light gradient-hero-button extra-shadow me-2 fs-2" id="demo-hero">Explore Demo</a>
        <a href="{{ $signupRoute }}" class="btn btn-lg gradient-hero-button btn-light extra-shadow ml-2 fs-2">Sign Up Now</a>
    </div>
</div>
