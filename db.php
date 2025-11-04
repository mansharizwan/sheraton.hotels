<?php
$host = "localhost";
$dbname = "dbygj2e1pfx4ug";
$username = "ufmo7njmacww5";
$password = "11sfr0qvmbjh";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
