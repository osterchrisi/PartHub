<?php 
use App\Http\Controllers\ProfileController;
$user = Auth::user();
?>

@include('header')
@include('navbar')
@include('profile.edit')
@include('footer')