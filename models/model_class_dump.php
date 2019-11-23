<?php
 
/**
 * MySqlDump Exporter une base de donnee MySql avec Pdo
 *
 * @author Fobec 06/14
 * @see http://www.fobec.com/php5/1148/exporter-une-base-donnee-mysql-avec-pdo.html
 */
 
class MySqlDump {
 
    private $DB_CONF =
            array('host' => '', 'name' => '', 'user' => '',
        'password' => '', 'filter' => '*'
    );
    private $colInfo = array();
    private $MAX_SQLFILE_SIZE = 1000; //in Mo
 
    public function dumpDatabase($host, $dbname, $user, $password, $filter = '*') {
        $this->DB_CONF = array('host' => $host,
        'name' => $dbname,
        'user' => $user,
        'password' => $password,
        'filter' => $filter);
 
        $files_dumpsql = $this->backupDB($this->DB_CONF);
        var_dump($files_dumpsql);
        
        $date = new DateTime();
        $result = $date->format('Ymd-His');
        echo $result;
        $result = $this->create_zip($files_dumpsql, $_SERVER['DOCUMENT_ROOT'] . '/licencies/sql/backups/' . 'csg_dump_'.$result.'.zip');
    }

    /**
     * Fixer la taille maximale du fichier d'export Sql
     * @param type $maxsize taille en Mo
     */
    public function setMaxFileSize($maxsize) {
        $this->MAX_SQLFILE_SIZE = $maxsize;
    }
 
