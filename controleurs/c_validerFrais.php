<?php
/**
 * Controleur Validation des frais
 
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
$situation = array('CR' => 'Saisie en cours', 'CL' => 'Non validé', 'VA' => 'Validé', 'RB' => 'Remboursé', 'EP' => 'Mis en paiement', 'RF' => 'Refusé');
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
            $lesMois = $pdo->getLesMoisDisponibles($leVisiteur);
            $lesCles = array_keys($lesMois);
            if(estDebutMois()){
                $moisASelectionner = $lesCles[1];
            }else{
                $moisASelectionner = $lesCles[0];
            }
            $actionListeMois = 'validerFiche&leVisiteur='. $leVisiteur;
        include('vues/v_listeMois.php');
        }
        break;
    case 'validerFiche':
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
                $modifFraisForfait = array();
                if($idEtatForfait == 'CL'){
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $modifFraisForfait[$unFraisForfait['idfrais']] = $_REQUEST['txtQteForfait'.$unFraisForfait['idfrais']];
                    if(!is_numeric($modifFraisForfait[$unFraisForfait['idfrais']])){
                        $enregistrementErreur .= '<span style="color: red;">Le champ '. $unFraisForfait['libelle']. ' doit être un nombre</span><br>';
                    }
                }
                $validerFraisForfait = (isset($_REQUEST['validerEtatForfait']) && $_REQUEST['validerEtatForfait'] == true);
                }
                $modifHorsForfait = array();
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    if($unFraisHorsForfait['idEtat'] == 'CL'){
                        $modifHorsForfait[$unFraisHorsForfait['id']]['date'] = $_REQUEST['txtHF'.$unFraisHorsForfait['id'].'date'];
                        if(!estDateValide($modifHorsForfait[$unFraisHorsForfait['id']]['date'])){
                            $enregistrementErreur .= '<span style="color: red;">Les champs Date doivent être des dates valides</span><br>';
                        }
                        $modifHorsForfait[$unFraisHorsForfait['id']]['libelle'] = $_REQUEST['txtHF'.$unFraisHorsForfait['id'].'libelle'];
                        $modifHorsForfait[$unFraisHorsForfait['id']]['libelle'] = substr($modifHorsForfait[$unFraisHorsForfait['id']]['libelle'],0,100);
                        //Controle longueur
                        //if(count($modifHorsForfait[$unFraisHorsForfait['id']]['libelle']) > 100){
                        //    $enregistrementErreur .= '<span style="color: red;">Le champ Libellé doit contenir au maximum 100 caractères.</span><br>';
                        //}
                        $modifHorsForfait[$unFraisHorsForfait['id']]['montant'] = $_REQUEST['txtHF'.$unFraisHorsForfait['id'].'montant'];
                        if(!is_numeric($modifHorsForfait[$unFraisHorsForfait['id']]['montant'])){
                            $enregistrementErreur .= '<span style="color: red;">Le champ Montant doit être un nombre</span><br>';
                        }
                        $modifHorsForfait[$unFraisHorsForfait['id']]['etat'] = $unFraisHorsForfait['idEtat'];
                        if(isset($_REQUEST['validerEtatHorsForfait'.$unFraisHorsForfait['id']]) && $_REQUEST['validerEtatHorsForfait'.$unFraisHorsForfait['id']] == true){
                            $modifHorsForfait[$unFraisHorsForfait['id']]['etat'] = 'VA';
                        }                       
                        if(isset($_REQUEST['refuserEtatHorsForfait'.$unFraisHorsForfait['id']]) && $_REQUEST['refuserEtatHorsForfait'.$unFraisHorsForfait['id']] == true){
                            $modifHorsForfait[$unFraisHorsForfait['id']]['etat'] = 'RF';
                        }
                        if(isset($_REQUEST['reporterEtatHorsForfait'.$unFraisHorsForfait['id']]) && $_REQUEST['reporterEtatHorsForfait'.$unFraisHorsForfait['id']] == true){
                            $modifHorsForfait[$unFraisHorsForfait['id']]['etat'] = 'RP';
                        }
                    }
                }
                
                $modifNbJustificatifs = $_REQUEST['txtNbJustificatifs'];
                if(!is_numeric($modifNbJustificatifs)){
                    $enregistrementErreur .= '<span style="color: red;">Le champ Nb Justificatifs doit être un nombre</span><br>';
                }
                
                $modifMontantValide = $_REQUEST['txtMontantValide'];
                if(!is_numeric($modifMontantValide)){
                    $enregistrementErreur .= '<span style="color: red;">Le champ Montant Validé doit être un nombre</span><br>';
                }                
                
                $validerFiche = (isset($_REQUEST['validerEtat']) && $_REQUEST['validerEtat'] == true);
                
                if($enregistrementErreur == ''){
                    $enregistrementErreur = '<span style="color:forestgreen;">Enregistrements effectués</span><br>';
                    if($validerFiche){
                        $enregistrementErreur .= '<span style="color: forestgreen">Fiche de frais validée.</span>';
                    }
                    //Enregistrement sur BDD
                    if($idEtatForfait == 'CL'){
                        $pdo->majFraisForfait($leVisiteur['id'],$leMois,$modifFraisForfait);
                    }
                    $pdo->majFraisHorsForfait($modifHorsForfait,$leMois);
                    $pdo->majNbJustificatifs($leVisiteur['id'],$leMois,$modifNbJustificatifs);
                    $pdo->majMontantValide($leVisiteur['id'],$leMois,$modifMontantValide);
                    if($idEtatForfait == 'CL' && $validerFraisForfait == true){
                        $pdo->majEtatFraisForfait($leVisiteur['id'],$leMois,'VA');
                    }
                    if($validerFiche == true){
                        $pdo->majEtatFicheFrais($leVisiteur['id'],$leMois,'VA');
                        $pdo->majEtatFraisForfait($leVisiteur['id'],$leMois,'VA');
                        $pdo->majEtatFraisHorsForfait($leVisiteur['id'],$leMois,'VA');
                    }
                    
                    //Rafraichissement des données depuis la base
                    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId,$leMois);
                    $lesFraisForfait= $pdo->getLesFraisForfait($leVisiteurId,$leMois);
                     $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteurId,$leMois);
                     $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
                     $montantValide = $lesInfosFicheFrais['montantValide'];
                     $libEtat = $lesInfosFicheFrais['libEtat'];
                     $idEtat = $lesInfosFicheFrais['idEtat'];
                     $idEtatForfait = $lesInfosFicheFrais['idEtatForfait'];
                     $libEtatForfait = $lesInfosFicheFrais['libEtatForfait'];

                }
            }
            //Affichage de la page

            $leVisiteurASelectionner = $leVisiteurId;
            include('vues/v_listeVisiteurs.php');
            $lesMois = $pdo->getLesMoisDisponibles($leVisiteurId);
            $moisASelectionner = $leMois;
            $actionListeMois = 'validerFiche&leVisiteur='. $leVisiteurId;
            include('vues/v_listeMois.php');
                     $numAnnee =substr( $leMois,0,4);
                     $numMois =substr( $leMois,4,2);
            include('vues/v_validerFiche.php');
        }
        
        
    default:
        break;
}

?>