<?php
require_once '../database/db.php';

$sql = "SELECT nAbb, cliente, inizio, fine, prezzo FROM Abbonamento WHERE 1=1";
$params = [];
$types = '';

if(!empty($_POST['nAbb'])) {
    $sql .= " AND nAbb LIKE ?";
    $params[] = '%' . $_POST['nAbb'] . '%';
    $types .= 's';
}
if(!empty($_POST['cliente'])) {
    $sql .= " AND cliente LIKE ?";
    $params[] = '%' . $_POST['cliente'] . '%';
    $types .= 's';
}

// Gestione del range di date di inizio
if (!empty($_POST['inizio_abbonamento_min'])) {
    $sql .= " AND inizio >= ?";
    $params[] = $_POST['inizio_abbonamento_min'];
    $types .= 's'; // 's' per stringa, dato che le date vengono passate come stringhe
}
if (!empty($_POST['inizio_abbonamento_max'])) {
    $sql .= " AND inizio <= ?";
    $params[] = $_POST['inizio_abbonamento_max'];
    $types .= 's';
}

// Gestione del range di date di fine
if (!empty($_POST['fine_abbonamento_min'])) {
    $sql .= " AND fine >= ?";
    $params[] = $_POST['fine_abbonamento_min'];
    $types .= 's';
}
if (!empty($_POST['fine_abbonamento_max'])) {
    $sql .= " AND fine <= ?";
    $params[] = $_POST['fine_abbonamento_max'];
    $types .= 's';
}
// Gestione del range di prezzo
if (!empty($_POST['prezzo_min'])) {
    $sql .= " AND prezzo >= ?";
    $params[] = $_POST['prezzo_min'];
    $types .= 'd'; // 'd' per double o float
}
if (!empty($_POST['prezzo_max'])) {
    $sql .= " AND prezzo <= ?";
    $params[] = $_POST['prezzo_max'];
    $types .= 'd';
}

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
                <td>{$row['nAbb']}</td>
                <td><a href=\"clienti.php?codice={$row['cliente']}\">{$row['cliente']}</a></td>
                <td>{$row['inizio']}</td>
                <td>{$row['fine']}</td>
                <td>{$row['prezzo']}</td>
              </tr>";
        }
    } else {
        $output = "<tr><td colspan='5'>Nessun abbonamento trovato con i filtri specificati.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='5'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output;
?>