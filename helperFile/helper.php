<?php
// ============================================================================
// Database Setups
// ============================================================================
$_db = new PDO('mysql:dbname=webassignment', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

$genders = [
    'F' => 'Female',
    'M' => 'Male',
];

$states = [
    'JHR' => 'Johor',
    'KDH' => 'Kedah',
    'KTN' => 'Kelantan',
    'MLK' => 'Melaka',
    'NSN' => 'Negeri Sembilan',
    'PHG' => 'Pahang',
    'PNG' => 'Penang',
    'PRK' => 'Perak',
    'PLS' => 'Perlis',
    'SBH' => 'Sabah',
    'SWK' => 'Sarawak',
    'SGR' => 'Selangor',
    'TRG' => 'Terengganu',
    'WKL' => 'Kuala Lumpur',
    'LBN' => 'Labuan',
    'PJY' => 'Putrajaya',
];

$citiesAndState = [
    "JHR" => ["Johor Bahru", "Muar", "Batu Pahat", "Kluang", "Segamat"],
    "KDH" => ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi", "Baling"],
    "KTN" => ["Kota Bharu", "Pasir Mas", "Tanah Merah", "Gua Musang", "Kuala Krai"],
    "MLK" => ["Malacca City", "Alor Gajah", "Jasin"],
    "NSN" => ["Seremban", "Port Dickson", "Nilai", "Tampin", "Rembau"],
    "PHG" => ["Kuantan", "Temerloh", "Bentong", "Raub", "Jerantut"],
    "PNG" => ["George Town", "Butterworth", "Bayan Lepas", "Bukit Mertajam", "Balik Pulau"],
    "PRK" => ["Ipoh", "Taiping", "Teluk Intan", "Manjung", "Kampar"],
    "PLS" => ["Kangar", "Arau", "Padang Besar"],
    "SBH" => ["Kota Kinabalu", "Sandakan", "Tawau", "Lahad Datu", "Keningau"],
    "SWK" => ["Kuching", "Miri", "Sibu", "Bintulu", "Limbang"],
    "SGR" => ["Shah Alam", "Petaling Jaya", "Klang", "Subang Jaya", "Kajang"],
    "TRG" => ["Kuala Terengganu", "Kemaman", "Dungun", "Marang", "Besut"],
    "WKL" => ["Kuala Lumpur"],
    "LBN" => ["Labuan"],
    "PJY" => ["Putrajaya"]
];

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
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

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}

