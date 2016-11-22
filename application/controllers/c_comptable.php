<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contrôleur du module VISITEUR de l'application
 */
class C_comptable extends CI_Controller {

	/**
	 * Aiguillage des demandes faites au contrôleur
	 * La fonction _remap est une fonctionnalit� offerte par CI destin�e à remplacer
	 * le comportement habituel de la fonction index. Grâce à _remap, on dispose
	 * d'une fonction unique capable d'accepter un nombre variable de paramètres.
	 *
	 * @param $action : l'action demand�e par le visiteur
	 * @param $params : les �ventuels paramètres transmis pour la r�alisation de cette action
	 */
	public function _remap($action, $params = array())
	{
		// chargement du modèle d'authentification
		$this->load->model('authentif');

		// contrôle de la bonne authentification de l'utilisateur
		if (!$this->authentif->estConnecte())
		{
			// l'utilisateur n'est pas authentifi�, on envoie la vue de connexion
			$data = array();
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		else
		{
			// Aiguillage selon l'action demand�e
			// CI a trait� l'URL au pr�alable de sorte à toujours renvoyer l'action "index"
			// même lorsqu'aucune action n'est exprim�e
			if ($action == 'index')				// index demand� : on active la fonction accueil du modèle visiteur
			{
				$this->load->model('a_comptable');

				// on n'est pas en mode "modification d'une fiche"
				$this->session->unset_userdata('mois');

				$this->a_comptable->accueil();
			}
			elseif ($action == 'lesFiches')		// mesFiches demand� : on active la fonction mesFiches du modèle visiteur
			{
				$this->load->model('a_comptable');

				// on n'est pas en mode "modification d'une fiche"
				$this->session->unset_userdata('mois');

				$idVisiteur = $this->session->userdata('idUser');
				$this->a_comptable->lesFiches($idVisiteur);
			}
			elseif ($action == 'lesSuivi')		// mesFiches demand� : on active la fonction mesFiches du modèle visiteur
			{
				$this->load->model('a_comptable');
			
				// on n'est pas en mode "modification d'une fiche"
				$this->session->unset_userdata('mois');
			
				$idVisiteur = $this->session->userdata('idUser');
				$this->a_comptable->lesSuivi($idVisiteur);
			}
			elseif ($action == 'deconnecter')	// deconnecter demand� : on active la fonction deconnecter du modèle authentif
			{
				$this->load->model('authentif');
				$this->authentif->deconnecter();
			}
			elseif ($action == 'voirFiche')		// voirFiche demand� : on active la fonction voirFiche du modèle authentif
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à consulter)
					
				$this->load->model('a_comptable');

				// obtention du mois de la fiche à modifier qui doit avoir �t� transmis
				// en second paramètre
				$mois = $params[0];
				// m�morisation du mode modification en cours
				// on m�morise le mois de la fiche en cours de modification
				$this->session->set_userdata('mois', $mois);
				// obtention de l'id utilisateur courant
				$idVisiteur = $this->session->userdata('idUser');

				$this->a_comptable->voirFiche($idVisiteur, $mois);
			}
			elseif ($action == 'modCompFiche')		// modFiche demand� : on active la fonction modFiche du modèle authentif
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
					
				$this->load->model('a_comptable');

				// obtention du mois de la fiche à modifier qui doit avoir �t� transmis
				// en second paramètre
				$mois = $params[0];
				// m�morisation du mode modification en cours
				// on m�morise le mois de la fiche en cours de modification
				$this->session->set_userdata('mois', $mois);
				// obtention de l'id utilisateur courant
				$idVisiteur = $params[1];

				$this->a_comptable->modCompFiche($idVisiteur, $mois);
			}
			elseif ($action == 'signeFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
				$this->load->model('a_comptable');

				// obtention du mois de la fiche à signer qui doit avoir �t� transmis
				// en second paramètre
				$mois = $params[0];
				// obtention de l'id utilisateur courant et du mois concern�
				$idVisiteur = $this->session->userdata('idUser');
				$this->a_comptable->signeFiche($idVisiteur, $mois);

				// ... et on revient à mesFiches
				$this->a_comptable->lesFiches($idVisiteur, "La fiche $mois a �t� sign�e. <br/>Pensez à envoyer vos justificatifs afin qu'elle soit trait�e par le service comptable rapidement.");
			}
			elseif ($action == 'mpFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
			$this->load->model('a_comptable');
			
			// obtention du mois de la fiche à signer qui doit avoir �t� transmis
			// en second paramètre
			$mois = $params[0];
			// obtention de l'id utilisateur courant et du mois concern�
			$idVisiteur = $params[1];
			$this->a_comptable->mpFiche($idVisiteur, $mois);
			
			// ... et on revient à mesFiches
			$this->a_comptable->lesSuivi($idVisiteur, "La fiche $mois a �t� sign�e. <br/>Pensez à envoyer vos justificatifs afin qu'elle soit trait�e par le service comptable rapidement.");
			}
			elseif ($action == 'rembourserFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
			$this->load->model('a_comptable');
				
			// obtention du mois de la fiche à signer qui doit avoir �t� transmis
			// en second paramètre
			$mois = $params[0];
			// obtention de l'id utilisateur courant et du mois concern�
			$idVisiteur = $params[1];
			$this->a_comptable->rembourserFiche($idVisiteur, $mois);
				
			// ... et on revient à mesFiches
			$this->a_comptable->lesSuivi($idVisiteur, "La fiche $mois a �t� sign�e. <br/>Pensez à envoyer vos justificatifs afin qu'elle soit trait�e par le service comptable rapidement.");
			}
				
