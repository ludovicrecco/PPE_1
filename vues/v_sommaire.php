<?php
/**
 * Vue conteneur de toutes les pages avec le menu de gauche
 
  * @author Recco Ludovic
  * @package default

 */
/**
 * 
 */
?>
<!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    
        <h2>
    
</h2>
    
      </div>  
        <ul id="menuList">
			<li >
				  <?php echo $_SESSION['typeName'] ?> :<br>
				<?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?>
			</li>
                        <?php if($_SESSION['type'] == 1){ ?>
           <li class="smenu">
              <a href="index.php?uc=gererFrais&action=saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
                        <?php }else if($_SESSION['type'] == 2){ ?>
           <li class="smenu">
              <a href="index.php?uc=validerFrais&action=selection" title="Saisie fiche de frais ">Valider fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="index.php?uc=suiviFrais&action=selection" title="Suivi remboursement">Suivi remboursement</a>
           </li> 
                        <?php } ?>
 	   <li class="smenu">
              <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Déconnexion</a>
           </li>
         </ul>
        
    </div>
<div id="contenu">