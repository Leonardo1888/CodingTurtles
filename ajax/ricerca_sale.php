<?php
    require_once '../database/db.php';
    
    $sql = "SELECT codice, nome, tema, mq FROM Sala WHERE 1=1";
    $params = [];
    $types = '';

    if(!empty($_POST['Codice'])) {
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
    if (!empty($_POST['Mq'])) {
        $sql .= " AND mq LIKE ?";
        $params[] = '%' . $_POST['Mq'] . '%';
        $types .= 's';
    }

    $stmt = $conn->prepare($sql);
    $output = '';

    if($stmt) {
        if($types) {
            $stmt -> bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $output .= "<tr>
                <td>{$row['codice']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['tema']}</td>
                <td>{$row['mq']}</td>
              </tr>";
            }
        } else {
            $output = "<tr><td colspan='4'>Nessuna sala trovata con i filtri specificati.</td></tr>";
        }
        $stmt->close();
    } else {
        $output = "<tr><td colspan='4'>Errore SQL: " . $conn->error . "</td></tr>";
    }

    $conn -> close();

    echo $output; // stampa il resto della pagina HTML, che sarebbe il corpo della risposta AJAX

?>