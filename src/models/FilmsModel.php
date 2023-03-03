<?php
namespace Vanier\Api\models;

use Vanier\Api\Models\BaseModel;
class FilmsModel extends BaseModel
{

    private $table_name = "film";
  
    public function __construct(){
        parent::__construct();
    }

    public function getAll(array $filters = [])
    {
        // Queries the DB and return the list of all filmms
        $query_values = [];
        $sql = "SELECT * FROM $this->table_name WHERE 1 ";

        if (isset($filters["title"]))
        {
            $sql .= " AND title LIKE CONCAT('%',:title,'%') ";
            $query_values[":title"] = $filters["title"];
        }

        if (isset($filters["description"]))
        {
            $sql .= " AND description LIKE :description";
            $query_values[":description"] = $filters["description"]."%";
        }

        return $this->run($sql, $query_values)->fetchAll();
    }
}