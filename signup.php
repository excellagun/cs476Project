<?php  
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); //encodes
    return $data;
}

// This variables will keep track of errors and form values
// we find while processing the form but we'll make them global
// so we can display POST results on the form when there's an error.
$errors = array();
$username = "";
$password = "";
//$dob = "";
$email ="";
//$fullname="";

    // Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // If we got here through a POST submitted form, process the form

    // Collect and validate form inputs
    $email = test_input($_POST["email"]);
    $username = test_input($_POST["screenname"]);
    $password = test_input($_POST["pass"]);
   // $dob = test_input($_POST["dob"]);
   // $fullname = $_POST["fname"];
    
    // Form Field Regular Expressions
   // $nameRegex = "/^[a-zA-Z]+$/";
    $unameRegex = "/^[a-zA-Z0-9_]+$/";
    $passwordRegex = "/^(?=.*[!@#$%^&*()\-_=+\[\]{};:'\",.<>\/?]).{8,}$/"; 
    // updated password regex to support condition of a minimum of 8 characters
    //  and 1 special character in html form
    //$dobRegex = "/^\d{4}[-]\d{2}[-]\d{2}$/";
    
    // Validate the form inputs against their Regexes 
    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
    {
        $errors["email"]= "Invalid email";
    }
    if (!preg_match($unameRegex, $username)) {
        $errors["username"] = "Invalid Username";
    }
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
    }
    /*if (!preg_match($dobRegex, $dob)) {
        $errors["dob"] = "Invalid DOB";
    }*/

    // Declare $target_file here so we can use it later
    // $target_file = "";
    // TODO 5: try to make a MySQL connection
    //         catch and report any  errors
    /* ??? */ 
         try {
            $db= new PDO($attr, $db_user, $db_pwd, $options);

            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        }
    catch (PDOException $e ) {
        die ("PDO Error >> " . $e->getMessage() . "\n<br />");
    }


    // SQL query that finds matches for the username in the Users table
    $query = "select * from Users where Username = '$username'";

    // use PDO::query() to run the query
    $result = $db->query($query);

    //use PDOStatement::fetch() to get the value of the first result of the query

    $match = $result->fetch();    ///it was $match = 0;

    // If that email address is already taken
    if ($match) {
        $errors["Account Taken"] = "A user with that username already exists.";
    }
    
    //If there are no errors so far we can try inserting a user
    if (empty($errors)) {
        $query = "insert into Users(Email, Username, PasswordHash ) values('$email','$username','$password')";
        $result = $db->exec($query);

        if (!$result) {
            $errors["Database Error:"] = "Failed to insert user info";
        } else 
         // TODO 8a: close the database connection
                    $db = null;
                    
                    // TODO 8b: redirect the user to the login page
                    header("Location: main_page.php");
            
                    // TODO 8c: exit the php script
                    exit();
    } 
     if (!empty($errors)) {
        foreach($errors as $type => $message) {
            print("$type: $message \n<br />");
        }
    }

} // submit method was POST

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8" />
  <title>Sign Up - BudgetWise</title>
  <link rel="stylesheet" href="./styles.css" /><!--stylee.css-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="eventHandler.js" ></script>
</head>

  <body>
    <header class="auth-header">
      <h1>Budget Tracker</h1>
    </header>
    <main class="auth-container">
        <form id="signup-form" action="mainpage.html" method="post" class="auth-form" novalidate>
          <h2 class="form-title">Create an account</h2>
  
          <div class="input-field">
            <label for="fname">Full Name</label>
            <input type="text" id="fname" name="fname" required />
          </div>
  
          <div class="input-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
            <p id="error-text-email" class="error-text hidden">Please enter a valid email address.</p>
          </div>
  
          <div class="input-field">
            <label for="screenname">Username</label>
            <input type="text" id="screenname" name="screenname" required />
            <p id="error-text-screenName" class="error-text hidden">Use letters, numbers, and underscores only.</p>
          </div>
  
          <div class="input-field">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="pass" required />
            <p id="error-text-password" class="error-text hidden">Min. 8 chars, 1 special character.</p>
          </div>
  
          <div class="input-field">
            <label for="confirm_pwd">Confirm Password</label>
            <input type="password" id="confirm_pwd" name="confirm_pwd" required />
            <p id="error-text-confirm-password" class="error-text hidden">Passwords do not match.</p>
          </div>
  
          <div class="input-field">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required />
          </div>
  
          <div class="input-field">
            <button type="submit">Sign Up</button>
          </div>
        </form>
  
        <div class="auth-option">
          Already have an account? <a href="login.html">Login here</a>
        </div>
    </main>
    <footer class="auth-footer">
      <p class="footer-text">BudgetWise - Personal Budget Tracker</p>
    </footer>
    <script src="eventRegisterSignup.js"></script>
  </body>
</html>
