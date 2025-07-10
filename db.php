<?php
    $db_host = "localhost";
    $db_user = "";
    $db_pwd = "";
    $db_db = "";
    $charset = "utf8mb4";
    
    $attr="mysql:host=$db_host;dbname=$db_db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    function connect_to_db($attr, $db_user, $db_pwd, $options) {
        try {
            $db = new PDO($attr, $db_user, $db_pwd, $options);
            return $db;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
?>
