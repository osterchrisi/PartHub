<?php
$version = file_get_contents('VERSION');
?>


<div class="container-fluid px-0 bg-body-tertiary text-dark" style="z-index: 2;">
    <table class="table table-bordered text-center px-0 mx-auto w-auto" style="borders: false">
        <tr>
            <td>
                <div>
                    <p class="fs-6 lead" data-bs-toggle="tooltip" data-bs-title="{{ $version }}">&copy; 2024 PartHub. All rights reserved.</p>
                </div>
            </td>
            <td>
                <div>
                    <p class="fs-6 lead"><a href="{{ route('TOS') }}">Terms of Service</a></p>
                </div>
            </td>
            <td>
                <div>
                    <p class="fs-6 lead"><a href="{{ route('imprint') }}">Imprint</a></p>
                </div>
            </td>
        </tr>
    </table>
</div>
