<?
global $D_R;
include_once($D_R.'/lib/_misc.php');
$country = $_POST['countryCode'];

if($country == "AA"){ ?>
	<select tabindex="4"  id="state" name="state" style="background-color:#FFFFFF;" class="slct_option" onKeyPress="get_order_keys(event);"><option value="">--Select--<?php display_states($state); ?></select>

<? }elseif($country == "AB"){ ?>
	<select tabindex="4" id="state" name="state" style="background-color:#FFFFFF;" class="slct_option" onKeyPress="get_order_keys(event);"><option value="">--Select--<?php display_canada_states($state); ?></select>

<? }
 ?>