<?php
session_start();
@include 'config.php';
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}

if (isset($_GET['id_tabella'])) {
    $_SESSION['id_tabella_selezionata'] = $_GET['id_tabella'];
    echo $_SESSION['id_tabella_selezionata'];
}
$sqlNomeTabella = "SELECT nome FROM TabellaDiEsercizio WHERE id_tabella = ?";
    // Ottieni il nome della tabella utilizzando l'ID della tabella
    $stmtNomeTabella = $pdo->prepare($sqlNomeTabella);
    $stmtNomeTabella->execute([$_SESSION['id_tabella_selezionata']]);
    $nomeTabella = $stmtNomeTabella->fetchColumn();
    $_SESSION['nome_tabella_selezionata']= $nomeTabella;
    echo $nomeTabella;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_tabella_selezionata'])){
    $nome_attributo = $_POST['nome_attributo'];
    $tipo = $_POST['tipo'];
    $idTabella= $_SESSION['id_tabella_selezionata'];
    $sqlNomeTabella = "SELECT nome FROM TabellaDiEsercizio WHERE id_tabella = ?";
    
    // Ottieni il nome della tabella utilizzando l'ID della tabella
    $stmtNomeTabella = $pdo->prepare($sqlNomeTabella);
    $stmtNomeTabella->execute([$idTabella]);
    $nomeTabella = $stmtNomeTabella->fetchColumn();


    if ($nomeTabella) {
        // Inserisci il record in attributotabella
        $sql = "CALL AggiungiAttributo(?, ?, ?);";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome_attributo, $tipo, $idTabella]);

            // Aggiungi fisicamente l'attributo come nuova colonna nella tabella
            $sqlAlterTable = "ALTER TABLE $nomeTabella ADD COLUMN $nome_attributo $tipo";
           
            $pdo->exec($sqlAlterTable);

            echo "Record inserito con successo in attributi tabella e colonna aggiunta.";
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
    } else {
        echo "Nome tabella non trovato.";
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Attributo alla Tabella</title>
    
<STYLE>
/* Stili di base per il corpo della pagina */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 40px 20px;
}

/* Stili per l'intestazione principale */
#intestazione h1 {
    color: #333;
    animation: fadeIn 1s ease-out;
}

/* Sottotitolo con colore personalizzato */
#sottotitolo h2 {
    color: #F47D2B;
    margin-bottom: 20px;
}

/* Stili per i titoli delle sezioni */
.titolo {
    font-size: 20px;
    margin-bottom: 10px;
}

/* Personalizzazione dei bottoni */
.bottoni input[type='button'] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease-in-out, transform 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.bottoni input[type='button']:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

/* Responsività */
@media only screen and (max-width: 600px) {
    .bottoni input[type='button'] {
        padding: 10px;
        font-size: 14px;
        width: 100%;
        margin: 10px 0;
    }
    
    #intestazione h1, #sottotitolo h2 {
        text-align: center;
    }
}

/* Animazioni */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stili aggiuntivi per form */
form {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 5px;
}

input[type='text'], input[type='email'], input[type='submit'] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

input[type='submit'] {
    background-color: #28a745;
    color: white;
    cursor: pointer;
}

    /* Contenitore Flex per le colonne */
    .container {
        display: flex;
    }
    /* Stile per la colonna di sinistra (form) */
    .colonna-sinistra {
        flex: 1;
        padding-right: 20px; /* Spazio tra le colonne */
    }
    /* Stile per la colonna di destra (dati) */
    .colonna-destra {
        flex: 2; /* Assegna più spazio alla colonna dei dati */
    }

</style>
</head>
<body>

<div class="container">
    <div class="colonna-sinistra">

<h2>Aggiungi Nuovo Attributo alla Tabella di Esercizio</h2>

<form action="pageInserisciAttributo.php" method="post">
    Nome Attributo: <input type="text" name="nome_attributo" ><br>
    Tipo: <select name="tipo" >
        <option value="INT">INT</option>
        <option value="VARCHAR(255)">VARCHAR</option>
        <option value="DATE">DATE</option>
        <option value="BOOLEAN">BOOLEAN</option>
    </select><br>
<br>
    <!-- Invia l'ID della tabella di esercizio come parte del form -->
    <input type="hidden" name="id_tabella" value="<?= $_SESSION['id_tabella_selezionata'] ?>">
    
    <input type="submit" value="Aggiungi Attributo">
</form>

<?php
try {
    // Preparazione della query
    $sql = "SELECT id_tabella, nome FROM TabellaDiEsercizio";
    $stmt = $pdo->prepare($sql);

    // Esecuzione della query
    $stmt->execute();

    // Recupero dei risultati
    $tabelleDiEsercizio = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Errore durante il recupero delle tabelle: " . $e->getMessage();
}


    ?>
<form action="aggiungiVincolo.php" method="post">
    <label for="tabella_origine">Tabella Origine (FK):</label>
    <select name="tabella_origine" id="tabella_origine" >
        <?php foreach ($tabelleDiEsercizio as $tabella): ?>
            <option value="<?= htmlspecialchars($tabella['id_tabella']); ?>"><?= htmlspecialchars($tabella['nome']); ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="attributo_origine">Attributo Origine (FK):</label>
    <input type="text" name="attributo_origine" ><br>

    <label for="tabella_destinazione">Tabella Destinazione (PK):</label>
    <select name="tabella_destinazione" id="tabella_destinazione" >
        <?php foreach ($tabelleDiEsercizio as $tabella): ?>
            <option value="<?= htmlspecialchars($tabella['id_tabella']); ?>"><?= htmlspecialchars($tabella['nome']); ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="attributo_destinazione">Attributo Origine (FK):</label>
    <input type="text" name="attributo_destinazione" ><br>

    <input type="submit" value="Aggiungi Vincolo">
        </div>

    <div class="colonna-destra">

    </form>
   
</div>
<div class="colonna-destra">
<?php

if (isset($_SESSION['id_tabella_selezionata'])) {
    $idTabellaSelezionata = $_SESSION['id_tabella_selezionata'];
    
    try {
        $sql = "SELECT * FROM attributotabella WHERE id_tabella = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idTabellaSelezionata]);
        $risultati = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($risultati) {
            echo "<table>"; // Inizia la tabella
            // Intestazione della tabella
            echo "<tr><th>Nome Attributo</th><th>Tipo</th><th>Chiave Primaria</th></tr>";
            // Ciclo per ogni record e lo visualizzi in una riga di tabella
            foreach ($risultati as $riga) {
                echo "<tr><td>" . htmlspecialchars($riga['nome_attributo']) . "</td>";
                echo "<td>" . htmlspecialchars($riga['tipo']) . "</td>";
                echo "<td>" . ($riga['chiave_primaria'] ? "Sì" : "No") . "</td></tr>";
            }
            echo "</table>"; // Chiudi la tabella
        } else {
            echo "Nessun attributo trovato per la tabella selezionata.";
        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}

?>
</div>

<div class="bottone">
    <a href="inserisciDatiTabelle.php" class="button">Vai agli Inserimenti</a>
</div>
</body>
</html>





