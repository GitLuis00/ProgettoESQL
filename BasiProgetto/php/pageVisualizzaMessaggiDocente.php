<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFORM</title>
    
<style>
body {
  font-family: "Lato", sans-serif;
}

.sidenav {
  width: 260px;
  position: fixed;
  z-index: 1;
  top: 100px;
  left: 10px;
  background: #eee;
  overflow-x: hidden;
  padding: 8px 0;
}

.sidenav a {
  padding: 6px 8px 6px 16px;
  text-decoration: none;
  font-size: 25px;
  color: #3399CC;
  display: block;
}

.sidenav a:hover {
  color: green;
}

.main {
  margin-left: 300px; /* Same width as the sidebar + left position in px */
  font-size: 20px; /* Increased text to enable scrolling */
  padding: 0px 10px;
  color: #3399CC;
}

.titolone{
color:antiquewhite;
background-color:#3399CC;
text-align:center;
height:75px;
font-family: 'Courier New', Courier, monospace;
line-height:75px;
font-size:50px;
}

.esci{
    z-index: 1;
    width: 250px;
  top: 100px;
  right: 1px;
  margin-left:1300px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
.message-container {
    padding: 20px;
    margin: 20px auto;
    width: 80%;
    max-width: 600px;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
  }

  .message-header {
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: bold;
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







</style>

</head>
<body>
<div class="titolone"><b>EFORM</b></div>
<form action='pagewelcome.php' method="post">

<div class="esci">
    <input id="pulsante" type='button' value ='Logout' onclick="location.href='ESQL.php'"/>
</div>

<div class="main">
    <?php 
    // Avvia la sessione
    session_start();
    @include 'config.php';

    // ID del docente - esempio statico, nella pratica potrebbe provenire da una variabile di sessione o da un input
    $emailDocente = $_SESSION['email']; // Assumendo che l'ID del docente sia 1 per l'esempio

    $risposte = $pdo->prepare("CALL GetMessaggiStudente(:emailDocente);");
    $risposte->bindParam(':emailDocente', $emailDocente, PDO::PARAM_STR);

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
</div>  
</form>
</body>
</html>
