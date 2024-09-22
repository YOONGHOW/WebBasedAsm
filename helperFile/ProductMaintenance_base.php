<?php
// Global PDO object
$_db = new PDO('mysql:dbname=webassignment;host=localhost', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);


// ============================================================================
// General Page Functions
// ============================================================================

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Encode function for sanitizing input (assumed existing function)
function encode($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// get html-input-text
function html_text($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

//get text area format
function html_textArea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

//get input number
function html_number($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value' $attr>";
}

//get input file
function html_file($key, $attr = '')
{
    echo "<input type='file' id='$key' name='{$key}[]' $attr multiple>";
}

//get hidden number for every id
function html_hidden($key, $value)
{
    $value = encode($value);
    echo "<input type='hidden' id='$key' name='$key' value='$value'>";
}



function getNextId($conn, $prefix, $columnName, $tableName)
{
    // Query to get the maximum ID from the table
    $query = "SELECT MAX($columnName) AS max_id FROM $tableName WHERE $columnName LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$prefix . '%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $maxId = $row['max_id'];

    if ($maxId === null) {
        // If no IDs exist, return the first ID with the prefix
        return $prefix . '001';
    } else {
        // Extract the numeric part and increment it
        $lastId = intval(substr($maxId, strlen($prefix)));
        return $prefix . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    }
}

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}


// ============================================================================
// Validation Check
// ============================================================================

function checkProductName($name)
{
    // Non-empty check
    if (trim($name) === "") {
        return "Product name cannot be empty.";
    }
    return null;
}

function checkProductPrice($price)
{
    //check price must be positive number
    if (!is_numeric($price) || $price < 0) {
        return "Price must be a positive number";
    }
}

function checkProductStock($stock)
{
    //check price must be positive number
    if (!is_numeric($stock) || $stock < 0) {
        return "Price must be a positive number";
    }
}

function checkDescription($description)
{
    if (strlen($description) > 20) {
        return "Description cannot exceed 100 characters";
    }
}
