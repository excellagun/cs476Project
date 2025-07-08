<?php
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); //encodes
    return $data;
}


// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $errors = array();
    $dataOK = TRUE;
    
    // Get and validate the username and password fields
    $username = test_input($_POST["username"]);
    $unameRegex = "/^[a-zA-Z0-9_]+$/";
    if (!preg_match($unameRegex, $username)) {
        $errors["username"] = "Invalid Username";
        $dataOK = FALSE;
    }

    $password = test_input($_POST["password"]);
    $passwordRegex = "/^.{8}$/";
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
        $dataOK = FALSE;
    }

    // Check whether the fields are not empty
    if ($dataOK) {

        // Connect to the database and verify the connection
        try {
            $db = new PDO($attr, $db_user, $db_pwd, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

        // TODO 10a: use PDO::query() to get the user_id, first_name, last_name, 
        //           and avatar_url of users that match the username and password
        $query = "SELECT user_id, first_name, last_name, avatar_url from Loggers WHERE username = '$username' AND password = '$password'";
        $result = $db->query($query);

        if (!$result) {
            // query has an error
            $errors["Database Error"] = "Could not retrieve user information";
        } elseif ($row = $result->fetch()) {
            // If there's a row, we have a match and login is successful!
            
            session_start();

            // TODO 10b: store the uid, first_name, last_name, and avatar_url fields
            //            from $row into the $_SESSION superglobal variable
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["first_name"] = $row["first_name"];
            $_SESSION["last_name"] = $row["last_name"];
            $_SESSION["avatar_url"] = $row["avatar_url"];

            // Learn the IP address of the logged in user.
            $ip = $_SERVER['REMOTE_ADDR'];

            $user_id = $_SESSION["user_id"];
            // TODO 10c: use PDO::exec() to save the destails of this login in the Logins table
            //           Hints in UR Courses
            $query = "INSERT INTO Logins (user_id, login_time, ip_address) VALUES ('$user_id', NOW(), '$ip')";
            $result = $db->exec($query);
            // TODO 10d: Close the database connection 
            //           and redirect the user to the loginHistory.php page.
            $db = null;
            header("Location: loginHistory.php");
            exit();
        } else {
            // login unsuccessful
            $errors["Login Failed"] = "That username/password combination does not exist.";
        }

        $db = null;

    } else {

        $errors['Login Failed'] = "You entered invalid data while logging in.";
    }
    if(!empty($errors)){
        foreach($errors as $type => $message) {
            echo "$type: $message <br />\n";
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" /> <!-- FIXED: spacing -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css" />
    <script src="eventHandlers.js"></script>
  </head>

  <body>
    <!-- Header Section -->
    <header>
      <div class="app-logo">
        <!-- FIXED: Corrected alt attribute syntax -->
        <img src="logo.png" alt="App Logo" />
      </div>
    </header>

    <!-- Main Layout -->
    <main>
      <!-- FIXED: Removed broken nested comment and added heading -->
      <div class="auth-container">
        <form class="auth-form" action="mainpage.html" id="login-page">
          <h2 class="auth-header">Login</h2> <!-- FIXED: Uncommented heading -->
          <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" />
            <p id="error-text-username" class="error-text hidden">Username is invalid</p>
          </div>
          <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" />
            <p id="error-text-password" class="error-text hidden">Password is invalid</p>
          </div>
          <button type="submit">Login</button>
        </form>
           <!-- FIXED: Moved foot-note outside form and formatted -->
        <div class="auth-option">
          <p>Don't have an account? <a href="signup.html">Signup</a></p>
        </div>
      </div>
    </main> <!-- FIXED: Closed main tag -->
    <footer id="footer-auth">
            <p class="footer-text">BudgetWise - Personal Budget Tracker</p>
        </footer>

    <!-- FIXED: Corrected 'src' spelling -->
   <script src="eventRegisterLogin.js"></script>
  </body>
</html>
