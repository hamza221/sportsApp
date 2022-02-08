<?php
include('storage.php');
$io = new JsonIO('users.json');
$users= new Storage($io);
$email =    "";
$username = "";
$password =  "";
$password2 = "";
$errors=[];
$emails=[];
foreach ($users->findAll() as $key => $value) {
  array_push($emails,$value["email"] );
}
if (isset($_POST["submit"])) {
  if (isset($_POST["email"]) && strlen($_POST["email"])>0) {
    if (!str_contains($_POST["email"], '@')) {
      $errors["email"] = "Check email format";
    } else {
      if(in_array(trim( $_POST["email"]),$emails)){
        $errors["email"] = "This email is already registerd. Try logging in";
      }else{
        $email = $_POST["email"];
      }
      
    }
  } else {
    $errors["email"] = "This field is required";
  }
  if (isset($_POST["username"])&&strlen($_POST["username"])>0) {

    $username = $_POST["username"];
  } else {
    $errors["username"] = "This field is required";
  }
  if (isset($_POST["password"])&&strlen($_POST["password"])>0) {

    $password = $_POST["password"];
  } else {
    $errors["password"] = "This field is required";
  }
  if (isset($_POST["password2"])&&strlen($_POST["password2"])>0) {
    if ($_POST["password2"] == $password) {
      $password2 = $_POST["password2"];
    } else {
      $errors["password2"] = "This field should be same as password";
    }
  } else {
    $errors["password2"] = "This field is required";
  }
  if(count($errors)==0){
    echo "jawek zboub";
    $newUser["email"]=$email;
    $newUser["username"]=$username;
    $newUser["password"]=$password;
    $newUser["roles"]=["user"];
    $users->add($newUser);
    header("Location: /assing/login.php");
  }
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
      <li><a href="login.php" class="nav-link text-light">Login</a></li>
      <li><a href="signup.php" class="nav-link text-light">SignUp</a></li>
    </ul>
  </header>
  <div class="d-flex justify-content-center">
    <form class="col-6 mt-5" method="POST" novalidate>
      <div class="form-group">
        <label for="exampleInputEmail1">Email </label>
        <input type="email" class="form-control" aria-describedby="emailHelp" name="email" value="<?php echo isset($_POST["email"]) ?$_POST["email"]:""  ?>"  />
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        <?php if (isset($errors["email"])&&strlen($errors["email"]) > 0) : ?>
          <p class=" h6 text-danger"><?= isset($errors["email"]) ?$errors["email"] :""  ?> </p>
        <?php endif ?>
      </div>
      <div class="form-group">
        <label for="username">username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST["username"]) ?$_POST["username"]:""  ?>"  />
        <?php if (isset($errors["username"]) &&strlen($errors["username"]) > 0) : ?>
          <p class=" h6 text-danger"><?= isset($errors["username"]) ?$errors["username"] :""  ?> </p>
        <?php endif ?>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" name="password" value="<?php echo isset($_POST["password"]) ?$_POST["password"]:""  ?>"  />
        <?php if (isset($errors["password"]) &&strlen($errors["password"]) > 0) : ?>
          <p class=" h6 text-danger"><?= isset($errors["password"]) ?$errors["password"] :""  ?> </p>
        <?php endif ?>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword2">Confirm Password</label>
        <input type="password" class="form-control" name="password2" value="<?php echo isset($_POST["password2"]) ?$_POST["password2"]:""  ?>"  />
        <?php if (isset($errors["password2"])&& strlen($errors["password2"]) > 0) : ?>
          <p class=" h6 text-danger"><?=  isset($errors["password2"]) ?$errors["password2"] :"" ?> </p>
        <?php endif ?>
      </div>

      <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</body>

</html>