<?php
session_start();
require "database.php";

    if($_GET){
        $errormessage = $_GET['errormessage'];
    }
    else{
        $errormessage = '';
    }

    if($_POST){
        $success = false;
        $email = $_POST['email'];
        $password = $_POST['password'];
       
        
        $pdo = Database::connect();
        $pdo->setAttribute (PDO::ATTR_ERRMODE, PDO ::ERRMODE_EXCEPTION);
        
        $sql = "SELECT * FROM customer WHERE email = '$email' AND PASSWORD = '$password'";
        
        $q = $pdo ->prepare($sql);
        $q->execute(array());
        $data = $q->fetch(PDO::FETCH_ASSOC);
        
        if ($data){
            $_SESSION ["email"] = $email;
            header ("Location: customer.php?id=$email ");
        }
        else{
            header ("Location: login.php?errormessage=Invalid username or password");
            
        }
    }
?>

<form class "form-horizontal" action"login.php" method="post">
      <div class = "control group">
          <label class ="control-label"> Username(Email Address) </label>
          <div class = "controls">
              <input name = "email" type="text" placeholder="me@email.com">
              <input name = "password" type="password" placeholder="password">
              </div>
          <button type = "submit" class = "btn btn-success"> Sign in</button>
          
          <a href="logout.php">Log Out</a>
          <p style ="color:red;"><?php echo $errormessage; ?></p>
          </div>
</form>

