<div class="container-fluid">
    <br>
    <h4>{{ $manufacturer_name ?? '' }}</h4>

    <div class="mt-3">
        <div><strong>Contact:</strong> {{ $manufacturer_contact_name ?? '-' }}</div>
        <div><strong>Email:</strong> {{ $manufacturer_email ?? '-' }}</div>
        <div><strong>Phone:</strong> {{ $manufacturer_phone ?? '-' }}</div>
        <div><strong>Website:</strong>
            @if(!empty($manufacturer_website))
                <a href="{{ $manufacturer_website }}" target="_blank" rel="noopener">{{ $manufacturer_website }}</a>
            @else
                -
            @endif
        </div>
    </div>

    @if(!empty($manufacturer_comment))
        <div class="mt-3">
            <strong>Comment</strong>
            <div class="text-muted">{{ $manufacturer_comment }}</div>
        </div>
    @endif
</div>
