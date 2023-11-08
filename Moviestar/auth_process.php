<?php

    require_once("models/User.php");
    require_once("models/Messages.php");
    require_once("dao/UserDAO.php");
    require_once("globals.php");
    require_once("db.php");


    $message = new Message($BASE_URL);
    $userDAO = new UserDAO($conn, $BASE_URL);

    //RESGATA TIPO DE FORMULARIO

    $type = filter_input(INPUT_POST, "type");




    //VERIFICA O TIPO DO FORMULARIO

    if($type === "register"){
        //REGISTRANDO DADOS

        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");


        //VERIFICAÇAO DE DADOS MINIMOS

        if($name && $lastname  && $email && $password){




            // VERIFICAR SE AS SENHAS BATEM
            if($password === $confirmpassword){

                //VERIFICAR SE EMAILS BATEM
                if($userDAO->findByEmail($email) === false){

                    $user = new User();


                    //Criação de senha e token

                    $userToken = $user->generateToken();
                    $finalPassword = $user->generatePassword($password);

                    $user->name = $name;
                    $user->lastname = $lastname;
                    $user->email = $email;
                    $user->password = $finalPassword;
                    $user->token = $userToken;


                    $auth = true;

                    $userDAO->create($user, $auth);


                } else{

                    //USUARIO JÁ EXISTE
                    $message->setMessage("Usuario já cadastrado, tente outro email.", "error", "back");
                }


            } else{
                $message->setMessage("As senhas não batem.", "error", "back");
            }

        } else{

            // ENVIAR MENSAGEM DE ERRO DADOS FALTANDO

            $message->setMessage("Por favor, preencha todos os campos.", "error", "back");
        }



    } else if($type === "login"){

        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        // TENTA AUTENTICAR USUARIO

        if($userDAO->authenticateUser($email, $password)){

            $message->setMessage("Seja bem vindo.", "sucess", "editprofile.php");

            //REDIRECIONA CASO O NÃO CONSEGUIR AUTENTICAR
        }else{

            $message->setMessage("Usuario e/ou senha incorretos.", "error", "back");
        }

    } else {
        $message->setMessage("Informações invalidas.", "error", "index.php");
    }

?>