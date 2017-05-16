<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class A_visiteur extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

		// chargement du modèle d'accès aux donn�es qui est utile à toutes les m�thodes
		$this->load->model('dataAccess');
    }

	/**
	 * Accueil du visiteur
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
		$idVisiteur = $this->session->userdata('idUser');
		
		// contrôle de l'existence des 6 dernières fiches et cr�ation si n�cessaire
		foreach ($lesMois as $unMois){
			if(!$this->dataAccess->ExisteFiche($idVisiteur, $unMois)) $this->dataAccess->creeFiche($idVisiteur, $unMois);
		}
		// envoie de la vue accueil du visiteur
		$this->templates->load('t_visiteur', 'v_visAccueil');
	}
	
	/**
	 * Liste les fiches existantes du visiteur connect� et 
	 * donne accès aux fonctionnalit�s associ�es
	 *
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $message : message facultatif destin� à notifier l'utilisateur du r�sultat d'une action pr�c�demment ex�cut�e
	*/
	public function mesFiches ($idVisiteur, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
	
		$idVisiteur = $this->session->userdata('idUser');

		$data['notify'] = $message;
		$data['mesFiches'] = $this->dataAccess->getFiches($idVisiteur);		
		$this->templates->load('t_visiteur', 'v_visMesFiches', $data);	
	}	

	/**
	 * Pr�sente le d�tail de la fiche s�lectionn�e 
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche à modifier 
	*/
	public function voirFiche($idVisiteur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session

		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idVisiteur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idVisiteur,$mois);
		$data['raison'] = $this->dataAccess->getLaRaison($idVisiteur,$mois);
		
		$this->templates->load('t_visiteur', 'v_visVoirListeFrais', $data);
	}

	/**
	 * Pr�sente le d�tail de la fiche s�lectionn�e et donne 
	 * acc�s à la modification du contenu de cette fiche.
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche à modifier 
	 * @param $message : message facultatif destin� à notifier l'utilisateur du r�sultat d'une action pr�c�demment ex�cut�e
	*/
	public function modFiche($idVisiteur, $mois, $message=null)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session

		$data['notify'] = $message;
		$data['numAnnee'] = substr( $mois,0,4);
		$data['numMois'] = substr( $mois,4,2);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idVisiteur,$mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idVisiteur,$mois);		

		$this->templates->load('t_visiteur', 'v_visModListeFrais', $data);
	}

	/**
	 * Signe une fiche de frais en changeant son �tat
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche à signer
	*/
	public function signeFiche($idVisiteur, $mois)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : int�grer une fonctionnalit� d'impression PDF de la fiche

	    $this->dataAccess->signeFiche($idVisiteur, $mois);
	}

	/**
	 * Modifie les quantit�s associ�es aux frais forfaitis�s dans une fiche donn�e
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $lesFrais : les quantit�s li�es à chaque type de frais, sous la forme d'un tableau
	*/
	public function majForfait($idVisiteur, $mois, $lesFrais)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : valider les donn�es contenues dans $lesFrais ...
		
		$this->dataAccess->majLignesForfait($idVisiteur,$mois,$lesFrais);
		$this->dataAccess->recalculeMontantFiche($idVisiteur,$mois);
	}

	/**
	 * Ajoute une ligne de frais hors forfait dans une fiche donn�e
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $lesFrais : les quantit�s li�es à chaque type de frais, sous la forme d'un tableau
	*/
	public function ajouteFrais($idVisiteur, $mois, $uneLigne)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session
		// TODO : valider la donn�e contenues dans $uneLigne ...

		$dateFrais = $uneLigne['dateFrais'];
		$libelle = $uneLigne['libelle'];
		$montant = $uneLigne['montant'];

		$this->dataAccess->creeLigneHorsForfait($idVisiteur,$mois,$libelle,$dateFrais,$montant);
	}

	/**
	 * Supprime une ligne de frais hors forfait dans une fiche donn�e
	 * 
	 * @param $idVisiteur : l'id du visiteur 
	 * @param $mois : le mois de la fiche concern�e
	 * @param $idLigneFrais : l'id de la ligne à supprimer
	*/
	public function supprLigneFrais($idVisiteur, $mois, $idLigneFrais)
	{	// TODO : s'assurer que les paramètres reçus sont coh�rents avec ceux m�moris�s en session et coh�rents entre eux

	    $this->dataAccess->supprimerLigneHorsForfait($idLigneFrais);
	}
}