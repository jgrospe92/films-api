<?php
namespace Vanier\Api\models;
use Vanier\Api\Models\BaseModel;
use Psr\Http\Message\ServerRequestInterface as Request;

class CustomersModel extends BaseModel
{
    private string $table_name = 'customer';

    public function __construct(){
        parent::__construct();
    }

    public function getAllCustomers(array $filters = [], Request $request)
    {
        // Queries the DB and return the list of all films
        $query_values = [];

        // Query select statement
        $sql = "SELECT customer.*, address.*, city.*, country.* FROM `customer` inner join address on customer.address_id = address.address_id" .
        " INNER JOIN city on city.city_id = address.address_id INNER JOIN country on city.country_id = country.country_id WHERE 1 ";

        if (isset($filters['first_name']))
        {
            $sql .= " AND first_name LIKE CONCAT(:first_name,'%') ";
            $query_values[":first_name"] = $filters["first_name"];
        }

        if (isset($filters['last_name']))
        {
            $sql .= " AND last_name LIKE CONCAT(:last_name,'%') ";
            $query_values[":last_name"] = $filters["last_name"];
        }

        if (isset($filters['city']))
        {
            $sql .= " AND city LIKE CONCAT(:city,'%') ";
            $query_values[":city"] = $filters["city"];
        }

        if (isset($filters['country']))
        {
            $sql .= " AND country LIKE CONCAT(:country,'%') ";
            $query_values[":country"] = $filters["country"];
        }

        if (isset($filters['sort_by']))
        {
            $sql .= " GROUP BY customer.customer_id ";

            if (!empty($filters['sort_by']))
            {
                $keyword = explode(".", $filters['sort_by']);
                $column = $keyword[0] ?? "";
                $order_by = $keyword[1] ?? "";

                $sql .= " ORDER BY " .   $column . " " .  $order_by;

            }
        }

        // if sort_by doesn't exists then append GROUP BY AT THE END 
        if (!isset($filters["sort_by"])){
            $sql .= " GROUP BY customer.customer_id ";
        }
        return $this->paginate($sql, $query_values);

    }


    /**
     * Summary of getFilmById
     * @param mixed $customer_id
     * @return mixed
     * return customer by id 
     */
    public function getFilmById($customer_id, array $filters)
    {
        // Queries the DB and return the list of all films
        $query_values = [];
        
        $sql = "SELECT film.* from customer INNER JOIN rental on customer.customer_id = rental.customer_id" .
        " INNER JOIN inventory on inventory.inventory_id = rental.inventory_id" .
        " INNER JOIN film on film.film_id = inventory.film_id WHERE 1";

        $sql .= " AND customer.customer_id LIKE :id ";
        $query_values['id'] = $customer_id;

    

        $sql .= " GROUP BY film.film_id ";
        return  $this->paginate($sql, $query_values);
 

    }

}

?>