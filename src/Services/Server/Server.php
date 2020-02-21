<?php

namespace App\Services\Server;

use App\Services\Server\AbstractServer;

final class Server extends AbstractServer
{

    /** @var string **/
    private $hostIP;

    public function __construct(string $hostIP)
    {
        $this->hostIP = $hostIP;
    }

    public function start()
    {
        return $this->sendCommand('start');
    }

    public function stop()
    {
        return $this->sendCommand('stop');
    }

    public function shutdown()
    {
        return $this->sendCommand('shutdown');
    }

    public function restart()
    {
        return $this->sendCommand('relaunch');
    }

    public function status()
    {
        return $this->sendCommand('status');
    }

    protected function host(): string
    {
        return $this->hostIP;
    }

}
