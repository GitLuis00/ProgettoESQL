<?php
@include 'config.php';
session_start();

// e che $emailStudente sia già definito e ottenuto in modo sicuro

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_quesito'], $_POST['categoria'])) {
    
    $idQuesito = $_POST['id_quesito'];
    $categoria = $_POST['categoria'];
    $emailStudente = $_SESSION['email'];

    // Inizia una transazione per garantire l'integrità dei dati
    

    try {
        // Inserimento iniziale nella tabella Risposta
        $sqlRisposta = "INSERT INTO Risposta (id_quesito, email_studente) VALUES (?, ?)";
        $stmtRisposta = $pdo->prepare($sqlRisposta);
        $stmtRisposta->execute([$idQuesito, $emailStudente]);
        $idRisposta = $pdo->lastInsertId(); // Ottiene l'ID dell'ultima risposta inserita

        if ($categoria == 'Risposta Chiusa') {
            $idOpzione = $_POST['risposta'];
            $sqlChiusa = "INSERT INTO RispostaChiusa (id_risposta, id_opzione) VALUES (?, ?)";
            $stmtChiusa = $pdo->prepare($sqlChiusa);
            $stmtChiusa->execute([$idRisposta, $idOpzione]);
        } else {
            $testoRisposta = $_POST['risposta'];
            $sqlAperta = "INSERT INTO RispostaAperta (id_risposta, testo_risposta) VALUES (?, ?)";
            $stmtAperta = $pdo->prepare($sqlAperta);
            $stmtAperta->execute([$idRisposta, $testoRisposta]);

            // Prende la soluzione del docente
            $stmt = $pdo->prepare("SELECT sketch_codice FROM SoluzioneCodice WHERE id_quesito = ?");
            $stmt->execute([$idQuesito]);
            $soluzioneDocente = $stmt->fetch(PDO::FETCH_ASSOC)['sketch_codice'];

// Esecuzione della query del docente per ottenere i risultati di riferimento
$stmtDocente = $pdo->prepare($soluzioneDocente);
$stmtDocente->execute();
$risultatiDocente = $stmtDocente->fetchAll(PDO::FETCH_ASSOC);

// Esecuzione della query dello studente
try {
    $stmtStudente = $pdo->prepare($testoRisposta);
    $stmtStudente->execute();
    $risultatiStudente = $stmtStudente->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Errore nell'esecuzione della query dello studente: " . $e->getMessage();
    
    exit;
}

// Confronto dei risultati
if ($risultatiDocente == $risultatiStudente) {
    echo "La soluzione dello studente è corretta.";
    $esito = true;
} else {
    echo "La soluzione dello studente è incorretta.";
    $esito = false;
}

// Aggiorna il campo esito nella tabella Risposta in base al risultato del confronto
try {
    $sqlAggiornaEsito = "UPDATE Risposta SET esito = :esito WHERE id_risposta = :idRisposta";
    $stmtAggiornaEsito = $pdo->prepare($sqlAggiornaEsito);
    $stmtAggiornaEsito->execute(['esito' => $esito, 'idRisposta' => $idRisposta]);
} catch (Exception $e) {
    // Gestisce eventuali errori durante l'aggiornamento
    echo "Errore nell'aggiornamento del campo esito: " . $e->getMessage();
    exit;
}

// Continua con il commit solo se non ci sono stati errori

echo "<p>Risposta registrata con successo.</p>";
}
} catch (Exception $e) {
// In caso di errore, annulla la transazione

echo "<p>Errore nella registrazione della risposta: " . $e->getMessage() . "</p>";
}
}


if (!isset($_SESSION['id_test_corrente'])) {
    // Gestisci l'errore o reindirizza l'utente
    die("ID del test non impostato.");
}

if (!isset($_SESSION['indice_quesito'])) {
    $_SESSION['indice_quesito'] = 0; // Inizia dal primo quesito
}

$idTest = $_SESSION['id_test_corrente'];

