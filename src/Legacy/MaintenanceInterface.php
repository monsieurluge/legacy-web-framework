<?php

namespace Legacy;

interface MaintenanceInterface
{

    /**
     * Returns a server management dedicated button (HTML)
     *
     * @param  string $code the server's name
     * @return object
     */
    public function actionBouton($code);

}
