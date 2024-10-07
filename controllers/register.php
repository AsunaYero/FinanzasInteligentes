<?php

include ('../config/db.php');

if (isset($_POST['register'])) {
    if(
        strlen($_POST['firstname']) >= 1 &&
        strlen($_POST['lastname']) >=  1 &&
        strlen($_POST['email']) >=  1 &&
        strlen($_POST['password']) >= 1 
        ){
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $consult = "INSERT INTO usuarios (firstname, lastname, email, password) 
                VALUES ('$firstname', '$lastname', '$email', '$password')";
            $result = mysqli_query($conn, $consult);
            
            if($result){
                echo "Inicio de sesión exitoso";
            }
            else{
                echo "El registro no fue exitoso";
            }
        }else{
            echo "Completa todos los campos";
        }
}