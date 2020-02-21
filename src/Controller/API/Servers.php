<?php

namespace App\Controller\API;

use Symfony\Component\HttpFoundation\Response;

/**
 * Servers API
 */
final class Servers
{
    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        return new Response(
            json_encode($this->servers()),
            200
        );
    }

    /**
     * Returns the servers list.
     *
     * @return array
     */
    private function servers(): array
    {
        return [
            [
                'nom' => 'Signification - Web Service DEV',
                'flags' => [
                    ['nom' => 'Serveur d\'échange web', 'id' => 'wss_exchanger_dev'],
                    ['nom' => 'Serveur de retour', 'id' => 'wss_trigger_dev'],
                    ['nom' => 'Serveur de postage de fichier', 'id' => 'wss_post_dev']
                ],
                'btns' => [
                    ['nom' => 'Start', 'type' => 'primary', 'code' => 'wss_start_dev'],
                    ['nom' => 'Stop', 'type' => 'primary', 'code' => 'wss_stop_dev'],
                    ['nom' => 'Reboot', 'type' => 'warning', 'code' => 'wss_relaunch_dev'],
                    ['nom' => 'Arrêt complet', 'type' => 'danger', 'code' => 'wss_shutdown_dev']
                ]
            ],
            [
                'nom' => 'Signification - Web Service PROD',
                'flags' => [
                    ['nom' => 'Serveur d\'échange web', 'id' => 'wss_exchanger_prod'],
                    ['nom' => 'Serveur de retour', 'id' => 'wss_trigger_prod'],
                    ['nom' => 'Serveur de postage de fichier', 'id' => 'wss_post_prod']
                ],
                'btns' => [
                    ['nom' => 'Start', 'type' => 'primary', 'code' => 'wss_start_prod'],
                    ['nom' => 'Stop', 'type' => 'primary', 'code' => 'wss_stop_prod'],
                    ['nom' => 'Reboot', 'type' => 'warning', 'code' => 'wss_relaunch_prod'],
                    ['nom' => 'Arrêt complet', 'type' => 'danger', 'code' => 'wss_shutdown_prod']
                ]
            ]
        ];
    }
}