			elseif ($action == 'refusFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
			$this->load->model('a_comptable');
			
			// obtention du mois de la fiche à signer qui doit avoir �t� transmis
			// en second paramètre
			$mois = $params[0];
			// obtention de l'id utilisateur courant et du mois concern�
			$idVisiteur = $params[1];
			$this->a_comptable->refuserFiche($idVisiteur, $mois);
			
			// ... et on revient à mesFiches
			$this->a_comptable->lesFiches($idVisiteur, "La fiche $mois est refuser.");
			}
			
			elseif ($action == 'validerFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
			$this->load->model('a_comptable');
			
			// obtention du mois de la fiche à signer qui doit avoir �t� transmis
			// en second paramètre
			$mois = $params[0];
			// obtention de l'id utilisateur courant et du mois concern�
			$idVisiteur = $params[1];
			$this->a_comptable->validerFiche($idVisiteur, $mois);
			
			// ... et on revient à mesFiches
			$this->a_comptable->lesFiches($idVisiteur, "La fiche $mois est valider.");
			}
			elseif ($action == 'voirFiche') 	// signeFiche demand� : on active la fonction signeFiche du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
			$this->load->model('a_comptable');
				
			// obtention du mois de la fiche à signer qui doit avoir �t� transmis
			// en second paramètre
			$mois = $params[0];
			// obtention de l'id utilisateur courant et du mois concern�
			$idVisiteur = $params[1];
			$this->a_comptable->validerFiche($idVisiteur, $mois);
				
			// ... et on revient à mesFiches
			$this->a_comptable->voirCompFiche($idVisiteur, "La fiche $mois est valider.");
			}
			elseif ($action == 'majForfait') // majFraisForfait demand� : on active la fonction majFraisForfait du modèle visiteur ...
			{	// TODO : conrôler que l'obtention des donn�es post�es ne rend pas d'erreurs
				// TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche

				$this->load->model('a_comptable');

				// obtention de l'id du visiteur et du mois concern�
				$idVisiteur = $params[0];
				$mois = $this->session->userdata('mois');

				// obtention des donn�es post�es
				$lesFrais = $this->input->post('lesFrais');

				$this->a_comptable->majForfait($idVisiteur, $mois, $lesFrais);

				// ... et on revient en modification de la fiche
				$this->a_comptable->modCompFiche($idVisiteur, $mois, 'Modification(s) des �l�ments forfaitis�s enregistr�e(s) ...');
			}
			elseif ($action == 'ajouteFrais') // ajouteLigneFrais demand� : on active la fonction ajouteLigneFrais du modèle visiteur ...
			{	// TODO : conrôler que l'obtention des donn�es post�es ne rend pas d'erreurs
				// TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche

				$this->load->model('a_comptable');

				// obtention de l'id du visiteur et du mois concern�
				$idVisiteur = $this->session->userdata('idUser');
				$mois = $this->session->userdata('mois');

				// obtention des donn�es post�es
				$uneLigne = array(
						'dateFrais' => $this->input->post('dateFrais'),
						'libelle' => $this->input->post('libelle'),
						'montant' => $this->input->post('montant')
				);

				$this->a_comptable->ajouteFrais($idVisiteur, $mois, $uneLigne);

				// ... et on revient en modification de la fiche
				$this->a_comptable->modFiche($idVisiteur, $mois, 'Ligne "Hors forfait" ajout�e ...');
			}
			elseif ($action == 'supprFrais') // suppprLigneFrais demand� : on active la fonction suppprLigneFrais du modèle visiteur ...
			{	// TODO : contrôler la validit� du second paramètre (mois de la fiche à modifier)
				// TODO : dans la dynamique de l'application, contrôler que l'on vient bien de modFiche
					
				$this->load->model('a_comptable');

				// obtention de l'id du visiteur et du mois concern�
				$idVisiteur = $this->session->userdata('idUser');
				$mois = $this->session->userdata('mois');

				// Quel est l'id de la ligne à supprimer : doit avoir �t� transmis en second paramètre
				$idLigneFrais = $params[0];
				$this->a_comptable->supprLigneFrais($idVisiteur, $mois, $idLigneFrais);

				// ... et on revient en modification de la fiche
				$this->a_comptable->modFiche($idVisiteur, $mois, 'Ligne "Hors forfait" supprim�e ...');
			}
			else								// dans tous les autres cas, on envoie la vue par d�faut pour l'erreur 404
			{
				show_404();
			}
		}
	}
}
