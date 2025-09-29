<?php
header('Content-Type: application/json');
require_once '../database/db.php'; //connessione al db

function sanitize_input($data) {    //per evitare sql injection
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_sala_data($codice, $nome, $tema, $mq) {   //gestione dell'input
    $errors = [];
    if (empty($codice)) {
        $errors[] = "Il codice sala è obbligatorio";
    } elseif (!preg_match('/^S\d{3}$/', $codice)) {
        $errors[] = "Il codice deve essere nel formato S001, S002, ecc.";
    }
    if (empty($nome)) {
        $errors[] = "Il nome della sala è obbligatorio";
    } elseif (strlen($nome) > 100) {
        $errors[] = "Il nome non può superare i 100 caratteri";
    }
    if (empty($tema)) {
        $errors[] = "Il tema è obbligatorio";
    }
    if (!is_numeric($mq) || $mq <= 0 || $mq > 1000) {
        $errors[] = "I metri quadrati devono essere un numero positivo (max 1000)";
    }
    return $errors;
}

function generate_next_codice($conn) {
    // Trova il più piccolo codice disponibile nel formato SNNN riempiendo i buchi
    $sql = "SELECT CAST(SUBSTRING(codice, 2) AS UNSIGNED) AS n FROM Sala ORDER BY n";
    $result = $conn->query($sql);
    $expected = 1;
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $n = (int)$row['n'];
            if ($n > $expected) {
                // Trovato buco: expected è il primo codice libero
                break;
            }
            if ($n === $expected) {
                $expected++;
            }
        }
        $result->free();
    }
    return 'S' . str_pad($expected, 3, '0', STR_PAD_LEFT);
}

$response = ['success' => false, 'message' => '', 'data' => null];
$action = $_POST['action'] ?? '';

