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


        $sql .= " ORDER BY customer.customer_id ";

        return $this->paginate($sql, $query_values);

    }


    /**
     * Summary of getFilmById
     * @param mixed $customer_id
     * @return mixed
     * return customer by id 
     */
    public function getFilmById($customer_id)
    {
             $sql = "SELECT * FROM $this->table_name WHERE customer_id=:customer_id";
             return  $this->run($sql, ["customer_id"=>$customer_id])->fetchAll();
    }

}

?>