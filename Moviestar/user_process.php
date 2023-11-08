<?php

    require_once("globals.php");
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Messages.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);

    //RESGATA O TIPO DO FORMULARIO
    $type = filter_input(INPUT_POST, "type");


    //ATUALIZANDO O USUARIO
    if($type === "update"){

        // Resgata dados dos usuarios
        $userData = $userDao->verifyToken();
        
        // Receber dados do POST
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        // Criar novo objeto de user

        $user = new User();

        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;
        

        // Upload de Img
        if(isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])){
            
            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpeg", "image/JPEG", "image/jpg", "image/JPG"];
            //CHECANDO DE TIPO DE IMAGEM

            if(in_array($image['type'], $imageTypes)){

                //Checar se é jpeg
                if(in_array($image['type'], $jpgArray)){

                    $imageFile = imagecreatefromjpeg($image['tmp_name']);
                    
                //IMAGEM PNG
                } else {
                    $imageFile = imagecreatefrompng($image['tmp_name']);
                }

                $imageName = $user->imageGenerateName();
                imagejpeg($imageFile, "./img/users/" . $imageName, 100);
                $userData->image = $imageName;


            }else {
                $message->setMessage("Tipo inválido !", "error", "back");
            }
        } 


        $userDao->update($userData);


     
    
    // Atulizar senha do usuario
    } else if($type === "changepassword"){
        
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
        
        $userData = $userDao->verifyToken();
        $id = $userData->id;


        if ($password === $confirmpassword){

            $user = new User();

            $finalPassword = $user->generatePassword($password);

            $user->password = $finalPassword;

            $user->id = $id;

            $userDao->changePassword($user);


        }else{
            $message->setMessage("As senhas não batem !", "error", "back");
        }


    } else {

        $message->setMessage("Informações inválidas !", "error", "index.php");
    }




     
    
?>