<?php
require_once '../database/db.php';

$sql = "SELECT sala, data, ora, durata FROM FasciaOraria WHERE 1=1";
$params = [];
$types = '';

if(!empty($_POST['sala'])) {
    $sql .= " AND sala LIKE ?";
    $params[] = '%' . $_POST['sala'] . '%';
    $types .= 's';
}
// Gestione del range di date delle prenotazioni
if (!empty($_POST['data_inizio'])) {
    $sql .= " AND data >= ?";
    $params[] = $_POST['data_inizio'];
    $types .= 's'; 
}
if (!empty($_POST['data_fine'])) {
    $sql .= " AND data <= ?";
    $params[] = $_POST['data_fine'];
    $types .= 's';
}

if (!empty($_POST['ora_inizio'])) {
    $sql .= " AND ora >= ?";
    $params[] = $_POST['ora_inizio'];
    $types .= 's'; 
}
if (!empty($_POST['ora_fine'])) {
    $sql .= " AND ora <= ?";
    $params[] = $_POST['ora_fine'];
    $types .= 's';
}

if (!empty($_POST['durata_inizio'])) {
    $sql .= " AND durata >= ?";
    $params[] = $_POST['durata_inizio'];
    $types .= 's'; 
}
if (!empty($_POST['durata_fine'])) {
    $sql .= " AND durata <= ?";
    $params[] = $_POST['durata_fine'];
    $types .= 's';
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
                <td><a href=\"sale.php?Codice={$row['sala']}\">{$row['sala']}</a></td>
                <td>{$row['data']}</td>
                <td>{$row['ora']}</td>
                <td>{$row['durata']}</td>
              </tr>";
        }
    } else {
        $output = "<tr><td colspan='5'>Nessuna fascia oraria trovata.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='5'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output;
?>