@include('header')
@include('navbar')

@include('components.modals.stockModal')
@include('components.modals.partEntryModal', ['part_name' => ''])
@include('components.menus.partsTableRightClickMenu')


<h4>Parts</h4>