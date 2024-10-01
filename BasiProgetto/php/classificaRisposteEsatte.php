<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {
    header('Location: pageloginUtente.php');
    exit();
}

$stmt_risposte_esatte = $pdo->query("SELECT * FROM ClassificaRisposteEsatte");
$classifica_risposte_esatte = $stmt_risposte_esatte->fetchAll(PDO::FETCH_ASSOC);
$stmt_risposte_esatte->closeCursor();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifica Risposte Corrette</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link href="stili.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Comic Neue', cursive;
            background-color: #FFEBCC;
            margin: 0;
            padding: 0;
        }

        .titolo {
            color: #3399CC;
            text-align: center;
            font-size: 36px;
            margin: 20px 0;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #FFFFFF;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #3399CC;
            color: #FFFFFF;
            font-size: 18px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .bottoni {
            text-align: center;
            margin: 20px;
        }

        #pulsante {
            background-color: #FF7043;
            color: #FFF;
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        #pulsante:hover {
            background-color: #FF5722;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <h2 class="titolo">Classifica degli Studenti per Risposte Corrette</h2>
    <table>
        <thead>
            <tr>
                <th>Codice Studente</th>
                <th>Risposte Corrette</th>
                <th>Totale Risposte</th>
                <th>Percentuale Corrette</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        foreach ($classifica_risposte_esatte as $studente) {
            echo "<tr>";
            echo "<td>{$studente['codice_studente']}</td>";
            echo "<td>{$studente['risposte_corrette']}</td>";
            echo "<td>{$studente['totale_risposte']}</td>";
            echo "<td>{$studente['percentuale_corrette']}%</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    <div class="bottoni">
        <form action="pagewelcome.php" method="post">
            <input id="pulsante" type="submit" value="Torna alla Home Page">
        </form>
    </div>
</body>
</html>
