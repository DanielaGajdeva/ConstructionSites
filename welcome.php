<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
include_once "config.php";

$tableRow = "id";
$order = "ASC";

if (isset($_GET["sort_by"])) {
    if ($_GET["sort_by"] == "a-z") {
        $tableRow = "investor";
        $order = "ASC";
    } elseif ($_GET["sort_by"] == "z-a") {
        $tableRow = "investor";
        $order = "DESC";
    } elseif ($_GET["sort_by"] == "1-9") {
        $tableRow = "id";
        $order = "ASC";
    } elseif ($_GET["sort_by"] == "9-1") {
        $tableRow = "id";
        $order = "DESC";
    }
}
$result = mysqli_query($link, "SELECT * from sites ORDER BY $tableRow $order");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Добре дошли</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
            text-align: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link active" href="welcome.php">Начало</a>
            <a class="nav-item nav-link " href="places.php">Населени места</a>
            <a class="nav-item nav-link " href="addPlace.php">Добави населено място</a>
            <a class="nav-item nav-link " href="addConstSite.php">Добави строителен обект</a>
        </div>
    </div>
    <form class="form-inline">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a href="logout.php" class="btn btn-danger ml-3">Излезте от акаунта си</a>
            </li>
        </ul>
    </form>
</nav>

<div class="container justify-content-center">
    <br>
    <h2 class="text-center">Добре дошли, <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b> !</h2>
    <br>
    <p>
        <a href="addConstSite.php" class="btn btn-success ml-3">Добави нов строителен обект</a>
    </p>

    <div class="row">
        <div class="col-sm">
            <h3>Списък на всички строителни обекти:</h3>
        </div>
        <form action="" method="GET">
        <div class="col-sm">
            <div class="input-group mb-3">
                <select name="sort_by" class="form-control">
                    <option value="">изберете опция...</option>
                    <option value="a-z"  <?php if(isset($_GET["sort_by"]) && $_GET["sort_by"] == "a-z"){echo "selected";}?>>Сортирай по инвеститор (възходящо)</option>
                    <option value="z-a"  <?php if(isset($_GET["sort_by"]) && $_GET["sort_by"] == "z-a"){echo "selected";}?>>Сортирай по инвеститор (низходящо)</option>
                    <option value="1-9"  <?php if(isset($_GET["sort_by"]) && $_GET["sort_by"] == "1-9"){echo "selected";}?>>Сортирай по # (възходящо)</option>
                    <option value="9-1"  <?php if(isset($_GET["sort_by"]) && $_GET["sort_by"] == "9-1"){echo "selected";}?>>Сортирай по # (низходящо)</option>
                </select>
                <button type="submit" class="input-group-text btn btn-primary">Сортирай</button>
            </div>
        </div>
        </form>
    </div>

    <table class="table thead-dark">
        <tr bgcolor='#CCCCCC'>
            <td>#</td>
            <td>Адрес</td>
            <td>Брой етажи</td>
            <td>Брой апартаменти</td>
            <td>Вътрешна мазилка</td>
            <td>Външна мазилка</td>
            <td>Инвеститор</td>
            <td>Населено място</td>
            <td>Редактиране</td>
        </tr>
        <?php
        while ($res = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $res['id'] . "</td>";
            echo "<td>" . $res['address'] . "</td>";
            echo "<td>" . $res['floorCount'] . "</td>";
            echo "<td>" . $res['aptCount'] . "</td>";
            echo "<td>" . $res['insidePlaster'] . "</td>";
            echo "<td>" . $res['outsidePlaster'] . "</td>";
            echo "<td>" . $res['investor'] . "</td>";
            echo "<td>" . $res['placeName'] . "</td>";
            echo "<td><a href=\"editConstSite.php?id=$res[id]\">Редактиране</a> | <a href=\"deleteConstSite.php?id=$res[id]\" onClick=\"return confirm('Сигурни ли сте, че искате да изтриете обекта?')\">Изтрий</a></td>";
        }
        ?>
    </table>
</body>
</html>
