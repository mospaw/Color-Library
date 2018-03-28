<?php 

require_once('color.php');

echo "<pre>";

/*
$color = "#666666";

box($color);
$unpack = _color_unpack($color,true);

$hsl = _color_rgb2hsl($unpack);
var_dump($hsl);
$hsl[0] += .3333;
var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb,true);

box($new_color);

$unpack = _color_unpack($new_color,true);

$hsl = _color_rgb2hsl($unpack);
var_dump($hsl);
$hsl[0] += .33333;
var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb,true);

box($new_color);

*/

$color = "#FF8000";

box($color);
$unpack = _color_unpack($color, true);

$hsl = _color_rgb2hsl($unpack);
//var_dump($hsl);
$hsl[1] -= .3333;
//var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb, true);

box($new_color);

$unpack = _color_unpack($new_color, true);

$hsl = _color_rgb2hsl($unpack);
//var_dump($hsl);
$hsl[1] -= .33333;
//var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb, true);

box($new_color);



$unpack = _color_unpack($new_color, true);

$hsl = _color_rgb2hsl($unpack);
//var_dump($hsl);
$hsl[1] -= .33333;
//var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb, true);


box($new_color);

$unpack = _color_unpack($new_color, true);

$hsl = _color_rgb2hsl($unpack);
//var_dump($hsl);
$hsl[1] -= .33333;
//var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb, true);

box($new_color);

$unpack = _color_unpack($new_color, true);

$hsl = _color_rgb2hsl($unpack);
//var_dump($hsl);
$hsl[1] += .33333;
//var_dump($hsl);

$new_rgb = _color_hsl2rgb($hsl);

$new_color = _color_pack($new_rgb, true);

box($new_color);


 $testrgb = array(0.2,0.75,0.4); //RGB to start with
print_r($testrgb);

  print "Hex: ";
  $testhex = "#C5003E";
  print $testhex;
  $testhex2rgb = _color_unpack($testhex, true);
  print "<br />RGB: ";
  var_dump($testhex2rgb);
  print "<br />HSL color module: ";
  $testrgb2hsl = _color_rgb2hsl($testhex2rgb); //Converteren naar HSL
  var_dump($testrgb2hsl);
  print "<br />RGB: ";
  $testhsl2rgb = _color_hsl2rgb($testrgb2hsl); // En weer terug naar RGB
  var_dump($testhsl2rgb);
  print "<br />Hex: ";
  $testrgb2hex = _color_pack($testhsl2rgb, true);
  var_dump($testrgb2hex);
