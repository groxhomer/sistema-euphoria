<?php
session_start();

// initializing variables
$usuario = "";
$email    = "";
$errors = array();

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'sistema_euphoria');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $usuario = mysqli_real_escape_string($db, $_POST['usuario']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $contraseña_1 = mysqli_real_escape_string($db, $_POST['contraseña_1']);
  $contraseña_2 = mysqli_real_escape_string($db, $_POST['contraseña_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($usuario)) { array_push($errors, "Ingrese un usuario"); }
  if (empty($email)) { array_push($errors, "Ingrese un correo electrónico"); }
  if (empty($contraseña_1)) { array_push($errors, "Ingrese una contraseña"); }
  if ($contraseña_1 != $contraseña_2) {
	array_push($errors, "Las contraseñas tienen que ser iguales");
  }

  // first check the database to make sure
  // a user does not already exist with the same usuario and/or email
  $user_check_query = "SELECT * FROM users WHERE usuario='$usuario' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
    if ($user['usuario'] === $usuario) {
      array_push($errors, "El usuario ya existe");
    }

    if ($user['email'] === $email) {
      array_push($errors, "El Email ya existe");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$contraseña = md5($contraseña_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO users (usuario, email, contraseña)
  			  VALUES('$usuario', '$email', '$contraseña')";
  	mysqli_query($db, $query);
  	$_SESSION['usuario'] = $usuario;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

if (isset($_POST['login_user'])) {
  $usuario = mysqli_real_escape_string($db, $_POST['usuario']);
  $contraseña = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($usuario)) {
  	array_push($errors, "Ingrese el usuario");
  }
  if (empty($contraseña)) {
  	array_push($errors, "Ingrese la contraseña");
  }

  if (count($errors) == 0) {
  	$contraseña = md5($contraseña);
  	$query = "SELECT * FROM users WHERE usuario='$usuario' AND contraseña='$contraseña'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['usuario'] = $usuario;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Combinacion usuario/contraseña incorrecta");
  	}
  }
}

?>
