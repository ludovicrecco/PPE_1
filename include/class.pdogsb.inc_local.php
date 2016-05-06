<?php

/**
 * Classe d'acc�s aux donn�es contenue sur la base de donn�es
 
  * @author Recco Ludovic
  * @package default

 */

/** 
 * Classe d'acc�s aux donn�es. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb{   	

		//Informations sur la base de donn�es
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=ludovicrecco_gsbfrais';         	    
		private static $user='root' ;    		
      	private static $mdp='' ;	
		/////////////////////////////
	
		private static $monPdo;
		private static $monPdoGsb=null;
/**
 * Constructeur priv�, cr�e l'instance de PDO qui sera sollicit�e
 * pour toutes les m�thodes de la classe
 */				
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';
		'.PdoGsb::$bdd,PdoGsb::$user,PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui cr�e l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}
/**
 * Retourne les informations d'un visiteur pour la connexion
 
 * @param $login 
 * @param $mdp
 * @return l'id, le nom et le pr�nom sous la forme d'un tableau associatif 
*/
	public function getInfosVisiteur($login, $mdp){
		$req = "select visiteur.id as id, visiteur.typecompte as type, typecompte.libelle as typeName, visiteur.nom as nom, visiteur.prenom as prenom from visiteur 
		 join typecompte on typecompte.id = visiteur.typecompte 
                where visiteur.login='$login' and visiteur.mdp='$mdp'";
		$rs = PdoGsb::$monPdo->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}
        
        /**
 * Retourne les informations d'un visiteur a partir de son ID
 
 * @param $id 
 * @return l'id, le nom et le pr�nom sous la forme d'un tableau associatif 
*/
	public function getInfosVisiteurById($id){
		$req = "select visiteur.id as id, visiteur.typecompte as type, typecompte.libelle as typeName, visiteur.nom as nom, visiteur.prenom as prenom from visiteur 
		 join typecompte on typecompte.id = visiteur.typecompte 
                where visiteur.id='$id'";
		$rs = PdoGsb::$monPdo->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concern�es par les deux arguments
 
 * La boucle foreach ne peut �tre utilis�e ici car on proc�de
 * � une modification de la structure it�r�e - transformation du champ date-
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select lfhf.id, lfhf.idVisiteur,lfhf.mois, lfhf.date, lfhf.montant, lfhf.libelle, lfhf.idEtat, etat.libelle as libEtat from lignefraishorsforfait lfhf join etat on lfhf.idEtat=etat.id where lfhf.idvisiteur ='$idVisiteur' 
		and lfhf.mois = '$mois' ";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donn�
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs 
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concern�es par les deux arguments
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantit� sous la forme d'un tableau associatif 
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
/**
 * Retourne tous les id de la table FraisForfait
 
 * @return un tableau associatif 
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Met � jour la table ligneFraisForfait
 
 * Met � jour la table ligneFraisForfait pour un visiteur et
 * un mois donn� en enregistrant les nouveaux montants
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de cl� idFrais et de valeur la quantit� pour ce frais
 * @return un tableau associatif 
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			PdoGsb::$monPdo->exec($req);
		}
		
	}
        
        /**
         * Met a jour les frais hors forfait d'un visiteur a partir des ID des frais
         
         * @param $lesFrais tableau associatif de frais (id)(date,libelle,montant,etat)
         */
        public function majFraisHorsForfait($lesFrais,$leMois){
            $lesCles = array_keys($lesFrais);
            foreach($lesCles as $unIdFrais){
                $date = dateFrancaisVersAnglais($lesFrais[$unIdFrais]['date']);
                $libelle = $lesFrais[$unIdFrais]['libelle'];
                $montant = $lesFrais[$unIdFrais]['montant'];
                $etat = $lesFrais[$unIdFrais]['etat'];
                if(($etat == 'RF' && !substr_startswith($libelle, 'REFUSE : '))){
                    $libelle = 'REFUSE : '.$libelle;
                }
                $ajoutReportModif = '';
                if($etat == 'RP'){
                    $mois = moisSuivant($leMois);
                    $ajoutReportModif = " lignefraishorsforfait.mois = '$mois', ";
                    $etat = 'CL';
                }
                $req = "update lignefraishorsforfait set lignefraishorsforfait.date = '$date',$ajoutReportModif lignefraishorsforfait.libelle = '$libelle',
                                lignefraishorsforfait.montant = $montant, lignefraishorsforfait.idEtat = '$etat'
                         where lignefraishorsforfait.id = $unIdFrais";
                PdoGsb::$monPdo->exec($req);
            }
        }
/**
 * met � jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concern�
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);	
	}

/**
 * met � jour le montant valid� de la table ficheFrais
 * pour le mois et le visiteur concern�
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $montantValide montant valid�
*/
	public function majMontantValide($idVisiteur, $mois, $montantValide){
		$req = "update fichefrais set montantvalide = $montantValide 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);	
	}        
/**
 * Teste si un visiteur poss�de une fiche de frais pour le mois pass� en argument
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux 
*/	
	public function estPremierFraisMois($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}
/**
 * Retourne le dernier mois en cours d'un visiteur
 
 * @param $idVisiteur 
 * @return le mois sous la forme aaaamm
*/	
	public function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}
	
