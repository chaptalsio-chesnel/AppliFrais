<?php
$this->load->helper ( 'url' );
?>

<div id="contenu">
	<h2>Modifier la fiche de frais du mois <?php echo $numMois."-".$numAnnee; ?></h2>

	<div class="corpsForm">
<form method="post" onsubmit="return valider();"
		action="<?php echo base_url("c_comptable/majForfait/$util");?>">
		<fieldset>
			<table>
				<thead>
					<th></th><th>Quantité</th><th>Montant*</th>
				</thead>
				<tbody>
					<?php
					foreach ( $lesFraisForfait as $unFrais ) {
						$idFrais = $unFrais ['idfrais'];
						$libelle = $unFrais ['libelle'];
						$quantite = $unFrais ['quantite'];
						$montant = $unFrais ['montant'];
						
						
						echo '<tr>
									<td><label for="' . $idFrais . '">' . $libelle . '</label></td>
									<td><input onchange="montant(this)" type="text" id="' . $idFrais . '" name="lesFrais[' . $idFrais . ']" class="input" size="10" maxlength="5" value="' . $quantite . '" />' . $quantite .'</td>
									<td><input onchange="montant(this)" type="text" id="' . $idFrais . '" name="lesMontant[' . $idFrais . ']" size="10" maxlength="10" value="' . $montant . '" /></td>
									
							</tr>
							';
					}
					?>
					
				</tbody>
			</table>
		</fieldset>
			<div class="piedForm">
			<p>
				<input id="ok" type="submit" value="Enregistrer" size="20" /> <input
				id="annuler" type="reset" value="Effacer" size="20" />
			</p>
		</div>
	</form>
	</div>


	<table class="listeLegere">
		<caption>Descriptif des éléments hors forfait</caption>
		<tr>
			<th>Date</th>
			<th>Libellé</th>
			<th>Montant</th>
			<th>&nbsp;</th>
		</tr>
          
		<?php
		foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) {
			$libelle = $unFraisHorsForfait ['libelle'];
			$date = $unFraisHorsForfait ['date'];
			$montant = $unFraisHorsForfait ['montant'];
			$id = $unFraisHorsForfait ['id'];
			echo '<tr>
					<td class="date">' . $date . '</td>
					<td class="libelle">' . $libelle . '</td>
					<td class="montant">' . $montant . '</td>
					<td class="action">' . anchor ( "c_visiteur/supprFrais/$id", "Supprimer ce frais", 'title="Suppression d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment supprimer ce frais ?\');"' ) . '</td>
				</tr>';
		}
		?>	  
                                          
    </table>

</div>
