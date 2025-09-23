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
if(!empty($_POST['inizio'])) {
    $sql .= " AND inizio LIKE ?";
    $params[] = '%' . $_POST['inizio'] . '%';
    $types .= 's';
}
if(!empty($_POST['fine'])) {
    $sql .= " AND fine LIKE ?";
    $params[] = '%' . $_POST['fine'] . '%';
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
                <td>{$row['cliente']}</td>
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