<?php
// Global PDO object
$_db = new PDO('mysql:dbname=webassignment;host=localhost', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);
    
// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

//generate Searching method
function searching($key, $attr = ''){
    //retrieve a value from global array using the key and to encode it 
    $value = encode($GLOBALS[$key] ?? '');
    
    //generate a input element for search 
    echo "<input type='search' id='$key' name='$key' value='$value' $attr placeholder='Gavin Perkins'>"; 
}

// get html-input-text
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}



?>
