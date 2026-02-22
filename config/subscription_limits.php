<?php

// Null means unlimited

return [
    'free' => [
        'part_limit' => 250,
        'bom_limit' => 10,
        'location_limit' => 10,
        'supplier_limit' => 10,
        'supplier_data_limit' => 1,
        'footprint_limit' => 10,
        'category_limit' => 10,
        'bom_revision_history' => false,
        'manufacturer_limit' => 10,
    ],
    'maker' => [
        'part_limit' => null,
        'bom_limit' => null,
        'location_limit' => null,
        'supplier_limit' => null,
        'supplier_data_limit' => null,
        'footprint_limit' => null,
        'category_limit' => null,
        'bom_revision_history' => true,
        'manufacturer_limit' => null,
    ],
];
