<?php

require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Create a service container
$container = new Container();

// Create a dispatcher
$events = new Dispatcher($container);

// Create a database manager instance
$db = new Capsule($container);
$db->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'automobile_discussion',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$db->setEventDispatcher($events);
$db->setAsGlobal();
$db->bootEloquent();

// Test the rating functionality
try {
    // Get the first thread
    $thread = \App\Models\Thread::first();
    
    if ($thread) {
        echo "Thread ID: " . $thread->id . "\n";
        echo "Thread Subject: " . $thread->subject . "\n";
        echo "Average Rating: " . $thread->average_rating . "\n";
        echo "Rating Count: " . $thread->ratings->count() . "\n";
    } else {
        echo "No threads found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}