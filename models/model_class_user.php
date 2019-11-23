<?php
	// modèle user : intéractions relatives aux users
	class ClassUser {
        
        // Définition des variables publiques
        // n° Licence & mot de passe corrects
		public $boolAutorise;
		// Est administarteur du site Web
		public $boolAdmin;
		// Est rédacteur du site Web
		public $boolRedac;
		// Est entraîneur du club
		public $boolCoach;
		// Prénom de l'utilisateur
        public $strFirstname;
        // Nom de l'utilisateur
        public $strLastname;
        // Catégorie de l'utilisateur
        public $arrCat;
        // Email de l'utilisateur
		public $strMail;
		// Notification des inscriptions par Email de l'utilisateur
        public $boolNotifMailInscript;
        // Notification des versions par Email de l'utilisateur
        public $boolNotifMailVersion;
        // Mobile de l'utilisateur
        public $strMobile;
		// Notification par Mobile de l'utilisateur
		public $boolNotifMobile;
		// Nombre d'article par ligne à afficher
		public $intNbArtParLigne;
		// Nombre d'inscriptions à des concours individuels dans l'année en cours
		public $intNbInscriptConcoursInd;
		// Nombre d'inscriptions à des championnats dans l'année en cours
		public $intNbInscriptChampionnat;

        function __construct($strLicence=null, $strPass=null) {
            require_once PATH_MODELS . 'model_class_db.php';
			// VERIFIER SI L'UTILISATEUR EXISTE
			$this->boolExist	= $this->existUser($strLicence);
			// SI L'UTILISATEUR EXISTE
			if ($this->boolExist) {
                // VERIFIER SI L'UTILISATEUR EST AUTORISE
                $this->boolAutorise	= $this->autoriseUser($strLicence, $strPass);
                if($this->boolAutorise) {
                    // RECUPERER LE PRENOM DE L'UTILISATEUR
                    $this->strFirstname = $this->getFirstname($strLicence);
					// RECUPERER LE NOM DE L'UTILISATEUR
                    $this->strLastname = $this->getLastname($strLicence);
					// RECUPERER L'ADRESSE MAIL DE L'UTILISATEUR
                    $this->strMail = $this->getMail($strLicence);
					// RECUPERER L'ETAT DE LA NOTIFICATION PAR MAIL DE L'UTILISATEUR
                    $this->boolNotifMailInscript = $this->getNotifMailInscript($strLicence);
					// RECUPERER L'ETAT DE LA NOTIFICATION PAR MAIL DE L'UTILISATEUR
                    $this->boolNotifMailVersion = $this->getNotifMailVersion($strLicence);
					// RECUPERER LE NUMERO DE MOBILE DE L'UTILISATEUR
                    $this->strMobile = $this->getMobile($strLicence);
					// RECUPERER L'ETAT DE LA NOTIFICATION PAR SMS DE L'UTILISATEUR
					$this->boolNotifMobile = $this->getNotifMobile($strLicence);
					// L'UTILISATEUR EST-IL ADMINISTRATEUR DU SITE
					$this->boolAdmin = $this->estAdmin($strLicence);
					// L'UTILISATEUR EST-IL REDACTEUR DU SITE
					$this->boolRedac = $this->estRedac($strLicence);
					// L'UTILISATEUR EST-IL ENTRAINEUR DU CLUB
					$this->boolCoach = $this->estCoach($strLicence);
					// RECUPERER LE NOMBRE D'ARTICLE PAR LIGNE
					$this->intNbArtParLigne = $this->getNbArtParLigne($strLicence);
					// RECUPERER LE NOMBRE D'INSCRIPTION A DES CONCOURS INDIVIDUELS
					$this->intNbInscriptConcoursInd = $this->getNbInscriptConcoursInd($strLicence);
					// RECUPERER LE NOMBRE D'INSCRIPTION A DES CHAMPIONNATS
					$this->intNbInscriptChampionnat = $this->getNbNbInscriptChamp($strLicence);

				// SI L'UTILISATEUR N'EST PAS AUTORISE
				} else {
					// UTILISATEUR NON AUTORISE
					$this->boolAutorise	= false;
					// UNSET MODEL USER VAR
					unset($this->strFirstname);
					unset($this->strLastname);
					unset($this->strMail);
					unset($this->boolNotifMailInscript);
					unset($this->boolNotifMailVersion);
					unset($this->strMobile);
					unset($this->boolNotifMobile);
					$this->intNbArtParLigne = 2;
					$this->boolAdmin = false;
					$this->boolRedac = false;
					$this->boolCoach = false;
				}
            // SI L'UTILISATEUR N'EXISTE PAS
			} else {
				// L'UTILISATEUR N'EXISTE PAS
				$this->boolExist	= false;
				// UNSET MODEL USER VAR
				unset($this->strLogin);
				unset($this->strFirstname);
				unset($this->strLastname);
				unset($this->strMail);
				unset($this->boolNotifMailInscript);
				unset($this->boolNotifMailVersion);
				unset($this->strMobile);
				unset($this->boolNotifMobile);
				$this->intNbArtParLigne = 2;
				$this->boolAdmin = false;
				$this->boolRedac = false;
				$this->boolCoach = false;
			}
        }

        // VERIFIER SI L'UTILISATEUR EXISTE
		// $strLogin	STRING	IPNLOG
		// RETURN		BOOLEAN
		private function existUser($_strLogin) {
			// NOUVELLE ISNTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
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

		// VERIFIER SI L'UTILISATEUR ET LE MOT DE PASSE CORRESPONDENT
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

		// VERIFIER SI L'UTILISATEUR EST ADMINISTRATEUR DU SITE
		// $_strLogin	STRING IPN
		// RETURN		BOOLEAN
		private function estAdmin($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_admin FROM csg_users WHERE usr_login = ? AND usr_admin = 1;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			return filter_var($result[0]['usr_admin'], FILTER_VALIDATE_BOOLEAN);
			$dbUser = null;
		}

		// VERIFIER SI L'UTILISATEUR EST ADMINISTRATEUR DU SITE
		// $_strLogin	STRING IPN
		// RETURN		BOOLEAN
		private function estRedac($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_redac FROM csg_users WHERE usr_login = ? AND usr_redac = 1;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			return filter_var($result[0]['usr_redac'], FILTER_VALIDATE_BOOLEAN);
			$dbUser = null;
		}

		// VERIFIER SI L'UTILISATEUR EST ADMINISTRATEUR DU SITE
		// $_strLogin	STRING IPN
		// RETURN		BOOLEAN
		private function estCoach($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_coach FROM csg_users WHERE usr_login = ? AND usr_coach = 1;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			return filter_var($result[0]['usr_coach'], FILTER_VALIDATE_BOOLEAN);
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


		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getMail($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_mail FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$mail = $result[0]['usr_mail'];
			// RETURN lastname contenu dans la table
			return $mail;
			$dbUser = null;
		}


		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getNotifMailInscript($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_mail_notif_inscript FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$notif = $result[0]['usr_mail_notif_inscript'];
			if ($notif) {
				return "checked";
			} else {
				return "";
			}

			// RETURN lastname contenu dans la table
			// return $lastname;
			$dbUser = null;
		}


		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getNotifMailVersion($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_mail_notif_version FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$notif = $result[0]['usr_mail_notif_version'];
			if ($notif) {
				return "checked";
			} else {
				return "";
			}

			// RETURN lastname contenu dans la table
			// return $lastname;
			$dbUser = null;
		}


		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getMobile($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_mobile FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$lastname = $result[0]['usr_mobile'];
			// RETURN lastname contenu dans la table
			return $lastname;
			$dbUser = null;
		}

		// RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
		// $_strIpn	STRING	IPN
		// RETURN 	STRING
		private function getNotifMobile($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT usr_mobile_notif FROM csg_users WHERE usr_login = ? ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->getData($strQuery, array($_strLogin));
			// RECUPERER LE RESULTAT
			$notif = $result[0]['usr_mobile_notif'];
			if ($notif) {
				return "checked";
			} else {
				return "";
			}

			// RETURN lastname contenu dans la table
			// return $lastname;
			$dbUser = null;
		}


		// RECUPERER LE NOMBRE D'ARTICLE PAR LIGNE CHOISI PAR L'UTILISATEUR
		// $_strLogin	STRING	IPN
		// RETURN 		INTEGER
		private function getNbArtParLigne($_strLogin) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			// $strQuery = "SELECT usr_nb_art_ligne FROM csg_users WHERE usr_login = ? ;";
			$strQuery = "SELECT usr_nb_art_ligne FROM csg_users WHERE usr_login = '".$_strLogin."' ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			// $result = $dbUser->getData($strQuery, array($_strLogin));
			$result = $dbUser->getAllData($strQuery);
			// RECUPERER LE RESULTAT
			$nbArt = intval($result[0]['usr_nb_art_ligne']);
			// RETURN usr_nb_art_ligne contenu dans la table
			return $nbArt;
			$dbUser = null;
		}

		// RECUPERER LE NOMBRE D'INSCRIPTION AUX CONCOURS INDIVIDUELS DANS L'ANNEE
		// $_strLogin	STRING	IPN
		// RETURN 		INTEGER
		private function getNbInscriptConcoursInd($_strLogin) {
			$intAnnee = intval(date('Y'));
			// echo "intAnnee : " . $intAnnee;
			// echo "Mois : " . intval(date('n'));
			if (intval(date('n')) >= 10 and intval(date('n') <= 12)) {
				$strIntervalle = "BETWEEN CAST('".$intAnnee."-10-01' AS DATE) AND CAST('".($intAnnee + 1)."-09-30' AS DATE)";
			} else {
				$strIntervalle = "BETWEEN CAST('".($intAnnee - 1)."-10-01' AS DATE) AND CAST('".$intAnnee."-09-30' AS DATE)";
			}
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT 
							COUNT(U.usr_login) AS Nb
							FROM 
								csg_users AS U,
								csg_rel_epreuve_user AS D,
								csg_epreuves AS E
							WHERE 
								U.usr_login = D.usr_login
							AND
								U.usr_login = '".$_strLogin."'
							AND
								E.epr_caracteristique = 'Individuel'
							AND 
								D.epr_id = E.epr_id
							AND 
								D.epr_id > 10000
							AND
								D.r_epr_date ".$strIntervalle."
						;";
			// echo $strQuery;
			// $strQuery = "SELECT usr_nb_art_ligne FROM csg_users WHERE usr_login = '".$_strLogin."' ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			// $result = $dbUser->getData($strQuery, array($_strLogin));
			$result = $dbUser->getAllData($strQuery);
			// RECUPERER LE RESULTAT
			$nbConcoursInd = intval($result[0]['Nb']);
			// RETURN usr_nb_ConcoursInd_ligne contenu dans la table
			return $nbConcoursInd;
			$dbUser = null;
		}

		// RECUPERER LE NOMBRE D'INSCRIPTION AUX CHAMPIONNATS DANS L'ANNEE
		// $_strLogin	STRING	IPN
		// RETURN 		INTEGER
		private function getNbNbInscriptChamp($_strLogin) {
			$intAnnee = intval(date('Y'));
			if (intval(date('n')) >= 10 and intval(date('n') <= 12)) {
				$strIntervalle = "BETWEEN CAST('".$intAnnee."-10-01' AS DATE) AND CAST('".($intAnnee + 1)."-09-30' AS DATE)";
			} else {
				$strIntervalle = "BETWEEN CAST('".($intAnnee - 1)."-10-01' AS DATE) AND CAST('".$intAnnee."-09-30' AS DATE)";
			}
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "SELECT 
							COUNT(U.usr_login) AS Nb
							FROM 
								csg_users AS U,
								csg_rel_epreuve_user AS D,
								csg_epreuves AS E
							WHERE 
								U.usr_login = D.usr_login
							AND
								U.usr_login = '".$_strLogin."'
							AND
								E.epr_caracteristique LIKE '%Champ%'
							AND 
								D.epr_id = E.epr_id
							AND 
								D.epr_id > 10000
							AND
								D.r_epr_date ".$strIntervalle."
						;";
			// $strQuery = "SELECT usr_nb_art_ligne FROM csg_users WHERE usr_login = '".$_strLogin."' ;";
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			// $result = $dbUser->getData($strQuery, array($_strLogin));
			$result = $dbUser->getAllData($strQuery);
			// RECUPERER LE RESULTAT
			$nbConcoursInd = intval($result[0]['Nb']);
			// RETURN usr_nb_ConcoursInd_ligne contenu dans la table
			return $nbConcoursInd;
			$dbUser = null;
		}

		// METTRE A JOUR LE USER
        public function updateUser($_strLicencie, $_strFirstname, $_strLastname, $_strMail, $_boolNotifMailInscript, $_boolNotifMailVersion, $_strMobile, $_boolNotifMobile, $_intNbArtParLigne=2) {
			if ($_boolNotifMailInscript=='on') {$_boolNotifMailInscript=1;} else {$_boolNotifMailInscript=0;};
			if ($_boolNotifMailVersion=='on') {$_boolNotifMailVersion=1;} else {$_boolNotifMailVersion=0;};
			if ($_boolNotifMobile=='on') {$_boolNotifMobile=1;} else {$_boolNotifMobile=0;};
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "UPDATE csg_users 
						 SET 
							usr_firstname = ?,
							usr_lastname = ?,
							usr_mail = ?,
							usr_mail_notif_inscript = ?,
							usr_mail_notif_version = ?,
							usr_mobile = ?,
							usr_mobile_notif = ?,
							usr_nb_art_ligne = ?
						 WHERE 
							usr_login = ?;
						";
			// $strQuery = "UPDATE csg_users 
			// 			SET 
			// 			usr_firstname = $_strFirstname,
			// 			usr_lastname = $_strLastname,
			// 			usr_mail = $_strMail,
			// 			usr_mail_notif = $_boolNotifMail,
			// 			usr_mobile = $_strMobile,
			// 			usr_mobile_notif = $_boolNotifMobile
			// 			WHERE 
			// 			usr_login = $_strLicencie;
			// 		";
			// echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->updateRow($strQuery, array($_strFirstname, $_strLastname, $_strMail, $_boolNotifMailInscript, $_boolNotifMailVersion, $_strMobile, $_boolNotifMobile, $_intNbArtParLigne, $_strLicencie));

            return $dbUser->queryOK;
            $dbUser = null;
		}
		
		// METTRE A JOUR LE MOT DE PASSE DE L'UTILISATEUR
		public function updatePassUser($_strLicencie, $_strPass) {
			// NOUVELLE INSTANCE DE MODEL DB
			$dbUser = new ClassDb();
			// REQUETE
			$strQuery = "UPDATE csg_users 
						SET 
							usr_pass = ?
						WHERE 
							usr_login = ?;
						";
			// $strQuery = "UPDATE csg_users 
			// 			SET 
			// 			usr_pass = $_strPass
			// 			WHERE 
			// 			usr_login = $_strLicencie;
			// 		";
			// echo $strQuery;
			// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
			$result = $dbUser->updateRow($strQuery, array($_strPass, $_strLicencie));

			return $dbUser->queryOK;
			$dbUser = null;
		}
				
    }
?>