<?php
    declare(strict_types=1);    
    class BD {
        private $host = 'localhost';
        private $username = 'root';
        private $password = '';
        private $databasename = 'moteur';
        private $bd;

        public function __construct()
        {
            try {
                $this->bd = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->databasename, $this->username, $this->password);
            } catch (PDOException $e) {
                die('<script>alert("Impossible de se connecter à la base de donnée");</script>');
            }
        }

        /*public function __construct($host = null, $username = null,$databasename = null, $password = null){
            if ($host != null){
                $this->host = $host;
                $this->username = $username;
                $this->databasename = $databasename;
                $this->password = $password;
            }
            try{
                $this->bd = new PDO('mysql:host='.$this->host.';dbname='.$this->databasename, $this->username, $this->password);
            }catch(PDOException $e){
                die('<script>alert("Impossible de se connecter à la base de donnée");</script>');
            }
        }*/
        
        public function query($table){
            $sql = "SELECT * FROM ".$table."";
            $req = $this->bd->query($sql);
            $data = array();
            $i = 0;
            while ($donne = $req->fetch()) {
                $data[$i]['url'] = $donne['url'];
                $data[$i]['thematique'] = $donne['thematique'];
                $data[$i]['titre'] = $donne['titre'];
                $data[$i]['resume'] = $donne['resume'];
                $data[$i]['contenu'] = $donne['contenu'];
                $i++;
            }
            return array('data'=>$data);
            //return $req->fetch();
        }

        public function query2($sql)
        {
            $req = $this->bd->query($sql);
            $data = array();
            $i = 0;
            while ($donne = $req->fetch()) {
                $data[$i]['id'] = $donne['id'];
                $data[$i]['url'] = $donne['url'];
                $data[$i]['thematique'] = $donne['thematique'];
                $data[$i]['titre'] = $donne['titre'];
                $data[$i]['resume'] = $donne['resume'];
                $data[$i]['contenu'] = $donne['contenu'];
                $i++;
            }
            return array('data' => $data);
            //return $req->fetch();
        }

        public function insererBD($sql){
            $this->bd->query($sql);
        }
        public function update($sql){
            $this->bd->query($sql);
        }

        public function querys($sql){
            //$req = $this->bd->query($sql) or die(print_r($bd->errorInfo();
            $req = $this->bd->query($sql) or die(print_r($this->bd->errorInfo()));
            //echo '<script>alert(\'Requette reuissie..............\');</script>'; 
            return $req->fetchAll(PDO::FETCH_OBJ);
        }
    }
?>