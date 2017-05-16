<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur par d�faut de l'application
 * Si aucune sp�cification de contrôleur n'est pr�cis�e dans l'URL du navigateur
 * c'est le contrôleur par d�faut qui sera invoqu�. Son rôle est :
 * 		+ d'orienter vers le bon contrôleur selon la situation
 * 		+ de traiter le retour du formulaire de connexion 
*/
class C_default extends CI_Controller {

	/**
	 * Fonctionnalit� par d�faut du contrôleur. 
	 * V�rifie l'existence d'une connexion :
	 * Soit elle existe et on redirige vers le contrôleur de VISITEUR, 
	 * soit elle n'existe pas et on envoie la vue de connexion
	*/
	public function index()
	{
		$this->load->model('authentif');
		$this->load->model('dataAccess');
		
		if (!$this->authentif->estConnecte()) 
		{
			$data = array();
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		else
		{
			$login = $this->input->post('login');
			$mdp = $this->input->post('mdp');
			$compt=$this->dataAccess->getComputilisateur($login, $mdp);
			
			$this->load->helper('url');
			$compt = $compt['statut'];
			if($compt==0){
				redirect('/c_visiteur/');
			}
			else if($compt==1){
				redirect('/c_comptable/');
			}
		}
	}
	
	/**
	 * Traite le retour du formulaire de connexion afin de connecter l'utilisateur
	 * s'il est reconnu
	*/
	public function connecter () 
	{	// TODO : conrôler que l'obtention des donn�es post�es ne rend pas d'erreurs 

		$this->load->model('authentif');
		
		$login = $this->input->post('login');
		$mdp = $this->input->post('mdp');
		
		$authUser = $this->authentif->authentifier($login, $mdp);

		if(empty($authUser))
		{
			$data = array('erreur'=>'Login ou mot de passe incorrect');
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		else
		{
			$this->authentif->connecter($authUser['id'], $authUser['nom'], $authUser['prenom']);
			$this->index();
		}
	}
	
}
