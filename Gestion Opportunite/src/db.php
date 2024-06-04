<?php
require_once('Conf.php');

try {
    $conn = new PDO("mysql:host=".Conf::getHostname().";dbname=".Conf::getDatabase().";port=".Conf::getPort(), Conf::getLogin(), Conf::getPassword());
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "La connexion a échoué: " . $e->getMessage();
}
?>
