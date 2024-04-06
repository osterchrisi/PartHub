/**
 * Send form "search_form" upon changing the results per page dropdown "resultspp"
 */
function sendFormOnDropdownChange() {
    var dropdown = document.getElementById("resultspp");
    dropdown.addEventListener("change", function () {
        var form = document.getElementById("search_form");
        form.submit();
    });
};