function fetchQuesitoCorrente($pdo, $idTest) {
    $indice = $_SESSION['indice_quesito'];
    $sql = "SELECT Quesito.id_quesito, Quesito.descrizione, Quesito.categoria 
            FROM Quesito 
            WHERE Quesito.id_test = :idTest 
            LIMIT :indice,1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idTest', $idTest, PDO::PARAM_INT);
    $stmt->bindParam(':indice', $indice, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$quesito = fetchQuesitoCorrente($pdo, $idTest);

// Gestisci la sottomissione delle risposte
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_quesito'])) {
    // Logica per gestire la sottomissione della risposta qui

    $_SESSION['indice_quesito'] += 1; // Passa al prossimo quesito
    header("Location: " . $_SERVER['PHP_SELF']); // Ricarica la pagina per mostrare il prossimo quesito
    exit;
}

// Se non ci sono più quesiti da visualizzare
if (!$quesito) {
    echo "<p>Hai completato tutti i quesiti!</p>";
    // Resetta o gestisci la fine del quiz qui
    unset($_SESSION['indice_quesito']);
    // Reindirizza o mostra un messaggio di completamento
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Rispondi ai Quesiti</title>
    <style>
        /* Stili di base per il corpo della pagina */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e0f7fa;
            color: #00796b;
            line-height: 1.6;
            margin: 0;
            padding: 40px 20px;
            text-align: center;
        }

        /* Stili per l'intestazione principale */
        h2 {
            color: #00796b;
            font-size: 36px;
            margin-bottom: 20px;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            text-shadow: 1px 1px 2px #004d40;
        }

        /* Stili per i titoli delle sezioni */
        .titolo {
            font-size: 22px;
            margin-bottom: 15px;
            color: #004d40;
        }

        /* Personalizzazione dei bottoni */
        input[type='submit'] {
            background-color: #00acc1;
            color: white;
            border: none;
            padding: 15px 30px;
            margin: 10px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 12px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        input[type='submit']:hover {
            background-color: #00838f;
            transform: scale(1.05);
        }

        /* Stili per i campi del form */
        textarea, input[type='text'] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #00acc1;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        /* Stili per il form */
        form {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: inline-block;
            max-width: 600px;
            text-align: left;
            margin: 0 auto;
        }

        fieldset {
            border: none;
        }

        legend {
            font-size: 28px;
            color: #00796b;
            margin-bottom: 20px;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            text-shadow: 1px 1px 2px #004d40;
        }

        /* Stili per il collegamento */
        a {
            color: #00796b;
            text-decoration: none;
            font-size: 18px;
            display: inline-block;
            margin-top: 20px;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            text-shadow: 1px 1px 2px #004d40;
        }

        a:hover {
            text-decoration: underline;
            color: #004d40;
        }
    </style>
</head>
<body>

<h2>Rispondi ai Quesiti</h2>

<?php 
if ($quesito) {
    echo "<form method='POST'>";
    echo "<fieldset>";
    echo "<legend>" . htmlspecialchars($quesito['descrizione']) . "</legend>";

    if ($quesito['categoria'] == 'Risposta Chiusa') {
        $sqlOpzioni = "SELECT id_opzione, testo FROM OpzioneRisposta WHERE id_quesito = :idQuesito";
        $stmtOpzioni = $pdo->prepare($sqlOpzioni);
        $stmtOpzioni->bindParam(':idQuesito', $quesito['id_quesito'], PDO::PARAM_INT);
        $stmtOpzioni->execute();
        $opzioni = $stmtOpzioni->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($opzioni)) {
            foreach ($opzioni as $opzione) {
                echo "<div><input type='radio' name='risposta' value='" . htmlspecialchars($opzione['id_opzione']) . "' required> " . htmlspecialchars($opzione['testo']) . "</div>";
            }
        } else {
            echo "<p>Non ci sono opzioni disponibili per questo quesito.</p>";
        }
    } else {
        echo "<textarea name='risposta' required></textarea>";
    }

    echo "<input type='hidden' name='id_quesito' value='" . htmlspecialchars($quesito['id_quesito']) . "'>";
    echo "<input type='hidden' name='categoria' value='" . htmlspecialchars($quesito['categoria']) . "'>";
    echo "<input type='submit' value='Invia Risposta'>";
    echo "</fieldset>";
    echo "</form>";
} else {
    echo "<p>Hai completato tutti i quesiti!</p>";
    echo "<a href='pagewelcome.php'>Torna alla pagina principale</a>";
}
?>

</body>
</html>

