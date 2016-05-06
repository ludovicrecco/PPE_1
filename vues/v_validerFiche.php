<?php

/** 
 * Vue fiche de frais et validation
 
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
        <form action="index.php?uc=validerFrais&action=validerFiche&leVisiteur=<?php echo $leVisiteur['id'] ?>" method="POST">
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
                $estModifiable = $idEtatForfait == 'CL';
          foreach (  $lesFraisForfait as $unFraisForfait  ) 
		  {
				$quantite = $unFraisForfait['quantite'];
                                $idFrais = $unFraisForfait['idfrais'];
		?>
            <td class="qteForfait">
                <?php if($estModifiable){
                    ?>
                    <input type="text" name="txtQteForfait<?php echo $idFrais; ?>" value="<?php echo $quantite?>" enabled="false"/>
                    <?php
                }else{
                    echo $quantite;
                }
                ?>
                 </td>
		 <?php
          }
		?>
                <td>                    <?php echo $situation[$idEtatForfait]; 
                    if($idEtatForfait == 'CL'){
                    ?><br>
                    <input type="checkbox" name="validerEtatForfait" /> <label for="validerEtatForfait"> Valider</label>
                    <?php } ?></td>
		</tr>
    </table>
  	<table class="listeLegere" style="margin:0;">
  	   <caption>Descriptif des éléments hors forfait
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle" style="width: 57%;">Libellé</th>
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
                        $estModifiable = $idEtatHorsForfait == 'CL';
		?>
             <tr>
                <td>
                    <?php if($estModifiable){
                        ?>
                    <input type="text" style="width: 100%;" name="txtHF<?php echo $id;?>date" value="<?php echo $date ?>" />
                           
                            <?php
                    }else{
                         echo $date; 
                    } ?>
                    </td>
                <td><?php if($estModifiable){
                        ?>
                    <input type="text" style="width: 100%;" name="txtHF<?php echo $id;?>libelle" value="<?php echo $libelle ?>" />
                           
                            <?php
                    }else{
                         echo $libelle; 
                    } ?></td>
                <td><?php if($estModifiable){
                        ?>
                    <input type="text" style="width: 100%;" name="txtHF<?php echo $id;?>montant" value="<?php echo $montant ?>" />
                           
                            <?php
                    }else{
                         echo $montant; 
                    } ?></td>
                <td style="padding: 0.25em">
                    <?php //echo $libEtatHorsForfait; 
                    if($idEtatHorsForfait == 'CL'){
                    ?>
                    <input type="checkbox" name="validerEtatHorsForfait<?php echo $id;?>" onchange="selectOne<?php echo $id;?>(this);" /> <label for="validerEtatHorsForfait<?php echo $id;?>"> Valider</label><br>
                    <input type="checkbox" name="refuserEtatHorsForfait<?php echo $id;?>" onchange="selectOne<?php echo $id;?>(this);" /> <label for="refuserEtatHorsForfait<?php echo $id;?>"> Refuser</label><br>
                    <input type="checkbox" name="reporterEtatHorsForfait<?php echo $id;?>" onchange="selectOne<?php echo $id;?>(this);" /> <label for="reporterEtatHorsForfait<?php echo $id;?>"> Reporter</label>
                    <script>
                    function selectOne<?php echo $id;?>(obj){
                        var id = '<?php echo $id;?>';
                        if(obj.checked === true){
                            var obj1 = document.getElementsByName('validerEtatHorsForfait'+id)[0];
                            var obj2 = document.getElementsByName('refuserEtatHorsForfait'+id)[0];
                            var obj3 = document.getElementsByName('reporterEtatHorsForfait'+id)[0];
                            obj1.checked = (obj === obj1);
                            obj2.checked = (obj === obj2);
                            obj3.checked = (obj === obj3);
                        }
                    }
                    </script>
                        <?php }else{
                        echo $libEtatHorsForfait;
                    } ?>
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
                <?php 
                    $estModifiable = $idEtat == 'CL';
                ?>
                <td>
                    <?php
                    if($estModifiable){
                        ?>
                    <input type="text" style="width: 60px;" name="txtNbJustificatifs" value="<?php echo $nbJustificatifs ?>" />
                            <?php
                    }else{
                        echo $nbJustificatifs;
                    }
                    ?>
                        </td>
                <td><?php
                    if($estModifiable){
                        ?>
                    <input type="text" name="txtMontantValide" value="<?php echo $montantValide ?>" />
                            <?php
                    }else{
                        echo $montantValide;
                    }
                    ?></td>
                <td>
                    <?php echo $situation[$idEtat] ;
                            if($idEtat == 'CL'){
                                ?> <br>
                            <input type="checkbox" name="validerEtat" /> <label for="validerEtat"> Valider</label>
                            <?php } ?>
                </td>
            </tr>
        </table>
            <?php if($idEtat == 'CL'){ ?>
              <div>
      <p>
          <input type="hidden" name="lstMois" value="<?php echo $leMois; ?>" />
          <input type="hidden" name="enregistrer" value="true" />
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
            <?php } ?>
        </form>
    </div>