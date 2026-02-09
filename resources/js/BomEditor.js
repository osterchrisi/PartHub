import { updateInfoWindow } from "./custom";
import { DataFetchService } from "./Services/DataFetchService";

export class BomEditor {
    constructor() {
        this.draft = null;
        this.partsIndex = null; // part_id -> part_name
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
        const bomId = $('#bomEditorToolbar').data('bom-id') || null;
        if (!bomId) { alert('Missing bom_id'); return; }

        // Toggle UI early
        $('#info-window').addClass('bom-editing');
        $('#bomDetailsViewContainer').addClass('d-none');
        $('#bomEditorContainer').removeClass('d-none');
        $('#bomEditBtn').addClass('d-none');
        $('#bomEditCancelBtn, #bomEditSaveBtn, #bomEditHint').removeClass('d-none');

        // Load both: editor payload + parts index
        const payloadReq = $.getJSON(`/bom/${bomId}/editor`);
        const partsReq = this.loadPartsIndex(); // fires /parts.get

        $.when(payloadReq, partsReq).done((payloadRes) => {
            // jQuery returns [data, status, xhr] for getJSON in $.when
            const payload = payloadRes[0];

            this.draft = {
                bom_id: payload.bom.bom_id,
                base_revision_id: payload.revision.bom_revision_id,
                lines: payload.lines.map(l => ({
                    line_uuid: l.line_uuid || (crypto.randomUUID ? crypto.randomUUID() : `line_${Date.now()}_${Math.random()}`),
                    part_id: l.part_id,
                    part_name: l.part_name || (this.partsIndex?.[l.part_id] || ''),
                    quantity: l.quantity,
                    refdes: l.refdes || '',
                }))
            };

            this.renderEditor(); // now button can be enabled
        }).fail((xhr) => {
            console.error(xhr);
            alert(xhr?.responseJSON?.error || xhr?.responseJSON?.message || 'Failed to load editor data.');
            this.exitEditMode();
        });
    }



    exitEditMode() {
        this.draft = null;
        this.partsIndex = null;

        $('#info-window').removeClass('bom-editing');
        $('#bomEditorContainer').addClass('d-none').empty();
        $('#bomDetailsViewContainer').removeClass('d-none');

        $('#bomEditBtn').removeClass('d-none');
        $('#bomEditCancelBtn, #bomEditSaveBtn, #bomEditHint').addClass('d-none');
    }

    buildDraftFromDom() {
        const bomId = $('#bomEditorToolbar').data('bom-id') || null;

        const lines = [];
        $('#BomDetailsTable tbody tr').each((_, tr) => {
            const $tr = $(tr);

            //Debug
            console.log('row', {
                partId: $tr.data('part-id'),
                qtyText: $tr.find('td[data-column="quantity"]').text(),
            });


            // Robust extraction:
            // - row's data-id is part_id in your HTML
            // - qty cell's data-id is bom_elements_id in your HTML
            const partId = parseInt(($tr.data('part-id') ?? $tr.data('id')), 10);
            if (!Number.isInteger(partId) || partId <= 0) return;

            const $qtyTd = $tr.find('td[data-column="quantity"]').first();
            const bomElementId = parseInt(($tr.data('bom-element-id') ?? $qtyTd.data('id')), 10) || null;

            const partName = ($tr.find('td[data-column="part_name"]').text() || '').trim();

            // qty is displayed in #contentSpan (editable cell component)
            const qtySpan = $qtyTd.find('#contentSpan').first();
            const qty = qtySpan.length ? parseInt(qtySpan.text().trim(), 10) : NaN;
            if (!Number.isInteger(qty) || qty <= 0) return;

            // Merge by part_id (current policy)
            const existing = lines.find(l => l.part_id === partId);
            if (existing) {
                existing.quantity += qty;
            } else {
                lines.push({
                    line_uuid: crypto.randomUUID ? crypto.randomUUID() : `line_${Date.now()}_${Math.random()}`,
                    part_id: partId,
                    part_name: partName,
                    quantity: qty,
                    refdes: '',
                    bom_element_id: bomElementId,
                });
            }
        });

        return {
            bom_id: bomId,
            base_revision_id: null,
            lines,
        };
    }

