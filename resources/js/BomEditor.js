export class BomEditor {
    constructor() {
        this.draft = null;
    }

    mount() {
        // Use event delegation because info-window HTML is replaced
        $(document).off('click', '#bomEditBtn').on('click', '#bomEditBtn', () => this.enterEditMode());
        $(document).off('click', '#bomEditCancelBtn').on('click', '#bomEditCancelBtn', () => this.exitEditMode());
        $(document).off('click', '#bomEditSaveBtn').on('click', '#bomEditSaveBtn', () => this.saveDraft());

        // Editor row events
        $(document).off('click', '.bom-editor-remove-line').on('click', '.bom-editor-remove-line', (e) => {
            const lineUuid = $(e.currentTarget).data('line-uuid');
            this.removeLine(lineUuid);
        });

        $(document).off('input', '.bom-editor-qty').on('input', '.bom-editor-qty', (e) => {
            const lineUuid = $(e.currentTarget).data('line-uuid');
            this.updateLineQty(lineUuid, $(e.currentTarget).val());
        });

        $(document).off('input', '.bom-editor-refdes').on('input', '.bom-editor-refdes', (e) => {
            const lineUuid = $(e.currentTarget).data('line-uuid');
            this.updateLineRefdes(lineUuid, $(e.currentTarget).val());
        });

        $(document).off('click', '#bomEditorAddLineBtn').on('click', '#bomEditorAddLineBtn', () => this.addLineFromInputs());
    }

    enterEditMode() {
        // Build draft from DOM
        this.draft = this.buildDraftFromDom();

        // Toggle UI
        $('#info-window').addClass('bom-editing');
        $('#bomDetailsViewContainer').addClass('d-none');
        $('#bomEditorContainer').removeClass('d-none');

        $('#bomEditBtn').addClass('d-none');
        $('#bomEditCancelBtn, #bomEditSaveBtn, #bomEditHint').removeClass('d-none');

        // Render editor
        this.renderEditor();
    }

    exitEditMode() {
        this.draft = null;

        $('#info-window').removeClass('bom-editing');
        $('#bomEditorContainer').addClass('d-none').empty();
        $('#bomDetailsViewContainer').removeClass('d-none');

        $('#bomEditBtn').removeClass('d-none');
        $('#bomEditCancelBtn, #bomEditSaveBtn, #bomEditHint').addClass('d-none');
    }

    buildDraftFromDom() {
        // Try to get bom_id from toolbar dataset (prefer passing from controller later)
        const bomId = $('#bomEditorToolbar').data('bom-id') || null;

        const lines = [];
        $('#BomDetailsTable tbody tr').each((_, tr) => {
            const $tr = $(tr);

            // Your row has data-id = part_id
            const partId = parseInt($tr.data('id'), 10);
            if (!partId) return;

            // Qty cell has data-column="element_quantity"
            const qtyCell = $tr.find('td[data-column="element_quantity"] #contentSpan').first();
            const qty = qtyCell.length ? parseInt(qtyCell.text().trim(), 10) : NaN;
            if (!Number.isInteger(qty) || qty <= 0) return;

            // Merge by part_id (current policy)
            const existing = lines.find(l => l.part_id === partId);
            if (existing) {
                existing.quantity += qty;
            } else {
                lines.push({
                    line_uuid: crypto.randomUUID ? crypto.randomUUID() : `line_${Date.now()}_${Math.random()}`,
                    part_id: partId,
                    quantity: qty,
                    refdes: '' // v1 empty, later editable
                });
            }
        });

        return {
            bom_id: bomId,
            base_revision_id: null,
            lines
        };
    }

    renderEditor() {
        const rowsHtml = this.draft.lines.map(line => `
            <tr>
                <td style="width: 90px;">${line.part_id}</td>
                <td style="width: 140px;">
                    <input type="number" min="1" step="1"
                        class="form-control form-control-sm bom-editor-qty"
                        data-line-uuid="${line.line_uuid}"
                        value="${line.quantity}">
                </td>
                <td>
                    <input type="text"
                        class="form-control form-control-sm bom-editor-refdes"
                        data-line-uuid="${line.line_uuid}"
                        value="${this.escapeHtml(line.refdes || '')}"
                        placeholder="R1,R2,... (optional)">
                </td>
                <td style="width: 60px;" class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger bom-editor-remove-line"
                        data-line-uuid="${line.line_uuid}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        const html = `
            <div class="mb-2 d-flex gap-2 align-items-end">
                <div style="width: 120px;">
                    <label class="form-label mb-1"><small>Part ID</small></label>
                    <input type="number" min="1" step="1" class="form-control form-control-sm" id="bomEditorAddPartId">
                </div>
                <div style="width: 120px;">
                    <label class="form-label mb-1"><small>Qty</small></label>
                    <input type="number" min="1" step="1" class="form-control form-control-sm" id="bomEditorAddQty" value="1">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label mb-1"><small>Refdes (optional)</small></label>
                    <input type="text" class="form-control form-control-sm" id="bomEditorAddRefdes" placeholder="R1,R2,...">
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="bomEditorAddLineBtn">
                    Add line
                </button>
            </div>

            <table class="table table-sm table-hover table-striped" style="font-size:12px">
                <thead>
                    <tr>
                        <th style="width: 90px;">Part ID</th>
                        <th style="width: 140px;">Qty</th>
                        <th>Refdes</th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml || `<tr><td colspan="4" class="text-muted">No lines</td></tr>`}
                </tbody>
            </table>
        `;

        $('#bomEditorContainer').html(html);
    }

    addLineFromInputs() {
        const partId = parseInt($('#bomEditorAddPartId').val(), 10);
        const qty = parseInt($('#bomEditorAddQty').val(), 10);
        const refdes = ($('#bomEditorAddRefdes').val() || '').trim();

        if (!Number.isInteger(partId) || partId <= 0) return;
        if (!Number.isInteger(qty) || qty <= 0) return;

        // Merge by part_id
        const existing = this.draft.lines.find(l => l.part_id === partId);
        if (existing) {
            existing.quantity += qty;
            if (refdes) {
                existing.refdes = existing.refdes ? `${existing.refdes},${refdes}` : refdes;
            }
        } else {
            this.draft.lines.push({
                line_uuid: crypto.randomUUID ? crypto.randomUUID() : `line_${Date.now()}_${Math.random()}`,
                part_id: partId,
                quantity: qty,
                refdes
            });
        }

        // Reset quick-add inputs
        $('#bomEditorAddPartId').val('');
        $('#bomEditorAddQty').val('1');
        $('#bomEditorAddRefdes').val('');

        this.renderEditor();
    }

    removeLine(lineUuid) {
        this.draft.lines = this.draft.lines.filter(l => l.line_uuid !== lineUuid);
        this.renderEditor();
    }

    updateLineQty(lineUuid, rawValue) {
        const qty = parseInt(rawValue, 10);
        const line = this.draft.lines.find(l => l.line_uuid === lineUuid);
        if (!line) return;

        // keep it strict: qty >= 1
        if (Number.isInteger(qty) && qty >= 1) {
            line.quantity = qty;
        }
    }

    updateLineRefdes(lineUuid, value) {
        const line = this.draft.lines.find(l => l.line_uuid === lineUuid);
        if (!line) return;
        line.refdes = (value || '').trim();
    }

    saveDraft() {
        // Phase A: no backend yet
        console.log('BOM editor draft payload:', this.draft);
        alert('Save not wired yet. Draft payload logged to console.');
    }

    escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
}
