<?php

class Conf {

    private static $database = array(
        'hostname' => 'webinfo.iutmontp.univ-montp2.fr',
        'database' => 'diasm',   // le nom de votre base de données (votre login)
        'login'    => 'diasm',   // votre login
        'password' => 'mdp123456',    // le mot de passe pour la connexion à la base de données
        'port' => '3316' //3316 à l'iut, sinon probablement 3306
    );

    static public function getLogin() {
        return self::$database['login'];
    }

    static public function getHostname() {
        return self::$database['hostname'];
    }

    static public function getDatabase() {
        return self::$database['database'];
    }

    static public function getPassword() {
        return self::$database['password'];
    }

    static public function getPort() {
        return self::$database['port'];
    }
}

?>
