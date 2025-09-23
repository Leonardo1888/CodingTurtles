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

    <div class="main-container">
        <div class="contenitore-centrale">
            <aside class="colonna-filtro">
                <h3>Filtra gli abbonamenti</h3>
                <form id="filtro-abbonamenti-form"> <label for="nAbb">Numero Abbonamento:</label>
                    <input type="text" id="nAbb" name="nAbb"><br>

                    <label for="cliente">Codice Cliente:</label>
                    <input type="text" id="cliente" name="cliente"><br>

                    <label for="inizio">Data Inizio:</label>
                    <input type="text" id="inizio" name="inizio"><br>

                    <label for="fine">Data Fine:</label>
                    <input type="text" id="fine" name="fine"><br>

                    <label for="prezzo_min">Prezzo:</label>
                    <div class="input-range">
                        <input type="text" id="prezzo_min" name="prezzo_min" placeholder="min">
                        <span>-</span>
                        <input type="text" id="prezzo_max" name="prezzo_max" placeholder="max">
                    </div><br>
                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>Benvenuto in Coding Turtles</h2>
                <p>Scopri gli abbonamenti acquistati filtrando a sinistra!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Numero Abbonamento</th>
                                <th>Codice Cliente</th>
                                <th>Data Inizio</th>
                                <th>Data Fine</th>
                                <th>Prezzo</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-abbonamenti">
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>