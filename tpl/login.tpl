<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <title>Gestion incidents LEGACY - Connexion</title>
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

            <form class="form-signin" action="/login" method="post">
                <h2 class="form-signin-heading">Gestion incidents</h2>
                <input type="text" class="form-control" name="login" placeholder="Login" autofocus>
                <input type="password" class="form-control" name="password" placeholder="Mot de passe">
                {if !empty($message)}
                    <div class="alert alert-warning">{$message}</div>
                {/if}
                <button class="btn btn-lg btn-default btn-block" type="submit">Connexion <span class="glyphicon glyphicon-log-in"></span></button>
            </form>
        </div>
    </body>
</html>
