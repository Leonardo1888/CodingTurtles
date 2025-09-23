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
                <h3>Filtra i clienti</h3>
                <form id="filtro-clienti-form"> <label for="codice">Codice:</label>
                    <input type="text" id="Codice" name="Codice"><br>

                    <label for="nome">Nome:</label>
                    <input type="text" id="Nome" name="Nome"><br>

                    <label for="cognome">Cognome:</label>
                    <input type="text" id="Cognome" name="Cognome"><br>

                    <label for="cf">Codice fiscale:</label>
                    <input type="text" id="Cf" name="Cf"><br> <label for="dataNas">Data di nascita:</label>
                    <input type="text" id="DataNas" name="DataNas"><br> <label for="indirizzo">Indirizzo:</label>
                    <input type="text" id="Indirizzo" name="Indirizzo"><br>

                    <label for="tel">Telefono:</label>
                    <input type="text" id="Tel" name="Tel"><br>

                    <label for="email">Email:</label>
                    <input type="text" id="Email" name="Email"><br>

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <h2>Benvenuto in Coding Turtles</h2>
                <p>Scopri i clienti filtrando a sinistra!</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Codice Fiscale</th>
                                <th>Data di Nascita</th>
                                <th>Indirizzo</th>
                                <th>Telefono</th>
                                <th>Email</th>
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