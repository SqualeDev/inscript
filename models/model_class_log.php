<?php
	// modèle log : intéractions relatives à l'authentification
	class ClassLog {
		// LOGIN
		public $strLog;
		// Mot de passe
		public $strPwd;
		// IPN existe
		public $boolExist;
		// IPN & password corrects
		public $boolAutorise;
		// NOM
		public $strLastname;
		// Prénom
		public $strFirstname;
		// Catégorie
		public $strCateg;

		// constructeur
		function __construct($strLogin=null, $strPass=null, $strLastname=null, $strFirstname=null) {
            display_debug("construct : ", $strLogin, 0);
			
            require_once PATH_MODELS . 'model_class_db.php';
			// VERIFIER SI L'UTILISATEUR EXISTE
			$this->boolExist	= $this->existUser($strLogin);
			// SI L'UTILISATEUR EXISTE
			if ($this->boolExist) {
				// VERIFIER SI L'UTILISATEUR EST AUTORISE
				$this->boolAutorise	= $this->autoriseUser($strLogin, $strPass);
				// RECUPERER L'IPN DANS LA TABLE
				// $this->strLog		= $this->getIpn($strLogin);
				// RECUPERER LE MOT DE PASSE DANS LA TABLE
				// $this->strPwd		= $this->getPwd($strLogin);
				// RECUPERER LE NOM DANS LA TABLE
				$this->strLastname	= $this->getLastname($strLogin);
				// RECUPERER LE PRENOM DANS LA TABLE
				$this->strFirstname	= $this->getFirstname($strLogin);
				// RECUPERER LA CATEGORIE DANS LA TABLE
				$this->strCateg	= $this->getCategorie($strLogin);
				// SI L'UTILISATEUR EST AUTORISE
				if($this->boolAutorise) {
					// RECUPERER LES ROLES DE L'UTILISATEUR : TABLE omsf_usr_a_rol
					//$this->arrayRoles   = $this->getRoles($strLogin);
					// RECUPERER LES ACTIONS AUTORISEES POUR CET UTILISATEUR
					//$this->arrayActions = $this->getActions($strLogin);
				// SI L'UTILISATEUR N'EST PAS AUTORISE
				} else {
					// UTILISATEUR NON AUTORISE
					$this->boolAutorise	= false;
					// UNSET MODEL USER VAR
					// unset($this->arrayRoles);
					// unset($this->arrayActions);
				}
			// SI L'UTILISATEUR N'EXISTE PAS
			} else {
				// L'UTILISATEUR N'EXISTE PAS
				$this->boolExist	= false;
				// UNSET MODEL USER VAR
				unset($this->strLogin);
				unset($this->strPass);
				unset($this->strLastname);
				unset($this->strFirstname);
				// unset($this->arrayRoles);
				// unset($this->arrayActions);
			}
		}

		// VERIFIER SI L'UTILISATEUR EXISTE
		// $strLogin	STRING	IPNLOG
		// RETURN		BOOLEAN
		private function existUser($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			// $strQuery = "SELECT usr_login FROM csg_users WHERE usr_login = '".$_strLogin."';";
			// display_debug("existUser : ", $strQuery, 0);
			$strQuery = "SELECT usr_login FROM csg_users WHERE usr_login = ?;";
			// EXECUTE LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			// si l'IPN existe dans la table
			if ($dbUser->existData($strQuery, array($_strLogin))) {
				// RETURN true
				return true;
			} else {
				// RETURN false
				return false;
			}
			$dbUser = null;
		}

		// VERIFIER SI L'IPN ET LE MOT DE PASSE CORRESPONDENT
		// $_strLogin	STRING IPN
		// $_strPass	md5(STRING) MOT DE PASSE
		// RETURN		BOOLEAN
		private function autoriseUser($_strLogin, $_strPass) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_login FROM csg_users WHERE usr_login = ? AND usr_pass = ?;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			// si le couple ipn / mot de passe correspondant est trouvé
			if ($dbUser->existData($strQuery, array($_strLogin, $_strPass))) {
				// RETURN true
				return true;
			} else {
				// RETURN false
				return false;
			}
			$dbUser = null;
		}

		// RECUPERER L'IPN DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strLogin	STRING IPN LOG
		// RETURN		STRING	IPN TABLE
		// private function getIpn($_strLogin) {
		// 	// NOUVELLE INSTANCE DE MODEL DB
		// 	$dbUser = new ClassDb();
		// 	// REQUETE
		// 	$strQuery = "SELECT usr_ipn FROM omsf_users WHERE usr_ipn = ? ;";
		// 	// EXECUTER LA REQUETE VIA MODEL DB
		// 	$result = $dbUser->getData($strQuery, array($_strLogin));
		// 	// RECUPERER LE RESULTAT
		// 	$ipn = $result[0]['usr_ipn'];
		// 	// RETURN l'ipn stocké dans la table
		// 	return $ipn;
		// 	$dbUser = null;
		// }

		// RECUPERER LE MOT DE PASSE DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	md5(STRING)
		// private function getPwd($_strIpn) {
		// 	// NOUVELLE INSTANCE DE MODEL DB
		// 	$dbUser = new ClassDb();
		// 	// REQUETE
		// 	$strQuery = "SELECT usr_password FROM omsf_users WHERE usr_ipn = ? ;";
		// 	// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
		// 	$result = $dbUser->getData($strQuery, array($_strIpn));
		// 	// RECUPERER LE RESULTAT
		// 	$pwd = $result[0]['usr_password'];
		// 	// RETURN la chaine md5 correspondant au mot de passe de l'utilisateur
		// 	return $pwd;
		// 	$dbUser = null;
		// }

		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getLastname($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_lastname FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$lastname = $result[0]['usr_lastname'];
			// RETURN lastname contenu dans la table
			return $lastname;
			$dbUser = null;
		}

		// 	RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getFirstname($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_firstname FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$firstname = $result[0]['usr_firstname'];
			// RETURN firstname contenu dans la table
			return $firstname;
			$dbUser = null;
		}

		// 	RECUPERER LA CATEGORIE DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strLogin	STRING	IPN
		// RETURN 	STRING
		private function getCategorie($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_cat FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$categorie = $result[0]['usr_cat'];
			// RETURN firstname contenu dans la table
			return $categorie;
			$dbUser = null;
		}

		// 	RECUPERER LES ROLES DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	ARRAY
        // private function getRoles($_strIpn) {
		// 	// NOUVELLE ISNTANCE DE MODEL DB
		// 	$dbUser = new ClassDb();
		// 	// REQUETE
		// 	$strQuery = "SELECT rol_nom FROM omsf_usr_a_rol WHERE usr_ipn = ? ;";
		// 	// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
		// 	$results = $dbUser->getData($strQuery, array($_strIpn));
		// 	// RECUPERER LES RESULTATS
		// 	$roles = $results;
		// 	// RETURN les roles affectés à l'utilisateur
		// 	return $roles;
		// 	$dbUser = null;
		// }

		// RECUPERER LES ACTIONS AUTORISEES POUR CET UTILISATEUR
		// $_strIpn	STRING	IPN
		// RETURN	ARRAY
		// private function getActions($_strIpn) {
		// 	// NOUVELLE INSTANCE DE MODEL DB
		// 	$dbUser = new ClassDb();
		// 	// REQUETE
		// 	$strQuery	= "SELECT DISTINCT omsf_fonctions.fct_controleur AS control, omsf_fonctions.fct_action AS action, omsf_fonctions.fct_description AS menuaction, omsf_fonctions.fct_menu AS menucontrol, omsf_fonctions.fct_id_action
		// 				FROM omsf_fonctions, omsf_rol_autorise_fct, omsf_usr_a_rol
		//  				WHERE omsf_rol_autorise_fct.rol_nom = omsf_usr_a_rol.rol_nom
		// 				AND omsf_rol_autorise_fct.fct_action = omsf_fonctions.fct_action
		// 				AND omsf_usr_a_rol.usr_ipn = ?
		// 				ORDER BY control, omsf_fonctions.fct_id_action ;";
		// 	// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
		// 	$results = $dbUser->getData($strQuery, array($_strIpn));
		// 	return $results;
			
		// 	$dbUser = null;
		// }
	}
?>
