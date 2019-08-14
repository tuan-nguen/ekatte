<?php
$db_connection = pg_connect("host=localhost dbname=ekatteDB user=postgres password=Fishface93");
if(!$db_connection){
  print("Was not able to connect");
}

/** Makes the bulk insert with COPY command, does not ignore duplicates
$result = pg_query($db_connection, "COPY selista(name_seliste,obstina,ekatte,t_v_m)
                                    FROM '/home/tuan/Desktop/Ekatte/test.csv' 
                                    DELIMITER ',' CSV HEADER;");
if(!$result){
  print("Was not able to execute the query.");
} */

/** TESTING - use multiple INSERTs 
function readCSV($csvFile){
  $file_handle = fopen($csvFile, 'r');
  fgetcsv($file_handle, 0);
  while (!feof($file_handle) ) {
  $line_of_text[] = fgetcsv($file_handle, 0);
  }
  fclose($file_handle);
  return $line_of_text;
}

$begin = pg_query($db_connection, "BEGIN");

$csvOblasti = readCSV('/home/tuan/Desktop/Ekatte/CSV/testOblasti.csv'); 
foreach ($csvOblasti as $c) {
  $result1 = pg_query($db_connection, "INSERT INTO oblasti
                                       VALUES ('" . $c[0] . "','" . $c[2]. "','" . $c[3] . "','" . $c[4] . "')
                                       ON CONFLICT (oblast) DO NOTHING;");
}

$csvObstini = readCSV('/home/tuan/Desktop/Ekatte/CSV/testObstini.csv'); 
foreach ($csvObstini as $c) {
  $result2 = pg_query($db_connection, "INSERT INTO obstini(obstina,name_obstina,category,document,oblast)
                                       VALUES ('" . $c[0] . "','" . $c[1]. "','" . $c[2] . "','" . $c[3] . "','" . $c[4] . "')
                                       ON CONFLICT (obstina) DO NOTHING;");
}

$csvSelista = readCSV('/home/tuan/Desktop/Ekatte/CSV/testSelista.csv'); 
foreach ($csvSelista as $c) {
  $result3 = pg_query($db_connection, "INSERT INTO selista
                                       VALUES ('" . $c[0] . "','" . $c[1]. "','" . $c[2] . "','" . $c[3] . "')
                                       ON CONFLICT (ekatte) DO NOTHING;");
}

$commit = pg_query($db_connection, "COMMIT"); */

/** Drop contraints, COPY the data into the tables, add the constraints */
// Drop selista constraints
pg_query($db_connection, "ALTER TABLE selista DROP CONSTRAINT selista_ekatte_key");
pg_query($db_connection, "ALTER TABLE selista DROP CONSTRAINT selista_obstina_fkey");
pg_query($db_connection, "ALTER TABLE selista DROP CONSTRAINT selista_pkey");

// Drop obstini constraints
pg_query($db_connection, "ALTER TABLE obstini DROP CONSTRAINT obstini_pkey");
pg_query($db_connection, "ALTER TABLE obstini DROP CONSTRAINT obstina_oblast_fkey");
pg_query($db_connection, "ALTER TABLE obstini DROP CONSTRAINT obstina_obstina_key");

// Drop oblasti constraints
pg_query($db_connection, "ALTER TABLE oblasti DROP CONSTRAINT oblast_pkey");



// Copy CSV to oblasti
pg_query($db_connection, "COPY oblasti(oblast,name_oblast,region, document)
                          FROM '/home/tuan/Desktop/Ekatte/CSV/testOblasti.csv' 
                          DELIMITER ',' CSV HEADER;");

// Copy CSV to obstini
pg_query($db_connection, "COPY obstini(obstina,name_obstina,category,document,oblast)
                          FROM '/home/tuan/Desktop/Ekatte/CSV/testObstini.csv' 
                          DELIMITER ',' CSV HEADER;");

// Copy CSV to selista
pg_query($db_connection, "COPY selista(name_seliste,obstina,ekatte,t_v_m)
                          FROM '/home/tuan/Desktop/Ekatte/CSV/testSelista.csv' 
                          DELIMITER ',' CSV HEADER;");



// Add oblasti constraints
pg_query($db_connection, "ALTER TABLE oblasti ADD CONSTRAINT oblast_pkey PRIMARY KEY (oblast)");

// Add obstini constraints
pg_query($db_connection, "ALTER TABLE obstini ADD CONSTRAINT obstini_pkey PRIMARY KEY (obstina)");
pg_query($db_connection, "ALTER TABLE obstini ADD CONSTRAINT obstina_oblast_fkey FOREIGN KEY (oblast)
                                                    REFERENCES public.oblasti (oblast) MATCH SIMPLE
                                                    ON UPDATE NO ACTION ON DELETE NO ACTION");
pg_query($db_connection, "ALTER TABLE obstini ADD CONSTRAINT obstina_obstina_key UNIQUE (obstina)");

// Add selista contraints back
pg_query($db_connection, "ALTER TABLE selista ADD CONSTRAINT selista_pkey PRIMARY KEY (name_seliste, obstina)");
pg_query($db_connection, "ALTER TABLE selista ADD CONSTRAINT selista_obstina_fkey FOREIGN KEY (obstina)
                                                    REFERENCES public.obstini (obstina) MATCH SIMPLE
                                                    ON UPDATE NO ACTION ON DELETE NO ACTION");
pg_query($db_connection, "ALTER TABLE selista ADD CONSTRAINT selista_ekatte_key UNIQUE (ekatte)");


?>
