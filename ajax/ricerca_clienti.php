<?php
require_once '../database/db.php';

$sql = "SELECT codice, nome, cognome, cf, dataNas, indirizzo, tel, email FROM Cliente WHERE 1=1";
$params = [];
$types = '';

if(!empty($_POST['Codice'])) {
    $sql .= " AND codice LIKE ?";
    $params[] = '%' . $_POST['Codice'] . '%';
    $types .= 's';
}
if(!empty($_POST['Nome'])) {
    $sql .= " AND nome LIKE ?";
    $params[] = '%' . $_POST['Nome'] . '%';
    $types .= 's';
}
if(!empty($_POST['Cognome'])) {
    $sql .= " AND cognome LIKE ?";
    $params[] = '%' . $_POST['Cognome'] . '%';
    $types .= 's';
}
if(!empty($_POST['Cf'])) {
    $sql .= " AND cf LIKE ?";
    $params[] = '%' . $_POST['Cf'] . '%';
    $types .= 's';
}
if(!empty($_POST['DataNas'])) {
    $sql .= " AND dataNas LIKE ?";
    $params[] = '%' . $_POST['DataNas'] . '%';
    $types .= 's';
}
if(!empty($_POST['Indirizzo'])) {
    $sql .= " AND indirizzo LIKE ?";
    $params[] = '%' . $_POST['Indirizzo'] . '%';
    $types .= 's';
}
if(!empty($_POST['Tel'])) {
    $sql .= " AND tel LIKE ?";
    $params[] = '%' . $_POST['Tel'] . '%';
    $types .= 's';
}
if(!empty($_POST['Email'])) {
    $sql .= " AND email LIKE ?";
    $params[] = '%' . $_POST['Email'] . '%';
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
                <td>{$row['codice']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['cognome']}</td>
                <td>{$row['cf']}</td>
                <td>{$row['dataNas']}</td>
                <td>{$row['indirizzo']}</td>
                <td>{$row['tel']}</td>
                <td>{$row['email']}</td>
              </tr>";
        }
    } else {
        $output = "<tr><td colspan='8'>Nessun cliente trovato con i filtri specificati.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='8'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output;
?>