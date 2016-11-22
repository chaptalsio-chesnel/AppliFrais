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
	 * Retourne les informations d'un visiteur
	 * 
	 * @param $login 
	 * @param $mdp
	 * @return l'id, le nom et le pr�nom sous la forme d'un tableau associatif 
	*/
	public function getInfosVisiteur($login, $mdp){
		$req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom ,visiteur.Comptable as Comptable
				from visiteur 
				where visiteur.login=? and visiteur.mdp=?";
		$rs = $this->db->query($req, array ($login, $mdp));
		$ligne = $rs->first_row('array'); 
		return $ligne;
	}
	/**
	 * Retourne les informations d'un visiteur
	 *
	 * @param $login
	 * @param $mdp
	 * @return le comptable
	 */
	public function getCompVisiteur($login, $mdp){
		$req = "select visiteur.comptable as comp
				from visiteur
				where visiteur.login=? and visiteur.mdp=?";
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
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
	*/
	public function getLesLignesHorsForfait($idVisiteur,$mois){
		$this->load->model('functionsLib');

		$req = "select * 
				from lignefraishorsforfait 
				where lignefraishorsforfait.idvisiteur ='$idVisiteur' 
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
	 * Retourne le nombre de justificatif d'un visiteur pour un mois donn�
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @return le nombre entier de justificatifs 
	*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb 
				from  fichefrais 
				where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->result_array();
		return $laLigne['nb'];
	}
		
	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
	 * concern�es par les deux arguments
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @return l'id, le libelle et la quantit� sous la forme d'un tableau associatif 
	*/
	public function getLesLignesForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, lignefraisforfait.quantite as quantite 
				from lignefraisforfait inner join fraisforfait 
					on fraisforfait.id = lignefraisforfait.idfraisforfait
				where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
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
	 * Met à jour la table ligneFraisForfait pour un visiteur et
	 * un mois donn� en enregistrant les nouveaux montants
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @param $lesFrais tableau associatif de cl� idFrais et de valeur la quantit� pour ce frais
	*/
	public function majLignesForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait 
					set lignefraisforfait.quantite = $qte
					where lignefraisforfait.idvisiteur = '$idVisiteur' 
						and lignefraisforfait.mois = '$mois'
						and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			$this->db->simple_query($req);
		}
	}
		
	/**
	 * met à jour le nombre de justificatifs de la table ficheFrais
	 * pour le mois et le visiteur concern�
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais 
				set nbjustificatifs = $nbJustificatifs 
				where fichefrais.idvisiteur = '$idVisiteur' 
					and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);	
	}
		
	/**
	 * Teste si un visiteur possède une fiche de frais pour le mois pass� en argument
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @return vrai si la fiche existe, ou faux sinon
	*/	
	public function existeFiche($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais 
				from fichefrais 
				where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		if($laLigne['nblignesfrais'] != 0){
			$ok = true;
		}
		return $ok;
	}
	
	/**
	 * Cr�e une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donn�s
	 * L'�tat de la fiche est mis à 'CR'
	 * Lles lignes de frais forfait sont affect�es de quantit�s nulles et du montant actuel de FraisForfait
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	*/
	public function creeFiche($idVisiteur,$mois){
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
				values('$idVisiteur','$mois',0,0,now(),'CR')";
		$this->db->simple_query($req);
		$lesFF = $this->getLesFraisForfait();
		foreach($lesFF as $uneLigneFF){
			$unIdFrais = $uneLigneFF['idfrais'];
			$montantU = $uneLigneFF['montant'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite, montantApplique) 
					values('$idVisiteur','$mois','$unIdFrais',0, $montantU)";
			$this->db->simple_query($req);
		 }
	}

	/**
	 * Signe une fiche de frais en modifiant son �tat de "CR" à "CL"
	 * Ne fait rien si l'�tat initial n'est pas "CR"
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	*/
	public function signeFiche($idVisiteur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idVisiteur,$mois);
		if($laFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais($idVisiteur, $mois,'CL');
		}
	}	
	public function mpFiche($idVisiteur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idVisiteur,$mois);
		if($laFiche['idEtat']=='VA'){
			$this->majEtatFicheFrais($idVisiteur, $mois,'MP');
		}
	}
	public function rembourserFiche($idVisiteur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idVisiteur,$mois);
		if($laFiche['idEtat']=='MP'){
			$this->majEtatFicheFrais($idVisiteur, $mois,'RB');
		}
	}
	public function validerFiche($idVisiteur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idVisiteur,$mois);
		if($laFiche['idEtat']=='CL'){
				$this->majEtatFicheFrais($idVisiteur, $mois,'VA');
		}
	}
	public function refuserFiche($idVisiteur,$mois){
		//met à 'CL' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idVisiteur,$mois);
		if($laFiche['idEtat']=='CL'){
			$this->majEtatFicheFrais($idVisiteur, $mois,'CR');
		}
	}

	/**
	 * Cr�e un nouveau frais hors forfait pour un visiteur un mois donn�
	 * à partir des informations fournies en paramètre
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @param $libelle : le libelle du frais
	 * @param $date : la date du frais au format français jj//mm/aaaa
	 * @param $montant : le montant
	*/
	public function creeLigneHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
		$this->load->model('functionsLib');
		
		$dateFr = $this->functionsLib->dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
				values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
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
	 * Retourne les mois pour lesquel un visiteur a une fiche de frais
	 * 
	 * @param $idVisiteur 
	 * @return un tableau associatif de cl� un mois -aaaamm- et de valeurs l'ann�e et le mois correspondant 
	*/
	public function getLesMoisDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois 
				from  fichefrais 
				where fichefrais.idvisiteur ='$idVisiteur' 
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
	 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donn�
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'�tat 
	*/	
	public function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, 
					ficheFrais.nbJustificatifs as nbJustificatifs, ficheFrais.montantValide as montantValide, etat.libelle as libEtat 
				from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
				where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		return $laLigne;
	}

	/**
	 * Modifie l'�tat et la date de modification d'une fiche de frais
	 * 
	 * @param $idVisiteur 
	 * @param $mois sous la forme aaaamm
	 * @param $etat : le nouvel �tat de la fiche 
	 */
	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update ficheFrais 
				set idEtat = '$etat', dateModif = now() 
				where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);
	}
	
	/**
	 * Obtient toutes les fiches (sans d�tail) d'un visiteur donn� 
	 * 
	 * @param $idVisiteur 
	*/
	public function getFiches ($idVisiteur) {
		$req = "select idVisiteur, mois, montantValide, dateModif, id, libelle
				from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
				where fichefrais.idvisiteur = '$idVisiteur'
				order by mois desc";
		$rs = $this->db->query($req);
		$lesFiches = $rs->result_array();
		return $lesFiches;
	}
	/**
	 * Obtient toutes les fiches (sans d�tail) d'un visiteur donn�
	 *
	 * @param $idVisiteur
	 */
	public function getLesFiches ($idVisiteur) {
		$req = "select idVisiteur, mois, montantValide, dateModif, Etat.id, libelle, nom, visiteur.id as idv
		from  visiteur, fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
		where visiteur.id = fichefrais.idVisiteur
		order by nom asc, mois desc";
		$rs = $this->db->query($req);
		$lesFiches = $rs->result_array();
		return $lesFiches;
	}
	
	/**
	 * Calcule le montant total de la fiche pour un visiteur et un mois donn�s
	 * 
	 * @param $idVisiteur 
	 * @param $mois
	 * @return le montant total de la fiche
	*/
	public function totalFiche ($idVisiteur, $mois) {
		// obtention du total hors forfait
		$req = "select SUM(montant) as totalHF
				from  lignefraishorsforfait 
				where idvisiteur = '$idVisiteur'
					and mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		$totalHF = $laLigne['totalHF'];
		
		// obtention du total forfaitis�
		$req = "select SUM(montantApplique * quantite) as totalF
				from  lignefraisforfait 
				where idvisiteur = '$idVisiteur'
					and mois = '$mois'";
		$rs = $this->db->query($req);
		$laLigne = $rs->first_row('array');
		$totalF = $laLigne['totalF'];

		return $totalHF + $totalF;
	}

	/**
	 * Modifie le montantValide et la date de modification d'une fiche de frais
	 * 
	 * @param $idVisiteur : l'id du visiteur
	 * @param $mois : mois sous la forme aaaamm
	 */
	public function recalculeMontantFiche($idVisiteur,$mois){
	
		$totalFiche = $this->totalFiche($idVisiteur,$mois);
		$req = "update ficheFrais 
				set montantValide = '$totalFiche', dateModif = now() 
				where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$this->db->simple_query($req);
	}
}
?>