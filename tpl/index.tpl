<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Incident</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Gestion incidents LEGACY">
        <link href="/min/?g=css" rel="stylesheet">
    </head>
    <body>
        <nav id="navbar"></nav>

        <div id="alerts" class="container-fluid"></div>

        <div id="pages" class="container-fluid fill">
            <div id="dashboard"></div>
            <div id="issues"></div>
            <div id="list"></div>
            <div id="create"></div>
            <div id="maintenance"></div>
            <div id="rights"></div>
            <div id="stats"></div>
            <div id="categories"></div>
        </div>

        <script>
            const incident_revision = '{$version}';
            const agent = '{$username}';
            const agentId = '{$userId}';
        </script>
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="/min/?g=js-fw,js-wk&debug=1"></script>
    </body>
</html>
