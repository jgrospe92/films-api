<?php
namespace Vanier\Api\models;

use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\Models\BaseModel;
/**
 * Summary of FilmsModel
 */
class FilmsModel extends BaseModel
{

    private $table_name = "film";
  
    public function __construct(){
        parent::__construct();
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return array
     * Sort_by title.asc or title.desc
     */
    public function getAll(array $filters = [])
    {
        // Queries the DB and return the list of all films
        $query_values = [];
        // WHERE 1 allows us to concatenate any filtering command
        //$sql = "SELECT * FROM $this->table_name WHERE 1 ";

        $sql = "SELECT film.*, actor.first_name, actor.last_name from film" .
            " inner join film_actor on film.film_id = film_actor.film_id inner join actor on actor.actor_id = film_actor.actor_id  WHERE 1 ";

        if (isset($filters["title"]))
        {
            // this check if it contains
            //$sql .= " AND title LIKE CONCAT('%',:title,'%') ";
            // this check if it starts
            $sql .= " AND title LIKE CONCAT(:title,'%') ";
            $query_values[":title"] = $filters["title"];
        }

        if (isset($filters["rating"]))
        {
            $sql .= " AND rating =:rating ";
            $query_values[":rating"] = $filters["rating"];
        }


        if (isset($filters["description"]))
        {
            $sql .= " AND description LIKE :description";
            $query_values[":description"] = $filters["description"]."%";
        }

        if (isset($filters["sort_by"])){
            if($filters["sort_by"] == "title.asc"){
                $sql .= " ORDER BY title asc ";
              
            } elseif($filters["sort_by"] == "title.desc")
            {
                $sql .= " ORDER BY title desc ";
            }
        }

        if (isset($filters['category']))
        {
            // Can only perform category
            $name = strtolower($filters['category']);
            $categories = $this->getCategory($name);

            return  $categories;
        }

        if (isset($filters['language'])){
            $languages = $this->getLanguage($filters['language']);
            return $languages;
        }
    
        $sql .= " GROUP BY 1";
        return $this->paginate($sql, $query_values);

        /*
        SELECT * from film inner join film_category on film.film_id 
        = film_category.film_id INNER join category on film_category.category_id = category.category_id where category.name = "horror" 
        */
    }

    /**
     * Summary of getCategoryID
     * @param mixed $name
     * @return array
     * Return film based on the category name
     */
    private function getCategory($name)
    {
        $sql = "SELECT * from film inner join film_category on film.film_id = film_category.film_id " .
        "INNER join category on film_category.category_id = category.category_id where category.name =:name ";
        //$STMT = $this->getPdo()->prepare($sql);
        //$STMT->execute(['name'=>$name]);
        return $this->paginate($sql, ['name'=>$name]);
        //return $STMT->fetchAll();
    }

   
    /**
     * Summary of getLanguage
     * @param mixed $language
     * @return array
     * fetch film(s) based on the language given
     */
    private function getLanguage($language){
        $lang = ucfirst($language);
        $sql = "SELECT * from film inner join language WHERE language.name =:name";
        return $this->paginate($sql, ['name'=>$lang]);
    }

    /**
     * Summary of getFilmById
     * @param mixed $film_id
     * @return mixed
     * return film by id 
     */
    public function getFilmById($film_id)
    {
             $sql = "SELECT * FROM $this->table_name WHERE film_id=:film_id";
             return  $this->run($sql, ["film_id"=>$film_id])->fetchAll();
    }

 
}

?>