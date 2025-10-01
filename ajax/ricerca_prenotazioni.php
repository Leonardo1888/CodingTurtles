<?php
require_once '../database/db.php';

$sql = "SELECT cliente, sala, data, ora, posto FROM Prenotazione WHERE 1=1";
$params = [];
$types = '';

if(!empty($_POST['cliente'])) {
    $sql .= " AND cliente LIKE ?";
    $params[] = '%' . $_POST['cliente'] . '%';
    $types .= 's';
}
if(!empty($_POST['sala'])) {
    $sql .= " AND sala LIKE ?";
    $params[] = '%' . $_POST['sala'] . '%';
    $types .= 's';
}
if(!empty($_POST['data'])) {
    $sql .= " AND data LIKE ?";
    $params[] = '%' . $_POST['data'] . '%';
    $types .= 's';
}
if(!empty($_POST['ora'])) {
    $sql .= " AND ora LIKE ?";
    $params[] = '%' . $_POST['ora'] . '%';
    $types .= 's';
}
if(!empty($_POST['posto'])) {
    $sql .= " AND posto LIKE ?";
    $params[] = '%' . $_POST['posto'] . '%';
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
                <td><a href=\"clienti.php?Codice={$row['cliente']}\">{$row['cliente']}</a></td>
                <td><a href=\"sale.php?Codice={$row['sala']}\">{$row['sala']}</a></td>
                <td>{$row['data']}</td>
                <td>{$row['ora']}</td>
                <td>{$row['posto']}</td>
              </tr>";
        }
    } else {
        $output = "<tr><td colspan='5'>Nessuna prenotazione trovata.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='5'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output;
?>