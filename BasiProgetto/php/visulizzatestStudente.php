<?php
// Includi il file di configurazione e avvia la sessione
@include 'config.php';
session_start();

// Ottieni l'ID dello studente dalla sessione (modifica questa logica in base al tuo sistema)
$emailStudente = $_SESSION['email']; 

// Verifica se è stato inviato un POST e se sono presenti l'ID del test e lo studente è loggato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_test']) && $emailStudente) {
    $idTest = $_POST['id_test'];
    $_SESSION['id_test_corrente'] = $idTest;

    // Verifica se lo studente ha già completato il test
    $stmt = $pdo->prepare("SELECT stato_del_completamento FROM CompletamentoTest WHERE email_studente = ? AND id_test = ?");
    $stmt->execute([$emailStudente, $idTest]);
    $statoTest = $stmt->fetchColumn();

    // Se lo stato del completamento è "Concluso", mostra un alert e interrompi l'esecuzione
    if ($statoTest === 'Concluso') {
        echo "<script>alert('Il test è già stato concluso.');</script>";
        exit();
    }

    // Se lo stato del completamento non è "Concluso", inserisci o aggiorna il record di completamento del test
    $inserisci = $pdo->prepare("INSERT INTO CompletamentoTest (email_studente, id_test, stato_del_completamento) VALUES (?, ?, 'Aperto') ON DUPLICATE KEY UPDATE stato_del_completamento = 'Aperto'");
    $inserisci->execute([$emailStudente, $idTest]);

    // Reindirizza alla pagina dei quesiti
    header('Location: pagequesiti.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inserimento Quesiti di Codice</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #main {
            text-align: center;
            padding: 20px;
        }

        .titolo {
            text-align: center;
            color: #3399CC;
            font-size: 36px;
            margin-top: 20px;
        }

        #intestazione {
            background-color: #3399CC;
            color: antiquewhite;
            text-align: center;
            padding: 15px;
            font-size: 24px;
        }

        #pulsante {
            margin-top: 15px;
            outline: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font: bold 14px 'Courier New', Courier, monospace;
            color: #fff;
            padding: 10px 20px;
            border: solid 1px #0076a3;
            background: #0095cd;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        #pulsante:hover {
            background: #0076a3;
            transform: scale(1.05);
        }

        .bottoni {
            text-align: center;
            margin-top: 20px;
        }

        #errore {
            text-align: center;
            color: red;
            font-size: 16px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            text-align: center;
            color: #3399CC;
            font-size: 18px;
            margin: 10px 0;
        }

        h2 {
            color: orange;
            font-size: 28px;
            margin-bottom: 20px;
        }

        h3 {
            color: #3399CC;
            font-size: 22px;
            margin-top: 20px;
        }
    </style>
</head>
<body id="main">

<?php
// Query to fetch tests grouped by id_docente
$sql = "CALL GetTestByStudenteId(?)";

// Execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute([$emailStudente]);

// Check if we have results
if ($stmt->rowCount() > 0) {
    echo "<h2>Lista dei Test</h2>";

    $currentDocente = null;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Check if we are still listing tests for the same docente
        if ($currentDocente !== $row['email_docente']) {
            if ($currentDocente !== null) {
                // Close the previous list
                echo "</ul>";
            }
            $currentDocente = $row['email_docente'];
            // Print the docente's name (potresti voler modificare per mostrare il nome del docente)
            echo "<h3>Docente: " . htmlspecialchars($row['email_docente']) . "</h3>";
            // Start a new list
            echo "<ul>";
        }

        // Display each test
        echo "<li>" . htmlspecialchars($row['titolo']) . " - Creato il: " . $row['data_creazione'];
        // Mostra lo stato del completamento
        if ($row['stato_del_completamento'] === 'Concluso') {
            echo " (Test Concluso)";
        }
        // Form for "Partecipa al Test"
        echo "<form method='POST' style='display: inline-block;'>";
        echo "<input type='hidden' name='id_test' value='" . $row['id_test'] . "'>";
        echo "<input id='pulsante' type='submit' value='Partecipa al Test'>";
        echo "</form>";
        // Form for "Invia Messaggio"
        echo "<form method='POST' action='InserimentoMessaggioStudente.php' style='display: inline-block; margin-left: 10px;'>";
        echo "<input type='hidden' name='id_test' value='" . $row['id_test'] . "'>";
        echo "<input type='hidden' name='email_docente' value='" . $row['email_docente'] . "'>";
        echo "<input id='pulsante' type='submit' value='Invia Messaggio'>";
        echo "</form>";
        echo "</li>";
        // Form for "Torna indietro"
echo "<form method='GET' action='pagewelcome.php' style='display: inline-block; margin-left: 10px;'>";
echo "<input id='pulsante' type='submit' value='Torna alla Home Page'>";
echo "</form>";
    }
    // Close the last list
    echo "</ul>";
} else {
    echo "<p>Nessun test trovato.</p>";
}
?>

</body>
</html>

