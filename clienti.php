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

    <?php // Verifica che quando clienti.php viene aperta non ci sia un valore per la GET.
    $cliente_filtro = '';
    if (isset($_GET['codice'])) {
        $codiceCliente_filtro = htmlspecialchars($_GET['codice']);
    }
    ?>

    <div class="main-container">
        <div class="contenitore-centrale">
            <aside class="colonna-filtro">
                <h3>Filtra i clienti</h3>
                <form id="filtro-clienti-form"> 
                    
                    <label for="codice">Codice Cliente:</label>
                    <input type="text" id="Codice" name="Codice" value="<?php echo $codiceCliente_filtro ?>"><br>

                    <label for="nome">Nome:</label>
                    <input type="text" id="Nome" name="Nome"><br>

                    <label for="cognome">Cognome:</label>
                    <input type="text" id="Cognome" name="Cognome"><br>

                    <label for="cf">Codice fiscale:</label>
                    <input type="text" id="Cf" name="Cf"><br>

                    <label>Data di nascita:</label>
                    <div class="input-range">
                        <input type="date" id="DataNas_min" name="DataNas_min" placeholder="da">
                        <span>-</span>
                        <input type="date" id="DataNas_max" name="DataNas_max" placeholder="a">
                    </div><br>

                    <label for="indirizzo">Indirizzo:</label>
                    <input type="text" id="Indirizzo" name="Indirizzo"><br>

                    <label for="tel">Telefono:</label>
                    <input type="text" id="Tel" name="Tel"><br>

                    <label for="email">Email:</label>
                    <input type="text" id="Email" name="Email"><br>

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>CLIENTI</h2>
                <p>Scopri i clienti filtrando a sinistra!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Cliente</th>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Codice Fiscale</th>
                                <th>Data di Nascita</th>
                                <th>Indirizzo</th>
                                <th>Tel <i class="fa-solid fa-phone-volume"></i></th>
                                <th>Email <i class="fa-solid fa-envelope"></i></th>
                                <th>n.Abb</th>
                                <th>n.Prenotazioni</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-clienti">
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

</body>

</html>