<?php
session_start();
include 'config.php';
$email_studente = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>ESQL - Inserimento Test</title>
    <style>
        /* Stili di base per il corpo della pagina */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e0f7fa;
            color: #00796b;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        /* Stili per l'intestazione */
        #intestazione {
            background-color: #00796b;
            color: #ffffff;
            padding: 15px;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Stili per il titolo principale */
        h2 {
            color: #004d40;
            font-size: 28px;
            margin: 20px 0;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        /* Stili per i bottoni */
        #pulsante {
            margin-top: 15px;
            outline: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font: bold 14px 'Courier New', Courier, monospace;
            color: #ffffff;
            padding: 12px 24px;
            border: solid 1px #004d40;
            background: #00796b;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        #pulsante:hover {
            background: #004d40;
            transform: scale(1.05);
        }

        /* Stili per le tabelle */
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th, td {
            border: solid 2px #00796b;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #00796b;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #e0f2f1;
        }

        tr:hover {
            background-color: #b2dfdb;
        }

        /* Stili per messaggi di errore */
        #errore {
            color: red;
            font-family: 'Courier New', Courier, monospace;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div id="intestazione">
    <h1>ESQL - Risultati Test</h1>
</div>

<?php
$query = "CALL OttieniRisposteStudente(?)";

$stmt = $pdo->prepare($query);
$stmt->execute([$email_studente]);

$risultati = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($risultati) {
    // Raggruppa i risultati per test
    $testRisultati = [];
    foreach ($risultati as $risultato) {
        $testRisultati[$risultato['id_test']][] = $risultato;
    }

    // Visualizza i risultati per ogni test in una tabella separata
    foreach ($testRisultati as $id_test => $quesiti) {
        echo "<h2>Test ID: $id_test</h2>";
        echo "<table>";
        echo "<tr><th>Quesito Numero</th><th>Descrizione</th><th>Esito</th><th>Data Risposta</th></tr>";
        foreach ($quesiti as $quesito) {
            $esito = $quesito['esito'] ? 'Corretto' : 'Errato';
            $dataRisposta = new DateTime($quesito['data_risposta']);
            echo "<tr>
                    <td>{$quesito['numero_progressivo']}</td>
                    <td>{$quesito['descrizione_quesito']}</td>
                    <td>$esito</td>
                    <td>" . $dataRisposta->format('d/m/Y H:i:s') . "</td>
                  </tr>";
        }
        echo "</table><br>";
    }
} else {
    echo "<p id='errore'>Nessuna risposta trovata per lo studente specificato.</p>";
}
?>

</body>
</html>
