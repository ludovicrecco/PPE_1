<?php
/**
 * Vue d'affichage des erreurs
 
 * Affiche les erreurs prises en charge sur la page en cours
 * @package default
 * @author GSB
 */
?>

<div class ="erreur">
<ul>
<?php 
foreach($_REQUEST['erreurs'] as $erreur)
	{
      echo "<li>$erreur</li>";
	}
?>
</ul></div>
