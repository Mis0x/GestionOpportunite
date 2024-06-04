<?php

require_once('Conf.php');

class Model {

    public static $pdo;

    /**
     * Connexion à la base de données
     */
    public static function init_pdo() {
        $host   = Conf::getHostname();
        $dbname = Conf::getDatabase();
        $login  = Conf::getLogin();
        $pass   = Conf::getPassword();
        $port   = Conf::getPort();

        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $login, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            die("Problème lors de la connexion à la base de données.");
        }
    }

    /**
     * Renvoie la liste de toutes les opportunités (id, nom, étape)
     */
    public static function get_opportunites() {
        $sql = "SELECT * FROM Opportunités";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie la liste de toutes les étapes (id, nom)
     */
    public static function get_etapes() {
        $sql = "SELECT * FROM Etapes";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie la liste de tout les evenements (idEvenement, idOpportunité, idTypeEvenement, dateEvenement)
     */
    public static function get_evenements() {
        $sql = "SELECT * FROM Evenement";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie la liste de tout les type d'evenements (idTypeEvenement, nom)
     */
    public static function get_type_evenements() {
        $sql = "SELECT * FROM TypeEvenement";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie l'historique des evenements d'une opportunite avec les etapes des evenements
     */
    public static function get_historique_opportunite($id) {
        $sql = "SELECT TypeEvenement.nomevenement, Evenement.dateevenement, Etapes.nomEtape, Evenement.idEvenement FROM Evenement 
            join TypeEvenement on Evenement.idTypeEvenement = TypeEvenement.idTypeEvenement
            join Etapes on Evenement.idEtape = Etapes.idEtape
            WHERE Evenement.idopportunité = $id";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result ? $result : [];
    }


    /**
     * Renvoie l'historique des evenements d'une opportunite pour une étape donnée
     */
    public static function get_historique_opportunite_etape($id, $idEtape) {
        $sql = "SELECT TypeEvenement.nomevenement, Evenement.dateevenement FROM Evenement 
                join TypeEvenement on Evenement.idEvenement = TypeEvenement.idtypeevenement 
                WHERE Evenement.idopportunité = $id AND Evenement.idEtape = $idEtape";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie l'id de l'etape actuelle de l'opportunite
     */
    public static function get_idetapes_opportunite($id) {
        $sql = "SELECT idEtape FROM Opportunités WHERE idOpportunité = $id";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renvoie le nom de l'etape actuelle de l'opportunite
     */
    public static function get_nometapes_opportunite($id) {
        $sql = "SELECT nomEtape FROM Opportunités 
                JOIN Etapes on Opportunités.idEtape = Etapes.idEtape
                WHERE idOpportunité = $id";
        $query = self::$pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fait passer une opportunité à l'étape suivante
     */
    public static function incr_opportunite($id) {
        $currentStep = self::get_idetapes_opportunite($id);
        if ($currentStep && $currentStep[0]['idEtape'] < 5) {
            $nextStep = $currentStep[0]['idEtape'] + 1;
            $sql = "UPDATE Opportunités SET idEtape = $nextStep WHERE idOpportunité = $id";
            $query = self::$pdo->prepare($sql);
            return $query->execute();
        } else {
            error_log("L'opportunite est déjà à l'étape maximale");
            return array('status' => 'error', 'message' => "L'opportunité est déjà à l'étape finale et ne peut pas être incrémentée.");
        }
    }

    /**
     * Ajoute une nouvelle opportunite
     * (l'id est incrémenté automatiquement)
     */
    public static function add_opportunite($nom) {
        $sql = "INSERT INTO Opportunités (nomOpportunité, idEtape) VALUES ('$nom',1)";
        $query = self::$pdo->prepare($sql);
        return $query->execute();
    }

    /**
     * Ajoute un nouveau type d'evenement
     * (l'id est incrémenté automatiquement)
     */
    public static function add_type_evenement($nom) {
        $sql = "INSERT INTO TypeEvenement (nomEvenement) VALUES ('$nom')";
        $query = self::$pdo->prepare($sql);
        return $query->execute();
    }

    /**
     * Ajoute un nouveau evenement
     * (l'id est incrémenté automatiquement)
     */
    public static function add_evenement($idOpp, $idEtape, $idTypeEvenement, $date) {
        $sql = "INSERT INTO Evenement (idOpportunité, idEtape, idTypeEvenement, dateEvenement) VALUES ('$idOpp', '$idEtape', '$idTypeEvenement', '$date')";
        $query = self::$pdo->prepare($sql);
        return $query->execute();
    }

    /**
     * Supprime une opportunité (et tout les evenement associées)
     */
    public static function delete_opportunite($id) {
        $sql = "DELETE FROM Opportunités WHERE idOpportunité = $id";
        $query = self::$pdo->prepare($sql);
        return $query->execute();
    }

    /**
     * Supprime un evenement
     */
    public static function delete_evenement($id) {
        $sql = "DELETE FROM Evenement WHERE idEvenement = '$id'";
        $query = self::$pdo->prepare($sql);
        return $query->execute();
    }

}

// on initialise la connexion $pdo
Model::init_pdo();

?>
