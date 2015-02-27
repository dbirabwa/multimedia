<?php
namespace MyApp;
require ("/opt/lampp/htdocs/vendor/autoload.php");

date_default_timezone_set('Europe/Berlin'); 
$start_time=date('H:i:s');

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {
    protected $subscribedTopics = array();
    public function onSubscribe(ConnectionInterface $conn, $topic) {
     // When a visitor subscribes to a topic link the Topic object in a  lookup array
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            $this->subscribedTopics[$topic->getId()] = $topic;
        }
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onBlogEntry($entry) {
      $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['cat'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['cat']];
	

	$min = $entryData['min'];
	$max = $entryData['max'];

	//random numbers
	$entryData['random'] = mt_rand($min, $max);
	$entryData['timestamp'] =($start_time+strtotime("+6 hours"));

	print_r($entryData);

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);


  }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
    public function onOpen(ConnectionInterface $conn) {
    }
    public function onClose(ConnectionInterface $conn) {
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}
