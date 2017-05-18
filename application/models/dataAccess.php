<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Modèle qui impl�mente les fonctions d'accès aux donn�es 
*/
class DataAccess extends CI_Model {
// TODO : Transformer toutes les requêtes en requêtes param�tr�es

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    /**
	 * Retourne les informations d'un utilisateur
	 * 
	 * @param $login 
	 * @param $mdp
	 * @return l'id, le nom et le pr�nom sous la forme d'un tableau associatif 
	*/
	public function getInfosutilisateur($login, $mdp){
		$req = "select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom ,utilisateur.statut as statut
				from utilisateur 
				where utilisateur.login=? and utilisateur.mdp=?";
		$rs = $this->db->query($req, array ($login, $mdp));
		$ligne = $rs->first_row('array'); 
		return $ligne;
	}
	/**
	 * Retourne les informations d'un utilisateur
	 *
	 * @param $login
	 * @param $mdp
	 * @return le role
	 */
	public function getComputilisateur($login, $mdp){
		$req = "select utilisateur.statut as statut
				from utilisateur
				where utilisateur.login=? and utilisateur.mdp=?";
		$rs = $this->db->query($req, array ($login, $mdp));
		$ligne = $rs->first_row('array');
		return $ligne;
	}
	

	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
	 * concern�es par les deux arguments
	 * La boucle foreach ne peut être utilis�e ici car on procède
	 * à une modification de la structure it�r�e - transformation du champ date-
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
	*/
	public function getLesLignesHorsForfait($idUtilisateur,$mois){
		$this->load->model('functionsLib');

		$req = "select * 
				from lignefraishorsforfait 
				where lignefraishorsforfait.idUtilisateur ='$idUtilisateur' 
					and lignefraishorsforfait.mois = '$mois' ";	
		$rs = $this->db->query($req);
		$lesLignes = $rs->result_array();
		$nbLignes = $rs->num_rows();
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  $this->functionsLib->dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
		
	/**
	 * Retourne le nombre de justificatif d'un utilisateur pour un mois donn�
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @return le nombre entier de justificatifs 
	*/
	public function getNbjustificatifs($idUtilisateur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb 
				from  fichefrais 
				where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->result_array();
		return $laLigne['nb'];
	}
		
	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
	 * concern�es par les deux arguments
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @return l'id, le libelle et la quantit� sous la forme d'un tableau associatif 
	*/
	public function getLesLignesForfaitVis($idUtilisateur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, lignefraisforfait.quantite as quantite, lignefraisforfait.montantApplique as montant
				from lignefraisforfait inner join fraisforfait 
					on fraisforfait.id = lignefraisforfait.idfraisforfait
				where lignefraisforfait.idUtilisateur ='$idUtilisateur' and lignefraisforfait.mois='$mois' 
				order by lignefraisforfait.idfraisforfait";	
		$rs = $this->db->query($req);
		$lesLignes = $rs->result_array();
		return $lesLignes; 
	}
	public function getLesLignesForfaitComp($idUtilisateur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, lignefraisforfait.quantite as quantite, lignefraisforfait.montantApplique as montant
		from lignefraisforfait inner join fraisforfait
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idUtilisateur ='$idUtilisateur' and lignefraisforfait.mois='$mois'
		order by lignefraisforfait.idfraisforfait";
		$rs = $this->db->query($req);
		$lesLignes = $rs->result_array();
		return $lesLignes;
	}
	
		
	/**
	 * Retourne tous les FraisForfait
	 * 
	 * @return un tableau associatif contenant les fraisForfaits
	*/
	public function getLesFraisForfait(){
		$req = "select fraisforfait.id as idfrais, libelle, montant from fraisforfait order by fraisforfait.id";
		$rs = $this->db->query($req);
		$lesLignes = $rs->result_array();
		return $lesLignes;
	}
	
	/**
	 * Met à jour la table ligneFraisForfait pour un utilisateur et
	 * un mois donn� en enregistrant les nouveaux montants
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @param $lesFrais tableau associatif de cl� idFrais et de valeur la quantit� pour ce frais
	*/
	public function majLignesForfait($idUtilisateur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait 
					set lignefraisforfait.montantApplique = $qte
					where lignefraisforfait.idUtilisateur = '$idUtilisateur' 
						and lignefraisforfait.mois = '$mois'
						and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			$this->db->simple_query($req);
		}
	}
	public function majLignesForfaitVis($idUtilisateur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait
			set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idUtilisateur = '$idUtilisateur'
			and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			$this->db->simple_query($req);
		}
	}
		
	/**
	 * met à jour le nombre de justificatifs de la table ficheFrais
	 * pour le mois et le utilisateur concern�
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	*/
	public function majNbJustificatifs($idUtilisateur, $mois, $nbJustificatifs){
		$req = "update fichefrais 
				set nbjustificatifs = $nbJustificatifs 
				where fichefrais.idUtilisateur = '$idUtilisateur' 
					and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);	
	}
		
	/**
	 * Teste si un utilisateur possède une fiche de frais pour le mois pass� en argument
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @return vrai si la fiche existe, ou faux sinon
	*/	
	public function existeFiche($idUtilisateur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais 
				from fichefrais 
				where fichefrais.mois = '$mois' and fichefrais.idUtilisateur = '$idUtilisateur'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		if($laLigne['nblignesfrais'] != 0){
			$ok = true;
		}
		return $ok;
	}
	
	/**
	 * Cr�e une nouvelle fiche de frais et les lignes de frais au forfait pour un utilisateur et un mois donn�s
	 * L'�tat de la fiche est mis à 'CR'
	 * Lles lignes de frais forfait sont affect�es de quantit�s nulles et du montant actuel de FraisForfait
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	*/
	public function creeFiche($idUtilisateur,$mois){
		$req = "insert into fichefrais(idUtilisateur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
				values('$idUtilisateur','$mois',0,0,now(),'CR')";
		$this->db->simple_query($req);
		$lesFF = $this->getLesFraisForfait();
		foreach($lesFF as $uneLigneFF){
			$unIdFrais = $uneLigneFF['idfrais'];
			$montantU = $uneLigneFF['montant'];
			$req = "insert into lignefraisforfait(idUtilisateur,mois,idFraisForfait,quantite, montantApplique) 
					values('$idUtilisateur','$mois','$unIdFrais',0, $montantU)";
			$this->db->simple_query($req);
		 }
	}

	/**
	 * Signe une fiche de frais en modifiant son �tat de "CR" à "CL"
	 * Ne fait rien si l'�tat initial n'est pas "CR"
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	*/
	public function signeFiche($idUtilisateur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur,$mois);
		if($laFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais($idUtilisateur, $mois,'CL');
		}
	}	
	public function mpFiche($idUtilisateur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur,$mois);
		if($laFiche['idEtat']=='VA'){
			$this->majEtatFicheFrais($idUtilisateur, $mois,'MP');
		}
	}
	public function rembourserFiche($idUtilisateur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur,$mois);
		if($laFiche['idEtat']=='MP'){
			$this->majEtatFicheFrais($idUtilisateur, $mois,'RB');
		}
	}
	public function validerFiche($idUtilisateur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur,$mois);
		if($laFiche['idEtat']=='CL'){
				$this->majEtatFicheFrais($idUtilisateur, $mois,'VA');
		}
	}
	public function refuserFiche($idUtilisateur,$mois,$raison){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur,$mois);
		if($laFiche['idEtat']=='CL'){
			$this->majEtatFicheFrais($idUtilisateur, $mois,'CR');
			$this->majRaison($idUtilisateur, $mois,$raison);
		}
	}

	/**
	 * Cr�e un nouveau frais hors forfait pour un utilisateur un mois donn�
	 * à partir des informations fournies en paramètre
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @param $libelle : le libelle du frais
	 * @param $date : la date du frais au format français jj//mm/aaaa
	 * @param $montant : le montant
	*/
	public function creeLigneHorsForfait($idUtilisateur,$mois,$libelle,$date,$montant){
		$this->load->model('functionsLib');
		
		$dateFr = $this->functionsLib->dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
				values('','$idUtilisateur','$mois','$libelle','$dateFr','$montant')";
		$this->db->simple_query($req);
	}
		
	/**
	 * Supprime le frais hors forfait dont l'id est pass� en argument
	 * 
	 * @param $idFrais 
	*/
	public function supprimerLigneHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait 
				where lignefraishorsforfait.id =$idFrais ";
		$this->db->simple_query($req);
	}

	/**
	 * Retourne les mois pour lesquel un utilisateur a une fiche de frais
	 * 
	 * @param $idUtilisateur 
	 * @return un tableau associatif de cl� un mois -aaaamm- et de valeurs l'ann�e et le mois correspondant 
	*/
	public function getLesMoisDisponibles($idUtilisateur){
		$req = "select fichefrais.mois as mois 
				from  fichefrais 
				where fichefrais.idUtilisateur ='$idUtilisateur' 
				order by fichefrais.mois desc ";
		$rs = $this->db->query($req);
		$lesMois =array();
		$laLigne = $rs->first_row('array');
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee = substr( $mois,0,4);
			$numMois = substr( $mois,4,2);
			$lesMois["$mois"] = array(
				"mois"=>"$mois",
				"numAnnee"  => "$numAnnee",
				"numMois"  => "$numMois"
			 );
			$laLigne = $rs->next_row('array'); 		
		}
		return $lesMois;
	}

	/**
	 * Retourne les informations d'une fiche de frais d'un utilisateur pour un mois donn�
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'�tat 
	*/	
	public function getLesInfosFicheFrais($idUtilisateur,$mois){
		$req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, 
					ficheFrais.nbJustificatifs as nbJustificatifs, ficheFrais.montantValide as montantValide, etat.libelle as libEtat 
				from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
				where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		return $laLigne;
	}

	/**
	 * Modifie l'�tat et la date de modification d'une fiche de frais
	 * 
	 * @param $idUtilisateur 
	 * @param $mois sous la forme aaaamm
	 * @param $etat : le nouvel �tat de la fiche 
	 */
	public function majEtatFicheFrais($idUtilisateur,$mois,$etat){
		$req = "update ficheFrais 
				set idEtat = '$etat', dateModif = now() 
				where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);
	}
	public function majRaison($idUtilisateur,$mois,$raison){
		$req = "update ficheFrais
		set raison ='$raison', dateModif = now()
		where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);
	}
	
	
	/**
	 * Obtient toutes les fiches (sans d�tail) d'un utilisateur donn� 
	 * 
	 * @param $idUtilisateur 
	*/
	public function getFiches ($idUtilisateur) {
		$req = "select idUtilisateur, mois, montantValide, dateModif, id, libelle
				from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
				where fichefrais.idUtilisateur = '$idUtilisateur'
				order by mois desc";
		$rs = $this->db->query($req);
		$lesFiches = $rs->result_array();
		return $lesFiches;
	}
	/**
	 * Obtient toutes les fiches (sans d�tail) d'un utilisateur donn�
	 *
	 * @param $idUtilisateur
	 */
	public function getLesFiches ($idUtilisateur) {
		$req = "select idUtilisateur, mois, montantValide, dateModif, Etat.id, libelle, nom, utilisateur.id as idv
		from  utilisateur, fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
		where utilisateur.id = fichefrais.idUtilisateur
		order by nom asc, mois desc";
		$rs = $this->db->query($req);
		$lesFiches = $rs->result_array();
		return $lesFiches;
	}
	
	/**
	 * Calcule le montant total de la fiche pour un utilisateur et un mois donn�s
	 * 
	 * @param $idUtilisateur 
	 * @param $mois
	 * @return le montant total de la fiche
	*/
	public function totalFiche ($idUtilisateur, $mois) {
		// obtention du total hors forfait
		$req = "select SUM(montant) as totalHF
				from  lignefraishorsforfait 
				where idUtilisateur = '$idUtilisateur'
					and mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		$totalHF = $laLigne['totalHF'];
		
		// obtention du total forfaitis�
		$req = "select SUM(montantApplique * quantite) as totalF
				from  lignefraisforfait 
				where idUtilisateur = '$idUtilisateur'
					and mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		$totalF = $laLigne['totalF'];

		return $totalHF + $totalF;
	}

	/**
	 * Modifie le montantValide et la date de modification d'une fiche de frais
	 * 
	 * @param $idUtilisateur : l'id du utilisateur
	 * @param $mois : mois sous la forme aaaamm
	 */
	public function recalculeMontantFiche($idUtilisateur,$mois){
	
		$totalFiche = $this->totalFiche($idUtilisateur,$mois);
		$req = "update ficheFrais 
				set montantValide = '$totalFiche', dateModif = now() 
				where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);
	}
	public function getLaRaison($idUtilisateur,$mois){
		$req = "select raison as raison
		from  fichefrais
		where fichefrais.idUtilisateur ='$idUtilisateur' and fichefrais.mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		return $laLigne;
	}
}
?>