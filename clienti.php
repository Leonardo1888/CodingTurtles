<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Coding Turtles - Palestra</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style src="https://code.jquery.com/jquery-3.6.0.min.js"></style>
  <script src="js/script.js" defer></script>
</head>
<body>

  <!-- Header -->
  <?php include("includes/header.php"); ?>

  <!-- Navigazione -->
  <?php include("includes/navbar.php"); ?>

  <!-- Contenuto principale -->
  <div class="main-container">
    <!-- Filtro Ricerca -->
    <div class="contenitore-centrale">
	  <aside class="colonna-filtro">
		<h3>Filtra i corsi</h3>
		<form id="filtro-form">
		  <label for="nome">Codice:</label>
		  <input type="text" id="Codice" name="Codice"><br>

		  <label for="nome">Nome:</label>
		  <input type="text" id="Nome" name="Nome"><br>

		  <label for="nome">Cognome:</label>
		  <input type="text" id="Cognome" name="Cognome"><br>

		  <label for="nome">Codice fiscale:</label>
		  <input type="text" id="Codice fiscale" name="Codice fiscale"><br>

		  <label for="nome">Data di nascita:</label>
		  <input type="text" id="Data di nascita" name="Data di nascita"><br>

		  <label for="nome">Indirizzo:</label>
		  <input type="text" id="Indirizzo" name="Indirizzo"><br>

	      <label for="nome">Telefono:</label>
		  <input type="text" id="Telefono" name="Telefono"><br>
		  
		  <label for="nome">Email:</label>
		  <input type="text" id="Email" name="Email"><br>


		  <button type="submit">Cerca</button>
		</form>
	  </aside>

	  <main class="colonna-risultati" id="risultati">
		<h2>Benvenuto in Coding Turtles</h2>
		<p>Scopri i clienti filtrando a sinistra!</p>

		<!-- Tabella per visualizzare i clienti -->
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
  <tbody>
    <?php
    // Includi il file di connessione al database
    require_once 'database/db.php';

    // Query per selezionare tutti i clienti
    $sql = "SELECT codice, nome, cognome, cf, dataNas, indirizzo, tel, email FROM Cliente";
    $result = $conn->query($sql);

    // Visualizza i risultati
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
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
        echo "<tr><td colspan='8'>Nessun cliente trovato</td></tr>";
    }

    // Chiudi la connessione
    $conn->close();
    ?>
  </tbody>
</table>

	  </main>
	</div>

  </div>

  <!-- Footer -->
  <?php include("includes/footer.php"); ?>

</body>
</html>
