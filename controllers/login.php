<?php

include 'config/db.php';

if ($_SERVER['REQUEST_METHOD']==='POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        echo "Inicio de sesión exitoso";
    }
    else{
        echo "Correo o contraseña incorrectos";
    }
}
