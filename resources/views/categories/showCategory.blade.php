<div class="container-fluid">
    <br>
    <h4>
        {{ $category_name }}
    </h4>
    <h5>
        {{-- {{ $category_alias }} --}}
    </h5>

    <!-- Parts Tabs -->
    <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </ul>

    <!-- Tabs Content -->

    <div class="tab-content" id="categoryTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <h5>Parts in this Category</h5>
            {{-- @include('categories.categoryDetailsTable') --}}
            @php
            $table = \buildHTMLTable($db_columns, $nice_columns, $parts_with_category);
            echo $table;
            @endphp
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            No history for categories
        </div>
    </div>
</div>