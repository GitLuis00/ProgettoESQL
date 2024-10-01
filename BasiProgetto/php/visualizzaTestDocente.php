<?php
// Includi il file di configurazione e avvia la sessione
@include 'config.php';
session_start();


// Verifica se l'utente ha effettuato l'accesso e se è un docente
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {

    header('Location: ESQL.php');
    exit();
}
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}

$email = $_SESSION['email']; // Modifica in base alla struttura effettiva della tua sessione
// Recupera l'id_docente dalla sessione (o ovunque tu lo abbia memorizzato)
$email_docente = $_SESSION['email'];

// Messaggio di conferma predefinito
$messaggio = "";

// Gestione del submit del modulo
if(isset($_POST['Salva'])) {
    // Verifica se sono stati inviati l'ID del test e lo stato di VisualizzaRisposte
    if(isset($_POST['id_test']) && isset($_POST['VisualizzaRisposte'])) {
        $id_test = $_POST['id_test'];
        $visualizza_risposte = $_POST['VisualizzaRisposte'];

        // Chiama la procedura memorizzata per aggiornare lo stato di VisualizzaRisposte per il test specificato
        $stmt = $pdo->prepare("CALL UpdateTestVisualizzaRisposte(?, ?)");
        $stmt->execute([$id_test, $visualizza_risposte]);
        
        $messaggio = "<b>Aggiornamento effettuato con successo!</b>";
    } else {
        $messaggio = "ID del test o stato di VisualizzaRisposte mancante.";
    }
}


// Preparazione e esecuzione della procedura memorizzata per ottenere i test creati dal docente attuale
$stmt = $pdo->prepare("CALL GetTestsByDocenteId(?)");
$stmt->bindParam(1, $email_docente, PDO::PARAM_STR);
$stmt->execute();
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESQL - Test Creati</title>
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

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #F47D2B;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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

        .message-container {
            margin-top: 20px;
            font-size: 1.2em;
            color: #d9534f;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <h2 class="titolo">Test Creati</h2>
    <div class="titolo">
        <p><b>Di seguito sono visualizzati i test da Lei creati:</b></p>
        <table>
            <tr>
                <th>ID Test</th>
                <th>Titolo</th>
                <th>Data Creazione</th>
                <th>Foto</th>
                <th>Visualizza Risposte</th>
                <th>Azione</th>
            </tr>
            <?php foreach ($tests as $test) { ?>
                <tr>
                    <td><a href="paginaTest.php?id_test=<?php echo htmlspecialchars($test['id_test']); ?>"><?php echo htmlspecialchars($test['id_test']); ?></a></td>
                    <td><a href="paginaTest.php?id_test=<?php echo htmlspecialchars($test['id_test']); ?>"><?php echo htmlspecialchars($test['titolo']); ?></a></td>
                    <td><?php echo htmlspecialchars($test['data_creazione']); ?></td>
                    <td><?php echo htmlspecialchars($test['foto']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_test" value="<?php echo htmlspecialchars($test['id_test']); ?>">
                            <input type="hidden" name="VisualizzaRisposte" value="0">
                            <input type="checkbox" name="VisualizzaRisposte" value="1" <?php if ($test['VisualizzaRisposte']) echo "checked"; ?>>
                            <input type="submit" name="Salva" value="Salva">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="message-container">
            <?php echo htmlspecialchars($messaggio); ?>
        </div>
    </div>

    <div class="button-container">
        <form action="pagewelcomeDocente.php" method="post">
            <input type="submit" name="dove" value="Torna alla Home Page Docente"/>
        </form>
        <form action="creaTest.php" method="post">
            <input type="button" value="Crea Test" onclick="location.href='creaTest.php'"/>
        </form>
        <form action="pageloginDocente.php" method="post">
            <input type="button" value="Logout" onclick="location.href='pageloginDocente.php'"/>
        </form>
    </div>
</body>
</html>
