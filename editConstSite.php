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
$address = $investor = $insidePlaster = $outsidePlaster = "";
$floorCount = $aptCount = $placeName = "";

$address_err = $investor_err = $insidePlaster_err = $outsidePlaster_err = "";
$floorCount_err = $aptCount_err = $placeName_err = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Get hidden input value
    $id = $_POST["id"];

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Въведете адрес на обекта.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate investor
    if (empty(trim($_POST["investor"]))) {
        $investor_err = "Въведете инвеститор на обекта.";
    } else {
        $investor = trim($_POST["investor"]);
    }

    // Validate insidePlaster
    if (empty(trim($_POST["insidePlaster"]))) {
        $insidePlaster_err = "Не е зададен статус на вътрешната мазилка на обекта.";
    } else {
        $insidePlaster = trim($_POST["insidePlaster"]);
    }

    // Validate outsidePlaster
    if (empty(trim($_POST["outsidePlaster"]))) {
        $outsidePlaster_err = "Не е зададен статус на външната мазилка на обекта.";
    } else {
        $outsidePlaster = trim($_POST["outsidePlaster"]);
    }

    // Validate floorCount
    if (empty(trim($_POST["floorCount"]))) {
        $floorCount_err = "Не е зададен брой на етажите в обекта.";
    } else {
        $floorCount = trim($_POST["floorCount"]);
    }

    // Validate aptCount
    if (empty(trim($_POST["aptCount"]))) {
        $aptCount_err = "Не е зададен брой на апартаментите в обекта.";
    } else {
        $aptCount = trim($_POST["aptCount"]);
    }

    // Validate placeName
    if (empty(trim($_POST["placeName"]))) {
        $placeName_err = "Моля изберете населено място.";
    } else {
        $placeName = trim($_POST["placeName"]);
    }

    // Check input errors before inserting in database
    if (empty($address_err) && empty($investor_err) && empty($insidePlaster_err) && empty($outsidePlaster_err)
        && empty($floorCount_err) && empty($aptCount_err) && empty($placeName_err)) {

        // Prepare an insert statement
        $sql = "UPDATE sites SET address=?, investor=?, insidePlaster=?, outsidePlaster=?, floorCount=?, aptCount=?, placeName=? WHERE id=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssssi", $param_address, $param_investor, $param_insidePlaster, $param_outsidePlaster,
                $param_floorCount, $param_aptCount, $param_placeName, $param_id);

            // Set parameters
            $param_address = $address;
            $param_investor = $investor;
            $param_insidePlaster = $insidePlaster;
            $param_outsidePlaster = $outsidePlaster;
            $param_floorCount = $floorCount;
            $param_aptCount = $aptCount;
            $param_placeName = $placeName;
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records updated successfully. Redirect to landing page
                header("location: welcome.php");
                exit();
            } else {
                echo "Опа... Нещо се обърка. Опитайте по-късно.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id = trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM sites WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $address = $row["address"];
                    $investor = $row["investor"];
                    $insidePlaster = $row["insidePlaster"];
                    $outsidePlaster = $row["outsidePlaster"];
                    $floorCount = $row["floorCount"];
                    $aptCount = $row["aptCount"];
                    $placeName = $row["placeName"];

                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }

            } else {
                echo "Опа... Нещо се обърка. Опитайте по-късно.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Редактирайте населено място</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link " href="welcome.php">Начало</a>
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
        <div class="wrapper w-50">
            <h2 class="mt-5">Редактирайте строителния обект</h2>
            <p>Попълнете празните полета.</p>
            <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                <div class="form-group">
                    <label>Адрес</label>
                    <input type="text" name="address"
                           class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $address; ?>">
                    <span class="invalid-feedback"><?php echo $address_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Брой етажи</label>
                    <input type="number" name="floorCount"
                           class="form-control <?php echo (!empty($floorCount_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $floorCount; ?>">
                    <span class="invalid-feedback"><?php echo $floorCount_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Брой апартаменти</label>
                    <input type="number" name="aptCount"
                           class="form-control <?php echo (!empty($aptCount_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $aptCount; ?>">
                    <span class="invalid-feedback"><?php echo $aptCount_err; ?></span>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Външна мазилка:</label>
                    </div>
                    <select name="outsidePlaster" class="custom-select">
                        <option value="Да">Да</option>
                        <option value="Не">Не</option>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Вътрешна мазилка:</label>
                    </div>
                    <select name="insidePlaster" class="custom-select">
                        <option value="Да">Да</option>
                        <option value="Не">Не</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Инвеститор</label>
                    <input type="text" name="investor"
                           class="form-control <?php echo (!empty($investor_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $investor; ?>">
                    <span class="invalid-feedback"><?php echo $investor_err; ?></span>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Населено място:</label>
                    </div>
                    <select name="placeName" class="custom-select">
                        <option disabled>...избери опция...</option>
                        <?php
                        $sqli = "SELECT name FROM places";
                        $result = mysqli_query($link, $sqli);
                        while ($row = mysqli_fetch_array($result)) {
                            echo '<option>' . $row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="submit" class="btn btn-primary" value="Редактирай">
                <a href="welcome.php" class="btn btn-secondary ml-2">Отмени</a>
            </form>
        </div>
    </div>
    </body>
    </html>
<?php
// Close connection
mysqli_close($link); ?>