    renderEditor() {
        const rowsHtml = this.draft.lines.map(line => `
            <tr>
                <td class="text-truncate" style="max-width: 1px;">
                    ${this.escapeHtml(line.part_name || '')}
                </td>

                <td style="width: 110px;">
                    <input type="number" min="1" step="1"
                        class="form-control form-control-sm bom-editor-qty text-end"
                        data-line-uuid="${line.line_uuid}"
                        value="${line.quantity}">
                </td>

                <td style="min-width: 180px;">
                    <input type="text"
                        class="form-control form-control-sm bom-editor-refdes"
                        data-line-uuid="${line.line_uuid}"
                        value="${this.escapeHtml(line.refdes || '')}"
                        placeholder="R1,R2,...">
                </td>

                <td style="width: 90px;" class="text-end text-muted">
                    <small>${line.part_id}</small>
                </td>

                <td style="width: 54px;" class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger bom-editor-remove-line"
                        data-line-uuid="${line.line_uuid}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        const addDisabled = this.partsIndex ? '' : 'disabled';

        const html = `
            <div class="row g-2 align-items-end mb-2">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1"><small>Part ID</small></label>
                    <input type="number" min="1" step="1" class="form-control form-control-sm" id="bomEditorAddPartId">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label mb-1"><small>Qty</small></label>
                    <input type="number" min="1" step="1" class="form-control form-control-sm text-end" id="bomEditorAddQty" value="1">
                </div>

                <div class="col-12 col-md">
                    <label class="form-label mb-1"><small>Refdes (optional)</small></label>
                    <input type="text" class="form-control form-control-sm" id="bomEditorAddRefdes" placeholder="R1,R2,...">
                </div>

                <div class="col-12 col-md-auto">
                    <button type="button" class="btn btn-sm btn-outline-primary w-100" id="bomEditorAddLineBtn" ${addDisabled}>
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
                    <tbody>
                        ${rowsHtml || `<tr><td colspan="5" class="text-muted">No lines</td></tr>`}
                    </tbody>
                </table>
            </div>
        `;

        $('#bomEditorContainer').html(html);
    }

    addLineFromInputs() {

        const partId = parseInt($('#bomEditorAddPartId').val(), 10);
        const qty = parseInt($('#bomEditorAddQty').val(), 10);
        const refdes = ($('#bomEditorAddRefdes').val() || '').trim();

        if (!Number.isInteger(partId) || partId <= 0) return;
        if (!Number.isInteger(qty) || qty <= 0) return;

        if (!this.partsIndex) { alert('Parts list not loaded yet. Try again.'); return; }

        const partName = this.partsIndex[partId] || '';

        const existing = this.draft.lines.find(l => l.part_id === partId);
        if (existing) {
            existing.quantity += qty;
            if (refdes) existing.refdes = existing.refdes ? `${existing.refdes},${refdes}` : refdes;
            if (!existing.part_name) existing.part_name = partName;
        } else {
            this.draft.lines.push({
                line_uuid: crypto.randomUUID ? crypto.randomUUID() : `line_${Date.now()}_${Math.random()}`,
                part_id: partId,
                part_name: partName,
                quantity: qty,
                refdes,
            });
        }

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
        const bomId = this.draft?.bom_id;
        if (!bomId) {
            alert('Missing bom_id. Cannot save.');
            return;
        }

        const token = $('input[name="_token"]').attr('value');

        $.ajax({
            url: `/bom/${bomId}/commit`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            data: {
                base_revision_id: this.draft.base_revision_id,
                lines: this.draft.lines.map(l => ({
                    part_id: l.part_id,
                    quantity: l.quantity,
                    refdes: l.refdes || null,
                    line_uuid: l.line_uuid
                }))
            },
            success: () => {
                this.exitEditMode();
                updateInfoWindow('bom', bomId);
            },
            error: (xhr) => {
                console.error(xhr);
                alert(xhr.responseJSON?.error || xhr.responseJSON?.message || 'Save failed.');
            }
        });
    }

    escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    loadPartsIndex() {
        // already loading/loaded
        if (this.partsIndex) return $.Deferred().resolve(this.partsIndex);

        return DataFetchService.getParts().done(parts => {
            this.partsIndex = {};
            parts.forEach(p => {
                this.partsIndex[p.part_id] = p.part_name;
            });

            // backfill missing names in already loaded draft
            if (this.draft?.lines?.length) {
                this.draft.lines.forEach(l => {
                    if (!l.part_name) l.part_name = this.partsIndex[l.part_id] || '';
                });
            }
        });
    }

}
