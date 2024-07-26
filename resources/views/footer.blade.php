<?php
$version = file_get_contents('VERSION');
?>


<div class="container-fluid px-0 bg-body-tertiary text-dark" style="z-index: 2;">
    <table class="table table-borderless text-center mx-auto w-auto footer-table">
        <tr>
            <td>
                <div>
                    <span class="fs-6 lead">&copy; 2024 </span><span class="fs-6 lead" data-bs-toggle="tooltip" data-bs-title="{{ $version }}">PartHub</span><span class="fs-6 lead">. All rights reserved.</p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('TOS') }}">Terms of Service</a></p>
                </div>
            </td>
            <td>
                <div>
                    <span class="fs-6 lead"><a href="{{ route('imprint') }}">Imprint</a></p>
                </div>
            </td>
        </tr>
    </table>
</div>
