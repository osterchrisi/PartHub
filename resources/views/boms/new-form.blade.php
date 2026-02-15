<div class="container-fluid mt-3">
    <h5>New BOM</h5>

    <form id="bomCreateForm" method="post">
        @csrf

        <div class="row g-2">
            <div class="col-md-4">
                <input class="form-control form-control-sm" name="bom_name" placeholder="BOM Name" required>
            </div>
            <div class="col-md-4">
                <input class="form-control form-control-sm" name="bom_description" placeholder="BOM Description">
            </div>
        </div>

        <hr class="my-3">

        <div id="bomEditorContainer">
            @include('boms.editor')
        </div>

        <div class="mt-2">
            <button type="button" class="btn btn-sm btn-primary" id="bomCreateBtn">Create BOM</button>
            <span id="create-response-message" class="ms-2"></span>
        </div>
    </form>
</div>
