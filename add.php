<?php
$db_connection = pg_connect("host=localhost dbname=ekatteDB user=postgres password=Fishface93");
if(!$db_connection){
  print("Was not able to connect");
}

/** Makes the bulk insert with COPY command, does not ignore duplicates
$result = pg_query($db_connection, "COPY selista(name_seliste,obstina,ekatte,t_v_m)
                                    FROM '/home/tuan/Desktop/Ekatte/test.csv' DELIMITER ',' CSV HEADER;");
if(!$result){
  print("Was not able to execute the query.");
}
*/

/** Create dummy table */
$createDummy = "CREATE TABLE selista_duplicate AS
                SELECT * FROM selista;";

/** Insert data from csv */
$importData = "COPY selista_duplicate(name_seliste,obstina,ekatte,t_v_m)
               FROM '/home/tuan/Desktop/Ekatte/test.csv' DELIMITER ',' CSV HEADER;";

/** Truncate existing table */
$truncate = "TRUNCATE TABLE selista;";

/** Insert into the original table */
$insertOriginal = "INSERT INTO selista
                   SELECT DISTINCT * FROM selista_duplicate;";

/** Delete duplicate table */
$deleteDuplicate = "DROP TABLE selista_duplicate;";

$result1 = pg_query($db_connection, $createDummy);
if(!$result1){
  print("Did not create dummy.");
}

$result2 = pg_query($db_connection, $importData);
if(!$result2){
  print("Did not import the data.");
}

$result3 = pg_query($db_connection, $truncate);
if(!$result3){
  print("Did not truncate the table.");
}

$result4 = pg_query($db_connection, $insertOriginal);
if(!$result4){
  print("Did not insert into original.");
}

$result5 = pg_query($db_connection, $deleteDuplicate);
if(!$result5){
  print("Did not delete the duplicate.");
}
?>
