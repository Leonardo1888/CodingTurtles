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

    <?php // Verifica che quando prenotazioni.php viene aperta non ci sia un valore per la GET.
    $prenotazioni_filtro_sala = '';
    $prenotazioni_filtro_cliente = '';
    if (isset($_GET['sala'])) {
        $prenotazioni_filtro_sala = htmlspecialchars($_GET['sala']);
    }
    if (isset($_GET['cliente'])) {
        $prenotazioni_filtro_cliente = htmlspecialchars($_GET['cliente']);
    }
    ?>

    <div class="main-container">
        <div class="contenitore-centrale">
            <aside class="colonna-filtro">
                <h3>Filtra le Prenotazioni</h3>
                <form id="filtro-prenotazioni-form">
                    <label for="cliente">Codice Cliente:</label>
                    <input type="text" id="cliente" name="cliente" value="<?php echo $prenotazioni_filtro_cliente ?>"><br>

                    <label for="sala">Codice Sala:</label>
                    <input type="text" id="sala" name="sala" value="<?php echo $prenotazioni_filtro_sala ?>"><br>

                    <label>Data:</label>
                    <div class="input-range">
                        <input type="date" id="inizio_prenotazione_min" name="inizio_prenotazione_min">
                        <span>-</span>
                        <input type="date" id="inizio_prenotazione_max" name="inizio_prenotazione_max">
                    </div><br>

                    <label>Ora:</label>
                    <div class="input-range">
                        <input type="time" id="ora_prenotazione_min" name="ora_prenotazione_min">
                        <span>-</span>
                        <input type="time" id="ora_prenotazione_max" name="ora_prenotazione_max">
                    </div><br>

                    <label>Posto:</label>
                    <input type="text" id="posto" name="posto"><br>

                    <label for="abbonamento">Codice Abbonamento:</label>
                    <input type="text" id="abbonamento" name="abbonamento"><br>

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>PRENOTAZIONI</h2>
                <p>Scopri le prenotazioni!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Cliente</th>
                                <th>Codice Sala</th>
                                <th>Data</th>
                                <th>Ora</th>
                                <th>Posto</th>
                                <th>Abbonamento usato</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-prenotazioni">
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>