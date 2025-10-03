<?php
require_once '../database/db.php';

// Query iniziale con LEFT JOIN, senza GROUP BY
$sql = "SELECT 
        C.codice, 
        C.nome, 
        C.tema, 
        C.mq, 
        COUNT(DISTINCT NULLIF(CONCAT_WS('|', A.sala, A.data, A.ora), '')) AS nFasceOrarie,
        COUNT(DISTINCT P.nProg) AS nPrenotazioni
        FROM Sala AS C
        LEFT JOIN FasciaOraria AS A ON C.codice = A.sala
        LEFT JOIN Prenotazione AS P ON C.codice = P.sala";

//$sql = "SELECT codice, nome, tema, mq FROM Sala WHERE 1=1";

$params = [];
$types = '';

if (!empty($_POST['Codice'])) {
    $sql .= " AND codice LIKE ?";
    $params[] = '%' . $_POST['Codice'] . '%';
    $types .= 's';
}
if (!empty($_POST['Nome'])) {
    $sql .= " AND nome LIKE ?";
    $params[] = '%' . $_POST['Nome'] . '%';
    $types .= 's';
}
if (!empty($_POST['Tema'])) {
    $sql .= " AND tema LIKE ?";
    $params[] = '%' . $_POST['Tema'] . '%';
    $types .= 's';
}
// Gestione del range dei metri quadrati
if (!empty($_POST['Mq_min'])) {
    $sql .= " AND mq >= ?";
    $params[] = $_POST['Mq_min'];
    $types .= 'd';
}
if (!empty($_POST['Mq_max'])) {
    $sql .= " AND mq <= ?";
    $params[] = $_POST['Mq_max'];
    $types .= 'd';
}

// Aggiungi la clausola GROUP BY solo alla fine
$sql .= " GROUP BY C.codice, C.nome, C.tema, C.mq";


$stmt = $conn->prepare($sql);
$output = '';

if ($stmt) {
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= "<tr>
                <td>{$row['codice']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['tema']}</td>
                <td>{$row['mq']}</td>
                <td><a href=\"fasce_orarie.php?sala={$row['codice']}\">{$row['nFasceOrarie']}</a></td>
                <td><a href=\"prenotazioni.php?sala={$row['codice']}\">{$row['nPrenotazioni']}</a></td>
              </tr>";
        }
    } else {
        $output = "<tr><td colspan='4'>Nessuna sala trovata con i filtri specificati.</td></tr>";
    }
    $stmt->close();
} else {
    $output = "<tr><td colspan='4'>Errore SQL: " . $conn->error . "</td></tr>";
}

$conn->close();

echo $output; // stampa il resto della pagina HTML, che sarebbe il corpo della risposta AJAX
