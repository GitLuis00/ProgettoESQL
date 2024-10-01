<?php
session_start();
include 'config.php';
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}
if(isset($_POST['Inserisci'])) {
    if(!empty($_POST['testo']) && !empty($_POST['codice'])) {
        // Recupera l'ID del test dalla sessione
        $test_id = $_SESSION['id_test'] ?? null;

        if($test_id === null) {
            echo "ID del test non specificato.";
            exit();
        }
        
        $test_id = intval($test_id); // Converte l'ID del test in un intero per sicurezza
        
        $testo = trim($_POST['testo']);
        $codice = trim($_POST['codice']);
        $livello_difficolta = $_POST['livello_difficolta']; // Aggiunto il livello di difficoltà
        
        // Categoria per i quesiti di codice
        $categoria = 'Codice';
        
        // Query per ottenere il numero progressivo massimo per il test corrente
        $stmt_max = $pdo->prepare('SELECT MAX(numero_progressivo) AS max_progressivo FROM Quesito WHERE id_test = ?');
        $stmt_max->execute([$test_id]);
        $row_max = $stmt_max->fetch(PDO::FETCH_ASSOC);
        $num_progressivo = ($row_max['max_progressivo'] ?? 0) + 1; // Incrementa il numero progressivo
        
        // Inserisci il nuovo quesito nel database
        $stmt = $pdo->prepare('INSERT INTO Quesito (id_test, numero_progressivo, livello_difficolta, descrizione, categoria) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$test_id, $num_progressivo, $livello_difficolta, $testo, $categoria]);
        $quesito_id = $pdo->lastInsertId();
        $_SESSION["id_quesito"] = $quesito_id ;


        // Inserisci la soluzione del codice nel database
        $stmt_soluzione = $pdo->prepare('call InserisciSoluzioneCodice(?, ?)');
        $stmt_soluzione->execute([$quesito_id, $codice]);

        echo "<b>Domanda di codice inserita nel sistema!</b>";

        header("Location: pageCreaTabellaEsercizio.php?id_quesito={$quesito_id}");
        exit();
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
    <title>Inserimento Quesiti di Codice</title>
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

        .titolo {
            color: #F47D2B;
            margin: 20px 0;
        }

        .titolo h2 {
            font-size: 2em;
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
    </style>
</head>
<body>
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>
    
    <div class="form-container">
        <form action="creaquesitidicodice.php" method="post" enctype="multipart/form-data">
            <div class="titolo">
                <h2>Inserimento Domande di Codice</h2>
                Riempi i campi obbligatori, poi premi 'Inserisci' per aggiungere la domanda di codice al sistema!
            </div>

            <div class="form-group">
                <label for="testo">Testo della Domanda:</label>
                <input type="text" name="testo" id="testo" maxlength="100" required>
                <i>La domanda può essere lunga al massimo 100 caratteri.</i>
            </div>

            <div class="form-group">
                <label for="codice">Codice:</label>
                <textarea name="codice" id="codice" rows="6" required></textarea>
                <i>Inserisci qui il codice della domanda.</i>
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
            <form action="pagewelcomeDocente.php" method="post">
                <input id="pulsante" type="submit" name="dove" value="Torna alla Home Page Docente">
            </form>
            <form action="visualizzaTestDocente.php" method="post">
                <input id="pulsante" type="button" value="Torna a tutti i test" onclick="location.href='visualizzaTestDocente.php'">
            </form>
            <form action="pageloginDocente.php" method="post">
                <input id="pulsante" type="button" value="Logout" onclick="location.href='pageloginDocente.php'">
            </form>
        </div>
    </div>
</body>
</html>

