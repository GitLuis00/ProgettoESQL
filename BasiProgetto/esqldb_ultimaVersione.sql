DROP DATABASE IF EXISTS esqldb_ultimaVersione;
CREATE DATABASE esqldb_ultimaVersione;
USE esqldb_ultimaVersione;

CREATE TABLE Utente (
    email VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(100),
    cognome VARCHAR(100),
    recapito_telefonico VARCHAR(20),
    tipo_utente ENUM('Docente', 'Studente') NOT NULL
);

CREATE TABLE Docente (
    email VARCHAR(255) PRIMARY KEY,
    nome_dipartimento VARCHAR(255),
    nome_corso VARCHAR(255),
    FOREIGN KEY (email) REFERENCES Utente(email)
);

CREATE TABLE Studente (
    email VARCHAR(255) PRIMARY KEY,
    anno_immatricolazione YEAR,
    codice_alfanumerico CHAR(16),
    FOREIGN KEY (email) REFERENCES Utente(email)
);

CREATE TABLE Test (
    id_test INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) UNIQUE NOT NULL,
    data_creazione DATE NOT NULL,
    foto VARCHAR(255), -- Percorso del file o URL dell'immagine
    email_docente VARCHAR(255),
    VisualizzaRisposte BOOLEAN NOT NULL,
    FOREIGN KEY (email_docente) REFERENCES Docente(email)
);

CREATE TABLE Quesito (
    id_quesito INT AUTO_INCREMENT PRIMARY KEY,
    id_test INT,
    numero_progressivo INT,
    livello_difficolta ENUM('Basso', 'Medio', 'Alto') NOT NULL,
    descrizione TEXT NOT NULL,
    num_risposte INT NOT NULL, -- Ridondanza concettuale, indica il numero di risposte per quesito
    categoria ENUM('Risposta Chiusa', 'Codice') NOT NULL,
    FOREIGN KEY (id_test) REFERENCES Test(id_test),
    UNIQUE (id_test, numero_progressivo)
);

CREATE TABLE OpzioneRisposta (
    id_opzione INT AUTO_INCREMENT PRIMARY KEY,
    id_quesito INT,
    numerazione INT,
    testo VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_quesito) REFERENCES Quesito(id_quesito),
    UNIQUE (id_quesito, numerazione)
);

CREATE TABLE SoluzioneCodice (
    id_soluzione INT AUTO_INCREMENT PRIMARY KEY,
    id_quesito INT,
    sketch_codice TEXT NOT NULL,
    FOREIGN KEY (id_quesito) REFERENCES Quesito(id_quesito)
);

CREATE TABLE TabellaDiEsercizio (
    id_tabella INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_creazione DATE NOT NULL,
    num_righe INT,
    email_docente VARCHAR(255),
    id_quesito INT,
    FOREIGN KEY (email_docente) REFERENCES Docente(email),
    FOREIGN KEY (id_quesito) REFERENCES Quesito(id_quesito)
);

CREATE TABLE AttributoTabella (
    id_attributo INT AUTO_INCREMENT PRIMARY KEY,
    id_tabella INT,
    nome_attributo VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    chiave_primaria BOOLEAN,
    FOREIGN KEY (id_tabella) REFERENCES TabellaDiEsercizio(id_tabella)
);

CREATE TABLE Risposta (
    id_risposta INT AUTO_INCREMENT PRIMARY KEY,
    id_quesito INT,
    email_studente VARCHAR(255),
    data_risposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    esito BOOLEAN, -- True se la risposta è corretta, False altrimenti. La logica specifica sarà gestita a livello applicativo.
    FOREIGN KEY (id_quesito) REFERENCES Quesito(id_quesito),
    FOREIGN KEY (email_studente) REFERENCES Studente(email)
);

CREATE TABLE RispostaChiusa (
    id_risposta INT PRIMARY KEY,
    id_opzione INT NOT NULL,
    FOREIGN KEY (id_risposta) REFERENCES Risposta(id_risposta),
    FOREIGN KEY (id_opzione) REFERENCES OpzioneRisposta(id_opzione)
);

