import { updateInfoWindow } from "./custom";
import { DataFetchService } from "./Services/DataFetchService";
import { TableManager } from "./Tables/TableManager";
import { TableRowManager } from "./Tables/TableRowManager";
import { DropdownManager } from "./DropdownManager";

export class BomEditor {
    constructor() {
        this.draft = null;
        this.partsList = null;
        this.partsReqPromise = null;
        this.dropdownManager = new DropdownManager();
    }

    mount() {
        // Use event delegation because info-window is replaced regularly
        $(document).off('click', '#bomEditBtn').on('click', '#bomEditBtn', () => this.enterEditMode());
        $(document).off('click', '#bomEditCancelBtn').on('click', '#bomEditCancelBtn', () => this.exitEditMode());
        $(document).off('click', '#bomCreateCancelBtn').on('click', '#bomCreateCancelBtn', () => this.exitCreateMode());
        $(document).off('click', '#bomEditSaveBtn').on('click', '#bomEditSaveBtn', () => this.saveDraft());
        $(document).off('click', '#bomCreateBtn').on('click', '#bomCreateBtn', (e) => {
            e.preventDefault();
            this.saveDraft();
        });


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

        // I feel like this could live somewhere else
        $(document).off('change', '#bomRevisionPicker').on('change', '#bomRevisionPicker', function () {
            const bomId = $(this).data('bom-id');
            const revId = $(this).val();
            updateInfoWindow('bom', `${bomId}?rev=${revId}`);
        });

        // Revision metadata inputs across both create and edit forms
        $(document).off('input', '#bomEditorRevLabel').on('input', '#bomEditorRevLabel', (e) => {
            if (!this.draft) return;
            this.draft.revision_label = $(e.currentTarget).val();
        });

        $(document).off('input', '#bomEditorRevNote').on('input', '#bomEditorRevNote', (e) => {
            if (!this.draft) return;
            this.draft.revision_note = $(e.currentTarget).val();
        });


    }

    enterEditMode() {
        const bomId = $('#bomEditorToolbar').data('bom-id') || null;
        if (!bomId) { alert('Missing bom_id'); return; }

        this.toggleUI();

        // Load editor payload + parts list (array)
        const payloadReq = $.getJSON(`/bom/${bomId}/editor`);
        const partsReq = this.loadPartsIndex(); // resolves to: [{part_id, part_name}, ...]

        $.when(payloadReq, partsReq).done((payloadRes, parts) => {
            // jqXHR-style: [data, statusText, jqXHR]
            const payload = payloadRes[0];

            this.draft = {
                bom_id: payload.bom.bom_id,
                base_revision_id: payload.revision.bom_revision_id,

                // revision metadata for next save
                revision_label: payload.revision.label || '',
                revision_note: payload.revision.note || '',

                lines: payload.lines.map(l => ({
                    line_uuid: this.makeUuid(),
                    part_id: l.part_id,
                    part_name: l.part_name || (this.partsIndex?.[l.part_id] || ''),
                    quantity: l.quantity,
                    refdes: l.refdes || '',
                }))
            };

            this.renderEditor();

            // Build selectized "add part" dropdown from parts array
            this.dropdownManager.addBomPartSelect(parts, 'bomEditorAddPartDropdown', 'bomEditorAddPartSelect');
        }).fail((xhr) => {
            console.error(xhr);
            alert(xhr?.responseJSON?.error || xhr?.responseJSON?.message || 'Failed to load editor data.');
            this.exitEditMode();
        });
    }

    enterCreateMode() {
        this.toggleUI();

        this.loadPartsIndex()
            .done((parts) => {
                this.draft = {
                    bom_id: null,
                    base_revision_id: null,
                    revision_label: null,
                    revision_note: null,
                    lines: []
                };

                this.renderEditor();

                this.dropdownManager.addBomPartSelect(
                    parts,
                    'bomEditorAddPartDropdown',
                    'bomEditorAddPartSelect'
                );
            })
            .fail((err) => {
                console.error(err);
                alert('Failed to load parts index.');
                this.exitEditMode();
            });
    }


