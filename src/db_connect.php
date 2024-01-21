<?php

define('DB_USERNAME', 'mentalcare');
define('DB_PASSWORD', 'nami202211');
define('DSN', 'mysql:host=www3526.sakura.ne.jp; dbname=mentalcare_yamauchi; charset=utf8');

function db_connect(){
    $dbh = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    return $dbh;
}

?>
