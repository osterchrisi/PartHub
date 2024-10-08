<div class="container">
    <h2>Search Mouser Part</h2>

    <!-- Search Input -->
    <input type="text" id="searchTerm" class="form-control" placeholder="Enter part number or keyword">

    <!-- Search Results Div -->
    <div id="searchResults" class="mt-4"></div>
</div>

<script>
    let searchCache = {};
    let timeout = null;

    document.getElementById('searchTerm').addEventListener('input', function() {
        clearTimeout(timeout);
        const searchTerm = this.value;

        if (searchTerm.length > 2) {
            if (searchCache[searchTerm]) {
                // Use cached results if available
                displayResults(searchCache[searchTerm]);
            } else {
                timeout = setTimeout(function() {
                    fetch(`/search-mouser-part/${searchTerm}`)
                        .then(response => response.json())
                        .then(data => {
                            searchCache[searchTerm] = data; // Cache the result
                            displayResults(data);
                        });
                }, 300); // Delay of 300ms
            }
        } else {
            document.getElementById('searchResults').innerHTML =
                ''; // Clear results if search term is too short
        }
    });

    function displayResults(data) {
        const searchResultsDiv = document.getElementById('searchResults');
        searchResultsDiv.innerHTML = ''; // Clear previous results

        const parts = data.SearchResults.Parts.slice(0, 10); // Limit to 10 results

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
                </div>
            `;
                searchResultsDiv.appendChild(partDiv);
            });
        } else {
            searchResultsDiv.innerHTML = '<p>No results found.</p>';
        }

        // Add event listeners for the select buttons
        document.querySelectorAll('.select-part-btn').forEach(button => {
            button.addEventListener('click', function() {
                const selectedPart = JSON.parse(this.getAttribute('data-part'));
                alert('Part Selected: ' + selectedPart.ManufacturerPartNumber);
            });
        });
    }
</script>
