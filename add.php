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



// Delete duplicates from oblasti
pg_query($db_connection, "DELETE FROM oblasti a USING (
                            SELECT MIN(ctid) as ctid, oblast
                            FROM oblasti 
                            GROUP BY oblast HAVING COUNT(*) > 1
                          ) b
                          WHERE a.oblast = b.oblast
                          AND a.ctid <> b.ctid;");

// Delete duplicates from obstini
pg_query($db_connection, "DELETE FROM obstini a USING (
                            SELECT MIN(ctid) as ctid, obstina
                            FROM obstini 
                            GROUP BY obstina HAVING COUNT(*) > 1
                          ) b
                          WHERE a.obstina = b.obstina
                          AND a.ctid <> b.ctid;");

// Delete duplicates from selista
pg_query($db_connection, "DELETE FROM selista a USING (
                            SELECT MIN(ctid) as ctid, ekatte
                            FROM selista 
                            GROUP BY ekatte HAVING COUNT(*) > 1
                          ) b
                          WHERE a.ekatte = b.ekatte
                          AND a.ctid <> b.ctid;");



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
