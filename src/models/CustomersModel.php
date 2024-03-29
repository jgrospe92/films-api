<?php
namespace Vanier\Api\models;

use PhpOption\None;
use Vanier\Api\Models\BaseModel;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Summary of CustomersModel
 */
class CustomersModel extends BaseModel
{
    private string $table_name = 'customer';

    /**
     * Summary of __construct
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Summary of get
     * @param int $id
     * @return mixed
     */
    private function get(int $id){
        $sql = "SELECT * FROM customer WHERE customer_id =:customer_id";
        return $this->run($sql, ['customer_id'=>$id])->fetch();
    }

    /**
     * Summary of getAllCustomers
     * @param array $filters
     * @param Request $request
     * @return array
     */
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
        $customer = $this->get($customer_id);
        
        $sql = "SELECT film.*, category.name, rental.rental_date from customer INNER JOIN rental on customer.customer_id = rental.customer_id" .
        " INNER JOIN inventory on inventory.inventory_id = rental.inventory_id" .
        " INNER JOIN film on film.film_id = inventory.film_id " .
        " INNER JOIN film_category on film_category.film_id = film.film_id INNER JOIN category on film_category.category_id = category.category_id" .
        " WHERE 1";

        $sql .= " AND customer.customer_id =:id ";
        $query_values['id'] = $customer_id;

        if (isset($filters["rating"]))
        {
            $sql .= " AND rating LIKE CONCAT(:rating, '%')";
            $query_values[":rating"] = $filters["rating"];
        }

        if (isset($filters['special_features']))
        {
            $sql .= " AND special_features LIKE CONCAT(:special_features ,'%') ";
            $query_values[":special_features"] = $filters["special_features"];
        }

        if (isset($filters['category']))
        {
            // Can only perform category
            $name = strtolower($filters['category']);
            // $categories = $this->getCategory($name);
            // return  $categories;
            $sql .= " AND category.name LIKE CONCAT(:name, '%')";
            $query_values["name"] =  $name;
        }
        if (isset($filters['from_rentalDate']) && isset($filters['to_rentalDate']))
        {
            $sql .= " AND DATE(rental.rental_date) BETWEEN :from_rentalDate AND :to_rentalDate ";
            $query_values['from_rentalDate'] = $filters['from_rentalDate'];
            $query_values['to_rentalDate'] = $filters['to_rentalDate'];
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
        
          // if sort_by doesn't exists then append GROUP BY AT THE END 
        if (!isset($filters["sort_by"])){
            $sql .= " GROUP BY film.film_id ";
        }
        $films =  $this->paginate($sql, $query_values);
        return   array("customer"=>$customer, "films"=>$films);
 

    }

    /**
     * Summary of updateCustomer
     * @param array $data
     * @return void
     * Update customer
     */
    public function updateCustomer(array $data)
    {
        $customer['store_id'] = $data['store_id'] ?? '';
        $customer['first_name'] = $data['first_name'] ?? '';
        $customer['last_name'] = $data['last_name'] ?? '';
        $customer['email'] = $data['email'] ?? '';
        $customer['address_id'] = $data['address_id'] ?? null;
        $customer['active'] = $data['active'] ?? '';

        $this->update('customer',$customer,["customer_id"=>$data['customer_id']] );
    }

    /**
     * Summary of deleteCustomer
     * @param mixed $customer_id
     * @return mixed
     */
    public function deleteCustomer($customer_id)
    {
       return $this->delete('customer',['customer_id'=>$customer_id]);
    }

}

?>