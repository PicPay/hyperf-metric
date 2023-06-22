<?php

declare(strict_types=1);

namespace Hyperf\Metric\Adapter\StatsD;

use Domnikl\Statsd\Connection\UdpSocket;

class Connection extends UdpSocket
{
    protected function isConnected(): bool
    {
        return true;
    }

    protected function writeToSocket(string $message): void
    {
        $errorNumber = 0;
        $errorMessage = '';
        $url = 'udp://' . $this->host;
        $socket = fsockopen($url, $this->port, $errorNumber, $errorMessage);
        fwrite($socket, $message);
        fclose($socket);
        $socket = null;
    }
}
