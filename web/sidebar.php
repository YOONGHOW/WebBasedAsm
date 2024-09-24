<?php
global $_user;
session_start();
$_user = $_SESSION['user'] ?? null;

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

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

if (strcmp($_user->user_rule, "admin") != 0) {
  temp("info", "Your permission is denied.");
  redirect("home.php");
}
?>

<link href="../css/sidebar.css" rel="stylesheet" type="text/css" />
<!-- sidebar-->
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  &nbsp<img class="img_user" src="../image/<?= $_user->users_IMG_source ?>">
  <div class="sidebar_name">Admin</div>
  <br>
  <br>
  <a href="member_list.php">Member Listing</a><br />
  <a href="productAdmin_list.php">Product listing</a><br />
  <a href="logout.php">Logout</a>
</div>

 <!-- Flash message -->
 <div id="info"><?= temp('info') ?></div>
<button class="sidebar_btn" style="margin:0;" onclick="openNav()">&#9776</button>

<script>
  function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    S
  }

  function closeNav() {
    document.getElementById("mySidenav").style.width = "0";

  }
</script>
<!-- sidebar-->