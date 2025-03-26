<?php
function get_db() {
    $host = getenv('MYSQL_HOST');
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');

    try {
        // create + return new pdo instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch(Exception $e) {
        print_r($e);
    }
}