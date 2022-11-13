<?php 

include_once "./config/Database.php";

$username_err = $email_err = $password_err = "";
$username = $email = $password = "";
$pdo_obj = new Database();
$pdo_connection = $pdo_obj->connect();


if ($_SERVER["REQUEST_METHOD"] == "POST"){
  # Validate username
   if(empty(trim($_POST['username']))){
    $username_err = 'Please enter a username';
   } else {
    $username = trim($_POST["username"]);
    if (!ctype_alnum(str_replace(array("@", "-", "_"), "", $username))){
      $username_err = "Username can only contain letters, numbers and symbols like '@', '_', or '-'.";
    } else {
      #prepare a select statement
      $sql_query = 'SELECT * FROM my_users WHERE username = :username';
      $stmt = $pdo_connection->prepare($sql_query);
      
      if($stmt->execute(['username' => $username])){
        
       if($stmt->rowCount() > 0){
        $username_err = "This username is already registered.";
    } 
    } else {
      echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
     
    }
   }
   }

   # Validate email
   if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email address";
   } else {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_err = "Please enter a valid email address.";
    } else {
      $sql_query = "SELECT id FROM my_users WHERE email = :email";
      $stmt = $pdo_connection->prepare($sql_query);

      if($stmt->execute(['email' => $email])){
        if($stmt->rowCount() > 0){
          $email_err = "This email is already registered.";
        }
      } else{
        echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";

      }
   }

  }
  # Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } else {
    $password = trim($_POST["password"]);
    if (strlen($password) < 8){
      $password_err = "Password must contain at least 8 or more characters";
    }
  } 

  # Check input errors before insterting data into database
  if (empty($username_err) && empty($email_err) && empty($password_err)) {
    # Prepare an insert statement
    $sql_query = "INSERT INTO my_users(username, email, password) VALUES (:username, :email, :password)"; 
    $stmt = $pdo_connection->prepare($sql_query);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if($stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password])){
      echo "<script>" . "alert('Registeration completed successfully. Login to continue.');" . "</script>";
      echo "<script>" . "window.location.href='./login.php';" . "</script>";

      exit;
    } else {
      echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
    }


   }

}

?>





<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script defer src="./js/script.js"></script>
    <title>Register Now!</title>
</head>

<body>
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <div class="form-wrap border rounded p-4">
          <h1>Sign up</h1>
          <p>Please fill this form to register</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" id="username" value="<?= $username; ?>">
              <small class="text-danger"><?= $username_err; ?></small>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" id="email" value="<?= $email; ?>">
              <small class="text-danger"><?= $email_err; ?></small>
            </div>
            <div class="mb-2">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="password" value="<?= $password; ?>">
              <small class="text-danger"><?= $password_err; ?></small>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="togglePassword">
              <label for="togglePassword" class="form-check-label">Show Password</label>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn btn-primary form-control" name="submit" value="Sign Up">
            </div>
            <p class="mb-0">Already have an account ? <a href="./login.php">Log In</a></p>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
</body>
</html>