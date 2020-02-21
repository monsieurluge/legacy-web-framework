<?php

namespace Legacy;

use Legacy\MaintenanceInterface;
use Legacy\WSAdmin;

/**
 * Maintenance
 */
class Maintenance implements MaintenanceInterface
{

    /**
     * @inheritDoc
     */
    public function actionBouton($code) {
        $json         = (object) array();
        $json->status = "OK";

        switch ($code) {
            case "wss_start_dev":
                $wss = WSAdmin::getInstance("wssdev");
                $wss->start();
                break;
            case "wss_stop_dev":
                $wss = WSAdmin::getInstance("wssdev");
                $wss->stop();
                break;
            case "wss_shutdown_dev":
                $wss = WSAdmin::getInstance("wssdev");
                $wss->shutdown();
                break;
            case "wss_relaunch_dev":
                $wss = WSAdmin::getInstance("wssdev");
                $wss->relaunch();
                break;
            case "wsj_start_dev":
                $wss = WSAdmin::getInstance("wsjdev");
                $wss->start();
                break;
            case "wsj_stop_dev":
                $wss = WSAdmin::getInstance("wsjdev");
                $wss->stop();
                break;
            case "wsj_shutdown_dev":
                $wss = WSAdmin::getInstance("wsjdev");
                $wss->shutdown();
                break;
            case "wsj_relaunch_dev":
                $wss = WSAdmin::getInstance("wsjdev");
                $wss->relaunch();
                break;
            case "wss_start_prod":
                $wss = WSAdmin::getInstance("wssprod");
                $wss->start();
                break;
            case "wss_stop_prod":
                $wss = WSAdmin::getInstance("wssprod");
                $wss->stop();
                break;
            case "wss_shutdown_prod":
                $wss = WSAdmin::getInstance("wssprod");
                $wss->shutdown();
                break;
            case "wss_relaunch_prod":
                $wss = WSAdmin::getInstance("wssprod");
                $wss->relaunch();
                break;
            default:
                $json->status = 'KO';
                $json->erreur = 'Action code ' . $code . ' inexistante';
        }

        return $json;
    }

}
