# deftek/php-stream-client-server

This project demonstrates how to use PHP's [stream functions](https://php.net/streams), available since PHP 4.3.0, to 
perform non-blocking, asynchronous operations in a client-server environment with TCP sockets. The files that comprise
the client and server programs each total 42 lines in length.


## Usage and Method of Operation

First, run the server at a command shell that we'll name `server` for reference:

    $ php server.php # server

Then, at another command shell (aka `client1`), prepare the following command, but don't run it yet:

    $ php client.php # client1

Next, in another command shell (aka `client2`), prepare the following command, but don't run it yet:

    $ php client.php # client2

Now that the server is running and the client shells are prepared to execute, run the client in `client1`, and then
quickly (i.e., within 5 seconds) run the client in `client2`.

The `server` shell should have the following output:

    Received from client: Hello, server!
    Working for 5 seconds...Responding
    Received from client: Hello, server!
    Working for 5 seconds...Responding

The server sleeps for 5 seconds after receiving a request from a client, which instead could be some useful work with
the data provided by the client, requiring whatever amount of time to complete. Upon completion, the server immediately
checks for a new socket connection or data available to read on an existing client socket.

The `client1` process should have generated output similar to the following:

    Connecting...Connected
    Making server request...Requested
    Waiting for server response...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Received server response: Hello, client! We received "Hello, server!" from you.
    Doing some useful work...
    Connection closed

The client sleeps for 1 second, which again could instead be some useful work that requires some amount of time, before
the client loops again and checks for a response from the server. After several iterations, the server completes its
work with the client data, the server responds with the data that it received, the client-server socket connection is
closed, and the client program terminates. The server continues looping, listening for new client connections and
reading data from existing client connections as such data become available.

The output for `client2` should be similar to the following:

    Connecting...Connected
    Making server request...Requested
    Waiting for server response...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Doing some useful work...
    Received server response: Hello, client! We received "Hello, server!" from you.
    Doing some useful work...
    Connection closed

Notice that `client2` waited for a server response approximately twice the amount of time that `client1` had waited for
a response. This is because the server does not receive data from clients *simultaneously* or *in parallel*. Instead,
the server waits 5 seconds after having received data from a client, which instead could be useful work with the data,
and then it continues looping over the client socket connections that it manages.

## References and Additional Reading

As demonstrated by this project, PHP's [stream functions](https://php.net/streams) may be used to implement
non-blocking, asynchronous processing.

The event loop implementations of
[Icicle](https://github.com/icicleio/icicle/blob/master/src/Loop/SelectLoop.php) and
[React](https://github.com/reactphp/event-loop/blob/master/src/StreamSelectLoop.php) are a couple of well-known
examples. This approach also resembles the way in which the event loop in [Node.js](https://nodejs.org/) operates. 

