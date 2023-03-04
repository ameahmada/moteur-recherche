<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://127.0.0.1/templates/font-awesome/css/all.css">
    <link rel="stylesheet" href="http://127.0.0.1/templates/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="http://127.0.0.1/templates/bootstrap/dist/js/bootstrap.js"></script>
    <script src="http://127.0.0.1/templates/font-awesome/js/all.js"></script>
    <script src="js/JQuery3.3.1.js"></script>

    <title>Moteur</title>
</head>

<body>
    <div class="container" style="text-align: center; margin-top:10%; width:60%;">
        <div class="row" style="width: 100%;">
            <div class="col-12">
                <h3>Moteur name</h3>
            </div>
            <div class="col-12 divRecherche">
                <form class="col-12 align-center" action="#">
                    <input type="text" placeholder="cherche" name="requete" id="requete" oninput="loadPost();">
                    <button id="btnRequete"><i class="fab fa-telegram-plane"></i></button>
                </form>
            </div>
            <div id="result">
                <!-- Resultat de la recherche -->
            </div>
        </div>
    </div>
</body>

<script>
    // Charger les postes de la page d'accueil */
    function loadPost() {
        var valRequete = document.querySelector("#requete").value;

        // URL de la requête avec les données ajoutées
        var url = 'moteur.php?' + "requete=" + valRequete;

        $.ajax({
            type: 'get',
            url: url,
            data: null,
            success: function(data) {
                console.log(data);
                //console.log(data == null);
                //var articles = data;
                const articles = JSON.parse(data);
                //console.log(articles.length);
                //console.log(articles[0]['contenu']);
                var contenu = ``;
                //const post = JSON.parse(data.articles);
                var taille = articles.length;
                console.log(taille);
                let i = 0;
                $("#result").html("");
                if (taille > 0) {
                    for (i = 0; i < taille; i++) {
                        console.log(i);
                        if (i == 0) {
                            contenu = `<div class="row col-sm-12 col-12" id="doc">
                                        <div class="row col-sm-12 col-12 lien">
                                            <a href=${articles[i]['url']} class="divdoc1">
                                                <span class="titre">${articles[i]['titre']}</span>
                                                <span class="score">${articles[i]['rang']}</span>
                                            </a>
                                        </div>
                                    </div>`;
                        } else {
                            contenu = `<hr><div class="row col-sm-12 col-12" id="doc">
                                        <div class="row col-sm-12 col-12 lien">
                                            <a href=${articles[i]['url']} class="divdoc1">
                                                <span class="titre">${articles[i]['titre']}</span>
                                                <span class="score">${articles[i]['rang']}</span>
                                            </a>
                                        </div>
                                    </div>`;
                        }

                        //contenu = `<p>${data.articles[i]['description']} </p>`;
                        $("#result").append(contenu);
                        if (i === 10) {
                            break; // sortir de la boucle prématurément si i est égal à 5
                        }
                    }
                } else {
                    $("#result").append("");
                }
                //$("#result").html(contenu);
            },
            error: function(xhr, status, error) {
                // Code à exécuter en cas d'erreur de la requête
                console.log(xhr.responseText);
            }
        });
    }
    // Reactualiser la page tous les 30 secondes 
    //var refresh = setInterval(loadPost, 30 * 1000);

    //loadPost();
</script>

</html>