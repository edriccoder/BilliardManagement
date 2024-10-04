<?php
include 'conn.php'; 
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: /index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = htmlspecialchars($_SESSION['user_id']);
$query = "SELECT email, password FROM users WHERE username = :username";

$stmt = $conn->prepare($query);

// Bind the parameter using PDO's bindParam or bindValue
$stmt->bindParam(':username', $username);

// Execute the statement
$stmt->execute();

// Fetch the result as an associative array
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Sanitize the output
    $email = htmlspecialchars($user['email']);
    $password = htmlspecialchars($user['password']);
} else {
    // No result found, set defaults or handle the case
    $email = '';
    $password = '';
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Profile</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>


    <div class="d-flex justify-content-center align-items-center vh-100">
        
        <form class="shadow w-450 p-3" 
              action="php/edit.php" 
              method="post"
              enctype="multipart/form-data">

            <h4 class="display-4  fs-1">Edit Profile</h4><br>
     
        
          <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" 
                   class="form-control"
                   name="fname"
                   value="<?php echo htmlspecialchars($username); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" 
                   class="form-control"
                   name="uname"
                   value="<?php echo htmlspecialchars($email); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" 
                   class="form-control"
                   name="uname"
                   value="<?php echo htmlspecialchars($password); ?>">
          </div>
          

          <button type="submit" class="btn btn-primary">Update</button>
  
        </form>
    </div>

</body>
</html>
