<?php
session_start();
include 'config.php';

// Verifica se l'utente ha effettuato l'accesso e se è un docente
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {

    header('Location: ESQL.php');
    exit();
}
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}


// Messaggio di errore predefinito
$messaggio = "";

// Gestione del submit del modulo
if(isset($_POST['Inserisci'])) {
    // Verifica se tutti i campi obbligatori sono stati riempiti
    if(!empty($_POST['titolo']) && !empty($_POST['data_creazione']) && !empty($_POST['foto'])) {
        $titolo = $_POST['titolo'];
        $data_creazione = $_POST['data_creazione'];
        $foto = $_POST['foto'];
        $email_docente = $_SESSION['email'];
        echo $email_docente;
        $VisualizzaRisposte = isset($_POST['VisualizzaRisposte']) ? 1 : 0; // Converte il valore booleano in 0 o 1

        // Chiama la procedura memorizzata per inserire il test nel database
        $stmt = $pdo->prepare("CALL InserisciTest(?, ?, ?, ?, ?)");
        $stmt->execute([$titolo, $data_creazione, $foto, $email_docente, $VisualizzaRisposte]);
        
        $messaggio = "<b>Test inserito nel sistema!</b>";
    } else {
        $messaggio = "Devi riempire tutti i campi obbligatori.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESQL - Inserimento Test</title>
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

        .titolo h2 {
            color: #F47D2B;
            font-size: 2em;
            margin: 20px 0;
            font-weight: bold;
        }

        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container label {
            display: block;
            margin: 15px 0 5px;
            font-size: 1.2em;
            color: #333;
        }

        .form-container input[type="text"], 
        .form-container input[type="date"], 
        .form-container input[type="checkbox"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 1em;
        }

        .form-container input[type="submit"], 
        .form-container input[type="button"] {
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

        .form-container input[type="submit"]:hover, 
        .form-container input[type="button"]:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .form-container #errore {
            color: #d9534f;
            font-weight: bold;
            margin-top: 15px;
        }

        .form-actions {
            margin-top: 20px;
        }

        .form-actions input {
            width: 200px;
        }

    </style>
</head>
<body>
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <div class="form-container">
        <form action="creaTest.php" method="post">
            <h2>Inserimento Test</h2>
            <p>Riempi tutti i campi obbligatori e premi 'Inserisci' per aggiungere un nuovo test al sistema.</p>

            <label for="titolo">Titolo del Test:</label>
            <input type="text" name="titolo" id="titolo" required>

            <label for="data_creazione">Data di Creazione:</label>
            <input type="date" name="data_creazione" id="data_creazione" required>

            <label for="foto">Foto (URL):</label>
            <input type="text" name="foto" id="foto" required>

            <label for="VisualizzaRisposte">Visualizza Risposte:</label>
            <input type="checkbox" name="VisualizzaRisposte" id="VisualizzaRisposte">

            <input type="submit" name="Inserisci" value="Inserisci">

            <div id="errore">
                <b><?php echo htmlspecialchars($messaggio); ?></b>
            </div>
        </form>

        <div class="form-actions">
            <form action="visualizzaTestDocente.php" method="post">
                <input type="submit" name="dove" value="Visualizza tutti i test"/>
            </form>

            <form action="pageloginDocente.php" method="post">
                <input type="button" value="Logout" onclick="location.href='pageloginDocente.php'"/>
            </form>
        </div>
    </div>
</body>
</html>

