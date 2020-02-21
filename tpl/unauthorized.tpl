<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion incidents LEGACY - Accès interdit</title>
        <link href="/public/js/lib/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/public/css/login.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12" style="text-align:center">
                    <img src="/public/images/logo_gris.png" width="300">
                </div>
            </div>

            <h2>Accès interdit</h2>
            <p>Vous n'avez pas les droits suffisants pour accéder à la page <strong>{$destination}</strong>.</p>
            <p>Nous vous invitons à vous connecter avec un compte disposant des autorisations nécessaires.</p>
            <ul>
                <li>
                    <a href="/login">page de connexion</a>
                </li>
                <li>
                    <a href="/dashboard">retour au tableau de bord</a>
                </li>
            </ul>
        </div>
    </body>
</html>
