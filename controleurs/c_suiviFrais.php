<?php

/**
 * Controleur suivi du remboursement des frais
 
 * @author Recco Ludovic
 * @package default
 */

/**
 * 
 */

//Controle des droits
if(isset($_SESSION['type']) && $_SESSION['type'] != 2){
    header('Location : index.php');
    exit(0);
}


if(!isset($_REQUEST['action'])){
    $_REQUEST['action'] = 'selection';
}


include('vues/v_sommaire.php');
$idVisiteur = $_SESSION['idVisiteur'];
//Variables pour la vue listeMois
$actionListeMois = 'selection';
$titreListeMois = '';
$action = $_REQUEST['action'];
$situation = array('CR' => 'Saisie en cours', 'CL' => 'Non validé', 'VA' => 'Validé', 'RB' => 'Remboursé', 'EP' => 'Mis en paiement');
switch ($action) {
    case 'selection':   // Selection du visiteur et du mois
        if(isset($_REQUEST['lstVisiteur'])){
            $leVisiteur = $_REQUEST['lstVisiteur'];
            $leVisiteurASelectionner = $leVisiteur;
        }else{
            $leVisiteurASelectionner = 0;
        }
        $lesVisiteurs = $pdo->getLesVisiteurs();
        include('vues/v_listeVisiteurs.php');
        if(isset($leVisiteur)){
            $lesMois = $pdo->getLesMoisDisponibles($leVisiteur, "'VA', 'RB', 'EP' ");
            $lesCles = array_keys($lesMois);
            if(estDebutMois()){
                $moisASelectionner = $lesCles[1];
            }else{
                $moisASelectionner = $lesCles[0];
            }
            $actionListeMois = 'suiviFiche&leVisiteur='. $leVisiteur;
        include('vues/v_listeMois.php');
        }
        break;
    case 'suiviFiche':
        if(isset($_REQUEST['leVisiteur']) && isset($_REQUEST['lstMois'])){
            
            //Variables
            $leVisiteurId = $_REQUEST['leVisiteur'];
            $lesVisiteurs = $pdo->getLesVisiteurs();
            $leMois = $_REQUEST['lstMois'];
            $leVisiteur = $pdo->getInfosVisiteurById($leVisiteurId);
            $numAnnee =substr( $leMois,0,4);
            $numMois =substr( $leMois,4,2);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId,$leMois);
            $lesFraisForfait= $pdo->getLesFraisForfait($leVisiteurId,$leMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteurId,$leMois);
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $idEtat = $lesInfosFicheFrais['idEtat'];
            $idEtatForfait = $lesInfosFicheFrais['idEtatForfait'];
            $libEtatForfait = $lesInfosFicheFrais['libEtatForfait'];
            
            $enregistrementErreur = ''; // message d'erreur a afficher sur la page
            //Enregistrement si modification
            //Recuperation des variables et verification
            if(isset($_REQUEST['enregistrer']) && $_REQUEST['enregistrer'] == 'true'){
                if($idEtat == 'VA'){
                        $pdo->majEtatFicheFrais($leVisiteur['id'],$leMois,'EP');
                        $pdo->majEtatFraisForfait($leVisiteur['id'],$leMois,'EP');
                        $pdo->majEtatFraisHorsForfait($leVisiteur['id'],$leMois,'EP');
                }elseif($idEtat == 'EP'){
                        $pdo->majEtatFicheFrais($leVisiteur['id'],$leMois,'RB');
                        $pdo->majEtatFraisForfait($leVisiteur['id'],$leMois,'RB');
                        $pdo->majEtatFraisHorsForfait($leVisiteur['id'],$leMois,'RB');
                }
                    
                    //Rafraichissement des données depuis la base
                    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId,$leMois);
                     $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteurId,$leMois);
                     $libEtat = $lesInfosFicheFrais['libEtat'];
                     $idEtat = $lesInfosFicheFrais['idEtat'];
                     $idEtatForfait = $lesInfosFicheFrais['idEtatForfait'];
                     $libEtatForfait = $lesInfosFicheFrais['libEtatForfait'];

                
            }
            //Affichage de la page

            $leVisiteurASelectionner = $leVisiteurId;
            include('vues/v_listeVisiteurs.php');
            $lesMois = $pdo->getLesMoisDisponibles($leVisiteurId, "'VA','EP','RB'");
            $moisASelectionner = $leMois;
            $actionListeMois = 'suiviFiche&leVisiteur='. $leVisiteurId;
            include('vues/v_listeMois.php');
                     $numAnnee =substr( $leMois,0,4);
                     $numMois =substr( $leMois,4,2);
            include('vues/v_suiviFiche.php');
        }
        
        
    default:
        break;
}


?>