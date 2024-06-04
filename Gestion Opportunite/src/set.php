<?php
require_once('Model.php');

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Invalid request');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'add_opportunite':
                $nomOpportunite = $_POST['nomOpportunite'];
                if (Model::add_opportunite($nomOpportunite)) {
                    $response = array('status' => 'success', 'message' => 'Opportunité ajoutée avec succès');
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de l\'ajout de l\'opportunité');
                }
                break;

            case 'add_type_evenement':
                $nomEvenement = $_POST['nomEvenement'];
                if (Model::add_type_evenement($nomEvenement)) {
                    $response = array('status' => 'success', 'message' => 'Type d\'événement ajouté avec succès');
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de l\'ajout du type d\'événement');
                }
                break;

            case 'add_evenement':
                $idOpportunité = $_POST['idOpportunité'];
                $idEtape = $_POST['idEtape'];
                $idTypeEvenement = $_POST['idTypeEvenement'];
                $dateEvenement = $_POST['dateEvenement'];
                if (Model::add_evenement($idOpportunité, $idEtape, $idTypeEvenement, $dateEvenement)) {
                    $response = array('status' => 'success', 'message' => 'Événement ajouté avec succès');
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de l\'ajout de l\'événement');
                }
                break;

            case 'incr_opportunite':
                $idOpportunité = $_POST['idOpportunité'];
                $result = Model::incr_opportunite($idOpportunité);
                if ($result === true) {
                    $response = array('status' => 'success', 'message' => 'Opportunité incrémentée avec succès');
                } elseif (is_array($result) && $result['status'] === 'error') {
                    $response = $result;
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de l\'incrémentation de l\'opportunité');
                }
                break;

            case 'delete_opportunite':
                $idOpportunité = $_POST['idOpportunité'];
                if (Model::delete_opportunite($idOpportunité)) {
                    $response = array('status' => 'success', 'message' => 'Opportunité supprimée avec succès');
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de la suppression de l\'opportunité');
                }
                break;

            case 'delete_evenement':
                $idEvenement = $_POST['idEvenement'];
                if (Model::delete_evenement($idEvenement)) {
                    $response = array('status' => 'success', 'message' => 'Événement supprimé avec succès');
                } else {
                    $response = array('status' => 'error', 'message' => 'Erreur lors de la suppression de l\'événement');
                }
                break;

            default:
                $response = array('status' => 'error', 'message' => 'Action non reconnue');
                break;
        }
    } catch (Exception $e) {
        $response = array('status' => 'error', 'message' => 'Une erreur est survenue : ' . $e->getMessage());
    }
}

echo json_encode($response);
?>
