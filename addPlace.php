<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $country = "";
$name_err = $country_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Въведете име на населеното място.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM places WHERE name = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_name);

            // Set parameters
            $param_name = trim($_POST["name"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $name_err = "Името вече се използва";
                } else{
                    $name = trim($_POST["name"]);
                }
            } else{
                echo "Опа... нещо се обърка. Моля опитайте по-късно.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate country
    if(empty(trim($_POST["country"]))){
        $country_err = "Въведете име на държава.";
    } else{
        $country = trim($_POST["country"]);
    }

    // Check input errors before inserting in database
    if(empty($name_err) && empty($country_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO places (name, country) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_country);

            // Set parameters
            $param_name = $name;
            $param_country = $country;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: places.php");
                exit();
            } else{
                echo "Опа... Нещо се обърка. Опитайте по-късно.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Добавете населено място</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link " href="welcome.php">Начало</a>
            <a class="nav-item nav-link " href="places.php">Населени места</a>
            <a class="nav-item nav-link active" href="addPlace.php">Добави населено място</a>
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
    <div class="wrapper w-50">
        <h2>Добавете населено място</h2>
        <p>Моля, попълнете празните полетата.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Име на населеното място:</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Име на държава:</label>
                <input type="text" name="country" class="form-control <?php echo (!empty($country_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $country_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Добавете населено място">
            </div>
        </form>
    </div>
</div>
</body>
</html>