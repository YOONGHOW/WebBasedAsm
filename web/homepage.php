<!DOCTYPE html>
<html>
<?php require "../helperFile/helper.php"; 
$_user = $_SESSION['user'] ?? null;
$name = $_user->user_name;
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="../css/jiazhe.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <h1>Homepage is here -_-</h1><br>
    <h2>Welcome, <?= $name ?></h2>
    <a href="logout.php">logout</a>
</body>

</html>