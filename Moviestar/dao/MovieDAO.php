<?php


    require_once("models/Movie.php");
    require_once("models/Messages.php");
    require_once("dao/ReviewDAO.php");



    //Review DAO


    class MovieDAO implements MovieDAOInterface{
        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        
        public function buildMovie($data){
            $movie = new Movie();

            $movie->id = $data["id"];
            $movie->title = $data["title"];
            $movie->description = $data["description"];
            $movie->image = $data["image"];
            $movie->trailer = $data["trailer"];
            $movie->category = $data["category"];
            $movie->length = $data["length"];
            $movie->users_id = $data["users_id"];

            // RECEBE AS RATINGS
            $reviewDao = new ReviewDao($this->conn, $this->url);

            $rating = $reviewDao->getRatings($movie->id);

            $movie->rating = $rating;

            return $movie;
        }
        public function findAll(){}
        public function getLatestMovies(){
            $movies = [];

            $stmt = $this->conn->query("SELECT * FROM movies ORDER BY id desc");

            $stmt->execute();

            if($stmt->rowCount() > 0 ){

                $moviesArray = $stmt->fetchAll();
                foreach($moviesArray as $movie){

                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function getMoviesByCategory($category){
            $movies = [];

            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE category = :category ORDER BY id desc");

            $stmt->bindParam(":category", $category);

            $stmt->execute();

            if($stmt->rowCount() > 0 ){

                $moviesArray = $stmt->fetchAll();
                foreach($moviesArray as $movie){

                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function getMoviesByUserId($id){
            $movies = [];

            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE users_id = :users_id");

            $stmt->bindParam(":users_id", $id);

            $stmt->execute();

            if($stmt->rowCount() > 0 ){

                $moviesArray = $stmt->fetchAll();
                foreach($moviesArray as $movie){

                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function findbyId($id){

            $movie = [];

            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE id = :id");

            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if($stmt->rowCount() > 0){

                $movieData = $stmt->fetch();

                $movie = $this->buildMovie($movieData);

                return $movie;

            } else {

                return false;
            
            }



        }
        public function findByTitle($title){
            $movies = [];

            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE title LIKE :title");

            $stmt->bindValue(":title",'%'.$title.'%');

            $stmt->execute();

            if($stmt->rowCount() > 0 ){

                $moviesArray = $stmt->fetchAll();
                foreach($moviesArray as $movie){

                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }
        public function create(Movie $movie){
            $stmt = $this->conn->prepare("INSERT INTO movies (title, description, image, trailer, category, length, users_id) 
            VALUES (:title, :description, :image, :trailer, :category, :length, :users_id)");

            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":image", $movie->image);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":users_id", $movie->users_id);

            $stmt->execute();

            $this->message->setMessage("Filme adicionado com sucesso.", "sucess", "index.php");
        }
        public function update(Movie $movie){

            $stmt = $this->conn->prepare("UPDATE movies SET title = :title, description = :description, image = :image, category = :category, trailer = :trailer, length = :length WHERE id = :id");

            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":image", $movie->image);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":id", $movie->id);

            $stmt->execute();

            $this->message->setMessage("O filme foi editado com sucesso", "sucess", "dashboard.php");
        }
        public function destroy($id){

            $stmt = $this->conn->prepare("DELETE FROM movies WHERE id = :id");

            $stmt->bindParam("id", $id);

            $stmt->execute();

            $this->message->setMessage("Filme removido com sucesso.", "sucess", "dashboard.php");

        }
    }

?>