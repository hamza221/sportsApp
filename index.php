<?php
include('storage.php');
session_start();
$_SESSION["team"]="";
$io = new JsonIO('teams.json');
$teamStorage = new Storage($io);
$teams = $teamStorage->findAll();
$io = new JsonIO('users.json');
$users = new Storage($io);
$io = new JsonIO('matches.json');
$matcheStorage = new Storage($io);
//$one = ["home"=>["id"=> "61b65f7fb85d6","score"=>"9"],"away"=>["id"=> "61b65f96afa94","score"=>"0"],"date"=> "1998-07-12"];
//$matcheStorage->add($one);

$user = isset($_SESSION["id"]) ? $users->findById($_SESSION["id"]) : null;

if (isset($_GET["fav"]) && $user != null) {
  if (($key = array_search($_GET["fav"], $user["favoriteTeam"])) !== false/* in_array($_GET["fav"],$user["favoriteTeam"] )*/) {
    unset($user["favoriteTeam"][$key]);
    $users->update($user["id"], $user);
    header("Location: /index.php");
  } else {
    array_push($user["favoriteTeam"], $_GET["fav"]);
    print_r(($user));
    $users->update($user["id"], $user);
    header("Location: /index.php");
  }
}
function isAdmin()
{
  global $users;
  if (isset($_SESSION["id"])) {
    $user = $users->findById($_SESSION["id"]);
    return in_array("admin", $user["roles"]);
  }
}
function topFive($var)
{
  global $user;
  if (in_array($var["home"]["id"], $user["favoriteTeam"]) || in_array($var["away"]["id"], $user["favoriteTeam"])) {
    return true;
  } else {
    return false;
  }
}
if ($user != null && count($user["favoriteTeam"]) > 0) {
  $matches = $matcheStorage->findMany('topFive');
} else {
  $matches = $matcheStorage->findAll();
}
usort($matches, function ($a, $b) {
  return -1 * (new DateTime($a['date']) <=> new DateTime($b['date']));
});

?>
<html>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title></title>
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
  <link rel="stylesheet" href="styles.css" />
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
      <?php if (!isset($_SESSION["user"])) : ?>
        <li><a href="login.php" class="nav-link text-light">Login</a></li>
        <li><a href="signup.php" class="nav-link text-light">SignUp</a></li>
      <?php endif ?>
      <?php if (isset($_SESSION["user"])) : ?>
        <li>
          <a href="index.php" class="nav-link text-light"> Welcome <?= $_SESSION["user"] ?></a>
        </li>
        <li><a href="logout.php" class="nav-link text-light">Logout</a></li>


      <?php endif ?>
    </ul>
  </header>
  <div class=" mt-5 container  d-flex  flex-wrap align-items-center justify-content-center justify-content-md-between">
  <p class="text-dark  h3 mb-5">The ELTE Stadium web page, here You can find matches played , and you can follow the results of your favorite teams.</p>
    <?php
    foreach ($teams as $key => $value) : ?>
      <div class="card mr-3 col-3 ">
        <img class="card-img-top " src="<?= $value["img"] ?>" alt="Card image cap">
        <div class="card-body">
          <h5 class="card-title"><?= $value["name"] ?></h5>
          <p class="card-text"><?= $value["city"] ?></p>

          <a href="\teams.php?id=<?= $value["id"] ?>" class="btn btn-primary">Team page</a>


          <a class='btn btn-transparent' href="<?php echo ($user != null) ? "/index.php?fav=$key" : "#" ?>"><i class="<?php if ($user != null) {
                                                                                                                                echo (in_array($key,$user["favoriteTeam"]) ) ? "bi bi-star-fill" : "bi bi-star";
                                                                                                                              } else {
                                                                                                                                echo "bi bi-star";
                                                                                                                              } ?>"></i></a>

        </div>
      </div>

    <?php endforeach  ?>
  </div>
  </div>

  </div>
  <table class="table mt-4">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Home</th>
        <th scope="col">Away</th>
        <th scope="col">Score</th>
        <th scope="col">Date</th>
        <?php if (isAdmin()) : ?>
          <th scope="col">Admin</th>
        <?php endif ?>
      </tr>
    </thead>
    <tbody>



      <?php
      $i = 0;
      foreach ($matches as $key => $value) {
        if ($i == 5) {
          break;
        }
        $home = $teamStorage->findById($value["home"]["id"]);
        $away = $teamStorage->findById($value["away"]["id"]);
        if (isset($user) && count($user["favoriteTeam"]) > 0) {
          if ($home["id"] == $user["favoriteTeam"]) {
            $test = true;
          } else {
            $test = false;
          }
          if ($value["home"]["score"] == $value["away"]["score"]) {
            $color = "bg-warning";
          } else if ((($value["home"]["score"] > $value["away"]["score"]) && $test) || (($value["home"]["score"] < $value["away"]["score"]) && !$test)) {
            $color = "bg-success";
          } else {
            $color = "bg-danger";
          }
        } else {
          $color = "bg-primary";
        }

        echo " <tr class =\"{$color}\">
          <th scope=\"row\">{$i}</th>
          <td class ='admin-link'>{$home["name"]}</td>
          <td class ='admin-link'>{$away["name"]}</td>
          <td class ='admin-link'>{$value["home"]["score"]}/{$value["away"]["score"]}</td>
          <td class ='admin-link'>{$value["date"]}</td>";
        if (isAdmin()) {
          echo "<td ><a class='admin-link' href='/admin.php?id={$value["id"]}'>Edit</a></td>";
        }
        echo "</tr>";
        $i++;
      }


      ?>


    </tbody>
  </table>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

</html>