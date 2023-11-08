<?php

    $dbname = "moviestar";
    $user = "root";
    $pass = "";
    $host = "localhost";

    $conn = new PDO("mysql:dbname=$dbname;host=$host", $user, $pass);

    // ERROS PDO

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);



?>