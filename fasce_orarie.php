<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coding Turtles - Palestra</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js" defer></script>
</head>

<body>

    <?php include("includes/header.php"); ?>
    <?php include("includes/navbar.php"); ?>

    <?php // Verifica che quando fasce_orarie.php viene aperta non ci sia un valore per la GET.
    $fasce_filtro = '';
    if (isset($_GET['sala'])) {
        $fasce_filtro = htmlspecialchars($_GET['sala']);
    }
    ?>

    <div class="main-container">
        <div class="contenitore-centrale">
            <aside class="colonna-filtro">
                <h3>Filtra le Fasce Orarie</h3>
                <form id="filtro-fasce_orarie-form">
                    <label for="sala">Codice Sala:</label>
                    <input type="text" id="sala" name="sala" value="<?php echo $fasce_filtro ?>"><br>

                    <label>Data:</label>
                    <div class="input-range">
                        <input type="date" id="data_inizio" name="data_inizio">
                        <span>-</span>
                        <input type="date" id="data_fine" name="data_fine">
                    </div><br>

                    <label>Ora:</label>
                    <div class="input-range">
                        <input type="time" id="ora_inizio" name="ora_inizio">
                        <span>-</span>
                        <input type="time" id="ora_fine" name="ora_fine">
                    </div><br>

                    <label>Durata:</label>
                    <div class="input-range">
                        da <input type="number" id="durata_inizio" name="durata_inizio" placeholder="min">
                        <span>a</span>
                        <input type="number" id="durata_fine" name="durata_fine" placeholder="max">
                    </div><br>

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>FASCE ORARIE</h2>
                <p>Scopri le fasce orarie!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Sala</th>
                                <th>Data</th>
                                <th>Ora</th>
                                <th>Durata</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-fasce_orarie">
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>