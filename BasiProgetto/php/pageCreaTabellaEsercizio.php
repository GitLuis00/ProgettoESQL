<?php
@include 'config.php';
session_start();
unset($_SESSION['authorized']);

// Verifica che l'email del docente sia presente nella sessione
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    echo "Errore: Nessuna email del docente presente nella sessione.";
    exit;
}
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}

$emailDocente = $_SESSION['email'];

// Recupera l'ID del quesito passato come parametro GET
$id_quesito = isset($_GET['id_quesito']) ? intval($_GET['id_quesito']) : null;

if (!$id_quesito) {
    echo "ID Quesito non specificato.";
    exit;
}

$sql = "CALL GetDettagliTabellaEAttributo();";

// Prepara ed esegue la query
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Recupera i risultati come array associativo
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se ci sono risultati, chiudi il cursore
$stmt->closeCursor();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeTabella'])) {
    // Preparazione dei dati da inserire per TabelleDiEsercizio
    $nomeTabella = $_POST['nomeTabella'];
    $dataCreazione = $_POST['dataCreazione'];
    $numRighe = $_POST['numRighe'];
    $primaryKey = $_POST['primarykey'];
    $tipoPrimaryKey = $_POST['tipoPrimaryKey'];

    // Esegui la stored procedure e recupera l'ID della tabella
    $sql = "CALL InserisciNellaTabellaDiEsercizio(?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nomeTabella, $dataCreazione, $numRighe, $emailDocente, $id_quesito]);
        
        // Recupera l'ID della tabella inserita
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $idTabellaInserita = $result['id_tabella'] ?? 0;
        
        if ($idTabellaInserita > 0) {
            echo "Record inserito con successo in TabelleDiEsercizio. ID Tabella Inserita: " . $idTabellaInserita;
        } else {
            echo "Errore: l'ID della tabella non è stato trovato.";
        }

        // Chiudi il cursore del result set precedente
        $stmt->closeCursor();

        // Ora puoi inserire l'attributo nella tabella AttributiTabella
        $nomeAttributo = $primaryKey; // Nome della chiave primaria inserita dall'utente
        $tipoAttributo = $tipoPrimaryKey; // Tipo della chiave primaria inserita dall'utente

        if (empty($nomeAttributo) || empty($tipoAttributo)) {
            echo "Errore: Nome o tipo dell'attributo non possono essere vuoti.";
        } else {
            $sql2 = "CALL AggiungiAttributoTabella(?, ?, ?)";
            try {
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute([$idTabellaInserita, $nomeAttributo, $tipoAttributo]);
                echo "Attributo inserito con successo in AttributiTabella.";
            } catch (PDOException $e) {
                echo "Errore nell'inserimento dell'attributo in AttributiTabella: " . $e->getMessage();
            }
            
            // Chiudi il cursore del result set precedente, se necessario
            $stmt2->closeCursor();
        }

        // Creazione della tabella fisica nel database
        $sqlCreateTable = "CREATE TABLE " . $nomeTabella . " (
            " . $primaryKey . " " . $tipoPrimaryKey . " PRIMARY KEY
            -- qui puoi aggiungere altre definizioni di colonne se necessario
        )";

        try {
            $pdo->exec($sqlCreateTable);
            echo "Tabella '$nomeTabella' creata con successo.";
        } catch (PDOException $e) {
            echo "Errore nella creazione della tabella '$nomeTabella': " . $e->getMessage();
        }
    } catch (PDOException $e) {
        echo "Errore generale: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Tabella di Esercizio</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="stilic.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Gestione Tabelle di Esercizio</h1>

        <section class="table-section">
            <h2>Elenco Tabelle</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Tabella</th>
                        <th>Nome Tabella</th>
                        <th>Data Creazione</th>
                        <th>Numero Righe</th>
                        <th>Email Docente</th>
                        <th>ID Quesito</th>
                        <th>ID Attributo</th>
                        <th>Nome Attributo</th>
                        <th>Tipo</th>
                        <th>Chiave Primaria</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_tabella']) ?></td>
                                <td><a href="pageInserisciAttributo.php?id_tabella=<?= $row['id_tabella'] ?>" class="link"><?= htmlspecialchars($row['nome_tabella']) ?></a></td>
                                <td><?= htmlspecialchars($row['data_creazione']) ?></td>
                                <td><?= htmlspecialchars($row['num_righe']) ?></td>
                                <td><?= htmlspecialchars($row['email_docente']) ?></td>
                                <td><?= htmlspecialchars($row['id_quesito']) ?></td>
                                <td><?= htmlspecialchars($row['id_attributo']) ?></td>
                                <td><?= htmlspecialchars($row['nome_attributo']) ?></td>
                                <td><?= htmlspecialchars($row['tipo']) ?></td>
                                <td><?= htmlspecialchars($row['chiave_primaria'] ? 'Sì' : 'No') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">Nessun dato trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="form-section">
            <h2>Aggiungi Nuova Tabella</h2>
            <form action="" method="post">
                <div class="form-group">
                    <label for="nomeTabella">Nome Tabella:</label>
                    <input type="text" id="nomeTabella" name="nomeTabella" required>
                </div>
                <div class="form-group">
                    <label for="dataCreazione">Data Creazione (YYYY-MM-DD):</label>
                    <input type="date" id="dataCreazione" name="dataCreazione" required>
                </div>
                <div class="form-group">
                    <label for="numRighe">Numero di Righe:</label>
                    <input type="number" id="numRighe" name="numRighe" required>
                </div>
                <div class="form-group">
                    <label for="primarykey">Nome Chiave Primaria:</label>
                    <input type="text" id="primarykey" name="primarykey" required>
                </div>
                <div class="form-group">
                    <label for="tipoPrimaryKey">Tipo Chiave Primaria:</label>
                    <select id="tipoPrimaryKey" name="tipoPrimaryKey" required>
                        <option value="INT">INT</option>
                        <option value="BIGINT">BIGINT</option>
                        <option value="VARCHAR(255)">VARCHAR(255)</option>
                        <option value="CHAR(255)">CHAR(255)</option>
                        <option value="DATE">DATE</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="TIMESTAMP">TIMESTAMP</option>
                        <option value="TIME">TIME</option>
                        <option value="DECIMAL(10,2)">DECIMAL(10,2)</option>
                        <option value="FLOAT">FLOAT</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Crea Tabella</button>
            </form>
        </section>
    </div>
</body>
</html>
