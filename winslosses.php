<?php

error_reporting(E_ALL);
ini_set('display_errors', True);

include "database.php";

$database="painterj_retrosheet2000";

mysql_connect('localhost',$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

// get all team names
$teams = array();
$sql = "
  SELECT DISTINCT painterj_retrosheet2000.games.HOME_TEAM_ID AS team
  FROM painterj_retrosheet2000.games 
  WHERE painterj_retrosheet2000.games.GAME_ID LIKE '%%%" . $_GET['year'] . "%%%%%'
  ORDER BY team;"; 
$qry = mysql_query($sql);
$numrows = mysql_numrows($qry);

$count = 0;
while($res = mysql_fetch_array($qry)) {
  $teams[$count++] = $res['team'];
}

// print header
$header = '';
foreach($teams as $key => $val) {
  if($key=="0") continue;
  $header .= $val . ",";
}
$header[strlen($header)-1] = "\n";
echo $header;


// get road wins vs. each opponent
$wins = array(array());
if($_GET['wins'] == 1) {
  $operator = ">";
} else {
  $operator = "<";
}

foreach($_GET as $key => $val) {
  if($key == "wins" || $key == "year") {
    continue;
  }

  $sql = "
  SELECT painterj_retrosheet2000.games.HOME_TEAM_ID AS opponent, COUNT(*) AS wins
  FROM painterj_retrosheet2000.games 
  WHERE painterj_retrosheet2000.games.GAME_ID LIKE '%%%" . $_GET['year'] . "%%%%%' AND painterj_retrosheet2000.games.AWAY_TEAM_ID='" . $val . "' AND painterj_retrosheet2000.games.AWAY_SCORE_CT" . $operator . "painterj_retrosheet2000.games.HOME_SCORE_CT
  GROUP BY painterj_retrosheet2000.games.HOME_TEAM_ID
  ORDER BY opponent;";
 
  $qry = mysql_query($sql);
  $numrows = mysql_numrows($qry);

  $teamnum = 0;
  while($res = mysql_fetch_array($qry)) {
    while($res['opponent'] != $teams[$teamnum]) {
      $wins[$val][$teams[$teamnum]] = 0;
      echo "0,";
      $teamnum++;
    }
    $wins[$val][$res['opponent']] = $res['wins'];
    echo $res['wins'] . ",";
    $teamnum++;
  }
  while($teamnum <= count($teams)) {
    echo "0,";
    $teamnum++;
  }

  echo "\n";
}

ksort($wins);


foreach($wins as $key1 => $val1) {
  $csv = '';
  $curopp = 0;
  foreach($val1 as $key2 => $val2) { 
    $csv .= $val2 . ",";
    $curopp++;
  }
  $csv[strlen($csv)-1] = "";

  if(is_array($csv)) continue;
  //echo $csv;
  //echo "\n";
}

mysql_close();

?>
