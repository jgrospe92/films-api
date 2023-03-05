<?php
namespace Vanier\Api\models;

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
        $sql = "SELECT * FROM $this->table_name WHERE 1 ";

        if (isset($filters["title"]))
        {
            // this check if it contains
            //$sql .= " AND title LIKE CONCAT('%',:title,'%') ";
            // this check if it starts
            $sql .= " AND title LIKE CONCAT(:title,'%') ";
            $query_values[":title"] = $filters["title"];
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
         
            $sql .= " AMD inner join film_category on film.film_id = film_category.film_id INNER join category on film_category.category_id = category.category_id where category.name =:name";
            $query_values[":name"] = $filters["category"];
        }

        //return $this->run($sql, $query_values)->fetchAll();
        return $this->paginate($sql, $query_values);

        /*
        Get category
        SELECT name from category inner join film_category on 
        category.category_id = film_category.category_id inner JOIN film on film.film_id = film_category.film_id WHERE film.film_id = 1
        */

        /*
        SELECT * from film inner join film_category on film.film_id 
        = film_category.film_id INNER join category on film_category.category_id = category.category_id where category.name = "horror" 


        */
    }

    public function getFilmById($film_id)
    {
             $sql = "SELECT * FROM $this->table_name WHERE film_id=:film_id";
             return  $this->run($sql, ["film_id"=>$film_id])->fetchAll();
    }

 
}

?>