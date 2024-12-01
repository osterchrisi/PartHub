@php
    $version = file_get_contents('../VERSION');
@endphp

<div class="container-fluid bg-light bg-gradient bg-opacity-10 text-dark pb-1" id="footer">
    <table class="table table-borderless text-center mx-auto w-auto footer-table">
        <tr>
            <td>
                <div>
                    <span class="fs-6 lead">&copy; 2024 </span><span class="fs-6 lead" data-bs-toggle="tooltip"
                        data-bs-title="{{ $version }}">PartHub</span><span class="fs-6 lead">. All rights reserved.
                        </p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('TOS') }}">Terms of Service</a></p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('privacy-policy') }}">Privacy Policy</a></p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"> <a href="#" class="termly-display-preferences">Consent
                            Preferences</a></p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('imprint') }}">Imprint</a></p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('support') }}">Support</a></p>
                </div>
            </td>
        </tr>
    </table>
</div>