/**
 * Cr�e une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donn�s
 
 * r�cup�re le dernier mois en cours de traitement, met � 'CL' son champs idEtat, cr�e une nouvelle fiche de frais
 * avec un idEtat � 'CR' et cr�e les lignes de frais forfait de quantit�s nulles 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function creeNouvellesLignesFrais($idVisiteur,$mois){
		$dernierMois = $this->dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
                if($laDerniereFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais($idVisiteur, $dernierMois,'CL');
                                $this->majEtatFraisForfait($idVisiteur, $dernierMois, 'CL');
                                $this->majEtatFraisHorsForfait($idVisiteur, $dernierMois, 'CL');
				
		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
		
                PdoGsb::$monPdo->exec($req);
		$lesIdFrais = $this->getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
			PdoGsb::$monPdo->exec($req);
		 }
	}
/**
 * Cr�e un nouveau frais hors forfait pour un visiteur un mois donn�
 * � partir des informations fournies en param�tre
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format fran�ais jj//mm/aaaa
 * @param $montant : le montant
*/
	public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
		$dateFr = dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Supprime le frais hors forfait dont l'id est pass� en argument
 
 * @param $idFrais 
*/
	public function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
 * @param $idVisiteur 
 * @param string $etatFiche (opyionnel) Etat des fiches recherch�es. (ex 'VA', 'RB')
 * @return un tableau associatif de cl� un mois -aaaamm- et de valeurs l'ann�e et le mois correspondant 
*/
	public function getLesMoisDisponibles($idVisiteur, $etatFiche = ''){
            $ajoutReqEtat = '';
            if($etatFiche != ''){
                $ajoutReqEtat = 'and fichefrais.idEtat in ('.$etatFiche.') ';
            }
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' $ajoutReqEtat  
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
        
        /**
         * Retourne un tableau contenant tous les visiteurs de la base de donn�es
         
         * @return tableau contenant (id, nom, prenom) des visiteurs
         * @author Franck Noel
         */
        public function getLesVisiteurs(){
            $req = "select id, nom, prenom FROM visiteur WHERE typecompte=1";
            $res = PdoGsb::$monPdo->query($req);
            $lesVisiteurs = array();
            $laLigne = $res->fetch();
            while($laLigne != null){
                $lesVisiteurs[$laLigne['id']] = $laLigne; 
                $laLigne = $res->fetch();
            }
            return $lesVisiteurs;
        }
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donn�
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'�tat 
*/	
	public function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select fichefrais.idEtat as idEtat, fichefrais.idEtatForfait as idEtatForfait, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat, etatForfait.libelle as libEtatForfait from  fichefrais inner join etat on fichefrais.idEtat = etat.id inner join etat etatForfait on fichefrais.idEtatForfait = etatForfait.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}
/**
 * Modifie l'�tat et la date de modification d'une fiche de frais
 
 * Modifie le champ idEtat et met la date de modif � aujourd'hui
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update fichefrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}
        
        /**
 * Modifie l'�tat des frais Forfaits
 
 * Modifie le champ idEtatForfait
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
	public function majEtatFraisForfait($idVisiteur,$mois,$etat){
		$req = "update fichefrais set idEtatForfait = '$etat' 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}
        
        
        /**
         * Modifie l'etat de tous les hors forfait a la fois pour le mois et visiteur selectionn�
         
         * @param type $idVisiteur
         * @param type $mois sous la forme aaaamm
         * @param type $etat 
         */
        public function majEtatFraisHorsForfait($idVisiteur,$mois,$etat){
            $req = "update lignefraishorsforfait set idEtat = '$etat'
                      where lignefraishorsforfait.idVisiteur='$idVisiteur' and lignefraishorsforfait.mois='$mois'";
            PdoGsb::$monPdo->exec($req);
            
        }
}
?>