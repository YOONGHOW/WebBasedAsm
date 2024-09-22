<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Initialize criteria checks
    $response = array(
        'uppercase' => false,
        'lowercase' => false,
        'number' => false,
        'special' => false,
        'length' => false,
    );

    // Check for uppercase letters (A-Z)
    if (preg_match('/[A-Z]/', $password)) {
        $response['uppercase'] = true;
    }

    // Check for lowercase letters (a-z)
    if (preg_match('/[a-z]/', $password)) {
        $response['lowercase'] = true;
    }

    // Check the special character
    if (preg_match('/[\W_]/', $password)) {
        $response['special'] = true;
    }

    // Check for numbers (0-9)
    if (preg_match('/\d/', $password)) {
        $response['number'] = true;
    }

    // Check if the password is at least 8 characters long
    if (strlen($password) >= 8) {
        $response['length'] = true;
    }

    // Return the response as JSON
    echo json_encode($response);
}