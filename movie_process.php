<?php

    
    require_once("globals.php");
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("models/Messages.php");
    require_once("dao/UserDAO.php");
    require_once("dao/MovieDAO.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDao($conn, $BASE_URL);
    
    $userData = $userDao->verifyToken();

    // Resgata o tipo do form
    $type = filter_input(INPUT_POST, "type");


    if($type === "create"){

        //Receber dados do filme

        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");

        $movie = new Movie();


        //Validacao minima de dados
        if(!empty($title) && !empty($description) && !empty($category)){

            $movie->title = $title;
            $movie->description = $description;
            $movie->category = $category;
            $movie->trailer = $trailer;
            $movie->lenght = $length;

            

            //UPload de imagem

            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

                $image = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                $jpgArray = ["image/jpeg", "image/jpg"];

                //CHECANDO TIPO IMAGEM

                if(in_array($image["type"], $imageTypes)){

                    //CHECA SE É JPEG
                    if(in_array($image["type"], $jpgArray)){
                        $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    } else {
                        $imageFile = imagecreatefrompng($image["tmp_name"]);
                    }

                    $imageName = $movie->imageGenerateName();

                    imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                    $movie->image = $imageName;


                } else{

                    $message->setMessage("Tipo inválido !", "error", "back");

                }


            } 

            print_r($_POST); 
            print_r($_FILES); 
            exit;
            //$movieDao->create($movie);

        } else {
            $message->setMessage("Você precisa adicionar o titulo, descrição e categoria","error", "back");
        }


    
    } else {
    
        $message->setMessage("Filme invalido","error", "index.php");
    }

?>