<?php

echo 'Connecting...';
$mySocket = stream_socket_client('tcp://127.0.0.1:3333', $errno, $errstr, 30);
if (!$mySocket) {
    die("{$errstr} ({$errno})\n");
}
echo "Connected\n";
echo 'Making server request...';
stream_set_blocking($mySocket, false);
$hello   = 'Hello, server!';
$written = fwrite($mySocket, $hello);
if (false === $written) {
    die("Failed writing to socket\n");
} elseif (strlen($hello) !== $written) {
    die('Expected to have written ' . strlen($hello) . " bytes but wrote {$written}\n");
}
echo "Requested\n";
echo "Waiting for server response...\n";
$sockets[] = $mySocket;
while (true) {
    $read          = $sockets;
    $write         = [];
    $except        = [];
    $modifiedCount = stream_select($read, $write, $except, 0, 200000);
    if (false === $modifiedCount) {
        die('Error running stream_select()');
    }
    foreach ($read as $key => $socket) {
        $data = fread($socket, 1024);
        if (false === $data) {
            die("Error reading from socket\n");
        } elseif (0 === strlen($data)) {
            die("Connection closed\n");
        } else {
            echo "Received server response: {$data}";
        }
    }
    echo "Doing some useful work...\n"; sleep(1);
}

