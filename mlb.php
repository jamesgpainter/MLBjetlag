<?php
include "database.php";

$numranges = $_POST['numranges'];
$database="retrosheet2000";

mysql_connect(localhost,$user,$password);
@mysql_select_db($database) or die( "Unable to select database");

// get road wins vs. each opponent
$sql = "
SELECT retrosheet2000.games.HOME_TEAM_ID AS opponent, COUNT(*) AS wins
FROM retrosheet2000.games 
WHERE retrosheet2000.games.GAME_ID LIKE '%%%2008%%%%%' AND retrosheet2000.games.AWAY_TEAM_ID='PHI' AND retrosheet2000.games.AWAY_SCORE_CT>retrosheet2000.games.HOME_SCORE_CT
GROUP BY retrosheet2000.games.HOME_TEAM_ID
ORDER BY opponent;";
$qry = mysql_query($sql);
$numrows = mysql_numrows($qry);
$teams = array();
$wins = array();
while($res = mysql_fetch_array($qry)) {
  $teams[] = $res['opponent'];
  $wins[] = $res['wins'];
}

// get road losses vs. each opponent
$sql = "
SELECT retrosheet2000.games.HOME_TEAM_ID AS opponent, COUNT(*) AS losses
FROM retrosheet2000.games 
WHERE retrosheet2000.games.GAME_ID LIKE '%%%2008%%%%%' AND retrosheet2000.games.AWAY_TEAM_ID='PHI' AND retrosheet2000.games.AWAY_SCORE_CT<retrosheet2000.games.HOME_SCORE_CT
GROUP BY retrosheet2000.games.HOME_TEAM_ID
ORDER BY opponent;";
$qry = mysql_query($sql);
$numrows = mysql_numrows($qry);
$losses = array();
while($res = mysql_fetch_array($qry)) {
  $losses[] = $res['losses'];
}

// final output
for($i=0; $i<$numrows; $i++) {
  $csv = $teams[$i] . "," . $wins[$i] . "," . $losses[$i] . "\n";
  echo $csv;
}


mysql_close();

?>
