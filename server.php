<?php

$sleep  = isset($argv[1]) ? ((int) $argv[1]) : 1;
$master = array();
$server = stream_socket_server(isset($argv[2]) ? $argv[2] : 'tcp://127.0.0.1:3333', $errno, $errstr);
if (!$server) {
    die(date('Y-m-d H:i:s') . ": {$errstr} ({$errno})\n");
}
echo date('Y-m-d H:i:s') . ": Server listening...\n";
$master[] = $server;
$read     = $master;
while (true) {
    $read          = $master;
    $write         = null;
    $except        = null;
    $modifiedCount = stream_select($read, $write, $except, null);
    if (false === $modifiedCount) {
        die(date('Y-m-d H:i:s') . ": Error running stream_select()\n");
    }
    foreach ($read as $socket) {
        if ($socket === $server) {
            echo date('Y-m-d H:i:s') . ": Accepting client connection\n";
            $master[] = stream_socket_accept($server);
        } else {
            $data = fread($socket, 1024);
            if (false === $data) {
                echo date('Y-m-d H:i:s') . ": Error reading from client\n";
            } elseif (0 === strlen($data)) {
                echo "Client connection closed\n";
                fclose($socket);
            } else {
                echo date('Y-m-d H:i:s') . ": Received from client: {$data}\n";
                echo date('Y-m-d H:i:s') . ": Working for $sleep second", (1 < $sleep ? 's' : ''), "...\n";
                sleep($sleep);
                echo date('Y-m-d H:i:s') . ": Responding\n";
                fwrite($socket, date('Y-m-d H:i:s') . ": Hello, client! We received \"{$data}\" from you.\n");
                fclose($socket);
            }
            unset($master[array_search($socket, $master)]);
        }
    }
}
