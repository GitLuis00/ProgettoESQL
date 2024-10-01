<?php
session_start();
include 'config.php';
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}

if(isset($_POST['Inserisci'])) {
    if(!empty($_POST['testo']) && !empty($_POST['op1']) && !empty($_POST['op2']) && !empty($_POST['op3']) && !empty($_POST['opzione_corretta'])) {
        // Recupera l'ID del test dalla sessione
        $test_id = $_SESSION['id_test'] ?? null;

        if($test_id === null) {
            echo "ID del test non specificato.";
            exit();
        }
        
        $test_id = intval($test_id); // Converte l'ID del test in un intero per sicurezza
        
        $testo = trim($_POST['testo']);
        if(strlen($testo) <= 100) {
            $categoria = 'Risposta Chiusa';
            
            // Query per ottenere il numero progressivo massimo per il test corrente
            $stmt_max = $pdo->prepare('SELECT MAX(numero_progressivo) AS max_progressivo FROM Quesito WHERE id_test = ?');
            $stmt_max->execute([$test_id]);
            $row_max = $stmt_max->fetch(PDO::FETCH_ASSOC);
            $num_progressivo = ($row_max['max_progressivo'] ?? 0) + 1; // Incrementa il numero progressivo
            
            $livello_difficolta = $_POST['livello_difficolta']; // Aggiunto il livello di difficoltà
            $descrizione = $testo;
            $num_risposte = 0; // Numero di risposte per quesito
            
            // Inserisci il nuovo quesito nella tabella Quesito
            $stmt = $pdo->prepare('INSERT INTO Quesito (id_test, numero_progressivo, livello_difficolta, descrizione, num_risposte, categoria) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$test_id, $num_progressivo, $livello_difficolta, $descrizione, $num_risposte, $categoria]);
            $quesito_id = $pdo->lastInsertId();

            // Inserisci le opzioni di risposta nella tabella OpzioneRisposta
            $opzioni = array($_POST['op1'], $_POST['op2'], $_POST['op3']);
            foreach($opzioni as $key => $opzione) {
                $stmt = $pdo->prepare('INSERT INTO OpzioneRisposta (id_quesito, numerazione, testo) VALUES (?, ?, ?)');
                $stmt->execute([$quesito_id, $key+1, $opzione]);
            }

            // Inserisci l'opzione corretta nella tabella SoluzioneRispostaChiusa
            $opzione_corretta = $_POST['opzione_corretta'];
            $stmt = $pdo->prepare('INSERT INTO SoluzioneRispostaChiusa (id_quesito, numerazioneOpzione) VALUES (?, ?)');
            $stmt->execute([$quesito_id, $opzione_corretta]);

            echo "<b>Domanda inserita nel sistema!</b>";
        } else {
            $mess = "La domanda deve essere lunga al massimo 100 caratteri!";
        }
    } else {
        $mess = "Devi riempire tutti i campi obbligatori.";
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserimento Domande Chiuse</title>
    <link href="stili.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        #intestazione {
            background-color: #F47D2B;
            color: #fff;
            padding: 20px;
            margin-bottom: 20px;
        }

        #intestazione h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: bold;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 0 auto;
            width: 80%;
            max-width: 600px;
            text-align: left;
        }

        .form-container h2 {
            color: #F47D2B;
            font-size: 2em;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group i {
            font-size: 0.9em;
            color: #777;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container input {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            margin: 10px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-container input:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        #errore {
            margin-top: 20px;
            color: red;
            font-weight: bold;
        }

        .form-group input[type="text"] {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <div class="form-container">
        <form action="creaquesiti.php" method="post" enctype="multipart/form-data">
            <h2>Inserimento Domande Chiuse</h2>
            <p>Riempi i campi obbligatori, poi premi 'Inserisci' per aggiungere la domanda al sistema!</p>

            <div class="form-group">
                <label for="testo">Testo della Domanda:</label>
                <input type="text" name="testo" id="testo" maxlength="100" required>
                <i>La domanda può essere lunga al massimo 100 caratteri.</i>
            </div>

            <div class="form-group">
                <label for="op1">Opzione 1:</label>
                <input type="text" name="op1" id="op1" required>
            </div>

            <div class="form-group">
                <label for="op2">Opzione 2:</label>
                <input type="text" name="op2" id="op2" required>
            </div>

            <div class="form-group">
                <label for="op3">Opzione 3:</label>
                <input type="text" name="op3" id="op3" required>
            </div>

            <div class="form-group">
                <label for="opzione_corretta">Opzione Corretta:</label>
                <select name="opzione_corretta" id="opzione_corretta" required>
                    <option value="1">Opzione 1</option>
                    <option value="2">Opzione 2</option>
                    <option value="3">Opzione 3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="livello_difficolta">Livello di Difficoltà:</label>
                <select name="livello_difficolta" id="livello_difficolta" required>
                    <option value="Basso">Basso</option>
                    <option value="Medio">Medio</option>
                    <option value="Alto">Alto</option>
                </select>
            </div>

            <div class="button-container">
                <input id="pulsante" type="submit" name="Inserisci" value="Inserisci">
            </div>

            <div id="errore">
                <?php
                if(isset($mess)) {
                    echo htmlspecialchars($mess);
                }
                ?>
            </div>
        </form>

        <div class="button-container">
            <input id="pulsante" type="button" value="Torna a tutti i test" onclick="location.href='visualizzaTestDocente.php'">
            <input id="pulsante" type="button" value="Logout" onclick="location.href='pageloginDocente.php'">
        </div>
    </div>
</body>
</html>

