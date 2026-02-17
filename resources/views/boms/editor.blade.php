{{-- boms/editor.blade.php --}}
<div id="bomEditorUi" class="d-none">
    <div class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-4">
            <label class="form-label mb-1"><small>Revision label (optional)</small></label>
            <input type="text" class="form-control form-control-sm" id="bomEditorRevLabel">
        </div>

        <div class="col-12 col-md-8">
            <label class="form-label mb-1"><small>Revision note (optional)</small></label>
            <input type="text" class="form-control form-control-sm" id="bomEditorRevNote">
        </div>
    </div>

    <div class="row g-2 align-items-end mb-2">
        <div class="col-12 col-md-3">
            <label class="form-label mb-1"><small>Part</small></label>
            <div id="bomEditorAddPartDropdown"></div>
        </div>

        <div class="col-6 col-md-2">
            <label class="form-label mb-1"><small>Qty</small></label>
            <input type="number" min="1" step="1" class="form-control form-control-sm text-end"
                id="bomEditorAddQty" value="1">
        </div>

        <div class="col-12 col-md">
            <label class="form-label mb-1"><small>Refdes (optional)</small></label>
            <input type="text" class="form-control form-control-sm" id="bomEditorAddRefdes" placeholder="R1,R2,...">
        </div>

        <div class="col-12 col-md-auto">
            <button type="button" class="btn btn-sm btn-outline-primary w-100" id="bomEditorAddLineBtn" disabled>
                Add line
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover table-striped align-middle" style="font-size:12px">
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th style="width: 110px;" class="text-end">Qty</th>
                    <th>Refdes</th>
                    <th style="width: 90px;" class="text-end">Part ID</th>
                    <th style="width: 54px;"></th>
                </tr>
            </thead>
            <tbody id="bomEditorTbody">
                <tr id="bomEditorEmptyRow">
                    <td colspan="5" class="text-muted">No lines</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Row template --}}
    <template id="bomEditorRowTpl">
        <tr>
            <td class="text-truncate" style="max-width: 1px;" data-slot="part_name"></td>

            <td style="width: 110px;">
                <input type="number" min="1" step="1"
                    class="form-control form-control-sm bom-editor-qty text-end" data-slot="qty">
            </td>

            <td style="min-width: 180px;">
                <input type="text" class="form-control form-control-sm bom-editor-refdes" data-slot="refdes"
                    placeholder="R1,R2,...">
            </td>

            <td style="width: 90px;" class="text-end text-muted">
                <small data-slot="part_id"></small>
            </td>

            <td style="width: 54px;" class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger bom-editor-remove-line" data-slot="remove">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>
</div>
