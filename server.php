<?php

$master = array();
$server = stream_socket_server('tcp://0.0.0.0:3333', $errno, $errstr);
if (!$server) {
    die("{$errstr} ({$errno})\n");
}
$master[] = $server;
$read     = $master;
while (true) {
    $read          = $master;
    $write         = null;
    $except        = null;
    $modifiedCount = stream_select($read, $write, $except, null);
    if (false === $modifiedCount) {
        die('Error running stream_select()');
    }
    foreach ($read as $socket) {
        if ($socket === $server) {
            $master[] = stream_socket_accept($server);
        } else {
            $data = fread($socket, 1024);
            if (false === $data) {
                echo "Error reading from client\n";
            } elseif (0 === strlen($data)) {
                echo "Client connection closed\n";
                fclose($socket);
            } else {
                echo "Received from client: {$data}\n";
                $sleep = 5;
                echo "Working for $sleep second", (1 < $sleep ? 's' : ''), "...";
                sleep($sleep);
                echo "Responding\n";
                fwrite($socket, "Hello, client! We received \"{$data}\" from you.\n");
                fclose($socket);
            }
            unset($master[array_search($socket, $master)]);
        }
    }
}

