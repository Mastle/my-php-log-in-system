<?php

session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION ["loggedin"] == TRUE){
    echo "<script>" . "window.location.href='./'" . "</script>";
    exit;
}

include_once "./config/Database.php";
$pdo_obj = new Database();
$pdo_connection = $pdo_obj->connect();


$user_login_err = $user_password_err = $login_err = "";
$user_login = $user_password = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["user_login"]))) {
    $user_login_err = "Please enter your username or an email id.";
  } else {
    $user_login = trim($_POST["user_login"]);
  }
 
  if (empty(trim($_POST["user_password"]))) {
    $user_password_err = "please enter your password.";
  } else {
    $user_password = trim($_POST["user_password"]);
  }



# validate credentials
if (empty($user_login_err) && empty($user_password_err)) {
    $sql_query = "SELECT id, username, password FROM my_users WHERE username = :username OR email = :email";
    $stmt = $pdo_connection->prepare($sql_query);

    if($stmt->execute(['username' => $user_login, 'email' => $user_login])){
        if($stmt->rowCount() == 1){
            $stmt->bindColumn('id', $id);
            $stmt->bindColumn('username', $username);
            $stmt->bindColumn('password', $hashed_password);
            if($stmt->fetch(PDO::FETCH_BOUND)){
                if (password_verify($user_password, $hashed_password)){
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["loggedin"] = TRUE;

                    echo "<script>" . "window.location.href='./'" . "</script>";
                    exit;
                } else {
                    $login_err = "The email or password you entere is incorrect.";
                }

            }

        } else {
            $login_err = "Invalid uername or password.";
        }
    }


}else {
    echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
    echo "<script>" . "window.location.href='./login.php'" . "</script>";
    exit;
  }


}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Now!</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/main.css">
  <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">
  <script defer src="./js/script.js"></script>
</head>

<body>
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <?php
        if (!empty($login_err)) {
          echo "<div class='alert alert-danger'>" . $login_err . "</div>";
        }
        ?>
        <div class="form-wrap border rounded p-4">
          <h1>Log In</h1>
          <p>Please login to continue</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="user_login" class="form-label">Email or username</label>
              <input type="text" class="form-control" name="user_login" id="user_login" value="<?= $user_login; ?>">
              <small class="text-danger"><?= $user_login_err; ?></small>
            </div>
            <div class="mb-2">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="user_password" id="password">
              <small class="text-danger"><?= $user_password_err; ?></small>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="togglePassword">
              <label for="togglePassword" class="form-check-label">Show Password</label>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn btn-primary form-control" name="submit" value="Log In">
            </div>
            <p class="mb-0">Don't have an account ? <a href="./register.php">Sign Up</a></p>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
</body>
</html>