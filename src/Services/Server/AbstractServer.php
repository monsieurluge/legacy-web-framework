<?php

namespace App\Services\Server;

abstract class AbstractServer
{

    abstract protected function host(): string;

    /**
     * Sends the command to the server
     * @param string $command
     * @return mixed
     * @throws Exception
     */
    protected function sendCommand(string $command)
    {
        list($hostname, $port) = explode(':', $this->host);

        $sock = @fsockopen($hostname, $port, $errno, $errstr, 10);

        if (false === $sock) {
            throw new Exception('erreur à l\'ouverture du socket : ' . $errno . ' - ' . $errstr);
        }

        //on est connecté, on écrit la requete
        fwrite($sock, "GET /" . $command . " fsockopen"); //Entete get

        fwrite($sock, "\r\n\r\n"); //REQUEST END !!

        // On attend / lit la réponse
        $resultRaw = '';

        while (!feof($sock)) {
            $resultRaw .= fgets($sock, 256);
        }

        fclose($sock);

        //On sépare l'entete des datas
        $results = explode("\r\n", $resultRaw);
        $index   = 0;

        while (false === empty($results[$index])) {
            $index++;
        }

        $result = implode(array_slice($results, $index + 1), "\r\n");

        return json_decode($result);
    }

}