CREATE TABLE RispostaAperta (
    id_risposta INT PRIMARY KEY,
    testo_risposta TEXT NOT NULL,
    FOREIGN KEY (id_risposta) REFERENCES Risposta(id_risposta)
);

CREATE TABLE Messaggio (
    id_messaggio INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL,
    testo TEXT NOT NULL,
    data_inserimento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_test INT,
    tipo_mittente ENUM('Docente', 'Studente') NOT NULL,
    FOREIGN KEY (id_test) REFERENCES Test(id_test)
);

CREATE TABLE MessaggioStudente (
    id_messaggio INT PRIMARY KEY,
    email_docente_destinatario VARCHAR(255),
    FOREIGN KEY (id_messaggio) REFERENCES Messaggio(id_messaggio),
    FOREIGN KEY (email_docente_destinatario) REFERENCES Docente(email)
);

CREATE TABLE CompletamentoTest (
    email_studente VARCHAR(255),
    id_test INT,
    data_inserimento_prima_risposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_inserimento_ultima_risposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    stato_del_completamento ENUM('Aperto', 'InCompletamento', 'Concluso') NOT NULL,
    PRIMARY KEY (email_studente, id_test),
    FOREIGN KEY (email_studente) REFERENCES Studente(email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_test) REFERENCES Test(id_test)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE SoluzioneRispostaChiusa (
    id_quesito INT,
    numerazioneOpzione INT,
    FOREIGN KEY (id_quesito) REFERENCES Quesito(id_quesito),
    PRIMARY KEY (id_quesito, numerazioneOpzione)
);


DELIMITER //
CREATE TRIGGER TriggerInCompletamento
AFTER INSERT ON Risposta
FOR EACH ROW
BEGIN
        UPDATE CompletamentoTest
        SET stato_del_completamento = 'InCompletamento',
            data_inserimento_ultima_risposta = CURRENT_TIMESTAMP
        WHERE email_studente = NEW.email_studente AND id_test = (SELECT id_test FROM Quesito WHERE id_quesito = NEW.id_quesito);

END; //
DELIMITER ;

DELIMITER //
CREATE TRIGGER TriggerConclusoPerTutti
AFTER UPDATE ON Test
FOR EACH ROW
BEGIN
    IF NEW.VisualizzaRisposte = TRUE THEN
        UPDATE CompletamentoTest
        SET stato_del_completamento = 'Concluso',
            data_inserimento_ultima_risposta = CURRENT_TIMESTAMP
        WHERE id_test = NEW.id_test;
    END IF;
END; //
DELIMITER ;


DELIMITER //
CREATE TRIGGER TriggerIncrementoRighe
AFTER INSERT ON AttributoTabella
FOR EACH ROW
BEGIN
    UPDATE TabellaDiEsercizio SET num_righe = num_righe + 1 WHERE id_tabella = NEW.id_tabella;
END; //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE VerificaCredenzialiStudente(IN emailParam VARCHAR(255), IN passwordParam VARCHAR(255))
BEGIN
    SELECT COUNT(*) AS counter
    FROM Utente
    WHERE email = emailParam AND password = passwordParam and tipo_utente = "studente";
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE VerificaCredenzialiDocente(IN emailParam VARCHAR(255), IN passwordParam VARCHAR(255))
BEGIN
    SELECT email AS email_docente, COUNT(*) AS counter
    FROM Utente
    WHERE email = emailParam AND password = passwordParam AND tipo_utente = 'Docente'
    GROUP BY email; -- Se ci sono più docenti con la stessa email e password, restituirà solo uno
END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE InserisciUtente(IN p_email VARCHAR(255), IN p_password VARCHAR(255), IN p_nome VARCHAR(100), IN p_cognome VARCHAR(100), IN p_telefono VARCHAR(20), IN p_tipo_utente ENUM('Docente', 'Studente'))
BEGIN
    INSERT INTO Utente (email, password, nome, cognome, recapito_telefonico, tipo_utente) VALUES (p_email, p_password, p_nome, p_cognome, p_telefono, p_tipo_utente);
    SELECT LAST_INSERT_ID() AS email;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE InserisciDocente(IN p_email_docente VARCHAR(255), IN p_nome_dipartimento VARCHAR(255), IN p_nome_corso VARCHAR(255))
BEGIN
    INSERT INTO Docente (email, nome_dipartimento, nome_corso) VALUES (p_email_docente, p_nome_dipartimento, p_nome_corso);
END //
DELIMITER ;

-- Procedura per registrare uno studente
DELIMITER //
CREATE PROCEDURE RegistraStudente(
    IN p_email VARCHAR(255), 
    IN p_password VARCHAR(255), 
    IN p_nome VARCHAR(100), 
    IN p_cognome VARCHAR(100), 
    IN p_telefono VARCHAR(20), 
    IN p_anno_immatricolazione YEAR
)
BEGIN
    -- Inserisce l'utente
    INSERT INTO Utente (email, password, nome, cognome, recapito_telefonico, tipo_utente) 
    VALUES (p_email, p_password, p_nome, p_cognome, p_telefono, 'Studente');
    
    -- Inserisce lo studente
    INSERT INTO Studente (email, anno_immatricolazione, codice_alfanumerico)
    VALUES (p_email, p_anno_immatricolazione, SUBSTRING(MD5(RAND()), 1, 16));
END //
DELIMITER ;

DELIMITER //

CREATE PROCEDURE GetMessaggiDocente()
BEGIN
    SELECT id_messaggio, titolo, testo, data_inserimento, id_test, tipo_mittente
    FROM Messaggio
    WHERE tipo_mittente="docente"
    ORDER BY data_inserimento DESC;
END //

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE GetMessaggiStudente(
    IN email_docente VARCHAR(255)
)
BEGIN
    SELECT m.id_messaggio, m.titolo, m.testo, m.data_inserimento, m.id_test, m.tipo_mittente, ms.email_docente_destinatario
    FROM Messaggio m
    JOIN MessaggioStudente ms ON m.id_messaggio = ms.id_messaggio
    WHERE m.tipo_mittente = 'Studente'
      AND ms.email_docente_destinatario = email_docente
    ORDER BY m.data_inserimento DESC;
END$$

DELIMITER ;

DELIMITER //

CREATE PROCEDURE InserisciMessaggio(
    IN titoloMessaggio VARCHAR(255),
    IN testoMessaggio TEXT,
    IN idTest INT
)
BEGIN
    INSERT INTO Messaggio (titolo, testo, tipo_mittente, id_test)
    VALUES (titoloMessaggio, testoMessaggio, 'Docente', idTest);
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE InserisciMessaggioStudente(
    IN titoloMessaggio VARCHAR(255),
    IN testoMessaggio TEXT,
    IN idTest INT,
    IN emailDocenteDestinario VARCHAR(255)
)
BEGIN
    -- Inserisce il nuovo messaggio nella tabella Messaggi, 
    -- impostando il tipo_mittente su 'Studente'
    INSERT INTO Messaggio (titolo, testo, id_test, tipo_mittente)
    VALUES (titoloMessaggio, testoMessaggio, idTest, 'Studente');

    -- Ottiene l'ID del messaggio appena inserito
    SET @ultimoIdMessaggio = LAST_INSERT_ID();

    -- Inserisce un record nella tabella MessaggiStudenti 
    -- con l'ID del messaggio e l'ID del docente destinatario
    INSERT INTO MessaggioStudente (id_messaggio, email_docente_destinatario)
    VALUES (@ultimoIdMessaggio, emailDocenteDestinario);
END //

DELIMITER //

-- Inserimento di un utente (docente)
INSERT INTO Utente (email, password, nome, cognome, recapito_telefonico, tipo_utente) 
VALUES ('docente@example.com', 'password123', 'Mario', 'Rossi', '1234567890', 'Docente');

-- Inserimento di un docente associato all'utente appena inserito
INSERT INTO Docente (email, nome_dipartimento, nome_corso) 
VALUES ('docente@example.com', 'Dipartimento di Informatica', 'Programmazione');

-- Inserimento di uno studente
INSERT INTO Utente (email, password, nome, cognome, recapito_telefonico, tipo_utente) 
VALUES ('studente@example.com', 'password456', 'Giulia', 'Bianchi', '0987654321', 'Studente');

-- Generazione di un codice alfanumerico univoco per lo studente
SET @codice_alfanumerico_studente = SUBSTRING(MD5(RAND()), 1, 16);

-- Inserimento dello studente
INSERT INTO Studente (email_studente, anno_immatricolazione, codice_alfanumerico) 
VALUES ('studente@example.com', 2023, @codice_alfanumerico_studente);

-- Inserimento di un test
INSERT INTO Test (titolo, data_creazione, foto, email_docente, VisualizzaRisposte) 
VALUES ('Test di Programmazione', '2024-03-25', 'path/immagine.jpg', 'docente@example.com', TRUE);

-- Inserimento di un messaggio da parte di un docente
INSERT INTO Messaggio (titolo, testo, tipo_mittente, id_test) 
VALUES ('Importante', 'Il compito è stato aggiornato.', 'Docente', (SELECT id_test FROM Test WHERE titolo = 'Test di Programmazione'));

-- Inserimento di un messaggio da parte di uno studente a un docente specifico
INSERT INTO Messaggio (titolo, testo, tipo_mittente, id_test) 
VALUES ('Domanda sul compito', 'Non ho capito come risolvere la terza domanda.', 'Studente', (SELECT id_test FROM Test WHERE titolo = 'Test di Programmazione'));

-- Recupero dell'ID dell'ultimo messaggio inserito
SET @id_messaggio = LAST_INSERT_ID();

-- Inserimento dell'associazione tra il messaggio e il docente destinatario
INSERT INTO MessaggioStudente (id_messaggio, email_docente_destinatario) 
VALUES (@id_messaggio, 'docente@example.com');  

DELIMITER ;

DELIMITER //
CREATE PROCEDURE InserisciTest(
    IN p_titolo VARCHAR(255),
    IN p_data_creazione DATE,
    IN p_foto VARCHAR(255),
    IN p_email_docente VARCHAR(255),
    IN p_VisualizzaRisposte BOOLEAN
)
BEGIN
    DECLARE error_message TEXT;

    -- Gestione degli errori
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_message = MESSAGE_TEXT;
        SELECT CONCAT('Error: ', error_message) AS error;
    END;

    INSERT INTO Test (titolo, data_creazione, foto, email_docente, VisualizzaRisposte)
    VALUES (p_titolo, p_data_creazione, p_foto, p_email_docente, p_VisualizzaRisposte);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE GetTestsByDocenteId(IN docente_email VARCHAR(255))
BEGIN
    SELECT * FROM Test WHERE email_docente = docente_email;
END//

DELIMITER ;

DELIMITER //

CREATE PROCEDURE UpdateTestVisualizzaRisposte(IN test_id INT, IN visualizza_risposte INT)
BEGIN
    UPDATE test SET VisualizzaRisposte = visualizza_risposte WHERE id_test = test_id;
END //

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE GetTestCompletati()
BEGIN
    SELECT S.codice_alfanumerico AS codice_studente, 
           COUNT(C.id_test) AS numero_test_completati
    FROM Studente S
    LEFT JOIN CompletamentoTest C ON S.email = C.email_studente AND C.stato_del_completamento = 'Concluso'
    GROUP BY S.email
    ORDER BY numero_test_completati DESC;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE InserisciOpzioneRisposta(
    IN _id_quesito INT,
    IN _numerazione INT,
    IN _testo TEXT
)
BEGIN
    INSERT INTO OpzioneRisposta (id_quesito, numerazione, testo) VALUES (_id_quesito, _numerazione, _testo);
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE InserisciSoluzioneCodice(
    IN idQuesito INT,
    IN sketchCodice TEXT
)
BEGIN
    INSERT INTO SoluzioneCodice (id_quesito, sketch_codice) VALUES (idQuesito, sketchCodice);
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `OttieniRisposteStudente`(
    IN _email_studente VARCHAR(255)
)
BEGIN
    SELECT Test.id_test, 
           Quesito.numero_progressivo, 
           Quesito.descrizione AS descrizione_quesito, 
           Risposta.esito, 
           Risposta.data_risposta
    FROM Risposta
    JOIN Quesito ON Risposta.id_quesito = Quesito.id_quesito
    JOIN Test ON Quesito.id_test = Test.id_test
    WHERE Risposta.email_studente = _email_studente AND Test.VisualizzaRisposte = 1
    ORDER BY Test.id_test, Quesito.numero_progressivo;
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE InserisciNellaTabellaDiEsercizio(
    IN _nome VARCHAR(255),
    IN _data_creazione DATE,
    IN _num_righe INT,
    IN _email_docente VARCHAR(255),
    IN _id_quesito INT
)
BEGIN
    -- Inserisci il record nella tabella
    INSERT INTO TabellaDiEsercizio (nome, data_creazione, num_righe, email_docente, id_quesito) 
    VALUES (_nome, _data_creazione, _num_righe, _email_docente, _id_quesito);
    
    -- Restituisci l'ID dell'ultima riga inserita
    SELECT LAST_INSERT_ID() AS id_tabella;
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE AggiungiAttributoTabella(
    IN _id_tabella INT,
    IN _nome_attributo VARCHAR(255),
    IN _tipo VARCHAR(255)
)
BEGIN
    INSERT INTO AttributoTabella (id_tabella, nome_attributo, tipo, chiave_primaria) 
    VALUES (_id_tabella, _nome_attributo, _tipo, 1);
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE GetDettagliTabellaEAttributo()
BEGIN
    SELECT 
        t.id_tabella, 
        t.nome AS nome_tabella, 
        t.data_creazione, 
        t.num_righe, 
        t.email_docente, 
        t.id_quesito, 
        a.id_attributo, 
        a.nome_attributo, 
        a.tipo, 
        a.chiave_primaria
    FROM 
        TabellaDiEsercizio t, AttributoTabella a
    WHERE 
        t.id_tabella = a.id_tabella;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE AggiungiAttributo(
    IN `_nome_attributo` VARCHAR(255), 
    IN `_tipo` VARCHAR(255), 
    IN `_id_tabella` INT
)
BEGIN
    INSERT INTO `attributotabella` (`nome_attributo`, `tipo`, `chiave_primaria`, `id_tabella`) 
    VALUES (_nome_attributo, _tipo, 0, _id_tabella);
END$$

DELIMITER ;

CREATE VIEW VistaTestCompletati AS
SELECT S.email AS codice_studente, 
       COUNT(C.id_test) AS numero_test_completati
FROM Studente S
LEFT JOIN CompletamentoTest C ON S.email = C.email_studente AND C.stato_del_completamento = 'Concluso'
GROUP BY S.email;


CREATE VIEW ClassificaRisposteEsatte AS
SELECT
    S.codice_alfanumerico AS codice_studente,
    COUNT(CASE WHEN R.esito = TRUE THEN 1 END) AS risposte_corrette,
    COUNT(R.id_risposta) AS totale_risposte,
    (COUNT(CASE WHEN R.esito = TRUE THEN 1 END) * 100.0 / COUNT(R.id_risposta)) AS percentuale_corrette
FROM
    Studente S
JOIN
    Risposta R ON S.email = R.email_studente
GROUP BY
    S.codice_alfanumerico
HAVING
    COUNT(R.id_risposta) > 0
ORDER BY
    percentuale_corrette DESC, totale_risposte DESC;
    

    CREATE VIEW ClassificaQuesiti AS
SELECT
    Q.id_quesito,
    COUNT(R.id_risposta) AS numero_risposte
FROM
    Quesito Q
LEFT JOIN
    Risposta R ON Q.id_quesito = R.id_quesito
GROUP BY
    Q.id_quesito
ORDER BY
    numero_risposte DESC;
    
    
    DELIMITER //
    
DELIMITER $$

CREATE PROCEDURE GetTestByStudenteId(
    IN email_studente_param VARCHAR(255)
)
BEGIN
    SELECT Test.*, Docente.email, CompletamentoTest.stato_del_completamento 
    FROM Test 
    JOIN Docente ON Test.email_docente = Docente.email 
    LEFT JOIN CompletamentoTest ON Test.id_test = CompletamentoTest.id_test 
        AND CompletamentoTest.email_studente = email_studente_param
    ORDER BY Test.email_docente, Test.data_creazione ASC;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE AggiornaNumRigheTabellaEsercizio(nome_tabella VARCHAR(255))
BEGIN
    -- Variabile per costruire la query dinamica
    SET @queryDinamica = CONCAT('SELECT COUNT(*) INTO @numRighe FROM ', nome_tabella);

    -- Esegue la query per ottenere il conteggio delle righe
    PREPARE stmt FROM @queryDinamica;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Aggiorna il campo num_righe in TabellaDiEsercizio
    SET @queryAggiornamento = CONCAT('UPDATE TabellaDiEsercizio SET num_righe = @numRighe WHERE nome = ''', nome_tabella, '''');
    PREPARE stmtAggiornamento FROM @queryAggiornamento;
    EXECUTE stmtAggiornamento;
    DEALLOCATE PREPARE stmtAggiornamento;
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER verificaRispostaCorrettaChiusa
AFTER INSERT ON RispostaChiusa
FOR EACH ROW
BEGIN
    DECLARE soluzioneCorretta INT;

    -- Verifica se l'id_opzione inserito corrisponde alla soluzione corretta per l'id_quesito associato
    SELECT COUNT(*) INTO soluzioneCorretta
    FROM SoluzioneRispostaChiusa
    WHERE id_quesito = (SELECT id_quesito FROM Risposta WHERE id_risposta = NEW.id_risposta)
          AND numerazioneOpzione = (SELECT numerazione FROM OpzioneRisposta WHERE id_opzione = NEW.id_opzione);

    -- Se soluzioneCorretta > 0, allora la risposta è corretta (esito = TRUE), altrimenti è incorretta (esito = FALSE)
    UPDATE Risposta
    SET esito = IF(soluzioneCorretta > 0, TRUE, FALSE)
    WHERE id_risposta = NEW.id_risposta;
END$$

DELIMITER ;

DELIMITER //
CREATE TRIGGER TriggerConclusoStudente
AFTER INSERT ON Risposta
FOR EACH ROW
BEGIN
    -- Controlla se il numero di quesiti unici nel test è uguale al numero di quesiti a cui lo studente ha risposto correttamente almeno una volta.
    IF (SELECT COUNT(DISTINCT id_quesito) FROM Quesito WHERE id_test = (SELECT id_test FROM Quesito WHERE id_quesito = NEW.id_quesito)) =
       (SELECT COUNT(DISTINCT id_quesito) FROM Risposta WHERE email_studente = NEW.email_studente AND esito = TRUE AND id_quesito IN (SELECT id_quesito FROM Quesito WHERE id_test = (SELECT id_test FROM Quesito WHERE id_quesito = NEW.id_quesito)))
    THEN
        -- Aggiorna lo stato del completamento del test a 'Concluso'.
        UPDATE CompletamentoTest
        SET stato_del_completamento = 'Concluso',
            data_inserimento_ultima_risposta = CURRENT_TIMESTAMP
        WHERE email_studente = NEW.email_studente AND id_test = (SELECT id_test FROM Quesito WHERE id_quesito = NEW.id_quesito);
    END IF;
END; //
DELIMITER ;
