<?php
// Includi il file di configurazione e avvia la sessione
@include 'config.php';
session_start();
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}

// Verifica se l'utente ha effettuato l'accesso e se è un docente
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {
    // Se l'utente non è loggato come docente, reindirizzalo alla pagina di login
    header('Location: pageloginUtente.php');
    exit();
}

// Verifica se è stato passato un ID test tramite GET
if (!isset($_GET['id_test'])) {
    // Se l'ID del test non è stato fornito, reindirizza l'utente alla pagina dei test creati
    header('Location: visualizzaTestDocente.php');
    exit();
}

// Recupera l'ID del test dalla query string
$id_test = $_GET['id_test'];

// Memorizza l'ID del test nella sessione
$_SESSION['id_test'] = $id_test;

// Modifica la query per recuperare i dettagli del test specificato
$testQuery = $pdo->prepare("SELECT * FROM Test WHERE id_test = ?");
$testQuery->bindParam(1, $id_test, PDO::PARAM_INT);
$testQuery->execute();

// Verifica se il test è stato trovato
if ($testQuery->rowCount() == 0) {
    // Se il test non esiste, reindirizza l'utente alla pagina dei test creati
    header('Location: visualizzaTestDocente.php');
    exit();
}

// Estrai i dettagli del test
$test = $testQuery->fetch(PDO::FETCH_ASSOC);

// Modifica la query per recuperare i quesiti relativi al test specificato
$quesitiQuery = $pdo->prepare("SELECT * FROM Quesito WHERE id_test = ?");
$quesitiQuery->bindParam(1, $id_test, PDO::PARAM_INT);
$quesitiQuery->execute();

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Test - <?php echo htmlspecialchars($test['titolo']); ?></title>
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

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            margin: 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

    </style>
</head>
<body>
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <h2 class="titolo">Visualizza Test - <?php echo htmlspecialchars($test['titolo']); ?></h2>
    <div class="titolo">
        <!-- Mostra i dettagli del test -->
        <p><b>Dettagli del Test:</b></p>
        <ul>
            <li><b>ID Test:</b> <?php echo htmlspecialchars($test['id_test']); ?></li>
            <li><b>Titolo:</b> <?php echo htmlspecialchars($test['titolo']); ?></li>
            <li><b>Data Creazione:</b> <?php echo htmlspecialchars($test['data_creazione']); ?></li>
            <!-- Aggiungi altri dettagli del test se necessario -->
        </ul>
    </div>
    
    <br>

    <div class="titolo">
        <!-- Mostra i quesiti relativi al test -->
        <p><b>Quesiti del Test:</b></p>
        <ul>
            <?php while ($quesito = $quesitiQuery->fetch(PDO::FETCH_ASSOC)) { ?>
                <li>
                    <b>DOMANDA:</b> <?php echo htmlspecialchars($quesito['descrizione']); ?>
                    <ul>
                        <?php 
                        // Query per recuperare le opzioni di risposta associate al quesito
                        $opzioniQuery = $pdo->prepare("SELECT * FROM OpzioneRisposta WHERE id_quesito = ?");
                        $opzioniQuery->bindParam(1, $quesito['id_quesito'], PDO::PARAM_INT);
                        $opzioniQuery->execute();
                        while ($opzione = $opzioniQuery->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li>" . htmlspecialchars($opzione['testo']) . "</li>";
                        }
                        ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>

    <br>

    <div class="button-container">
        <form action="pagewelcomeDocente.php" method="post">
            <input type="submit" name="dove" value="Torna alla Home Page Docente"/>
        </form>
        <form action="visualizzaTestDocente.php" method="post">
            <input type="button" value="Torna a tutti i test" onclick="location.href='visualizzaTestDocente.php'"/>
        </form>
        <form action="creaquesiti.php" method="post">
            <input type="button" value="Crea Quesiti Chiusi" onclick="location.href='creaquesiti.php'"/>
        </form>
        <form action="creaquesitidicodice.php" method="post">
            <input type="button" value="Crea Quesiti di Codice" onclick="location.href='creaquesitidicodice.php'"/>
        </form>
        <form action="pageloginDocente.php" method="post">
            <input type="button" value="Logout" onclick="location.href='pageloginDocente.php'"/>
        </form>
    </div>

    <br>

    <form action="pageInserimentoMessaggioDocente.php" method="post">
        <input type="hidden" name="id_test" value="<?php echo htmlspecialchars($id_test); ?>">
        <input id="pulsante" type="submit" value="Inserisci Messaggio Docente">
    </form>

</body>
</html>

