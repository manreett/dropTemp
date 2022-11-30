<?php

    session_start();

    if(isset($_SESSION['loggedin']) && !empty($_SESSION['loggedin'])){
        header('location: data.php');
        exit;
    }

	$configs = include('config.php');
	
	$conn = new mysqli($configs['db_server'], $configs['db_user'], $configs['db_pass'], $configs['db_name']);
	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

    $email = $email_err = "";

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(empty(trim($_POST['email']))){
            $email_err = "Please enter an email.";
        } else{
            $email = trim($_POST['email']);
        }

        if(empty($email_err)){
            $sql = "SELECT email FROM data WHERE email = ?";
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("s", $param_email);
                $param_email = $email;
                if($stmt->execute()){
                    $stmt->store_result();
                    if($stmt->num_rows>=1){
                        session_start();
                        $_SESSION['loggedin'] = true;
                        $_SESSION['email'] = $email;

                        header("location: data.php");
                        #$email_err = "Email exists, this is just a cool placeholder message!";
                    } else{
                        $email_err = "Email doesn't exist, please register your email on your Drop controller.";
                    }
                }
                $stmt->close();
            }
        }
    }
    $conn->close();


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Droptemp Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Login
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <label for="emailInput" class="col-md-1 col-sm-2 col-form-label">Email</label>
                                <div class="col-md-10 col-sm-10">
                                    <input type="email" class="form-control" name="email" id="emailInput">
                                </div>
                                <div class="col-md-1 col-sm-12">
                                    <input type="submit" class="btn btn-primary" value="Login">
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php
                    if (!empty($email_err)){
                        echo '<div class="card-footer">'.$email_err.'</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>

