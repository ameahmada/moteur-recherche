<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://127.0.0.1/templates/font-awesome/css/all.css">
    <link rel="stylesheet" href="http://127.0.0.1/templates/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="css/style2.css">
    <script src="http://127.0.0.1/templates/bootstrap/dist/js/bootstrap.js"></script>
    <script src="http://127.0.0.1/templates/font-awesome/js/all.js"></script>
    <script src="js/JQuery3.3.1.js"></script>

    <title>Moteur</title>
</head>

<body>
    <div class="registerBg"> <br /></div>
        <div class="container row m-0 p-3">

        <?php

        require_once('class_fonction/db.class.php');
        require_once('vendor/autoload.php');
        require_once('fonction.php');
        require_once('mot_vides.php');
        header('Content-type: text/html; charset=utf-8');

        use Wamania\Snowball\StemmerFactory;
        //Langage du stemmer français
        $stemmer = StemmerFactory::create('french');

        // Traitements de la requete
        // Recuperer de la requete de recherche
        if (isset($_GET['requete']) and !empty($_GET['requete'])) {
            $requete = $_GET['requete'];

            // Triatements de la requete
            $requete = tokenisation($requete, $motsVides);

            // Tokénisation (Convertir le texte en tableau de termes)
            $tab = explode(" ", $requete);

            //Lematisation des termes de la requetes
            $taille = count($tab);
            for ($i = 0; $i < $taille; $i++) {
                $tab[$i] = $stemmer->stem($tab[$i]);
            }

            $terms = array_unique($tab);
            sort($terms);

            //print_r($terms);

            // Lancement de la requete à la base de données
            $chaines = '';
            $nbTerms = count($terms);
            $chaines = '"%' . $terms[1] . '%" )';
            for ($i = 2; $i < $nbTerms; $i++) {
                $chaines = $chaines . ' OR (contenu LIKE "%' . $terms[$i] . '%" )';
            }

            $sql = "SELECT * FROM article where (contenu LIKE " . $chaines . " ORDER BY id";
            //$sql = "SELECT * FROM article where (contenu LIKE 'a')";
            //echo "<br>".$sql."<br>";


            //Connexion à la base de données
            $bd = new BD();
            $datas = $bd->query2($sql);

            $document = $datas['data'];

            $nbDoc = count($document);

            //echo "<br> nombre de documents : ".$nbDoc."<br>";
            

            //Tester si nombre de document non nul
            if ($nbDoc > 0) {
                //print_r($article);

                //print_r($datas);

                //Matrice termes documents
                $matrice = array();
                $k = 0;
                //echo "<br> Le premier terme est : ".$terms[1]."<br>";
                for ($i = 0; $i < $nbDoc; $i++) {
                    $texts =  $document[$i]['contenu'];
                    for ($k = 1; $k < $nbTerms; $k++) {
                        $mot = $terms[$k];
                        //echo $mot . " - ";
                        $occurrences = substr_count($texts, $mot);
                        //$matrice[$i][$terms[$j]] = 0;
                        //$textes = tokenisation($article[$i],$motsVides);
                        $matrice[$i][$k] = (($occurrences / $nbTerms) * (1 / $nbDoc));
                    }
                }
                //print_r($matrice);

                //Page rank

                // Définition du facteur d'amortissement
                $dampingFactor = 0.85;

                // Calcul du nombre de nœuds dans le graphe
                $numNodes = count($matrice);

                if ($numNodes != 0) {
                    // Initialisation des scores PageRank pour chaque nœud
                    $pageRank = array_fill(0, $numNodes, 1 / $numNodes);

                    // Boucle à travers les itérations PageRank
                    for ($i = 0; $i < 10; $i++) {
                        // Initialisation des nouveaux scores PageRank pour chaque nœud
                        $newPageRank = array_fill(0, $numNodes, 0);

                        // Boucle à travers chaque nœud dans le graphe
                        for ($j = 0; $j < $numNodes; $j++) {
                            // Boucle à travers les termes
                            for ($k = 1; $k < $nbTerms; $k++) {
                                if ($matrice[$j][$k] > 0) {
                                    // Mise à jour du score PageRank pour le nœud actuel
                                    $newPageRank[$j] += $pageRank[$j] / count(array_keys($matrice[$j]));
                                }
                            }

                            // Application du facteur d'amortissement et ajout du facteur de saut aléatoire
                            $newPageRank[$j] = (1 - $dampingFactor) / $numNodes + $dampingFactor * $newPageRank[$j];
                        }

                        // Mise à jour des scores PageRank pour chaque nœud
                        $pageRank = $newPageRank;
                    }

                    // Affichage des scores PageRank finaux pour chaque nœud
                    //print_r($pageRank);
                    //Ajout des rangs dans les documents
                    // Ajouter une colonne "rang" à chaque ligne
                    $l = 0;
                    foreach ($document as &$ligne) {
                        $ligne["rang"] = $pageRank[$l];
                        $l++;
                    }

                    // Fonction de comparaison
                    function comparer_rang($a, $b)
                    {
                        if ($a['rang'] == $b['rang']) {
                            return 0;
                        }
                        return ($a['rang'] > $b['rang']) ? -1 : 1;
                    }

                    usort($document, 'comparer_rang');

                    // Affichage des resultat
                    echo '<div class="col-12">';

                    $i = 0;
                    foreach($document as $doc){
                        if($i == 0){
                            echo '<div class="row col-sm-12 col-12" id="doc">
                                            <div class="row col-sm-12 col-12">
                                                <p>'.$doc['resume']. '</p>
                                            </div>
                                            <div class="row col-sm-12 col-12 lien">
                                                <a href=' . $doc['url'] . ' class="divdoc1">
                                                    <span class="titre">' . $doc['titre'] . '</span>
                                                    <span class="score">' . $doc['rang'] . '</span>
                                                </a>
                                            </div>
                                        </div>';
                        }else{
                            echo '<hr><div class="row col-sm-12 col-12" id="doc">
                                            <div class="row col-sm-12 col-12">
                                                <p>' . $doc['resume'] . '</p>
                                            </div>
                                            <div class="row col-sm-12 col-12 lien">
                                                <a href=' . $doc['url'] . ' class="divdoc1">
                                                    <span class="titre">' . $doc['titre'] . '</span>
                                                    <span class="score">' . $doc['rang'] . '</span>
                                                </a>
                                            </div>
                                        </div>';
                        }
                        $i++;
                    }

                    echo '</div>';

                } else {
                    //echo $_GET['requete']."001";
                    echo "<h3>Pas de resultats disponible </h3>";
                }
            } else{
                //echo $_GET['requete'] . "002";
                echo "<h3>Pas de resultats disponible </h3>";
            }
        } else {
            echo "<h3>Pas de resultats disponible </h3>";
        }
        ?>
        </div>
    </div>
</body>
</html>