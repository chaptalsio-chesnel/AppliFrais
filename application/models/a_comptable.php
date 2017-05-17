<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class A_comptable extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

		// chargement du modèle d'accès aux donn�es qui est utile à toutes les m�thodes
		$this->load->model('dataAccess');
    }

	/**
	 * Accueil du utilisateur
	 * La fonction intègre un m�canisme de contrôle d'existence des 
	 * fiches de frais sur les 6 derniers mois. 
	 * Si l'une d'elle est absente, elle est cr��e
	*/
	public function accueil()
	{	// TODO : Contrôler que toutes les valeurs de $unMois sont valides (chaine de caractère dans la BdD)
	
		// chargement du modèle contenant les fonctions g�n�riques
		$this->load->model('functionsLib');

		// obtention de la liste des 6 derniers mois (y compris celui ci)
		$lesMois = $this->functionsLib->getSixDerniersMois();
		
		// obtention de l'id de l'utilisateur m�moris� en session
		$idutilisateur = $this->session->userdata('idUser');
		
		// contrôle de l'existence des 6 dernières fiches et cr�ation si n�cessaire
		foreach ($lesMois as $unMois){
			if(!$this->dataAccess->ExisteFiche($idutilisateur, $unMois)) $this->dataAccess->creeFiche($idutilisateur, $unMois);
		}
		// envoie de la vue accueil du utilisateur
		$this->templates->load('t_comptable', 'v_visAccueil');
	}
	
	/**
	 * Liste les fiches existantes du utilisateur connect� et 
	 * donne accès aux fonctionnalit�s associ�es
	 *
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $message : message facultatif destin� à notifier l'utilisateur du r�sultat d'une action pr�c�demment ex�cut�e
	*/
	public function lesFiches ($idutilisateur, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	
		$idutilisateur = $this->session->userdata('idUser');

		$data['notify'] = $message;
		$data['lesFiches'] = $this->dataAccess->getLesFiches($idutilisateur);		
		$this->templates->load('t_comptable', 'v_compLesFiches', $data);	
	}	
	public function lesSuivi ($idutilisateur, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	
	$idutilisateur = $this->session->userdata('idUser');
	
	$data['notify'] = $message;
	$data['lesFiches'] = $this->dataAccess->getLesFiches($idutilisateur);
	$this->templates->load('t_comptable', 'v_compSuiviFiche', $data);
	}
	/**
	 * Pr�sente le d�tail de la fiche s�lectionn�e 
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche à modifier 
	*/
	public function voirFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session

		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfaitComp($idutilisateur,$mois);		

		$this->templates->load('t_comptable', 'v_compVoirFrais', $data);
	}

	/**
	 * Pr�sente le d�tail de la fiche s�lectionn�e et donne 
	 * acc�s à la modification du contenu de cette fiche.
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche à modifier 
	 * @param $message : message facultatif destin� à notifier l'utilisateur du r�sultat d'une action pr�c�demment ex�cut�e
	*/
	public function modCompFiche($idutilisateur, $mois, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session

		$data['notify'] = $message;
		$data['util'] = $idutilisateur;
		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfaitComp($idutilisateur,$mois);		

		$this->templates->load('t_comptable', 'v_compModListeFrais', $data);
	}
	public function voirCompFiche($idutilisateur, $mois, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	
	$data['notify'] = $message;
	$data['util'] = $idutilisateur;
	$data['numAnnee'] = substr( $mois,0,4);
	$data['numMois'] = substr( $mois,4,2);
	$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idutilisateur,$mois);
	$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idutilisateur,$mois);
	
	$this->templates->load('t_comptable', 'v_compVoirFrais', $data);
	}

	/**
	 * Signe une fiche de frais en changeant son �tat
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche à signer
	*/
	public function signeFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche

	    $this->dataAccess->signeFiche($idutilisateur, $mois);
	}
	public function mpFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche
	
	$this->dataAccess->mpFiche($idutilisateur, $mois);
	}
	public function rembourserFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche
	
	$this->dataAccess->rembourserFiche($idutilisateur, $mois);
	}

	public function validerFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche
	
	$this->dataAccess->validerFiche($idutilisateur, $mois);
	}
	
	public function refuserFiche($idutilisateur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche
		$data['util'] = $idutilisateur;
		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['mois'] = $mois;
		
		$this->templates->load('t_comptable', 'v_compRefus', $data);
	//$this->dataAccess->refuserFiche($idutilisateur, $mois);
	}
	public function refusFiche($idutilisateur, $mois,$raison)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche
	
	$this->dataAccess->refuserFiche($idutilisateur, $mois,$raison);
	}
	/**
	 * Modifie les quantit�s associ�es aux frais forfaitis�s dans une fiche donn�e
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $lesFrais : les quantit�s li�es à chaque type de frais, sous la forme d'un tableau
	*/
	public function majForfait($idutilisateur, $mois, $lesFrais)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : valider les donn�es contenues dans $lesFrais ...
		
		$this->dataAccess->majLignesForfait($idutilisateur,$mois,$lesFrais);
		$this->dataAccess->recalculeMontantFiche($idutilisateur,$mois);
	}

	/**
	 * Ajoute une ligne de frais hors forfait dans une fiche donn�e
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $lesFrais : les quantit�s li�es à chaque type de frais, sous la forme d'un tableau
	*/
	public function ajouteFrais($idutilisateur, $mois, $uneLigne)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : valider la donn�e contenues dans $uneLigne ...

		$dateFrais = $uneLigne['dateFrais'];
		$libelle = $uneLigne['libelle'];
		$montant = $uneLigne['montant'];

		$this->dataAccess->creeLigneHorsForfait($idutilisateur,$mois,$libelle,$dateFrais,$montant);
	}

	/**
	 * Supprime une ligne de frais hors forfait dans une fiche donn�e
	 * 
	 * @param $idutilisateur : l'id du utilisateur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $idLigneFrais : l'id de la ligne à supprimer
	*/
	public function supprLigneFrais($idutilisateur, $mois, $idLigneFrais)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session et coh�rents entre eux

	    $this->dataAccess->supprimerLigneHorsForfait($idLigneFrais);
	}
}