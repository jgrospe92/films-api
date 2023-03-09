<?php

namespace Vanier\Api\models;

use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\BaseModel;

class ActorsModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }



    public function getAllActors(array $filters)
    {
        // Queries the DB and return the list of all films
        $query_values = [];

        $sql = "SELECT * FROM actor WHERE 1";

        if (isset($filters['first_name'])) {
            $sql .= " AND first_name LIKE CONCAT(:first_name,'%') ";
            $query_values[":first_name"] = $filters["first_name"];
        }

        if (isset($filters['last_name'])) {
            $sql .= " AND last_name LIKE CONCAT(:last_name,'%') ";
            $query_values[":last_name"] = $filters["last_name"];
        }

        if (isset($filters['sort_by'])) {

            if (!empty($filters['sort_by'])) {
                $keyword = explode(".", $filters['sort_by']);
                $column = $keyword[0] ?? "";
                $order_by = $keyword[1] ?? "";

                $sql .= " ORDER BY " .   $column . " " .  $order_by;
            }
        }

        return $this->paginate($sql, $query_values);
    }

    public function getFilmByActorId($actor_id, array $filters)
    {
         // Queries the DB and return the list of all films
         $query_values = [];
         $sql = "SELECt film.*, category.name as category from film INNER JOIN film_actor on film_actor.film_id = film.film_id" .
                " INNER JOIN actor ON actor.actor_id = film_actor.actor_id" .
                " INNER JOIN film_category ON film_category.film_id = film.film_id".
                " INNER JOIN category ON category.category_id = film_category.category_id";

        $sql .= " AND actor.actor_id =:id";
        $query_values['id'] = $actor_id;

        if (isset($filters['category']))
        {
            // Can only perform category
            $name = strtolower($filters['category']);
            // $categories = $this->getCategory($name);
            // return  $categories;
            $sql .= " AND category.name LIKE CONCAT(:name, '%')";
            $query_values["name"] =  $name;
        }

        $sql .= " GROUP BY film.film_id ";

        return  $this->paginate($sql, $query_values);
    }


    /**
     * createActors
     *
     * @param  mixed $actors
     * @return void
     */
    public function createActors(array $actors)
    {
        return $this->insert("actor", $actors);
    }

    /**
     * updateActor
     *
     * @param  mixed $actors
     * @return void
     */
    public function updateActor(array $actors)
    {
        $this->update("actor", $actors, ['film_id']);
    }
}
