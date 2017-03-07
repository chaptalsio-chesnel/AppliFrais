<?php
$this->load->helper ( 'url' );
?>
<div id="contenu">
	<h2>Renseigner ma fiche de frais du mois <?php echo $numMois."-".$numAnnee; ?></h2>
	<div class="corpsForm">
		<fieldset>
			<legend>Eléments forfaitisés</legend>
			<table>
				<thead>
					<th></th><th>Quantité</th><th>Montant*</th><th>Total</th>
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
									<td><input onchange="montant(this)" type="text" id="' . $idFrais . '" name="lesFrais[' . $idFrais . ']" size="10" maxlength="5" value="' . $quantite . '" /></td>
									<td id="montant'.$idFrais.'">'.$montant.'</td>
									<td id="total'.$idFrais.'"></td>
							</tr>
							';
					}
					?>
					<tr> <td></td><td></td><td>Total frais forfitisés</td><td id="total"></td></tr>
				</tbody>
			</table>
		</fieldset>
		<p></p>
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