<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {
    header('Location: pageloginUtente.php');
    exit();
}

$stmt_test_completati = $pdo->query("CALL GetTestCompletati()");
$classifica_studenti_test_completati = $stmt_test_completati->fetchAll(PDO::FETCH_ASSOC);
$stmt_test_completati->closeCursor();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifica Test Completati</title>
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
    <div class="bottoni">
        <form action="pagewelcomeDocente.php" method="post">
            <input id="pulsante" type="submit" value="Torna alla Home Page Docente">
        </form>
    </div>
</body>
</html>