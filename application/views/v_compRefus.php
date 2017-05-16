<?php
$raison="";
$this->load->helper ( 'url' );
?>
<div id="contenu">
	<h2>Refus de la fiche de l'utilisateur <?php  echo $util. " du " .$numMois ." ". $numAnnee ?></h2>
	<p>Raison du refus de la fiche de frais : </p>
	<form method="post" action="<?php echo base_url("c_comptable/refuserFiche/". $mois . '/' . $util);?>">
	<textarea name ="raison" rows="4" cols="50" onKeyUp="maxL(this, 255);">
	</textarea>
	<br/>
<input id="ok" type="submit" value="Enregistrer" size="20" /> <input
					id="annuler" type="reset" value="Effacer" size="20" />
					</form>
</div>