    /**
     * Lancer l'export d'une base de donnée
     * @param type $db_conf
     * @return type
     */
    private function backupDB($db_conf) {
        set_time_limit(60);
        $file_dumptable = array();
 
        try {
            $dsn = "mysql:host=". $db_conf['host'].";port=3306;dbname=".$db_conf['name'].";charset=utf8";
			$pdo = new PDO ($dsn,  $db_conf['user'], $db_conf['password']); // MYSQL
			
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
            //List table from DBname
            $stmt = $pdo->prepare('SHOW TABLES');
            $stmt->execute();
 
            /** Table filter * */
            $filter_table = explode(';', $db_conf['filter']);
 
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                if (in_array($row[0], $filter_table) || $db_conf['filter'] == '*') {
                    $tables[] = $row[0];
                }
            }
            $stmt->closeCursor();
            $pdo = null;
 
            /* Dump each Table **/
            foreach ($tables as $tablename) {
                $ntemp = $this->backupTable($db_conf, $tablename);
                foreach ($ntemp as $sqlfile) {
                    $file_dumptable[] = $sqlfile;
                }
            }//end loop table
 
            return $file_dumptable;
        } catch (PDOException $err) {
            $msg = __METHOD__ . ' - ' . $err->getMessage();
            echo $msg;
            $this->pdo = NULL;
        }
    }
 
    /**
     * Export Table to SQL File, files can be split
     * @param type $db_conf
     * @param type $tablename
     * @return string
     */
    private function backupTable($db_conf, $tablename) {
        $dsn = "mysql:host=". $db_conf['host'].";port=3306;dbname=".$db_conf['name'].";charset=utf8";
        $pdo = new PDO ($dsn,  $db_conf['user'], $db_conf['password']); // MYSQL
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        //Avoid General error: 2008 MySQL client ran out of memory
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
 
        $sql_files = array();
        //Open new SQL File
        $filelabel = $tablename . '.sql';
        $tmpfile = $_SERVER['DOCUMENT_ROOT'] . '/licencies/sql/backups/' . $filelabel;

        $sql_files[] = array('file' => $tmpfile, 'name' => $filelabel);
        $fhandle = fopen($tmpfile, 'w');
 
        //Create Table SQL
        $stmt = $pdo->prepare('SHOW CREATE TABLE ' . $tablename);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_NUM);
        $buf = '-- MySqlDump v.09 ' . "\n";
        $buf.='-- http://www.fobec.com/php5/1148/exporter-une-base-donnee-mysql-avec-pdo.html' . "\n\n";
        $buf.='-- server: ' . $db_conf['host'] . "\n";
        $buf.='-- date: ' . date('d/m/Y H:i:s') . "\n";
        $buf.='-- db: ' . $db_conf['name'] . "\n\n";
        $buf.='-- create table: ' . $tablename . "\n\n";
        $buf.= $rs[1] . ';' . "\n\n";
        fwrite($fhandle, $buf);
        $stmt->closeCursor();
 
        //colmuns type
        $stmt = $pdo->prepare('SHOW COLUMNS FROM ' . $tablename);
        $stmt->execute();
        $this->colInfo = $stmt->fetchAll(PDO::FETCH_NUM);
        $col_mapping = $this->parseColInfo($this->colInfo);
        $stmt->closeCursor();
 
        //Select All
        $stmt = $pdo->prepare('SELECT * FROM ' . $tablename);
        $stmt->execute();
 
        $line = '';
        $buf = '';
        $filecount = 2;
 
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $buf = "INSERT INTO " . $tablename . " VALUES (";
 
            $len = count($row);
            //Buffer value add slash if dif. than int
            for ($i = 0; $i < $len; $i++) {
                if ($i != ($len - 1)) {
                    $buf.=$this->setValue($row[$i], $col_mapping[$i]) . ',';
                } else {
                    $buf.=$this->setValue($row[$i], $col_mapping[$i]);
                }
            }

            $line.=$buf . ");\n";
            $buf = '';
 
            //Write SQL commands to file
            fwrite($fhandle, $line);
 
            //Start new file if max size reach
            if (ftell($fhandle) > (1024 * 1024 * $this->MAX_SQLFILE_SIZE)) {
                fclose($fhandle);

                $sql_files[] = array('file' => $tmpfile, 'name' => $filelabel);
                // $sql_files[] = array($tmpfile);

                $filelabel = $tablename . '_' . $filecount . '.sql';
                $tmpfile = $_SERVER['DOCUMENT_ROOT'] . '/licencies/sql/backups/' . $filelabel;
                $filecount++;
                $fhandle = fopen($tmpfile, 'w');
            }
            $line = '';
        }
 
        fclose($fhandle);
 
        $stmt->closeCursor();
        $pdo = null;
 
        return $sql_files;
    }

    /**
     * Add Slashes if Value is String
     * @param type $val
     * @param type $isInt
     * @return string
     */
    private function setValue($val, $isInt) {
        if ($isInt) {
            return $val;
        } else {
            if (!empty($val)) {
                $sval = addslashes($val);
                return "'" . $sval . "'";
            } else {
                return "''";
            }
        }
    }
 
    /**
     * Build Field array with field type
     * @param type $rs
     * @return boolean
     */
    private function parseColInfo($rs) {
        $len = count($rs);
        $map = array();
        for ($i = 0; $i < $len; $i++) {
            if (substr($rs[$i][1], 0, 3) == 'int' || $rs[$i][1] == 'float') { //'Type'
                $map[] = true;
            } else {
                $map[] = false;
            }
        }
        return $map;
    }

    /* creates a compressed zip file */
    private function create_zip($files = array(),$destination = '',$overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        $i = 0;
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
                $tmp_files = array();

                if(file_exists($file['file'])) {
                    $tmp_files['file'] =  $file['file']; 
                    $tmp_files['name'] =  $file['name'];
                    $valid_files[$i] = $tmp_files;
                    $i++;
                }
            }
        }
        // var_dump($valid_files);

        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                //     echo $file;
                echo $file['name'] . " => " . $file['file']. "<br>";
                // $zip->addFile($file,$file);
                $zip->addFile($file['file'], $file['name']);
            }
            
            //debug
            echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            
            //close the zip -- done!
            $zip->close();
            
            //check to make sure the file exists
            return file_exists($destination);
        }
        else
        {
            return false;
        }
    }
}
?>