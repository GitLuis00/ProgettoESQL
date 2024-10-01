<?php

// CONNESSIONE AL DB MySQL
try {
   $pdo = new PDO('mysql:host=localhost;dbname=esqldb_ultimaVersione', 'root', '');
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // Log di avvenuta connessione MySQL
   echo "Connessione al DB MySQL eseguita con successo.<br>";
}
catch(PDOException $e) {
   echo "[ERRORE] Connessione al DB MySQL non riuscita. Errore: " . $e->getMessage();
   exit();
}

/*
date_default_timezone_set('Europe/Rome');
try {
   $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
   // Log di avvenuta connessione MongoDB
   echo "Connessione al DB MongoDB eseguita con successo.<br>";
}
catch(MongoDB\Driver\Exception\Exception $e) {
   echo "[ERRORE] Connessione al DB MongoDB non riuscita. Errore: " . $e->getMessage();
   exit();
}

$bulk = new MongoDB\Driver\BulkWrite;

*/
