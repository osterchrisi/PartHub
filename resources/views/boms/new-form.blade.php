<div class="container-fluid">
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
            <div class="col-md-4">
                <input class="form-control form-control-sm" name="revision_label"
                    placeholder="Revision label (optional)">
            </div>
            <div class="col-12">
                <textarea class="form-control form-control-sm" name="revision_note" rows="2"
                    placeholder="Revision note (optional)"></textarea>
            </div>
        </div>

        <div class="mt-2">
            <button type="button" class="btn btn-sm btn-primary" id="bomCreateBtn">Create BOM</button>
            <span id="create-response-message" class="ms-2"></span>
        </div>
    </form>

    <hr class="my-3">

    <div id="bomEditorContainer"></div>
</div>
