<?php
session_start();
unset($_SESSION['authorized']);
?>

<html>
<head>
    <title>ESQL</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="stili.css" rel="stylesheet">

    <style>
        body {
            background-color: #FFEBCC;
            font-family: 'Comic Neue', cursive;
            text-align: center;
            padding: 50px;
        }
        #intestazione {
            background-color: #FFB84D;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #FFFFFF;
            font-size: 50px;
            margin: 0;
        }
        #sottotitolo h2 {
            color: #F47D2B;
            font-size: 30px;
        }
        .titolo {
            color: #FF7043;
            font-size: 24px;
            margin: 20px 0;
        }
        .bottoni {
            margin-top: 20px;
        }
        #pulsante {
            background-color: #FF7043;
            color: #FFF;
            font-size: 18px;
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            margin: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        #pulsante:hover {
            background-color: #FF5722;
        }
        .bottoni i {
            margin-right: 10px;
        }
    </style>
</head>

<body>
<form action='EFORM.php' method="post">
    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <div id="sottotitolo">
        <h2 class="titolo">STUDENTI o DOCENTI?</h2>
    </div>

    <div class="titolo">
        <p>Vuoi Loggarti come Studente o come Docente?</p>
    </div>
    <div class="bottoni">
        <button id="pulsante" type='button' onclick="location.href='pageloginStudente.php'">
            <i class="fas fa-user-graduate"></i> Login Studente
        </button>
        <button id="pulsante" type='button' onclick="location.href='pageloginDocente.php'">
            <i class="fas fa-chalkboard-teacher"></i> Login Docente
        </button>
    </div>

    <br><br>

    <div class="titolo">
        <p>Non sei registrato?</p>
    </div>
    <div class="bottoni">
        <button id="pulsante" type='button' onclick="location.href='pageregister.php'">
            <i class="fas fa-user-plus"></i> Registrazione Studente
        </button>
        <button id="pulsante" type='button' onclick="location.href='pageregisterDocente.php'">
            <i class="fas fa-user-plus"></i> Registrazione Docente
        </button>
    </div>
</form>
</body>
</html>
