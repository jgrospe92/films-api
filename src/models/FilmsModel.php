<?php
namespace Vanier\Api\models;
use Psr\Http\Message\ServerRequestInterface as Request;

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
    public function getAll(array $filters = [], Request $request)
    {
        // Queries the DB and return the list of all films
        $query_values = [];
        // WHERE 1 allows us to concatenate any filtering command
        //$sql = "SELECT * FROM $this->table_name WHERE 1 ";

        $sql = "SELECT film.*, actor.first_name, actor.last_name, category.name as category, language.name as language from film" .
            " inner join film_actor on film.film_id = film_actor.film_id inner join actor on actor.actor_id = film_actor.actor_id" .
            " inner join film_category on film.film_id = film_category.film_id" .
            " inner join category on film_category.category_id = category.category_id inner join language on language.language_id = film.language_id WHERE 1 ";

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

        if (isset($filters['special_features']))
        {
            $sql .= " AND special_features LIKE CONCAT('%',:special_features ,'%') ";
            $query_values[":special_features"] = $filters["special_features"];
        }


        if (isset($filters["description"]))
        {
            $sql .= " AND description LIKE :description";
            $query_values[":description"] = $filters["description"]."%";
        }
        // only sort_by title is supported
        if (isset($filters["sort_by"])){
            // Append GROUP BY before ORDER BY
            $sql .= " GROUP BY film.film_id ";
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
            // $categories = $this->getCategory($name);
            // return  $categories;
            $sql .= " AND category.name =:name";
            $query_values["name"] =  $name;
        }

        if (isset($filters['language'])){          
            $name = ucfirst($filters['language']);
            $sql .= " AND language.name =:lang";
            $query_values["lang"] = $name;
        }

        // if sort_by doesn't exists then append GROUP BY AT THE END 
        if (!isset($filters["sort_by"])){
            $sql .= " GROUP BY film.film_id ";
        }
    
    
        return $this->paginate($sql, $query_values);

    }

    /**
     * Summary of getCategoryID
     * @param mixed $name
     * @return array
     * Return film based on the category name
     * depreciated 
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

    /*
    Reference : Run this on myPhpAdmin
    SELECT film.*, actor.first_name, actor.last_name, language.name as Lang from film 
    inner join film_actor on film.film_id = film_actor.film_id inner join actor on actor.actor_id = film_actor.actor_id 
    inner join film_category on film.film_id = film_category.film_id inner join category on film_category.category_id = category.category_id 
    inner JOIN language on language.language_id = film.language_id WHERE language.name = 'English' GROUP BY film.film_id;
    */

 
}

?>