<?php
include('storage.php');

session_start();
$io = new JsonIO('matches.json');
$matcheStorage = new Storage($io);
$io = new JsonIO('teams.json');
$teamStorage = new Storage($io);
$io = new JsonIO('comments.json');
$commentStorage = new Storage($io);
$io = new JsonIO('users.json');
$users = new Storage($io);
$_SESSION["team"] = $_GET["id"];
function check($var)
{

    if ($var["home"]["id"] == $_GET["id"] || $var["away"]["id"] == $_GET["id"]) {
        return true;
    } else {
        return false;
    }
}

function checkComment($var)
{
    if ($var["teamid"] == $_GET["id"]) {
        return true;
    } else {
        return false;
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

$error = "";
$newComment = [];
$matches = $matcheStorage->findMany("check");
$comments = $commentStorage->findMany("checkComment");
$team = $teamStorage->findById($_GET["id"]);
if (isset($_POST["comment"])) {
    if (strlen($_POST["comment"]) == 0) {
        $error = "*comment cant be empty";
    } else {
        $newComment["author"] = $_SESSION["user"];
        $newComment["text"] = $_POST["comment"];
        $newComment["teamid"] = $_GET["id"];
        $newComment["timestamp"] = date('Y-m-d / H:i');
        $commentStorage->add($newComment);
        header("refresh:0.1 ");
    }
}
if (isset($_POST["admRemov"])) {
    $commentStorage->delete($_POST["admRemov"]);
    header("refresh:0.1 ");
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
        <!--<h1 class="text-light">Elte's stadium</h1> !-->
        <div style="display: flex;">
            <img style="width: 70px;" src="<?= $team["img"] ?>" />
            <h1 style=" transform: translateY(25%); margin-left:20px;" class="text-light"><?= $team["name"] ?></h1>
        </div>
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
</body>
<div>
    <table class="table">
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
                $home = $teamStorage->findById($value["home"]["id"]);
                $away = $teamStorage->findById($value["away"]["id"]);
                if ($home["id"] == $_GET["id"]) {
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


                echo " <tr class =\"{$color}\">
          <th scope=\"row\">{$i}</th>
          <td class ='admin-link'>{$home["name"]}</td>
          <td class ='admin-link'>{$away["name"]}</td>
          <td class ='admin-link'>{$value["home"]["score"]}/{$value["away"]["score"]}</td>
          <td class ='admin-link'>{$value["date"]}</td>";
                if (isAdmin()) {
                    echo "<td><a class='admin-link' href='/admin.php?id={$key}'>Edit</a></td>";
                }
                echo "</tr>";
                $i++;
            }


            ?>


        </tbody>
    </table>
    <div class="container">
        <?php
        foreach ($comments as $key => $value) : ?>
            <div class=" bg-light" style="padding: 10px; border-bottom:1px solid black; ">
                <div style="display: flex;">
                    <img src="avatar.png" style="width: 90px; height:50px">
                    <div>
                        <h2 style="margin: 0;"><?= $value["author"] ?></h2>

                        <p style="color: #878786;"><?= $value["timestamp"] ?></p>
                    </div>
                </div>

                <p style="margin-top: 5px;"><?= $value["text"] ?></p>
                <?php if (isAdmin()) : ?>
                    <form method="post">
                        <button type="submit" name="admRemov" value="<?= $value["id"] ?>" class="btn btn-danger">Remove comment</button>
                    </form>
                <?php endif ?>

            </div>


        <?php endforeach ?>
        <?php if (isset($_SESSION["user"])) : ?>
            <form method="post" novalidate>
                <div class="form-group mt-1">
                    <label class="h4" for="newComment">New Comment</label>
                    <textarea class="form-control" id="newComment" rows="3" name="comment" required></textarea>
                    <?php if (strlen($error) > 0) : ?>
                        <p class=" h6 text-danger"><?= $error ?> </p>
                    <?php endif ?>
                    <button class="btn btn-primary mt-1" type="submit">Submit</button>
                </div>
            </form>
        <?php endif ?>
        <?php if (!isset($_SESSION["user"])) : ?>
            <form method="post" novalidate>
                <div class="form-group mt-1">
                    <label class="h4" for="newComment">New Comment</label>
                    <textarea class="form-control" id="newComment" rows="3" name="comment" disabled></textarea>
                    <p class=" h6 text-danger"> Please LogIn </p>
                    <button class="btn btn-primary mt-1 " type="submit" disabled>Submit</button>
                </div>
            </form>
        <?php endif ?>
    </div>


</html>