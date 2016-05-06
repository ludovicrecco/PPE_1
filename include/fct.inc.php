<?php
/** 
 * Fonctions pour l'application GSB
 
 * @author Recco Ludovic
 * @package default

 * @version    1.0
 */

 /**
 * Teste si un quelconque visiteur est connect�
 * @return vrai ou faux 
 */
function estConnecte(){
  return isset($_SESSION['idVisiteur']);
}
/**
 * Enregistre dans une variable session les infos d'un visiteur
 
 * @param $id 
 * @param $nom
 * @param $prenom
 */
function connecter($id,$type,$typeName,$nom,$prenom){
	$_SESSION['idVisiteur']= $id; 
	$_SESSION['nom']= $nom;
	$_SESSION['prenom']= $prenom;
        $_SESSION['type'] = $type;
        $_SESSION['typeName'] = $typeName;
}
/**
 * D�truit la session active
 */
function deconnecter(){
	session_destroy();
}
/**
 * Transforme une date au format fran�ais jj/mm/aaaa vers le format anglais aaaa-mm-jj
 
 * @param $madate au format  jj/mm/aaaa
 * @return la date au format anglais aaaa-mm-jj
*/
function dateFrancaisVersAnglais($maDate){
	@list($jour,$mois,$annee) = explode('/',$maDate);
	return date('Y-m-d',mktime(0,0,0,$mois,$jour,$annee));
}
/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format fran�ais jj/mm/aaaa 
 
 * @param $madate au format  aaaa-mm-jj
 * @return la date au format format fran�ais jj/mm/aaaa
*/
function dateAnglaisVersFrancais($maDate){
   @list($annee,$mois,$jour)=explode('-',$maDate);
   $date="$jour"."/".$mois."/".$annee;
   return $date;
}
/**
 * retourne le mois au format aaaamm selon le jour dans le mois
 
 * @param $date au format  jj/mm/aaaa
 * @return le mois au format aaaamm
*/
function getMois($date){
		@list($jour,$mois,$annee) = explode('/',$date);
		if(strlen($mois) == 1){
			$mois = "0".$mois;
		}
		return $annee.$mois;
}


function moisSuivant($mois){
    $annee = substr($mois, 0, 4);
    $mois = intval(substr($mois, 4,2));
        $mois++;
        if($mois == 13){
            $mois = 1;
            $annee = intval($annee);
            $annee++;
            $annee = ''.$annee;
        }elseif($mois < 10){
            $mois = '0'.$mois;
        }else{
            $mois = ''.$mois;
        }
    return $annee.$mois;
}
/* gestion des erreurs*/
/**
 * Indique si une valeur est un entier positif ou nul
 
 * @param $valeur
 * @return vrai ou faux
*/
function estEntierPositif($valeur) {
	return preg_match("/[^0-9]/", $valeur) == 0;
	
}

/**
 * Indique si un tableau de valeurs est constitu� d'entiers positifs ou nuls
 
 * @param $tabEntiers : le tableau
 * @return vrai ou faux
*/
function estTableauEntiers($tabEntiers) {
	$ok = true;
	foreach($tabEntiers as $unEntier){
		if(!estEntierPositif($unEntier)){
		 	$ok=false; 
		}
	}
	return $ok;
}
/**
 * V�rifie si une date est inf�rieure d'un an � la date actuelle
 
 * @param $dateTestee 
 * @return vrai ou faux
*/
function estDateDepassee($dateTestee){
	$dateActuelle=date("d/m/Y");
	@list($jour,$mois,$annee) = explode('/',$dateActuelle);
	$annee--;
	$AnPasse = $annee.$mois.$jour;
	@list($jourTeste,$moisTeste,$anneeTeste) = explode('/',$dateTestee);
	return ($anneeTeste.$moisTeste.$jourTeste < $AnPasse); 
}
/**
 * V�rifie la validit� du format d'une date fran�aise jj/mm/aaaa 
 
 * @param $date 
 * @return vrai ou faux
*/
function estDateValide($date){
	$tabDate = explode('/',$date);
	$dateOK = true;
	if (count($tabDate) != 3) {
	    $dateOK = false;
    }
    else {
		if (!estTableauEntiers($tabDate)) {
			$dateOK = false;
		}
		else {
			if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
				$dateOK = false;
			}
		}
    }
	return $dateOK;
}

/**
 * V�rifie que le tableau de frais ne contient que des valeurs num�riques 
 
 * @param $lesFrais 
 * @return vrai ou faux
*/
function lesQteFraisValides($lesFrais){
	return estTableauEntiers($lesFrais);
}
/**
 * V�rifie la validit� des trois arguments : la date, le libell� du frais et le montant 
 
 * des message d'erreurs sont ajout�s au tableau des erreurs
 
 * @param $dateFrais 
 * @param $libelle 
 * @param $montant
 */
function valideInfosFrais($dateFrais,$libelle,$montant){
	if($dateFrais==""){
		ajouterErreur("Le champ date ne doit pas �tre vide");
	}
	else{
		if(!estDatevalide($dateFrais)){
			ajouterErreur("Date invalide");
		}	
		else{
			if(estDateDepassee($dateFrais)){
				ajouterErreur("date d'enregistrement du frais d�pass�, plus de 1 an");
			}			
		}
	}
	if($libelle == ""){
		ajouterErreur("Le champ description ne peut pas �tre vide");
	}
	if($montant == ""){
		ajouterErreur("Le champ montant ne peut pas �tre vide");
	}
	else
		if( !is_numeric($montant) ){
			ajouterErreur("Le champ montant doit �tre num�rique");
		}
}
/**
 * Ajoute le libell� d'une erreur au tableau des erreurs 
 
 * @param $msg : le libell� de l'erreur 
 */
function ajouterErreur($msg){
   if (! isset($_REQUEST['erreurs'])){
      $_REQUEST['erreurs']=array();
	} 
   $_REQUEST['erreurs'][]=$msg;
}
/**
 * Retoune le nombre de lignes du tableau des erreurs 
 
 * @return le nombre d'erreurs
 */
function nbErreurs(){
   if (!isset($_REQUEST['erreurs'])){
	   return 0;
	}
	else{
	   return count($_REQUEST['erreurs']);
	}
}

/**
 * Retourne vrai si la date courrante est en d�but de mois
 */

function estDebutMois(){
    $jour = date('j');
    $nbJoursMois = date_add(date_create('01-'.date('m-Y')), date_interval_create_from_date_string('-1day'))
                            ->format('j');
    return $jour < ($nbJoursMois/2);
}

/**
 * Retourne vrai si la chaine commence par la deuxi�me chaine
 
 * @param string $haystack   Variable de recherche
 * @param string $needle    Variable a rechercher
 */
function substr_startswith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}
?>