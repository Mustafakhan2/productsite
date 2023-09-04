<?php
$showAlert = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include "./partials/connect.php";
    $email = $_POST["email"];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $username = $_POST["uname"];

    $existSql = "SELECT * FROM `user_tbl` WHERE username = '$username'";
    $res = mysqli_query($con, $existSql);
    $numExistRows = mysqli_num_rows($res);

    if ($numExistRows > 0) {
        $showError = "Username already exists";
    } else {
        if ($pass == $cpass) {
            $query = "INSERT INTO `user_tbl` (`id`, `email`, `pass`, `username`) VALUES (NULL, '$email', '$pass', '$username')";
            $res = mysqli_query($con, $query);
            if ($res) {
                $showAlert = true;
            }
        } else {
            $showError = "Password Don't Match";
        }
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
    <?php require("./partials/nav.php")  ?>
    <?php
    if ($showAlert) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sucess!</strong> Your account has been created. Go to login now
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> ';
    }
    if ($showError) {
        echo ' <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>' . $showError . '</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> ';
    }
    ?>
    <div class="container">
        <h1 class="text-center">SignUp Here</h1>
        <!-- form -->
        <form action="./signup.php" method="post">

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" name="pass" id="pass" value="<?php echo isset($_POST['pass']) ? $_POST['pass'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="cpass" id="confirmpass" value="<?php echo isset($_POST['cpass']) ? $_POST['cpass'] : ''; ?>" required>
                <small class="text-danger">Make sure the Password is the same</small>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">UserName</label>
                <input type="text" class="form-control" id="username" name="uname" value="<?php echo isset($_POST['uname']) ? $_POST['uname'] : ''; ?>" aria-describedby="username" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">SignUp</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>