switch ($action) {  //operazioni CRUD 
    case 'create':
        $codice = sanitize_input($_POST['codice'] ?? '');
        $nome = sanitize_input($_POST['nome'] ?? '');
        $tema = sanitize_input($_POST['tema'] ?? '');
        $mq = (int)($_POST['mq'] ?? 0);

        // Se non viene fornito un codice, generane uno automaticamente
        if (empty($codice)) {
            $codice = generate_next_codice($conn);
        }

        $validation_errors = validate_sala_data($codice, $nome, $tema, $mq);
        if (!empty($validation_errors)) {
            $response['message'] = implode(', ', $validation_errors);
            break;
        }

        //controlla duplicato codice
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Sala WHERE codice = ?");
        $stmt->bind_param('s', $codice);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $response['message'] = "Il codice sala '$codice' esiste già";
            break;
        }

        //inserisci sala
        $stmt = $conn->prepare("INSERT INTO Sala (codice, nome, tema, mq) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $codice, $nome, $tema, $mq);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Sala '$codice' aggiunta con successo";
            $response['data'] = ['codice' => $codice, 'nome' => $nome, 'tema' => $tema, 'mq' => $mq];
        } else {
            $response['message'] = "Errore durante l'inserimento della sala";
        }
        $stmt->close();
        break;

    case 'read':
        $where = [];
        $params = [];
        $types = '';

        if (!empty($_POST['codice'])) {
            $where[] = "codice LIKE ?";
            $params[] = '%' . sanitize_input($_POST['codice']) . '%';
            $types .= 's';
        }
        if (!empty($_POST['nome'])) {
            $where[] = "nome LIKE ?";
            $params[] = '%' . sanitize_input($_POST['nome']) . '%';
            $types .= 's';
        }
        if (!empty($_POST['tema'])) {
            $where[] = "tema = ?";
            $params[] = sanitize_input($_POST['tema']);
            $types .= 's';
        }
        if (!empty($_POST['mq_min']) && is_numeric($_POST['mq_min'])) {
            $where[] = "mq >= ?";
            $params[] = (int)$_POST['mq_min'];
            $types .= 'i';
        }
        if (!empty($_POST['mq_max']) && is_numeric($_POST['mq_max'])) {
            $where[] = "mq <= ?";
            $params[] = (int)$_POST['mq_max'];
            $types .= 'i';
        }

        //$sql = "SELECT codice, nome, tema, mq FROM Sala";

        $sql = "SELECT C.codice, C.nome, C.tema, C.mq, COUNT(A.sala) AS nFasceOrarie
                FROM Sala AS C
                LEFT JOIN FasciaOraria AS A ON C.codice = A.sala";

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " GROUP BY C.codice, C.nome, C.tema, C.mq ORDER BY C.codice";

        $stmt = $conn->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $sale = [];
        while ($row = $result->fetch_assoc()) {
            $sale[] = $row;
        }
        $stmt->close();

        $response['success'] = true;
        $response['data'] = $sale;
        $response['message'] = count($sale) . " sale trovate";
        break;

    case 'update':
        $codice = sanitize_input($_POST['codice'] ?? '');
        $nome = sanitize_input($_POST['nome'] ?? '');
        $tema = sanitize_input($_POST['tema'] ?? '');
        $mq = (int)($_POST['mq'] ?? 0);

        if (empty($codice)) {
            $response['message'] = "Codice sala mancante per l'aggiornamento";
            break;
        }

        $validation_errors = validate_sala_data($codice, $nome, $tema, $mq);
        if (!empty($validation_errors)) {
            $response['message'] = implode(', ', $validation_errors);
            break;
        }

        // Aggiorna sala
        $stmt = $conn->prepare("UPDATE Sala SET nome = ?, tema = ?, mq = ? WHERE codice = ?");
        $stmt->bind_param('ssis', $nome, $tema, $mq, $codice);

        if ($stmt->execute()) {
            // Chiudi lo statement di update e rileggi i dati persistiti dal DB
            $stmt->close();
            $stmt = $conn->prepare("SELECT codice, nome, tema, mq FROM Sala WHERE codice = ?");
            $stmt->bind_param('s', $codice);
            $stmt->execute();
            $result = $stmt->get_result();
            $saved = $result->fetch_assoc();
            $stmt->close();

            $response['success'] = true;
            $response['message'] = "Sala '$codice' aggiornata con successo";
            $response['data'] = $saved ?: ['codice' => $codice, 'nome' => $nome, 'tema' => $tema, 'mq' => $mq];
        } else {
            $response['message'] = "Errore durante l'aggiornamento della sala";
            $stmt->close();
        }
        break;

    case 'delete':
        $codice = sanitize_input($_POST['codice'] ?? '');
        if (empty($codice)) {
            $response['message'] = "Codice sala mancante per l'eliminazione";
            break;
        }

        // Controlla esistenza sala
        $stmt = $conn->prepare("SELECT nome FROM Sala WHERE codice = ?");
        $stmt->bind_param('s', $codice);
        $stmt->execute();
        $stmt->bind_result($nome);
        if (!$stmt->fetch()) {
            $response['message'] = "Sala con codice '$codice' non trovata";
            $stmt->close();
            break;
        }
        $stmt->close();

        // Elimina sala
        $stmt = $conn->prepare("DELETE FROM Sala WHERE codice = ?");
        $stmt->bind_param('s', $codice);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Sala '$codice' - '$nome' eliminata con successo";
        } else {
            $response['message'] = "Errore durante l'eliminazione della sala";
        }
        $stmt->close();
        break;

    case 'get_single':
        $codice = sanitize_input($_POST['codice'] ?? '');
        if (empty($codice)) {
            $response['message'] = "Codice sala mancante";
            break;
        }
        $stmt = $conn->prepare("SELECT codice, nome, tema, mq FROM Sala WHERE codice = ?");
        $stmt->bind_param('s', $codice);
        $stmt->execute();
        $result = $stmt->get_result();
        $sala = $result->fetch_assoc();
        $stmt->close();
        if ($sala) {
            $response['success'] = true;
            $response['data'] = $sala;
            $response['message'] = "Sala trovata";
        } else {
            $response['message'] = "Sala non trovata";
        }
        break;

    default:
        $response['message'] = "Azione non valida";
        break;
}

echo json_encode($response);
?>
