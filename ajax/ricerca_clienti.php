<?php
require_once '../database/db.php';

// Query iniziale con LEFT JOIN, senza GROUP BY
$sql = "SELECT C.codice, C.nome, C.cognome, C.cf, C.dataNas, C.indirizzo, C.tel, C.email, COUNT(A.nAbb) AS nAbbonamenti, COUNT(DISTINCT P.nProg) AS nPrenotazioni
FROM Cliente AS C 
LEFT JOIN Abbonamento AS A ON C.codice = A.cliente
LEFT JOIN Prenotazione AS P ON C.codice = P.cliente
WHERE 1 = 1";

$params = [];
$types = '';

// Aggiunta dei filtri WHERE con la specificazione della tabella (C.)
if (!empty($_POST['Codice'])) {
    $sql .= " AND C.codice LIKE ?";
    $params[] = '%' . $_POST['Codice'] . '%';
    $types .= 's';
}
if (!empty($_POST['Nome'])) {
    $sql .= " AND C.nome LIKE ?";
    $params[] = '%' . $_POST['Nome'] . '%';
    $types .= 's';
}
if (!empty($_POST['Cognome'])) {
    $sql .= " AND C.cognome LIKE ?";
    $params[] = '%' . $_POST['Cognome'] . '%';
    $types .= 's';
}
if (!empty($_POST['Cf'])) {
    $sql .= " AND C.cf LIKE ?";
    $params[] = '%' . $_POST['Cf'] . '%';
    $types .= 's';
}
// Gestione del range per la data di nascita
if (!empty($_POST['DataNas_min'])) {
    $sql .= " AND C.dataNas >= ?";
    $params[] = $_POST['DataNas_min'];
    $types .= 's';
}
if (!empty($_POST['DataNas_max'])) {
    $sql .= " AND C.dataNas <= ?";
    $params[] = $_POST['DataNas_max'];
    $types .= 's';
}
if (!empty($_POST['Indirizzo'])) {
    $sql .= " AND C.indirizzo LIKE ?";
    $params[] = '%' . $_POST['Indirizzo'] . '%';
    $types .= 's';
}
if (!empty($_POST['Tel'])) {
    $sql .= " AND C.tel LIKE ?";
    $params[] = '%' . $_POST['Tel'] . '%';
    $types .= 's';
}
if (!empty($_POST['Email'])) {
    $sql .= " AND C.email LIKE ?";
    $params[] = '%' . $_POST['Email'] . '%';
    $types .= 's';
}

// Aggiungi la clausola GROUP BY solo alla fine
$sql .= " GROUP BY C.codice";

$stmt = $conn->prepare($sql);
$output = '';

if($stmt) {
    if($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $output .= "<tr>
                <td>{$row['codice']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['cognome']}</td>
                <td>{$row['cf']}</td>
                <td>{$row['dataNas']}</td>
                <td>{$row['indirizzo']}</td>
                <td><a href=\"tel:+{$row['tel']}\">{$row['tel']}</a></td>
                <td><a href=\"mailto:{$row['email']}\">{$row['email']}</a></td>
                <td><a href=\"abbonamenti.php?cliente={$row['codice']}\">{$row['nAbbonamenti']}</a></td>
                <td><a href=\"prenotazioni.php?cliente={$row['codice']}\">{$row['nPrenotazioni']}</a></td>
            </tr>";
        }
    } else {
        $output = "<tr><td colspan='10'>Nessun cliente trovato con i filtri specificati.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='10'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output;
?>