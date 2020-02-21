<?php

namespace App\Controller\API;

use Legacy\WSAdmin;
use Symfony\Component\HttpFoundation\Response;

/**
 * ServersStatus API
 */
final class ServersStatus
{
    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        return new Response(
            json_encode(array_merge($this->getEtatWSSignification())),
            200
        );
    }

    /**
     * [getEtatWSSignification description]
     *
     * @return array
     */
    private function getEtatWSSignification()
    {
        $wss_dev   = WSAdmin::getInstance("wssdev");
        $data_dev  = $wss_dev->status();
        $wss_prod  = WSAdmin::getInstance("wssprod");
        $data_prod = $wss_prod->status();
        $tmp       = array();

        if (!empty($data_dev) && $data_dev->status == "OK") {
            $tmp["wss_exchanger_dev"] = nvl($data_dev->exchanger_engine, "");
            $tmp["wss_trigger_dev"]   = nvl($data_dev->trigger_engine, "");
            $tmp["wss_post_dev"]      = nvl($data_dev->post_engine, "");
        }

        if (!empty($data_prod) && $data_prod->status == "OK") {
            $tmp["wss_exchanger_prod"] = nvl($data_prod->exchanger_engine, "");
            $tmp["wss_trigger_prod"]   = nvl($data_prod->trigger_engine, "");
            $tmp["wss_post_prod"]      = nvl($data_prod->post_engine, "");
        }

        return $tmp;
    }

}
