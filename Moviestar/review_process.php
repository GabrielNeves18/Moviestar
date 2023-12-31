<?php   
    require_once("globals.php");
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("models/Messages.php");
    require_once("models/Review.php");
    require_once("dao/UserDAO.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/ReviewDAO.php");
    

    $message = new Message($BASE_URL);
    $userDao = new UserDao($conn, $BASE_URL);
    $movieDao = new MovieDao($conn, $BASE_URL);
    $reviewDao = new ReviewDao($conn, $BASE_URL);

    //RECEBENDO TIPO DO FORMULARIO
    $type = filter_input(INPUT_POST, "type");
    $userData = $userDao->verifyToken();

    if($type === "create"){

        //Recebendo dados dos post
        $rating = filter_input(INPUT_POST, "rating");
        $review = filter_input(INPUT_POST, "review");
        $movies_id = filter_input(INPUT_POST, "movies_id");
        $users_id = $userData->id;

        $reviewObject = new Review();

        $movieData = $movieDao->findbyId($movies_id);
        //VALIDANDO SE O FILME EXISTE

        if($movieData){

            //VERIFICAR DADOS MINIMOS
            if(!empty($rating)  && !empty($review) && !empty($movies_id)){

                $reviewObject->rating = $rating;
                $reviewObject->review = $review;
                $reviewObject->movies_id = $movies_id;
                $reviewObject->users_id = $users_id;

                $reviewDao->create($reviewObject);
                

            } else {
                $message->setMessage("Você precisa inserir a nota e o comentário!", "error", "back");
            }

        }else {
            $message->setMessage("Filme não existe!", "error", "index.php");
        }
    } else {

        $message->setMessage("Informações Inválidas!", "error", "index.php");

    }
?>