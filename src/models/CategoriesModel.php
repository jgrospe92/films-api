<?php

namespace Vanier\Api\models;
use Vanier\Api\Models\BaseModel;

/**
 * Summary of ActorsModel
 */
class CategoriesModel extends BaseModel
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Summary of get
     * @param mixed $category_id
     * @return mixed
     */
    public function get($category_id)
    {
        $sql = "SELECT * from category WHERE category_id =:id";
        return $this->run($sql, ['id'=>$category_id])->fetch();
    }

    /**
     * Summary of getAllFilmsByCategory
     * @param mixed $category_id
     * @param mixed $filters
     * @return array
     */
    public function getAllFilmsByCategory($category_id, $filters){
        // get category by id
        $category = $this->get($category_id);

         // Queries the DB and return the list of all films
         $query_values = [];
         $sql = "SELECT film.* from film INNER JOIN film_category on film_category.film_id = film.film_id" .
         " INNER JOIN category on film_category.category_id = category.category_id WHERE 1";
        
        $sql .= " AND category.category_id =:id";
        $query_values['id'] = $category_id;

        if (isset($filters["rating"]))
        {
            $sql .= " AND rating LIKE CONCAT(:rating, '%')";
            $query_values[":rating"] = $filters["rating"];
        }

        if (isset($filters['film_length'])){
            $sql .= " AND length >= :length";
            $query_values[":length"] = $filters["film_length"];
        }

        
        if (isset($filters['sort_by']))
        {
            $sql .= " GROUP BY film.film_id ";

            if (!empty($filters['sort_by']))
            {
                $keyword = explode(".", $filters['sort_by']);
                $column = $keyword[0] ?? "";
                $order_by = $keyword[1] ?? "";

                $sql .= " ORDER BY " .   $column . " " .  $order_by;

            }
        }

        if (!isset($filters["sort_by"])){
            $sql .= " GROUP BY film.film_id ";
        }

        $films = $this->paginate($sql, $query_values);
        return array("category"=>$category, "films"=>$films);

    }
   
}
