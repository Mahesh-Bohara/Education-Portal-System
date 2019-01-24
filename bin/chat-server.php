<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 1/17/2019
 * Time: 12:54 PM
 */

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

    require dirname(__DIR__) . '/vendor/autoload.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new \App\Controller\RatchetChatController()
            )
        ),
        8083
    );

    $server->run();