<?php
global $_user;
session_start();
$_user = $_SESSION['user'] ?? null;

?>

<html>

<head>
  <title>Product Edit</title>
  <link rel="stylesheet" href="../css/Admin.css" />
</head>

<body>
  <div class="container">
    <nav>
      <div class="navbar">
        <div class="logo">
          <img src="../image/<?= ($_user) ? $_user->users_IMG_source : "" ?>" alt="photo">
          <h1>Phaethon Electronic</h1>
        </div>

        <ul>
          <li><a href="AdminHome.php">
              <i class="fas fa-user"></i>
              <span class="nav-item"></span>
            </a>
          </li>

          <ul>
            <li><a href="adminPage.php">
                <i class="fas fa-user"></i>
                <span class="nav-item">Dashboard</span>
              </a>
            </li>

            <li><a href="member_list.php">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-item">Customer Details</span>
              </a>
            </li>

            <li><a href="productAdmin_list.php">
                <i class="fas fa-tasks"></i>
                <span class="nav-item">Product Details</span>
              </a>
            </li>


            <li><a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-item">Logout</span>
              </a>
            </li>
          </ul>
      </div>
    </nav>


</body>

</html>

</session>