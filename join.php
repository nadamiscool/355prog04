<?php

session_start();

require "database.php";

?>

<form class "form-horizontal" action"join.php" method="post">
      <div class = "control group">
          <label class ="control-label"> Username(Email Address) </label>
          <div class = "controls">
              <input name = "email" type="text" placeholder="me@email.com">
              <input name = "password" type="password" placeholder="password">
              <input name = "name" type="text" placeholder="name">
              <input name = "mobile" type="text" placeholder="mobile number">
              </div>
          <button type = "submit" class = "btn btn-success"> join</button>
          <a href="logout.php">Log Out</a>
          
          </div>
</form>