    /**
     * Loads parts once and caches:
     * - this.partsIndex: { [part_id]: part_name }
     * - this.partsList:  [{part_id, part_name}, ...]
     *
     * Always returns a *jQuery promise* that resolves with the parts array.
     */
    loadPartsIndex() {
        // Already loaded
        if (this.partsList?.length) {
            return $.Deferred().resolve(this.partsList).promise();
        }

        // Already in-flight (jQuery promise)
        if (this.partsReqPromise) {
            return this.partsReqPromise;
        }

        const dfd = $.Deferred();
        this.partsReqPromise = dfd.promise();

        // Normalize to a native Promise, regardless of jqXHR or native promise
        Promise.resolve(DataFetchService.getParts())
            .then((parts) => {
                this.partsList = parts || [];

                this.partsIndex = {};
                this.partsList.forEach(p => {
                    this.partsIndex[p.part_id] = p.part_name;
                });

                // backfill missing names in already loaded draft
                if (this.draft?.lines?.length) {
                    this.draft.lines.forEach(l => {
                        if (!l.part_name) l.part_name = this.partsIndex[l.part_id] || '';
                    });
                }

                dfd.resolve(this.partsList);
            })
            .catch((err) => {
                dfd.reject(err);
            })
            .finally(() => {
                this.partsReqPromise = null; // allow retry later if needed
            });

        return this.partsReqPromise;
    }



    /**
     * Loads parts once and caches:
     * - this.partsIndex: { [part_id]: part_name }
     * - this.partsList:  [{part_id, part_name}, ...]
     *
     * Always resolves to the parts array (not the index map).
     */
    loadPartsIndex() {
        // Already loaded
        if (this.partsList?.length) {
            return $.Deferred().resolve(this.partsList).promise();
        }

        // Already in-flight: reuse the same request
        if (this.partsReqPromise) {
            return this.partsReqPromise;
        }

        this.partsReqPromise = DataFetchService.getParts()
            .then((parts) => {
                this.partsList = parts || [];

                this.partsIndex = {};
                this.partsList.forEach(p => {
                    this.partsIndex[p.part_id] = p.part_name;
                });

                // backfill missing names in already loaded draft
                if (this.draft?.lines?.length) {
                    this.draft.lines.forEach(l => {
                        if (!l.part_name) l.part_name = this.partsIndex[l.part_id] || '';
                    });
                }

                return this.partsList;
            })
            .always(() => {
                // allow future reloads if you ever want them
                this.partsReqPromise = null;
            });

        return this.partsReqPromise;
    }

