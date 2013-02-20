<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$coords = array();
$citynames = array();

// collect GET data
$numcities = 0;
foreach($_GET as $key => $val) {
  $curcity = substr($key, 0, 3);
  if(substr($key,4) == "lat") {
    $coords[$curcity][0] = $val;
  } else {
    $coords[$curcity][1] = $val;
    $citynames[$numcities] = $curcity;
    $numcities++;
  }
}

// calculate all distances
foreach($coords as $city1 => $coord1) {
  foreach($coords as $city2 => $coord2) {
    $lat1 = $coord1[0];
    $lat2 = $coord2[0];
    $long1 = $coord1[1];
    $long2 = $coord2[1];
    $distances[$city1][$city2] = 
      distance($lat1,$long1,$lat2,$long2,true);
  }
}

// format CSV string
$outstr = '';
foreach($citynames as $val) {
  $outstr .= $val . ",";
}
$outstr[strlen($outstr)-1] = "\n";

foreach($citynames as $city1) {
  foreach($citynames as $city2) {
    $outstr .= $distances[$city1][$city2] . ",";
  }
  $outstr[strlen($outstr)-1] = "\n";
}

echo $outstr;

function distance($lat1, $lng1, $lat2, $lng2, $miles = true) {
  $pi80 = M_PI / 180;
  $lat1 *= $pi80;
  $lng1 *= $pi80;
  $lat2 *= $pi80;
  $lng2 *= $pi80;

  $r = 6372.797; // mean radius of Earth in km
  $dlat = $lat2 - $lat1;
  $dlng = $lng2 - $lng1;
  $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  $km = $r * $c;

  return ($miles ? ($km * 0.621371192) : $km);
}

?>
