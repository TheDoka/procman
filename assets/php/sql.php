<?php

    function createPDO()
    {
        
        try {
            $pdo = new PDO("mysql:host=localhost; dbname=procman", "root", "root");
        } catch (PDOException $e) {
            die();
        }
            
        return $pdo;

    }

?>