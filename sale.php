<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coding Turtles - Palestra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js" defer></script>
</head>

<body>

    <?php include("includes/header.php"); ?>
    <?php include("includes/navbar.php"); ?>

    <div class="main-container">
        <div class="contenitore-centrale">
            <aside class="colonna-filtro">
                <h3>Filtra i corsi</h3>
                <form id="filtro-form">
                    <label for="Codice">Codice Sala:</label>
                    <input type="text" id="Codice" name="Codice"><br>

                    <label for="Nome">Nome:</label>
                    <input type="text" id="Nome" name="Nome"><br>

                    <label for="Tema">Tema:</label>
                    <select id="Tema" name="Tema">
                        <option value="">Tutti</option>
                        <?php
                        require_once 'database/db.php';
                        $sql = "SELECT DISTINCT tema FROM Sala";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['tema']) . "'>" . htmlspecialchars($row['tema']) . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select><br>

                    <label>Metri quadrati:</label>
                    <div class="input-range">
                        da <input type="number" id="Mq_min" name="Mq_min" placeholder="min">
                        <span>a</span>
                        <input type="number" id="Mq_max" name="Mq_max" placeholder="max">
                    </div><br>

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>SALE</h2>
                <p>Scopri le sale della palestra Coding Turtles filtrando a sinistra!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Sala</th>
                                <th>Nome</th>
                                <th>Tema</th>
                                <th>Metri Quadrati</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-sale">
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
</body>

</html>