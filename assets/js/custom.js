function NumberURLSorter(a, b) {
    // Remove the href tag and return only the string values
    // Otherwise cells get sorted by the URL which contains part_id
    return $(a).text() - $(b).text();
}