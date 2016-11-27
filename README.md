# deftek/php-stream-client-server

This project demonstrates how to use [PHP's stream functions](https://php.net/streams) to perform non-blocking,
asynchronous operations in a client-server environment with TCP sockets. The files that comprise the client and server
programs each total 42 lines in length.


## Usage and Method of Operation

First, run the server at a command shell:

    $ php server.php # server

The server accepts TCP socket connections from clients. When a client connects, the server immediately checks for a new
socket connection or data available to read on an existing client socket.

When the server receives a request from a client, it sleeps for a second, which could instead be other useful work. Then
it responds to the client and closes the connection.

At another command shell (aka `client1`), prepare the following command, but don't run it yet:

    $ php client.php 10 # client1

Once it has connected to the server and sent a request, `client1` will sleep 10 seconds, which could instead be some
other useful work. Then it tries to read from the server socket connection once every 10 seconds. Upon receiving a
response from the server, it echoes the message that it receives. The client exits after the server has closed the
connection.

In yet another command shell (aka `client2`), prepare the following command, but don't run it yet:

    $ php client.php 5 # client2

This `client2` instance will sleep for 5 seconds, whereas `client1` sleeps for 10. We will run `client1` first, but
`client2` should finish first, because both the clients and servers operate in a non-blocking, asynchronous fashion.   

Now that the server is running and the client shells are prepared to execute, run the client in `client1`, and then
quickly (i.e., within 5 seconds) run the client in `client2`.

The `server` shell should have output similar to the following:

    2016-11-27 12:54:07: Server listening...
    2016-11-27 12:54:14: Accepting client connection
    2016-11-27 12:54:14: Received from client: Hello, server!
    2016-11-27 12:54:14: Working for 1 second...
    2016-11-27 12:54:15: Responding
    2016-11-27 12:54:15: Accepting client connection
    2016-11-27 12:54:15: Received from client: Hello, server!
    2016-11-27 12:54:15: Working for 1 second...
    2016-11-27 12:54:16: Responding

Note the times of the various server events. The server listens for 7 seconds before `client1` connects and sends a
request. The server sleeps for a second before it responds to `client1`, then it immediately accepts the connection and
request from `client2`. Again it sleeps for a second before responding to `client2`.

The `client1` process should have generated output similar to the following:

    2016-11-27 12:54:14: Connecting...
    2016-11-27 12:54:14: Connected to server. Making server request...
    2016-11-27 12:54:14: Requested
    2016-11-27 12:54:14: Waiting for server response...
    2016-11-27 12:54:24: Received server response: 2016-11-27 12:54:15: Hello, client! We received "Hello, server!" from you.
    2016-11-27 12:54:24: Doing some useful work...
    2016-11-27 12:54:34: Connection closed

The `client1` process sleeps for 10 seconds before receiving the server response, and then it sleeps for another 10
seconds after receiving the server response and before closing the connection.

The output for `client2` should be similar to the following:

    2016-11-27 12:54:15: Connecting...
    2016-11-27 12:54:15: Connected to server. Making server request...
    2016-11-27 12:54:15: Requested
    2016-11-27 12:54:15: Waiting for server response...
    2016-11-27 12:54:20: Received server response: 2016-11-27 12:54:16: Hello, client! We received "Hello, server!" from you.
    2016-11-27 12:54:20: Doing some useful work...
    2016-11-27 12:54:25: Connection closed

Although `client2` connected to the server after `client1`, `client2` finished its work before `client1`.

The client and server programs each accept two arguments. The first argument is the number of seconds that it should
sleep, and the second argument is the TCP socket specification (e.g., `tcp://127.0.0.1:3333`, the default).


## References and Additional Reading

As demonstrated by this project, PHP's [stream functions](https://php.net/streams) may be used to implement
non-blocking, asynchronous processing.

The event loop implementations of
[Icicle](https://github.com/icicleio/icicle/blob/master/src/Loop/SelectLoop.php) and
[React](https://github.com/reactphp/event-loop/blob/master/src/StreamSelectLoop.php) are a couple of well-known
examples. This approach also resembles the way in which the event loop in [Node.js](https://nodejs.org/) operates. 

