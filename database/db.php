<?php
$host = 'localhost';          // o nome host specifico se fornito da Altervista
$dbname = 'my_codingturtles';    // cambia con il tuo nome database
$username = 'codingturtles';       // il tuo username Altervista
$password = '';       // la tua password

// Crea la connessione
$conn = new mysqli($host, $username, $password, $dbname);

// Controlla la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>