<select multiple size="10" class="form-select form-select-sm" id="cat-select">
    <option value="all" {{ (!$sc or $sc == "all" OR in_array("all", $sc)) ? "selected" : "" }}>All Categories</option>
    @foreach ($categories as $category)
        @php
            $selected = '';
            if ($sc && is_array($sc) && in_array($category['category_id'], $sc)) {
                $selected = 'selected';
            }
        @endphp
        <option value="{{ $category['category_id'] }}" {{ $selected }}>{{ $category['category_name'] }}</option>
    @endforeach
</select>
