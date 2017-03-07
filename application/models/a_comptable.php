<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class A_comptable extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

		// chargement du modÃ¨le d'accÃ¨s aux données qui est utile Ã  toutes les méthodes
		$this->load->model('dataAccess');
    }

	/**
	 * Accueil du utilisateur
	 * La fonction intÃ¨gre un mécanisme de contrÃ´le d'existence des 
	 * fiches de frais sur les 6 derniers mois. 
	 * Si l'une d'elle est absente, elle est créée
	*/
	public function accueil()
	{	// TODO : ContrÃ´ler que toutes les valeurs de $unMois sont valides (chaine de caractÃ¨re dans la BdD)
	
		// chargement du modÃ¨le contenant les fonctions génériques
		$this->load->model('functionsLib');

		// obtention de la liste des 6 derniers mois (y compris celui ci)
		$lesMois = $this->functionsLib->getSixDerniersMois();
		
		// obtention de l'id de l'utilisateur mémorisé en session
		$idutilisateur = $this->session->userdata('idUser');
		
		// contrÃ´le de l'existence des 6 derniÃ¨res fiches et création si nécessaire
		foreach ($lesMois as $unMois){
			if(!$this->dataAccess->ExisteFiche($idutilisateur, $unMois)) $this->dataAccess->creeFiche($idutilisateur, $unMois);
		}
		// envoie de la vue accueil du utilisateur
		$this->templates->load('t_comptable', 'v_visAccueil');
	}
	
	/**
	 * Liste les fiches existantes du utilisateur connecté et 
	 * donne accÃ¨s aux fonctionnalités associées
	 *
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $message : message facultatif destiné Ã  notifier l'utilisateur du résultat d'une action précédemment exécutée
	*/
	public function lesFiches ($idutilisateur, $message=null)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	
		$idutilisateur = $this->session->userdata('idUser');

		$data['notify'] = $message;
		$data['lesFiches'] = $this->dataAccess->getLesFiches($idutilisateur);		
		$this->templates->load('t_comptable', 'v_compLesFiches', $data);	
	}	
	public function lesSuivi ($idutilisateur, $message=null)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	
	$idutilisateur = $this->session->userdata('idUser');
	
	$data['notify'] = $message;
	$data['lesFiches'] = $this->dataAccess->getLesFiches($idutilisateur);
	$this->templates->load('t_comptable', 'v_compSuiviFiche', $data);
	}
	/**
	 * Présente le détail de la fiche sélectionnée 
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche Ã  modifier 
	*/
	public function voirFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session

		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idutilisateur,$mois);		

		$this->templates->load('t_comptable', 'v_visVoirListeFrais', $data);
	}

	/**
	 * Présente le détail de la fiche sélectionnée et donne 
	 * accés Ã  la modification du contenu de cette fiche.
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche Ã  modifier 
	 * @param $message : message facultatif destiné Ã  notifier l'utilisateur du résultat d'une action précédemment exécutée
	*/
	public function modCompFiche($idutilisateur, $mois, $message=null)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session

		$data['notify'] = $message;
		$data['util'] = $idutilisateur;
		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idutilisateur,$mois);		

		$this->templates->load('t_comptable', 'v_compModListeFrais', $data);
	}
	public function voirCompFiche($idutilisateur, $mois, $message=null)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	
	$data['notify'] = $message;
	$data['util'] = $idutilisateur;
	$data['numAnnee'] = substr( $mois,0,4);
	$data['numMois'] = substr( $mois,4,2);
	$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
	$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idutilisateur,$mois);
	
	$this->templates->load('t_comptable', 'v_compVoirFrais', $data);
	}

	/**
	 * Signe une fiche de frais en changeant son état
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche Ã  signer
	*/
	public function signeFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
		// TODO : intégrer une fonctionnalité d'impression PDF de la fiche

	    $this->dataAccess->signeFiche($idutilisateur, $mois);
	}
	public function mpFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	// TODO : intégrer une fonctionnalité d'impression PDF de la fiche
	
	$this->dataAccess->mpFiche($idutilisateur, $mois);
	}
	public function rembourserFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	// TODO : intégrer une fonctionnalité d'impression PDF de la fiche
	
	$this->dataAccess->rembourserFiche($idutilisateur, $mois);
	}

	public function validerFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	// TODO : intégrer une fonctionnalité d'impression PDF de la fiche
	
	$this->dataAccess->validerFiche($idutilisateur, $mois);
	}
	
	public function refuserFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
	// TODO : intégrer une fonctionnalité d'impression PDF de la fiche
	
	$this->dataAccess->refuserFiche($idutilisateur, $mois);
	}
	
	/**
	 * Modifie les quantités associées aux frais forfaitisés dans une fiche donnée
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concernée
	 * @param $lesFrais : les quantités liées Ã  chaque type de frais, sous la forme d'un tableau
	*/
	public function majForfait($idutilisateur, $mois, $lesFrais)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
		// TODO : valider les données contenues dans $lesFrais ...
		
		$this->dataAccess->majLignesForfait($idutilisateur,$mois,$lesFrais);
		$this->dataAccess->recalculeMontantFiche($idutilisateur,$mois);
	}

	/**
	 * Ajoute une ligne de frais hors forfait dans une fiche donnée
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concernée
	 * @param $lesFrais : les quantités liées Ã  chaque type de frais, sous la forme d'un tableau
	*/
	public function ajouteFrais($idutilisateur, $mois, $uneLigne)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session
		// TODO : valider la donnée contenues dans $uneLigne ...

		$dateFrais = $uneLigne['dateFrais'];
		$libelle = $uneLigne['libelle'];
		$montant = $uneLigne['montant'];

		$this->dataAccess->creeLigneHorsForfait($idutilisateur,$mois,$libelle,$dateFrais,$montant);
	}

	/**
	 * Supprime une ligne de frais hors forfait dans une fiche donnée
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concernée
	 * @param $idLigneFrais : l'id de la ligne Ã  supprimer
	*/
	public function supprLigneFrais($idutilisateur, $mois, $idLigneFrais)
	{	// TODO : s'assurer que les paramÃ¨tres reÃ§us sont cohérents avec ceux mémorisés en session et cohérents entre eux

	    $this->dataAccess->supprimerLigneHorsForfait($idLigneFrais);
	}
}