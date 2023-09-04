<?php
$login = false;
$showError = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "./partials/connect.php";
    $email = $_POST["email"];
    $pass = $_POST['pass'];
    $query = "SELECT * FROM user_tbl where email = '$email' AND pass = '$pass'";
    $res = mysqli_query($con, $query);
    $num = mysqli_num_rows($res);
    if ($num == 1) {
        $login = true;
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;

        // Fetch the user's name and store it in the session
        $row = mysqli_fetch_assoc($res);
        $username = $row['username']; // Replace 'username' with the actual column name in your database that stores the user's name
        $_SESSION['username'] = $username; // Store the user's name in the session
        // After successful login
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row['id']; // Store the user's ID in the session
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;
        header("location: welcome.php");


        header("location: welcome.php");
    } else {
        $showError = "Invalid Email or password";
    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
    <?php require("./partials/nav.php")  ?>
    <?php
    if ($login) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sucess!</strong> You are logged in 
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
        <h1 class="text-center">Login Here</h1>
        <!-- form -->
        <form action="./login.php" method="post">

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" name="pass" id="pass" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>