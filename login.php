<?php
session_start();
include('storage.php');
$io = new JsonIO('users.json');
$users = new Storage($io);
$userStorage = $users->findAll();
$loginErr = "";
$errors = [];
$input =false;

if (isset($_POST["submit"])) {
  $input=true;
  if (!isset($_POST["username"]) || strlen($_POST["username"])==0) {
    $errors["username"] = "This Field is required";
    $input =false;
  }
  if (!isset($_POST["password"]) || strlen($_POST["password"])==0) {
    $errors["password"] = "This Field is required";
    $input =false;
  }
}
$test = false;
$id="";
if ((count($errors) == 0) && $input ){

  foreach ($userStorage as $key => $value) {
    if ($value["username"] == $_POST["username"]) {
      if ($value["password"] == $_POST["password"]) {
        $test = true;
        $id=$value["id"];
        break;
      }
    }
  }
  if (!$test) {
    $loginErr = "verify you username or password";
  }
}
if ($test) {
  $_SESSION["id"] = $id;
  $_SESSION["user"] = $_POST["username"];
  header("Location: /index.php");
}




?>
<html>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title></title>
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
</head>

<body style="background-color: #eee;">
  <header class="
        d-flex
        flex-wrap
        align-items-center
        justify-content-center justify-content-md-between
        py-3
        px-3
        bg-dark
      ">
    <h1 class="text-light">Elte's stadium</h1>
    <ul class="nav col-12 col-md-auto justify-content-center">
      <li><a href="login.php" class="nav-link text-light" active>Login</a></li>
      <li><a href="signup.php" class="nav-link text-light">SignUp</a></li>
    </ul>
  </header>
  <div class="d-flex justify-content-center">
  
    <form class="col-6 mt-5" method="post" novalidate>
    <?php if (strlen($loginErr) > 0) : ?>
      <p class=" h6 text-danger"><?= $loginErr ?> </p>
    <?php endif ?>
      <div class="form-group">
        <label for="Username">Username</label>
        <input type="text" class="form-control" id="Username" name="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : "" ?>"  />
        <?php if (isset($errors["username"])) : ?>
          <p class=" h6 text-danger"><?= $errors["username"] ?> </p>
        <?php endif ?>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : "" ?>"  />
        <?php if (isset($errors["password"])) : ?>
          <p class=" h6 text-danger"><?= $errors["password"] ?> </p>
        <?php endif ?>
      </div>

      <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</body>

</html>