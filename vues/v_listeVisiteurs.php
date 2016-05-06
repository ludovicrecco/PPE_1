<?php

/** 
 * Vue de selection du visiteur
 
 * @author Recco Ludovic
 * @package default

 */

/**
 * 
 */
?>

<div id="selVisiteur">
    <h3>Visiteur a selectionner : </h3>
    <form action="index.php?uc=<?php echo $_REQUEST['uc']?>&action=<?php echo $actionListeMois;?>" method="post">
      <div class="corpsForm">
         
      <p>
	 
        <label for="lstVisiteur" accesskey="n">Visiteur : </label>
        <select id="lstVisiteur" name="lstVisiteur">
            <?php
			foreach ($lesVisiteurs as $unVisiteur)
			{
                            ?>
                          <option <?php if($leVisiteurASelectionner == $unVisiteur['id']){echo 'selected=true';}?> value="<?php echo $unVisiteur['id'] ?>"><?php echo  $unVisiteur['nom']. ' '. $unVisiteur['prenom'] ?> </option>
                          <?php
			
			}
           
		   ?>    
            
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
    </form>
</div>