    // Toggle UI between details view and editor view, used for both create and edit mode
    toggleUI() {
        $('#info-window').addClass('bom-editing');
        $('#bomDetailsViewContainer').addClass('d-none');
        $('#bomEditorContainer').removeClass('d-none');
        $('#bomEditBtn').addClass('d-none');
        $('#bomEditCancelBtn, #bomEditSaveBtn, #bomEditHint').removeClass('d-none');
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

    exitCreateMode() {
        // reset editor state
        this.draft = null;
        this.partsIndex = null;

        // remove any editor-mode CSS you might have applied
        $('#info-window').removeClass('bom-editing');

        // show the last BOM again (whatever was selected before "New BOM")
        const lastBomId = localStorage.getItem('lastSelectedRow_bom_list_table'); // adjust key if yours differs
        if (lastBomId) {
            updateInfoWindow('bom', lastBomId);
            return;
        }

        // fallback: no previous selection -> empty info-window
        $('#info-window').empty();
    }

    renderEditor() {
        const $ui = $('#bomEditorUi');
        $ui.removeClass('d-none');

        // revision meta
        $('#bomEditorRevLabel').val(this.draft.revision_label || '');
        $('#bomEditorRevNote').val(this.draft.revision_note || '');

        // enable add button once parts are loaded
        $('#bomEditorAddLineBtn').prop('disabled', !this.partsIndex);

        // rows
        const $tbody = $('#bomEditorTbody');
        const tpl = document.getElementById('bomEditorRowTpl');
        $tbody.empty();

        if (!this.draft.lines.length) {
            $tbody.append($('#bomEditorEmptyRow').clone().removeAttr('id'));
            return;
        }

        this.draft.lines.forEach(line => {
            const node = tpl.content.firstElementChild.cloneNode(true);

            node.querySelector('[data-slot="part_name"]').textContent = line.part_name || '';
            node.querySelector('[data-slot="part_id"]').textContent = String(line.part_id);

            const qty = node.querySelector('[data-slot="qty"]');
            qty.value = String(line.quantity);
            qty.dataset.lineUuid = line.line_uuid;

            const ref = node.querySelector('[data-slot="refdes"]');
            ref.value = line.refdes || '';
            ref.dataset.lineUuid = line.line_uuid;

            const rm = node.querySelector('[data-slot="remove"]');
            rm.dataset.lineUuid = line.line_uuid;

            $tbody.append(node);
        });
    }


    addLineFromInputs() {
        // const partId = parseInt($('#bomEditorAddPartId').val(), 10);
        const sel = $('#bomEditorAddPartSelect')[0]?.selectize;
        const partId = parseInt(sel?.getValue(), 10);
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
                line_uuid: this.makeUuid(),
                part_id: partId,
                part_name: partName,
                quantity: qty,
                refdes,
            });
        }

        // $('#bomEditorAddPartId').val('');
        sel?.clear(true);
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
        const token = $('input[name="_token"]').val();

        // CREATE MODE: draft has no bom_id yet
        if (!this.draft?.bom_id) {
            const $form = $('#bomCreateForm');
            if (!$form.length) {
                alert('Missing create form.');
                return;
            }

            const bom_name = ($form.find('[name="bom_name"]').val() || '').trim();
            if (!bom_name) {
                alert('BOM name is required.');
                return;
            }

            const payload = {
                type: 'bom', // for resource limits middleware
                bom_name,
                bom_description: ($form.find('[name="bom_description"]').val() || '').trim() || null,
                revision_label: this.draft.revision_label || null,
                revision_note: this.draft.revision_note || null,
                lines: this.draft.lines.map(l => ({
                    part_id: l.part_id,
                    quantity: l.quantity,
                    refdes: l.refdes || null,
                    line_uuid: l.line_uuid
                }))
            };

            $.ajax({
                url: '/bom.create',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: (response) => {
                    const id = response.new_bom_id;

                    const bomTable = new TableManager({ type: 'bom' });
                    bomTable.rebuildTable().done(function () {
                        const bomListTable = new TableRowManager('#bom_list_table', 'bom');
                        bomListTable.selectNewRow(id);
                    });

                    // After create, show normal BOM view
                    this.exitEditMode();
                    updateInfoWindow('bom', id);
                },
                error: (xhr) => {
                    console.error(xhr);
                    const msg = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Creation failed.';

                    const host = document.getElementById('create-response-message');
                    if (host) {
                        host.replaceChildren();
                        const div = document.createElement('div');
                        div.className = 'alert alert-danger';
                        div.textContent = String(msg ?? '');
                        host.appendChild(div);
                    }
                }
            });

            return;
        }

        // EDIT MODE: normal commit against existing BOM
        const bomId = this.draft.bom_id;

        $.ajax({
            url: `/bom/${bomId}/commit`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            data: {
                base_revision_id: this.draft.base_revision_id,
                revision_label: this.draft.revision_label || null,
                revision_note: this.draft.revision_note || null,
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

    makeUuid() {
        // Secure contexts
        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        // RFC4122 v4 polyfill (36 chars) - essentially for local testing
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
}
