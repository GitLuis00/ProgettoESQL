<?php
// Include il file di configurazione del database
@include 'config.php';
session_start();

if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {
    // Se l'utente non è loggato come docente, reindirizzalo alla pagina di login
    header('Location: pageloginUtente.php');
    exit();
}

// Supponendo che "GetTestCompletati()" sia una stored procedure che vogliamo chiamare
// Esegui la query per ottenere la classifica degli studenti per i test completati
// (Assicurati che la stored procedure restituisca i dati desiderati per questa classifica)
$stmt_test_completati = $pdo->query("CALL GetTestCompletati()");
$classifica_studenti_test_completati = $stmt_test_completati->fetchAll(PDO::FETCH_ASSOC);
$stmt_test_completati->closeCursor();

// Esegui la query per ottenere la classifica degli studenti per risposte esatte
$stmt_risposte_esatte = $pdo->query("SELECT * FROM ClassificaRisposteEsatte");
$classifica_risposte_esatte = $stmt_risposte_esatte->fetchAll(PDO::FETCH_ASSOC);
$stmt_risposte_esatte->closeCursor();

// Esegui la query per ottenere la classifica dei quesiti
$stmt_quesiti = $pdo->query("SELECT * FROM ClassificaQuesiti");
$classifica_quesiti = $stmt_quesiti->fetchAll(PDO::FETCH_ASSOC);
$stmt_quesiti->closeCursor(); // Chiudi il cursore dopo l'uso
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifica Studenti</title>
    <link rel="stylesheet" href="stili.css">
</head>
<body>




    <h2 class="titolo">Classifica degli Studenti per Test Completati</h2>
<table>
    <thead>
        <tr>
            <th>Posizione</th>
            <th>Codice Studente</th>
            <th>Numero di Test Completati</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $posizione = 1;
        foreach ($classifica_studenti_test_completati as $studente) {
            echo "<tr>";
            echo "<td>{$posizione}</td>";
            echo "<td>{$studente['codice_studente']}</td>";
            echo "<td>{$studente['numero_test_completati']}</td>";
            echo "</tr>";
            $posizione++;
        }
        ?>
    </tbody>
</table>

<h2 class="titolo">Classifica degli Studenti per Risposte Esatte</h2>
<table>
    <thead>
        <tr>
            <th>Posizione</th>
            <th>Codice Studente</th>
            <th>Risposte Corrette</th>
            <th>Totale Risposte</th>
            <th>Percentuale Corrette</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $posizione = 1;
        foreach ($classifica_risposte_esatte as $studente) {
            echo "<tr>";
            echo "<td>{$posizione}</td>";
            echo "<td>{$studente['codice_studente']}</td>";
            echo "<td>{$studente['risposte_corrette']}</td>";
            echo "<td>{$studente['totale_risposte']}</td>";
            echo "<td>{$studente['percentuale_corrette']}%</td>";
            echo "</tr>";
            $posizione++;
        }
        ?>
    </tbody>
</table>

<h2 class="titolo">Classifica Quesiti per Numero di Risposte</h2>
<table>
    <thead>
        <tr>
            <th>Posizione</th>
            <th>ID Quesito</th>
            <th>Numero di Risposte</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $posizione = 1;
        foreach ($classifica_quesiti as $quesito) {
            echo "<tr>";
            echo "<td>{$posizione}</td>";
            echo "<td>{$quesito['id_quesito']}</td>";
            echo "<td>{$quesito['numero_risposte']}</td>";
            echo "</tr>";
            $posizione++;
        }
        ?>
    </tbody>
</table>

    <div class="bottoni">
        <form action="pagewelcomeDocente.php" method="post">
            <input id="pulsante" type="submit" value="Torna alla Home Page Docente">
        </form>
    </div>
</body>
</html>