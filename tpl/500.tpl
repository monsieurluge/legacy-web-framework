<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Incident - Page non trouvée</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Gestion incidents LEGACY">
        <link rel="stylesheet" href="/public/css/error-pages.css">
    </head>

    <body>
        <div id="main-content" class="error-content">
            <h1>(╯°□°）╯︵ ┻━┻</h1>

            <p class="subtitle">Le serveur n'a pas été en mesure de traiter correctement la requête.</p>

            <div class="separator"></div>

            <button type="button" name="button" id="report-button" class="button-red">envoyer le rapport d'erreur</button>

            <ul class="links">
                <li><a href="/">Retour à l'accueil<a/></li>
            </ul>
        </div>

        <div id="report-modal" class="hidden">
            <div class="content">
                <h1>Rapport d'erreur</h1>
                <form action="#" method="post" id="report-form" class="modal-form">
                    <label for="description">Informations supplémentaires</label>
                    <textarea name="description" value="" rows="5" placeholder="ex: dernière action réalisée"></textarea>
                    <input type="hidden" name="trace" value="{$report}">
                    <button type="submit" name="report-button" id="send" class="button-blue">envoyer</button>
                </form>
            </div>
        </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="/public/js/error-pages.js"></script>
</html>
