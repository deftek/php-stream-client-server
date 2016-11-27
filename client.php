<?php

$sleep = isset($argv[1]) ? ((int) $argv[1]) : 1;
echo date('Y-m-d H:i:s') . ": Connecting...\n";
$mySocket = stream_socket_client(isset($argv[2]) ? $argv[2] : 'tcp://127.0.0.1:3333', $errno, $errstr, 30);
if (!$mySocket) {
    die(date('Y-m-d H:i:s') . ": {$errstr} ({$errno})\n");
}
echo date('Y-m-d H:i:s') . ": Connected to server. Making server request...\n";
stream_set_blocking($mySocket, false);
$hello   = 'Hello, server!';
$written = fwrite($mySocket, $hello);
if (false === $written) {
    die(date('Y-m-d H:i:s') . ": Failed writing to socket\n");
} elseif (strlen($hello) !== $written) {
    die(date('Y-m-d H:i:s') . ': Expected to have written ' . strlen($hello) . " bytes but wrote {$written}\n");
}
echo date('Y-m-d H:i:s') . ": Requested\n";
echo date('Y-m-d H:i:s') . ": Waiting for server response...\n";
sleep($sleep);
$sockets[] = $mySocket;
while (true) {
    $read          = $sockets;
    $write         = array();
    $except        = array();
    $modifiedCount = stream_select($read, $write, $except, 0, 200000);
    if (false === $modifiedCount) {
        die(date('Y-m-d H:i:s') . ": Error running stream_select()\n");
    }
    foreach ($read as $key => $socket) {
        $data = fread($socket, 1024);
        if (false === $data) {
            die(date('Y-m-d H:i:s') . ": Error reading from socket\n");
        } elseif (0 === strlen($data)) {
            die(date('Y-m-d H:i:s') . ": Connection closed\n");
        } else {
            echo date('Y-m-d H:i:s') . ": Received server response: {$data}";
        }
    }
    echo date('Y-m-d H:i:s') . ": Doing some useful work...\n";
    sleep($sleep);
}
