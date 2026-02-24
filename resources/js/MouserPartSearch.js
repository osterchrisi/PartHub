export class MouserPartSearch {
    constructor(inputId, resultsContainerId, spinnerId, onSelectPart) {
        this.inputElement = document.getElementById(inputId);
        this.resultsContainer = document.getElementById(resultsContainerId);
        this.spinnerElement = document.getElementById(spinnerId); // Spinner element
        this.onSelectPart = onSelectPart; // Callback function to handle part selection
        this.searchCache = {};
        this.timeout = null;

        // Bind the event listener to the input element
        this.inputElement.addEventListener('input', (event) => this.handleInput(event));
    }

    handleInput(event) {
        clearTimeout(this.timeout);  // Clear any previously set timeout
        const searchTerm = event.target.value;

        // Ensure that the search term is at least 3 characters long
        if (searchTerm.length > 2) {
            this.timeout = setTimeout(() => {
                this.showSpinner(); // Show the spinner when API call starts

                if (this.searchCache[searchTerm]) {
                    this.displayResults(this.searchCache[searchTerm]);
                    this.hideSpinner();  // Hide the spinner after cache is used
                } else {
                    fetch(`/search-mouser-part/${encodeURIComponent(searchTerm)}`)
                        .then(async (response) => {
                            const payload = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                const msg = payload.message || `Mouser search failed (${response.status}).`;
                                throw new Error(msg);
                            }

                            // Also handle “Errors” even if backend didn’t convert them (belt + suspenders)
                            if (payload?.Errors?.length) {
                                const msg = payload.Errors[0]?.Message || 'Mouser API returned an error.';
                                throw new Error(msg);
                            }

                            return payload;
                        })
                        .then(data => {
                            this.searchCache[searchTerm] = data;
                            this.displayResults(data);
                        })
                        .catch(err => {
                            this.showError(err.message);
                        })
                        .finally(() => {
                            this.hideSpinner();
                        });

                }
            }, 500);  // Delay of 500ms to debounce
        } else {
            this.resultsContainer.replaceChildren();  // Clear results if the search term is too short
        }
    }

    showSpinner() {
        this.spinnerElement.classList.remove('d-none');  // Show the spinner
    }

    hideSpinner() {
        this.spinnerElement.classList.add('d-none');  // Hide the spinner
    }

    displayResults(data) {
        this.resultsContainer.replaceChildren(); // Clear previous results safely

        const parts = data?.SearchResults?.Parts?.slice(0, 10) || [];

        if (!parts.length) {
            const p = document.createElement('p');
            p.textContent = 'No results found from Mouser.';
            this.resultsContainer.appendChild(p);
            return;
        }

        parts.forEach(part => {
            const partDiv = document.createElement('div');
            partDiv.classList.add('part-result');

            const card = document.createElement('div');
            card.className = 'card mb-3';

            const row = document.createElement('div');
            row.className = 'row g-0';

            // Left image column
            const colImg = document.createElement('div');
            colImg.className = 'col-md-4 d-flex justify-content-center align-items-center';

            const img = document.createElement('img');
            img.className = 'img-fluid w-50 rounded-start';
            img.alt = 'Part Image';
            img.src = String(part?.ImagePath || ''); // safe assignment (no HTML parsing)

            colImg.appendChild(img);

            // Right content column
            const colBody = document.createElement('div');
            colBody.className = 'col-md-8';

            const body = document.createElement('div');
            body.className = 'card-body';

            // Helper to build label + value line
            const makeLine = (labelText, valueText, valueTag = 'span', valueClass = 'd-inline card-text') => {
                const line = document.createElement('div');

                const label = document.createElement('span');
                label.className = 'card-text text-muted';
                label.textContent = labelText;

                const value = document.createElement(valueTag);
                value.className = valueClass;
                value.textContent = String(valueText || '');

                line.append(label, value);
                return line;
            };

            body.appendChild(
                makeLine('Mfg P/N: ', part?.ManufacturerPartNumber, 'h5', 'd-inline card-title'),
            );
            body.appendChild(
                makeLine('Manufacturer: ', part?.Manufacturer, 'h6', 'd-inline card-title'),
            );
            body.appendChild(
                makeLine('Mouser P/N: ', part?.MouserPartNumber, 'span', 'd-inline card-text'),
            );
            body.appendChild(
                makeLine('Desc: ', part?.Description, 'span', 'd-inline card-text'),
            );

            const btnWrap = document.createElement('div');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-primary select-part-btn';
            btn.textContent = 'Select Part';

            // Store object safely without HTML/attribute injection
            btn._mouserPart = part;

            btn.addEventListener('click', (event) => this.selectPart(event));

            btnWrap.appendChild(btn);
            body.appendChild(btnWrap);

            colBody.appendChild(body);

            row.append(colImg, colBody);
            card.appendChild(row);
            partDiv.appendChild(card);

            this.resultsContainer.appendChild(partDiv);
        });
    }

    selectPart(event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        const btn = event.currentTarget;
        const selectedPart = btn?._mouserPart;

        if (!selectedPart) {
            alert('Could not read selected part.');
            return;
        }

        if (typeof this.onSelectPart === 'function') {
            this.onSelectPart(selectedPart);
            return;
        }

        alert('Mouser Part Selected: ' + String(selectedPart.ManufacturerPartNumber || ''));
    }

    showError(message) {
        this.resultsContainer.replaceChildren();

        const div = document.createElement('div');
        div.className = 'alert alert-warning mb-3';
        div.textContent = String(message ?? '');

        this.resultsContainer.appendChild(div);
    }

}
