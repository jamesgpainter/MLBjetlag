<?php

include "database.php";

$database="painterj_retrosheet2000";

mysql_connect('localhost',$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

// get road wins vs. each opponent

$teams = array();
$sql = "
  SELECT DISTINCT painterj_retrosheet2000.games.HOME_TEAM_ID AS team
  FROM painterj_retrosheet2000.games
  WHERE painterj_retrosheet2000.games.GAME_ID LIKE '%%%2008%%%%%'
  ORDER BY team;";
$qry = mysql_query($sql);
$numrows = mysql_numrows($qry);

$count = 0;
while($res = mysql_fetch_array($qry)) {
  $teams[$count++] = $res['team'];
}

$output = '';
for($t=0; $t<$count; $t++) {
  $output .= $teams[$t] . ",";
}
$output[strlen($output)-1] = "";

echo $output;
?>
