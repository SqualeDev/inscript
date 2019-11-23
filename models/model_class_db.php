<?php
	// MODEL DB : INTERACTION AVEC LA BASE
	class ClassDb {
		public $queryOK;
		public $queryNbRows;

		// CHAINE DE CONNEXION
		protected $dsn;
		// PDO OBJECT
        protected $datab;
		// ETAT CONNEXION
		private $isConnected;

		private $_db_host;
		private $_db_port;
		private $_db_name;
		private $_db_user;
		private $_db_mdp;
		private $_db_options;

		// CONSTRUCTEUR
		function __construct() {
			require_once PATH_ROOT . 'constantes.php';

			// APPEL DES CONSTANTES
			global $db_driv, $db_host, $db_port, $db_name, $db_user, $db_pass, $db_options;
            
			$this->isConnected	= false;
			$this->queryOK		= false;
			$this->queryNbRows	= 0;
			// affecter les constantes à des variables privées
			$this->_db_driv     = $db_driv;
			$this->_db_host     = $db_host;
			$this->_db_port     = $db_port;			
			$this->_db_name     = $db_name;
			$this->_db_user     = $db_user;
			$this->_db_pass     = $db_pass;
			
			$this->_db_options  = $db_options;
		}

		// CONNEXION A LA BASE DE DONNEES
		// RETURN	PDO OBJECT or FALSE
		private function connect() {
			global $debug;

			try {
				switch ($this->_db_driv) {
					case 'mysql':
                        // MYSQL
                        display_debug("Connect MySQL host : ", $this->_db_host, $debug);
						display_debug("Connect MySQL name : ", $this->_db_name, $debug);
						display_debug("Connect MySQL user : ", $this->_db_user, $debug);
						display_debug("Connect MySQL pass : ", $this->_db_pass, $debug);
						$dsn = $this->_db_driv.":host=".$this->_db_host.";port=".$this->_db_port.";dbname=".$this->_db_name.";charset=utf8";
						display_debug("Connect MySQL dsn  : ", $dsn, $debug);
						$this->datab = new PDO ($dsn,  $this->_db_user, $this->_db_pass); // MYSQL
						break;
					case 'pgsql':
						// POSTGRESQL
						//$dsn = $this->_db_driv.":host=".$this->_db_host.";port=".$this->_db_port.";dbname=".$this->_db_name.";user=".$this->_db_user.";password=".$this->_db_pass;
						//$this->datab = new PDO	($dsn);
						break;
					default:
						# code...
                        break;
                }
                // EN LOCAL
				global $local;
				// CONNEXION PGSQL
				// $dsn = "pgsql:host=".$this->_db_host.";port=".$this->_db_port.";dbname=".$this->_db_name.";user=".$this->_db_user.";password=".$this->_db_mdp;

				// $datab PDO OBJECT
				// $this->datab = new PDO	($dsn);
				$this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				// les requêtes SQL renvoient un tableau associatif
				// $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				// CONNECTE
				$this->isConnected = true;
				display_debug("IsConnect MySQL : ", var_export($this->isConnected, true), $debug);
				return $this->datab;
			// EN CAS D'ECHEC : ENVOIE UNE ERREUR PDO / SQL
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// PAS CONNECTE
				$this->isConnected = false;
				display_debug("IsConnect MySQL : ", var_export($this->isConnected, true), $debug);
			}
		}

		// DECONNEXION DE LA BASE
		private function disconnect() {
			$this->datab = null;
			// PAS CONNECTE
			$this->isConnected = false;
		}

        // VERIFIE SI UNE DONNE EXISTE
        // $query	STRING	REQUETE
        // $params	ARRAY	PARAMETRES
        // RETURN	BOOLEAN
		public function existData($query, $params=array()) {
			global $debug;
			
			display_debug("existData : ", $query . '-' . implode(':', $params), $debug);
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
					// préparer la requête
					$stmt = $this->datab->prepare($query);
					// executer la requête avec les paramètres demandés
					$stmt->execute($params);
					// compter les lignes touchées par la requête
					$numRow = $stmt->rowCount();
					// si au moins une ligne est touchée, il y a des résultats
					if ($numRow > 0){
						// la requête est passée
						$this->queryOK = true;
						// les données existent
						return true;
					// si aucune ligne n'est touchée, il n'y a pas de résultat
					} else {
						// la requête n'est pas passée
						$this->queryOK = false;
						// les données n'existent pas
						return false;
					}
					// CLORE LA REQUETE
					$stmt->closeCursor();
					// DECONNEXION
					if ($this->isConnected) { $this->disconnect(); }
				}
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

        // RECUPERE DES VALEURS DEPUIS UNE TABLE, AVEC DES PARAMETRES
        // $query	STRING	REQUETE
        // $params	ARRAY	PARAMETRES
        // RETURN	ARRAY
		public function getData($query, $params=array()) {
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
					// préparer la requête
					$stmt = $this->datab->prepare($query);
					// executer la requête avec les paramètres demandés
					$stmt->execute($params);
					// compter les lignes touchées par la requête
					$numRow = $stmt->rowCount();
					// si au moins une ligne est touchée, il y a des résultats
					if ($numRow > 0){
						// la requête est passée
						$this->queryNbRows = $numRow;
						$this->queryOK = true;
						// transforme les résultats en un tableau associatif
						$results = $stmt->fetchAll();
						// retourne le tableau associatif
						return $results;
					// si aucune ligne n'est touchée, il n'y a pas de résultat
					} else {
						// la requête n'est pas passée
						$this->queryOK = false;
						// pas de données à retourner
						return false;
					}
					// CLORE LA REQUETE
					$stmt->closeCursor();
					// DECONNEXION
					if ($this->isConnected) { $this->disconnect(); }
				}
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

        // RECUPERE TOUTES LES VALEURS DEPUIS UNE TABLE
        // $query	STRING	REQUETE
        // RETURN	ARRAY
        public function getAllData($query) {
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
					// préparer la requête
					$stmt = $this->datab->prepare($query);
					// executer la requête
					$stmt->execute();
					// compter les lignes touchées par la requête
					$numRow = $stmt->rowCount();
					// si au moins une ligne est touchée, il y a des résultats
					if ($numRow > 0){
						// la requête est  passée
						$this->queryOK = true;
						$this->queryNbRows = $numRow;
						// transforme les résultats en un tableau associatif
						$results = $stmt->fetchAll();
						// retourne le tableau associatif
						return $results;
					// si aucune ligne n'est touchée, il n'y a pas de résultat
					} else {
						// la requête n'est pas passée
						$this->queryOK = false;
						// pas de données à retourner
						return false;
					}
					// CLORE LA REQUETE
					$stmt->closeCursor();
					// DECONNEXION
					if ($this->isConnected) { $this->disconnect(); }
				}
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

		// INSERER UNE LIGNE DANS UNE TABLE, AVEC DES PARAMETRES
        // $query	STRING	REQUETE
        // $params	ARRAY	PARAMETRES
        // RETURN	NOTHING
        public function insertRow($query, $params=array()){
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
				}
				// préparer la requête
				$stmt = $this->datab->prepare($query);
				// executer la requête avec les paramètres demandés
				$stmt->execute($params);
				// compter les lignes touchées par la requête
				$numRow = $stmt->rowCount();
				// si au moins une ligne a été ajoutée
				if ($numRow > 0) {
					// la requête est passée
					$this->queryNbRows = $numRow;
					$this->queryOK = true;
				// si aucune ligne n'a été ajoutée
				} else {
					// la requête n'est pas passée
					$this->queryOK = false;
				}
				// CLORE LA REQUETE
				$stmt->closeCursor();
				// DECONNEXION
				if ($this->isConnected) { $this->disconnect(); }
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

		// MODIFIER LA VALEUR D'UN CHAMP DANS UNE TABLE, AVEC DES PARAMETRES
        // $query	STRING	REQUETE
        // $params	ARRAY	PARAMETRES
		// RETURN	NOTHING
        public function updateRow($query, $params=array()){
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
					// préparer la requête
					$stmt = $this->datab->prepare($query);
					// executer la requête
					$stmt->execute($params);
					// compter les lignes touchées par la requête
					$numRow = $stmt->rowCount();
					// si au moins une ligne est mise à jour
					if ($numRow > 0){
						// la requête est passée
						$this->queryNbRows = $numRow;
						$this->queryOK = true;
						// si aucune ligne n'est mise à jour
					} else {
						// la requête est passée mais rien n'a été modifié
						$this->queryNbRows = 0;
						$this->queryOK = true;
					}
					// CLORE LA REQUETE
					$stmt->closeCursor();
					// DECONNEXION
					if ($this->isConnected) { $this->disconnect(); }
				}
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

        // SUPPRIMER UNE LIGNE DANS UNE TABLE, AVEC DES PARAMETRES
        // $query	STRING	REQUETE
        // $params	ARRAY	PARAMETRES	chaine à rechercher
        // RETURN	NOTHING
        public function deleteRow($query, $params){
			try {
				// CONNEXION
				if (!$this->isConnected) {
					$this->connect();
					// préparer la requête
					$stmt = $this->datab->prepare($query);
					// executer la requête avec les paramètres demandés
					$stmt->execute($params);
					// compter les lignes touchées par la requête
					$numRow = $stmt->rowCount();
					// si au moins une ligne est supprimée
					if ($numRow > 0){
						// la requête est passée
						$this->queryNbRows = $numRow;
						$this->queryOK = true;
					// si aucune ligne n'est supprimée
					} else {
						// la requête est passée mais rien n'a été modifié
						$this->queryNbRows = 0;
						$this->queryOK = true;
					}
					// CLORE LA REQUETE
					$stmt->closeCursor();
					// DECONNEXION
					if ($this->isConnected) { $this->disconnect(); }
				}
			// EN CAS D'ECHEC : envoie une erreur PDO
			} catch(PDOException $e) {
				// echo $e->getMessage() . '<br/>';
				// la requête n'est pas passée
				$this->queryOK = false;
			}
		}

    }
?>