//generate the text field
function generateTextField($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

//generate the number field
function generateNumberField($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value' $attr>";
}

// generate the date field
function generateDateField($key, $min = '', $max = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='date' id='$key' name='$key' value='$value' min='$min' max='$max' $attr>";
}

// generate the password field
function generatePassword($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// generate the list of the gender
function displayGenderList()
{
    global $genders;
    $gender = encode($GLOBALS["gender"] ?? '');
    echo '<select name="gender">';
    echo '<option value="none">--select your gender--</option>';
    foreach ($genders as $id => $name) {
        if (strcmp($gender, $id) == 0) {
            echo "<option value = '$id' selected>$name</option>";
        } else {
            echo "<option value = '$id'>$name</option>";
        }
    }
    echo "</select>";
}

// generate the list of the states in Malaysia
function displayStateList()
{
    global $states;
    $state = encode($GLOBALS["state"] ?? '');
    echo '<select name="state">';
    echo '<option value="none">--select one state--</option>';

    foreach ($states as $id => $name) {
        if (strcmp($state, $id) == 0) {
            echo "<option value = '$id' selected>$name</option>";
        } else {
            echo "<option value = '$id'>$name</option>";
        }
    }

    echo "</select>";
}

// display the cities based on the state selected
function displayCitiesForEachState()
{
    global  $citiesAndState;
    $state = encode($GLOBALS["state"] ?? '');
    $city = encode($GLOBALS["city"] ?? '');
    echo '<select name="city">';
    echo '<option value="none">--select one city--</option>';
    foreach ($citiesAndState as $stateName => $cities) {
        if (strcmp($stateName, $state) == 0) {
            for ($i = 0; $i < count($cities); $i++) {

                if (strcmp($city, $cities[$i]) == 0) {
                    echo "<option value = '$cities[$i]' selected>$cities[$i]</option>";
                } else {
                    echo "<option value = '$cities[$i]'>$cities[$i]</option>";
                }
            }
        }
    }

    echo "</select>";
}



//generate Searching method
function searching($key, $attr = '')
{
    //retrieve a value from global array using the key and to encode it 
    $value = encode($GLOBALS[$key] ?? '');

    //generate a input element for search 
    echo "<input type='search' id='$key' name='$key' value='$value' $attr placeholder='Gavin Perkins'>";
}


// ============================================================================
// Validation Check
// ============================================================================

//email validation
function checkGmail($email)
{
    if ($email == NULL) {
        return 'Please enter your gmail.';
    } else if (!preg_match("/^[A-Za-z0-9]+@[A-Za-z0-9\.]+$/", $email)) {
        return 'The format of the gmail entered is invalid.';
    } else if (memberGmailExist($email)) {
        return 'This gmail had been used, please using other gmail for registration';
    }
}

//check the gmail provided already exist in system or not
function memberGmailExist($email)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM users WHERE Email = ?");
    $stm->execute([$email]);
    return $stm->fetchColumn() > 0;
}

//name validation
function checkName($name)
{
    if ($name == '') {
        return 'Please enter your name.';
    } else if (!preg_match("/^[A-Za-z ]+$/", $name)) {
        return  "The format of the name is invalid.";
    } else if (strlen($name) > 100) {
        return 'Maximum length 100';
    }
}

//IC validation
function checkIC($ic)
{
    if ($ic == '') {
        return 'Please enter your ic.';
    } else if (!preg_match("/^(\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{6}$/", $ic)) {
        return  "The format of the ic number is invalid.";
    } else if (strlen($ic) > 12) {
        return 'Maximum length 12';
    } else if (checkICExist()) {
        return 'The ic number already used, please used others ic.';
    }
}

function validateDate($dateString)
{
    $ic = encode($GLOBALS["ic"] ?? '');
    $year = substr($ic, 0, 2);
    $month = substr($ic, 2, 2);
    $day = substr($ic, 4, 2);

    return checkdate($month, $day, $year);
}

function checkICExist()
{
    $ic = encode($GLOBALS["ic"] ?? '');
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM users WHERE user_ic = ?");
    $stm->execute([$ic]);
    return $stm->fetchColumn() > 0;
}

//password validation
function checkPassword($password)
{
    if ($password == null) {
        return 'Please enter your password.';
    } else if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {
        return 'The format of the password is invalid';
    } else if (strlen($password) > 15) {
        return 'Length of password too long, should be shorter!';
    }
}

//confirm password validation
function confirmPassword($password, $confirm_password)
{
    if (strcmp($password, $confirm_password) != 0) {
        return 'the password is not match.';
    }
}

//birth date validation
function checkDateFormat($date)
{
    $today = new DateTime();
    $date2 = new DateTime($date);



    if ($date == null) {
        return "Please enter the date";
    } else if ($today <= $date2) {
        return "It already future, not a valid date.";
    }

    if (validateDate(retrieveDatefromIC())) {
        $icDate = new DateTime(retrieveDatefromIC());
        if ($icDate != $date2) {
            $date11 = date_format($icDate, "d-m-Y");
            return "Your birth date is not match with your ic birth date." . $date11;
        }
    }
}

//address validation
function checkAddress($address)
{
    if (preg_match('/^$/', $address)) {
        return "The address format is invalid";
    }
}

// retieve the birth date from ic for comparision purpose
function retrieveDatefromIC()
{
    $ic = encode($GLOBALS["ic"] ?? '');
    $year = substr($ic, 0, 2);
    $month = substr($ic, 2, 2);
    $day = substr($ic, 4, 2);

    $formatDate = "$year-$month-$day";
    return $formatDate;
}

// ============================================================================
// Database Functions
// ============================================================================

function generateID($idFormat, $tableName, $idName)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $tableName");
    $stm->execute();

    if ($stm->fetchColumn() == 0) {
        return $idFormat . "001";
    } else {
        $stm = $_db->prepare("SELECT $idName FROM $tableName ORDER BY $idName DESC LIMIT 1");
        $stm->execute();
        //get the last record(id) from the table
        $getID = $stm->fetchColumn();

        //retrieve the number from the id
        $getID = substr($getID, -3);

        // convert the string into number
        $num = number_format($getID);

        // recalculate the id num
        $num = str_pad(($num + 1), 3, "0", STR_PAD_LEFT);

        //merge the id format and number into become new id. e.g. $newID = "U" . 010; 
        $newID = $idFormat . $num;
        return $newID;
    }
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_user = $_SESSION['user'] ?? null;

// Login user
function login($user, $url = '/')
{

    $_SESSION['user'] = $user;

    redirect($url);
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['user']);
    redirect($url);
}

// // Authorization
// function auth(...$roles) {
//     global $_user;
//     if ($_user) {
//         if ($roles) {
//             if (in_array($_user->role, $roles)) {
//                 return; // OK
//             }
//         }
//         else {
//             return; // OK
//         }
//     }

//     redirect('/login.php');
// }

// ============================================================================
// Email Functions
// ============================================================================

// Demo Accounts:
// --------------
// AACS3173@gmail.com           npsg gzfd pnio aylm
// BAIT2173.email@gmail.com     ytwo bbon lrvw wclr
// liaw.casual@gmail.com        wtpa kjxr dfcb xkhg
// liawcv1@gmail.com            obyj shnv prpa kzvj

// Initialize and return mail object
function get_mail()
{
    require_once '../lib/PHPMailer.php';
    require_once '../lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'jiazhehello@gmail.com';
    $m->Password = 'kumh rjjw aiik tzfu';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'YB Lim Sdn. Bhd.');

    return $m;
}
