<?php
session_start(); // Avvia la sessione

// Controlla se la variabile di sessione è impostata
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {
    // Ridirezionamento alla pagina di login
    header('Location: ESQL.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ESQL - Dashboard Studente</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="stili.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Comic Neue', cursive;
            background-color: #FFEBCC;
            margin: 0;
            padding: 0;
        }

        .titolone {
            color: #FFFFFF;
            background-color: #3399CC;
            text-align: center;
            height: 75px;
            font-family: 'Courier New', Courier, monospace;
            line-height: 75px;
            font-size: 50px;
            margin: 0;
        }

        .sidenav {
            width: 260px;
            position: fixed;
            top: 100px;
            left: 0;
            background: #F1F1F1;
            padding: 20px;
            border-right: 2px solid #ddd;
        }

        .sidenav a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #3399CC;
            margin-bottom: 10px;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidenav a:hover {
            color: #FFFFFF;
            background-color: #3399CC;
        }

        .main {
            margin-left: 300px; /* Same width as the sidebar + padding */
            padding: 20px;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: 50px;
        }

        .welcome-message {
            margin-bottom: 30px;
        }

        .actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .welcome-message h2 {
            color: green;
            font-size: 36px; /* Aumenta la dimensione del testo */
        }

        .welcome-message p {
            font-size: 24px; /* Aumenta la dimensione del testo */
            margin: 10px 0;
        }

        .actions p {
            font-size: 20px; /* Dimensione del testo per le azioni */
            margin: 0;
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

        .esci {
            position: fixed;
            top: 100px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="titolone"><b>ESQL</b></div>

    <div class="sidenav">
        <a href='ClassificaTestCompletati.php'>Classifica Studenti per Test</a>
        <a href='classificaRisposteEsatte.php'>Classifica Studenti per Risposte</a>
        <a href='classificaQuesiti.php'>Classifica Quesiti</a>
    </div>

    <div class="esci">
        <input id="pulsante" type='button' value='Logout' onclick="location.href='pageloginStudente.php'"/>
    </div>

    <div class="main">
        <div class="content">
            <div class="welcome-message">
                <h2>STUDENTE</h2>
                <p>Benvenuto, <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>!</p>
            </div>
            <div class="actions">
                <p>Per <b style="color:green">visualizzare i test disponibili</b>:</p>
                <input id="pulsante" type='button' value='->' onclick="location.href='visulizzatestStudente.php'"/>
                <p>Per visualizzare gli <b style="color:green">esiti</b>:</p>
                <input id="pulsante" type='button' value='->' onclick="location.href='esiti.php'"/>
                <p>Per visualizzare i <b style="color:green">messaggi ricevuti</b>:</p>
                <input id="pulsante" type='button' value='->' onclick="location.href='pageVisualizzaMessaggiStudente.php'"/>
            </div>
        </div>
    </div>
</body>
</html>
