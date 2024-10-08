export class MouserPartSearch {
    constructor(inputId, resultsContainerId, spinnerId) {
        this.inputElement = document.getElementById(inputId);
        this.resultsContainer = document.getElementById(resultsContainerId);
        this.spinnerElement = document.getElementById(spinnerId); // Spinner element
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
                    fetch(`/search-mouser-part/${searchTerm}`)
                        .then(response => response.json())
                        .then(data => {
                            this.searchCache[searchTerm] = data; // Cache the result
                            this.displayResults(data);
                        })
                        .catch(error => {
                            console.error("API call failed:", error);
                        })
                        .finally(() => {
                            this.hideSpinner();  // Hide the spinner after API call completes
                        });
                }
            }, 500);  // Delay of 500ms to debounce
        } else {
            this.resultsContainer.innerHTML = '';  // Clear results if the search term is too short
        }
    }

    showSpinner() {
        this.spinnerElement.classList.remove('d-none');  // Show the spinner
    }

    hideSpinner() {
        this.spinnerElement.classList.add('d-none');  // Hide the spinner
    }

    displayResults(data) {
        this.resultsContainer.innerHTML = '';  // Clear previous results
        const parts = data.SearchResults.Parts.slice(0, 10);  // Limit to 10 results

        if (parts.length > 0) {
            parts.forEach(part => {
                const partDiv = document.createElement('div');
                partDiv.classList.add('part-result');
                partDiv.innerHTML = `
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4 d-flex justify-content-center align-items-center">
                                <img src="${part.ImagePath}" class="img-fluid w-100 rounded-start" alt="Part Image">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div><span class="card-text text-muted">Mfg P/N: </span><h5 class="d-inline card-title">${part.ManufacturerPartNumber}</h5></div>
                                    <div><span class="card-text text-muted">Mouser P/N: </span><h5 class="d-inline card-title">${part.MouserPartNumber}</h5></div>
                                    <div><span class="card-text text-muted">Desc: </span><span class="d-inline card-text">${part.Description}</span></div>
                                    <div><button class="btn btn-primary select-part-btn" data-part='${JSON.stringify(part)}'>Select Part</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                this.resultsContainer.appendChild(partDiv);
            });

            // Add event listeners for the select buttons
            document.querySelectorAll('.select-part-btn').forEach(button => {
                button.addEventListener('click', (event) => this.selectPart(event));
            });
        } else {
            this.resultsContainer.innerHTML = '<p>No results found from Mouser.</p>';
        }
    }

    selectPart(event) {
        const selectedPart = JSON.parse(event.target.getAttribute('data-part'));
        alert('Part Selected: ' + selectedPart.ManufacturerPartNumber);
    }
}
