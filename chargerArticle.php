<?php
    require_once('class_fonction/db.class.php');
    require_once('vendor/autoload.php');
    require_once('fonction.php');
    require_once('mot_vides.php');
    header('Content-type: text/html; charset=utf-8');


$dir = "C:/Users/Ame-Ahmada-Ngom/Desktop/Master2/articles/*";

$tab = array();

$files = glob($dir);
$i = 0;
$bd = new BD();
foreach ($files as $file) {
    if (is_file($file)) {
        $contents = file_get_contents($file);
        if ($contents === false) {
            echo "Error reading file: $file";
        } else {
            echo "Fichier : ".$i." ; Contents of $file:<br>";
            //echo nl2br(htmlspecialchars($contents));
            $tab[$i] = json_decode($contents);
            $sql = 'INSERT INTO article values("","' . $tab[$i]->identifiantArticle . '","' . $tab[$i]->thematiqueArticle . '","' . $tab[$i]->titreArticle . '","' . $tab[$i]->resumeArticle . '","' . $tab[$i]->auteurArticle . '","' . $tab[$i]->contenuArticle . '")';
            //print_r($j);
            $bd->insererBD($sql);
            $i++;
        }
    }
}
echo "<br>--------------------------------------<br>";
    
?>