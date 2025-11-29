<?php
// Test file to check the JSON response structure

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Simulate the ThreadController index method
// This is just for testing the data structure

$data = [
    'threads' => [
        [
            'id' => 1,
            'subject' => 'Test Thread',
            'user_rating' => 4,
            'average_rating' => 3.5,
            'ratings_count' => 10
        ]
    ],
    'pagination' => [
        'current_page' => 1,
        'last_page' => 1,
        'per_page' => 25,
        'total' => 1
    ]
];

header('Content-Type: application/json');
echo json_encode($data);