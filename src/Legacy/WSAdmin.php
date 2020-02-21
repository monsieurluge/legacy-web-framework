<?php

namespace Legacy;

use Exception;

/**
 * WSAdmin
 */
class WSAdmin
{

    /** @var array */
    private static $instances = array();
    /** @var string */
    private $host = null;
    /** @var array */
    private static $hosts = array( // TODO extraire dans conf
        'local'   => "localhost:1234"
    );

    /**
     * Creates a WSAdmin object
     * @param string $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * TODO [getInstance description]
     * @param string     $phase local, dev ou prod
     * @return WSAdmin
     * @throws Exception
     */
    public static function getInstance($phase)
    {
        if (!isset(self::$instances[$phase])) {
            self::$instances[$phase] = new WSAdmin(self::$hosts[$phase]);
        }

        return self::$instances[$phase];
    }

    /**
     * TODO [start description]
     * @return [type]
     */
    public function start()
    {
        return $this->sendRequest("start");
    }

    /**
     * TODO [stop description]
     * @return [type]
     */
    public function stop()
    {
        return $this->sendRequest("stop");
    }

    /**
     * TODO [shutdown description]
     * @return [type]
     */
    public function shutdown()
    {
        return $this->sendRequest("shutdown");
    }

    /**
     * TODO [relaunch description]
     * @return [type]
     */
    public function relaunch()
    {
        return $this->sendRequest("relaunch");
    }

    /**
     * TODO [status description]
     * @return [type]
     */
    public function status()
    {
        return $this->sendRequest("status");
    }

    /**
     * TODO [sendRequest description]
     * @param  string $functionToCall
     * @return [type]
     * @throws Exception
     */
    private function sendRequest($functionToCall)
    {
        try {
            list($hostname, $port) = explode(':', $this->host);
            $sock                  = @fsockopen($hostname, $port, $errno, $errstr, 10);

            if (!$sock) {
                throw new Exception("ERREUR OUVERTURE SOCKET : " . $errno . " - " . $errstr);
            }

            //on est connecté, on écrit la requete
            fwrite($sock, "GET /" . $functionToCall . " fsockopen"); //Entete get

            fwrite($sock, "\r\n\r\n"); //REQUEST END !!

            // On attend / lit la réponse
            $result_raw = '';

            while (!feof($sock)) {
                $result_raw .= fgets($sock, 256);
            }

            fclose($sock);

            //On sépare l'entete des datas
            $tr = explode("\r\n", $result_raw);
            $i  = 0;

            while (isset($tr[$i]) && !empty($tr[$i])) {
                $i++;
            }

            $result = implode(array_slice($tr, $i + 1), "\r\n");

            return json_decode($result);
        } catch (Exception $exception) {
            throw new Exception(
                'TODO gérer l\'exception remontée par WSAdmin::sendRequest',
                123,
                $exception
            );
        }
    }

}
