<?php
    require_once('class_fonction/db.class.php');
    require_once('vendor/autoload.php');
    require_once('fonction.php');
    require_once('mot_vides.php');
    header('Content-type: text/html; charset=utf-8');

    use Wamania\Snowball\StemmerFactory;
    //Langage du stemmer français
    $stemmer = StemmerFactory::create('french');

    //Connexion à la base de données
    $bd = new BD();
    $datas = $bd->query("article");
    $donne = $datas['data'];
    //var_dump($donne[0]);
    $len = count($donne);
    $article = array();
    $j = 0;
    while ($j < $len) {
        $article[$j] = $donne[$j]['contenu'];
        $j++;
    }
    $nbArticle = count($article);
    //print_r($article);
    echo "<br>".$nbArticle."<br>";
    $mat = array();
    $i = 0;

    //Convertir les articles choisis en tableau de mot (Tokénisation)
    for($i=0; $i<$nbArticle;$i++){
        $texte = tokenisation($article[$i],$motsVides);
        $tableau = explode(" ",$texte);
        $mat[$i] = array_unique($tableau);
    }

    echo "<br>"."_____________________________________________________________"."<br>";
    // Regrouper l'ensemble des termes dans un tableau
    $tab = array();
    $swap = array();
    foreach($mat as $mats){
        $tab = array_merge($swap,$mats);
        $swap = $tab;
    }

    //print_r($tab);
    // Stemming
    $taille = count($tab);
    for($i=0 ; $i<$taille; $i++){
        $tab[$i] = $stemmer->stem($tab[$i]);
    }

    $terms = array_unique($tab);
    sort($terms);
    //print_r($terms);

    
    //Matrice termes documents
    $nbTerms = count($terms);
    echo "<br>".$nbTerms."<br>";
    //print_r($terms);
    $matrice = array();
    $k = 0;
    //echo "<br> Le premier terme est : ".$terms[1]."<br>";
    for($i = 0 ; $i < $nbArticle ; $i++){
        $texts =  $article[$i];
        for($k = 1; $k < $nbTerms; $k++) {
            $mot = $terms[$k];
            echo $mot." - ";
            $occurrences = substr_count($texts, $mot);
            //$matrice[$i][$terms[$j]] = 0;
            //$textes = tokenisation($article[$i],$motsVides);
            $matrice[$i][$terms[$k]] = $occurrences;
        }
    }
    print_r($matrice);
?>