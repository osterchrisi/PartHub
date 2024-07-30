<select multiple size="10" class="form-select form-select-sm" id="cat-select">
    <option value="all" {{ (!$search_category or $search_category == "all" OR in_array("all", $search_category)) ? "selected" : "" }}>All Categories</option>
    @foreach ($categories as $category)
        @php
            $selected = '';
            if ($search_category && is_array($search_category) && in_array($category['category_id'], $search_category)) {
                $selected = 'selected';
            }
        @endphp
        <option value="{{ $category['category_id'] }}" {{ $selected }}>{{ $category['category_name'] }}</option>
    @endforeach
</select>
