<?php
include('storage.php');
session_start();
$io = new JsonIO('users.json');
$users = new Storage($io);
$io = new JsonIO('matches.json');
$matcheStorage = new Storage($io);
$io = new JsonIO('teams.json');
$teams = new Storage($io);

function isAdmin()
{
    global $users;
    if (isset($_SESSION["id"])) {
        $user = $users->findById($_SESSION["id"]);
        return in_array("admin", $user["roles"]);
    }
}
$match = $matcheStorage->findById($_GET["id"]);
$home = $teams->findById($match["home"]["id"]);
$away = $teams->findById($match["away"]["id"]);
if (isset($_POST["submit"])) {
    $matcheStorage->update($_GET["id"], [
        "home" => ["id"=>$home["id"], "score"=>$_POST["home"]],
        "away" => ["id" => $away["id"], "score" => $_POST["away"]],
        "date"=>$_POST["date"],
        "id"=>$_GET["id"]
    ]);
    if(isset($_SESSION["team"]) && strlen($_SESSION["team"])>0){
    header("Location: /teams.php?id=".$_SESSION["team"]);
    }
    else{
        header("Location: /");
    }
 
}
if(isset($_POST["back"])){
    if(isset($_SESSION["team"]) && strlen($_SESSION["team"])>0){
        header("Location: /teams.php?id=".$_SESSION["team"]);
        }
        else{
            header("Location: /");
        }
     
}
if(isset($_POST["delete"])){
    $matcheStorage->delete($_GET["id"]);
    if(isset($_SESSION["team"]) && strlen($_SESSION["team"])>0){
        header("Location: /teams.php?id=".$_SESSION["team"]);
        }
        else{
            header("Location: /");
        }
     ;

}


if (isAdmin()) : ?>
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

    <body class="d-flex justify-content-center bg-light ">
        <form class="col-6 mt-5" method="post" novalidate>
            <div class="form-group">
                <label for="home"><?= $home["name"] ?></label>
                <input type="text" class="form-control" name="home" value="<?php echo $match["home"]["score"];  ?>">

            </div>
            <div class="form-group">
                <label for="away"><?= $away["name"] ?></label>
                <input type="text" class="form-control" name="away" value="<?php echo $match["away"]["score"];  ?>">

            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" name="date" value="<?php echo $match["date"];  ?>">

            </div>

            <button type="button" name="delete" id="liveToastBtn" class="btn btn-danger">Delete the match</button>
            <button type="submit" name ="back" class="btn btn-success">Back</button>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast " role="alert aria-live=" assertive" aria-atomic="true">
                <div class="toast-header">
                    <div class=" me-2">
                        <img src="<?= $home["img"] ?>" class="rounded" style="width: 30px;" alt="...">
                        <img src="<?= $away["img"] ?>" class="rounded mr-2" style="width: 30px;" alt="...">
                    </div>
                    <strong class="mr-auto">Are you sure?</strong>
                    <button type="button" class="ml-2 mb-1 close" id="close"  aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>


                </div>
                <div class="toast-body ">
                    Are you sure you want to delete the match?
                    <form method="post" class="mt-2">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm ">Delete</button></form>
                  
                    
                </div>
            </div>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        const btn = document.getElementById("liveToastBtn");
        const toastt = document.getElementById("liveToast");
        const close =document.getElementById("close");
        if (btn) {
            btn.addEventListener('click', function() {
                var toast = new bootstrap.Toast(toastt)

                toast.show()
            })
        }
        if (close) {
            close.addEventListener('click', function() {
                var toast = new bootstrap.Toast(toastt)

                toast.hide()
            })
        }
    </script>

    </html>
<?php endif ?>