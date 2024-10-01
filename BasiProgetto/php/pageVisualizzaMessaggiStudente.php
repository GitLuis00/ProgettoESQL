<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>EFORM</title>
    <link href="stili.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Stili di base per il corpo della pagina */
        body {
            font-family: "Lato", sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Stili per la barra laterale */
        .sidenav {
            width: 260px;
            position: fixed;
            z-index: 1;
            top: 100px;
            left: 10px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow-x: hidden;
            padding: 8px 0;
        }

        .sidenav a {
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #3399CC;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .sidenav a:hover {
            color: #00796b;
            background: #e0f2f1;
        }

        /* Stili per il contenuto principale */
        .main {
            margin-left: 280px; /* Same width as the sidebar + left position in px */
            padding: 20px;
            width: calc(100% - 280px);
            box-sizing: border-box;
            color: #3399CC;
        }

        /* Stili per il titolo della pagina */
        .titolone {
            background-color: #3399CC;
            color: antiquewhite;
            text-align: center;
            height: 75px;
            line-height: 75px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 50px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 2;
        }

        /* Stili per il pulsante di logout */
        .esci {
            position: fixed;
            top: 20px;
            right: 20px;
        }

        #pulsante {
            background-color: #ff5722;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #pulsante:hover {
            background-color: #e64a19;
            transform: scale(1.05);
        }

        /* Stili per i messaggi */
        .message-container {
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
        }

        .message-header {
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: bold;
            color: #00796b;
        }

        .message-sender {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .message-body {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .message-footer {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
        }

        @media screen and (max-width: 768px) {
            .sidenav {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                left: 0;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            #pulsante {
                font-size: 14px;
            }
        }

        @media screen and (max-height: 450px) {
            .sidenav { padding-top: 15px; }
            .sidenav a { font-size: 18px; }
        }
    </style>
</head>
<body>
    <div class="titolone"><b>EFORM</b></div>

    <div class="sidenav">
        <!-- You can add sidebar links here -->
    </div>

    <div class="main">
        <div class="esci">
            <input id="pulsante" type='button' value='Logout' onclick="location.href='ESQL.php'"/>
        </div>

        <form action='pagewelcome.php' method="post">
            <?php 
            // Avvia la sessione
            session_start();
            @include 'config.php';
            
            // Assumo che $pdo sia già configurato per la connessione al database

            $risposte = $pdo->prepare("CALL `GetMessaggiDocente`();");

            // Esecuzione della query preparata
            $risposte->execute();

            // Fetch dei risultati
            while ($messaggio = $risposte->fetch(PDO::FETCH_ASSOC)) {
                // Stampa dinamica dei dati del messaggio utilizzando l'array associativo ottenuto
                echo '<div class="message-container">';
                echo '<div class="message-header">' . htmlspecialchars($messaggio["titolo"]) . '</div>';
                echo '<div class="message-sender">Tipo mittente: ' . htmlspecialchars($messaggio["tipo_mittente"]) . '</div>';
                echo '<div class="message-body">';
                echo '<p>' . htmlspecialchars($messaggio["testo"]) . '</p>';
                echo '</div>';
                echo '<div class="message-footer">';
                echo '<span>Data: ' . htmlspecialchars($messaggio["data_inserimento"]) . '</span>';
                echo '<span>Id test: ' . htmlspecialchars($messaggio["id_test"]) . '</span>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </form>
    </div>
</body>
</html>
