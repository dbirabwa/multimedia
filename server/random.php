
<?php
header('Access-Control-Allow-Origin: *');




/**require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

    $entryData = array(
        'cat' => 'random_data',
	'min' => $_REQUEST['min'],
	'max' => $_REQUEST['max']
    );
**/
//ZMQ push socket
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");

    $socket->send(json_encode($entryData));


  ?>

   
