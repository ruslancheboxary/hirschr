<?php
set_time_limit(40);
$host = "localhost";
$user = "hirschr";
$pass = "hirschr";
$db = "hirschr";
$tablename = "books";

//Подключение к postreges
$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass");
if (!$connection)
{
die("Could not open connection to database server");
}

$query = 'SELECT id,text FROM '.$tablename.''; 
$result= pg_query($connection, $query) or die("Error in query: $query. " .pg_last_error($connection));
// get the number of rows in the resultset
// this is PG-specific
$rows = pg_num_rows($result);
// if records present
if ($rows > 0)
{
// iterate through resultset
for ($i=0; $i<$rows; $i++)
{
$row['text'] = pg_fetch_row($result, $i);
$rowg = split("\n",$row['text']);
$title=$rowg[0].' '.$rowg[1].' '$rowg[2];
$query = "UPDATE ".$tablename." SET title='".$title."' WHERE id = '".intval($row['id'])."';";
$result=pg_query($connection, $query) or die("Error in query: $query. " .pg_last_error($connection));
}
}
?>