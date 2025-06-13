<?php
$host = 'localhost';          // o nome host specifico se fornito da Altervista
$dbname = 'my_codingturtles';    // cambia con il tuo nome database
$username = 'codingturtles';       // il tuo username Altervista
$password = '';       // la tua password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connessione avvenuta con successo";
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}
?>
