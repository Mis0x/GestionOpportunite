<?php
require_once 'Model.php';

// Récupérer les données de la requête GET
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Traiter les différentes actions
switch ($action) {
    case 'get_opportunites':
        $opportunites = Model::get_opportunites();
        echo json_encode($opportunites);
        break;

    case 'get_etapes':
        $etapes = Model::get_etapes();
        echo json_encode($etapes);
        break;

    case 'get_evenements':
        $evenements = Model::get_evenements();
        echo json_encode($evenements);
        break;

    case 'get_type_evenements':
        $type_evenements = Model::get_type_evenements();
        echo json_encode($type_evenements);
        break;

    case 'get_historique_opportunite':
        if ($id > 0) {
            $historique = Model::get_historique_opportunite($id);
            echo json_encode($historique);
        } else {
            echo json_encode(['error' => 'ID d\'opportunité invalide']);
        }
        break;

    case 'get_historique_opportunite_etape':
        $idEtape = isset($_GET['idEtape']) ? intval($_GET['idEtape']) : 0;
        if ($id > 0 && $idEtape > 0) {
            $historique = Model::get_historique_opportunite_etape($id, $idEtape);
            echo json_encode($historique);
        } else {
            echo json_encode(['error' => 'ID d\'opportunité ou d\'étape invalide']);
        }
        break;

    case 'get_etape':
        $idEtape = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($idEtape > 0) {
            $etape = Model::get_etape($idEtape);
            echo json_encode($etape);
        } else {
            echo json_encode(['error' => 'ID d\'étape invalide']);
        }
        break;

    case 'get_nometapes_opportunite':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $etape = Model::get_nometapes_opportunite($id);
            echo json_encode($etape);
        } else {
            echo json_encode(['error' => 'ID d\'opportunité invalide']);
        }
        break;

    default:
        echo json_encode(['error' => 'Action invalide']);
        break;
}