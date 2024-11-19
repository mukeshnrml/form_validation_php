<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reg";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$name_error = $mobile_error = $email_error = $password_error = $success = "";
$is_valid = true;
$name = $mobile_number = $gender = $gmail_id = $password = $address = ""; // Initialize form values

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = htmlspecialchars($_POST['name']);
    $mobile_number = htmlspecialchars($_POST['mobile_number']);
    $gender = htmlspecialchars($_POST['gender']);
    $gmail_id = htmlspecialchars($_POST['gmail_id']);
    $password = htmlspecialchars($_POST['password']);
    $address = htmlspecialchars($_POST['address']);

   
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $name_error = "Name can only contain letters and spaces.";
        $is_valid = false;
    }


    if (!preg_match("/^[0-9]{10}$/", $mobile_number)) {
        $mobile_error = "Please enter a valid 10-digit mobile number.";
        $is_valid = false;
    } elseif (!ctype_digit($mobile_number)) { 
      
        $mobile_error = "Enter only numbers for mobile number.";
        $is_valid = false;
    } else {
        //  mobile number already exists 
        $mobile_check_query = "SELECT * FROM users WHERE mbl = '$mobile_number' LIMIT 1";
        $result = $conn->query($mobile_check_query);
        
        if ($result && $result->num_rows > 0) {
            $mobile_error = "User already registered with this mobile number.";
            $is_valid = false;
        }
    }

    //  email already exists
    $email_check_query = "SELECT * FROM users WHERE mail = '$gmail_id' LIMIT 1";
    $result = $conn->query($email_check_query);
    if ($result && $result->num_rows > 0) {
        $email_error = "This email is already registered.";
        $is_valid = false;
    }

    // Password validation
    $password_pattern = "/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/";
    if (!preg_match($password_pattern, $password)) {
        $password_error = "Password must be at least 6 characters long, include at least one number, one letter, and one special character.";
        $is_valid = false;
    }

   
    if ($is_valid) {
        $sql = "INSERT INTO users (name, mbl, gender, mail, pass, address) 
                VALUES ('$name', '$mobile_number', '$gender', '$gmail_id', '$password', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful!";
            // successful registration
            $name = $mobile_number = $gender = $gmail_id = $password = $address = ""; 
        } else {
            $success = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>

    <style>
        .required:after {
            content: "*";
            color: red;
        }
        body{
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>User Registration Form</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>

                        <!-- Registration Form -->
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="name" class="required">Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo $name; ?>" required>
                                <?php if (!empty($name_error)) { echo "<div class='text-danger'>$name_error</div>"; } ?>
                            </div>

                            <div class="form-group">
                                <label for="mobile_number" class="required">Mobile Number</label>
                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" value="<?php echo $mobile_number; ?>" required>
                                <?php if (!empty($mobile_error)) { echo "<div class='text-danger'>$mobile_error</div>"; } ?>
                            </div>

                            <div class="form-group">
                                <label class="required">Gender</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="male" name="gender" value="male" <?php if ($gender == 'male') echo 'checked'; ?> required>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="female" name="gender" value="female" <?php if ($gender == 'female') echo 'checked'; ?> required>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gmail_id" class="required">Email ID</label>
                                <input type="email" id="gmail_id" name="gmail_id" class="form-control" value="<?php echo $gmail_id; ?>" required>
                                <?php if (!empty($email_error)) { echo "<div class='text-danger'>$email_error</div>"; } ?>
                            </div>

                            <div class="form-group">
                                <label for="password" class="required">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye" id="eye-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <?php if (!empty($password_error)) { echo "<div class='text-danger'>$password_error</div>"; } ?>
                            </div>

                            <div class="form-group">
                                <label for="address" class="required">Address</label>
                                <textarea id="address" name="address" class="form-control" required><?php echo $address; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="users_list.php" class="btn btn-link">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript for Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome for eye icon -->
</body>
</html>
