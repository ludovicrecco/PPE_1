<?php

/** 
 * Vue de suivi fiche de frais pour le remboursement des frais
 
  * @author Recco Ludovic
  * @package default

 */



/**
 * 
 */
?>

<h3>Fiche de frais de <?php echo $leVisiteur['nom'] . ' '. $leVisiteur['prenom'];?> du mois <?php echo $numMois."-".$numAnnee?> : 
    </h3>
<h4><?php echo $enregistrementErreur;?> <h4/>
    <div class="encadre">
        <form action="index.php?uc=suiviFrais&action=suiviFiche&leVisiteur=<?php echo $leVisiteur['id'] ?>" method="POST">
  	<table class="listeLegere">
  	   <caption>Eléments forfaitisés </caption>
        <tr>
         <?php
         foreach ( $lesFraisForfait as $unFraisForfait ) 
		 {
			$libelle = $unFraisForfait['libelle'];
		?>	
			<th> <?php echo $libelle?></th>
		 <?php
        }
		?>
                        <th>Situation</th>
		</tr>
        <tr>
        <?php
          foreach (  $lesFraisForfait as $unFraisForfait  ) 
		  {
				$quantite = $unFraisForfait['quantite'];
                                $idFrais = $unFraisForfait['idfrais'];
		?>
            <td class="qteForfait">
                   <?php echo $quantite;
                ?>
                 </td>
		 <?php
          }
		?>
                <td>                    <?php echo $situation[$idEtatForfait]; ?></td>
		</tr>
    </table>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>  
                <th>Situation</th>
             </tr>
        <?php      
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {
                        $id = $unFraisHorsForfait['id'];
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
			$montant = $unFraisHorsForfait['montant'];
                        $idEtatHorsForfait = $unFraisHorsForfait['idEtat'];
                        $libEtatHorsForfait = $situation[$idEtatHorsForfait];
		?>
             <tr>
                <td>
                    <?php      echo $date; 
                    ?>
                    </td>
                <td><?php
                         echo $libelle; 
                     ?></td>
                <td><?php 
                         echo $montant; 
                     ?></td>
                <td>
                    <?php echo $libEtatHorsForfait; 
                     ?>
                </td>
             </tr>
        <?php 
          }
		?>
    </table>
        <table class="listeLegere">
            <caption>Fiche de frais</caption>
            <tr>
                <th>Nb justificatifs</th>
                <th>Montant</th>
                <th>Situation</th>
            </tr>
            <tr>
                <td>
                    <?php
                        echo $nbJustificatifs;
                    
                    ?>
                        </td>
                <td><?php echo $montantValide;
                    
                    ?></td>
                <td>
                    <?php echo $situation[$idEtat] ;?>
                </td>
            </tr>
        </table>
            <?php if($idEtat == 'VA' || $idEtat == 'EP'){ ?>
              <div>
      <p>
          <input type="hidden" name="lstMois" value="<?php echo $leMois; ?>" />
          <input type="hidden" name="enregistrer" value="true" />
          <?php
          if($idEtat == 'VA'){ ?>
          <input type="submit" value="Mettre en paiement" />
          <?php }elseif($idEtat == 'EP'){ ?>
          <input type="submit" value="Remboursée" />
          <?php } ?>
      </p> 
      </div>
            <?php } ?>
        </form>
    </div>
