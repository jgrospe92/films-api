<?php
namespace Vanier\Api\models;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\BaseModel;
use Vanier\Api\models\ActorsModel;
use Exception;
/**
 * Summary of FilmsModel
 */
class FilmsModel extends BaseModel
{

    private $table_name = "film";
  
    public function __construct(){
        parent::__construct();
    }


    public function createFilm(array $film)
    {
        return $this->insert("film", $film);
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return array
     * Sort_by title.asc or title.desc
     */
    public function getAll(array $filters)
    {
        $actor_model = new ActorsModel();

        // Queries the DB and return the list of all films
        $query_values = [];

        $sql = "SELECT film.*, category.name as category, language.name as language from film" .
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
            $sql .= " AND rating LIKE CONCAT(:rating, '%')";
            $query_values["rating"] = $filters["rating"];
        }

        if (isset($filters['special_features']))
        {
            $sql .= " AND special_features LIKE CONCAT(:special_features ,'%') ";
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

            if (!empty($filters['sort_by']))
            {
                $keyword = explode(".", $filters['sort_by']);
                $column = $keyword[0] ?? "";
                $order_by = $keyword[1] ?? "";
                $sql .= " ORDER BY " .   $column . " " .  $order_by;
            }
        }
        if (isset($filters['category']))
        {
            // Can only perform category
            $name = strtolower($filters['category']);
            $sql .= " AND category.name LIKE CONCAT(:name, '%')";
            $query_values["name"] =  $name;
        }

        if (isset($filters['language'])){
            $name = ucfirst($filters['language']);
            $sql .= " AND language.name LIKE CONCAT(:lang, '%') ";
            $query_values["lang"] = $name;
        }

        // if sort_by doesn't exists then append GROUP BY AT THE END
        if (!isset($filters["sort_by"])){
            $sql .= " GROUP BY film.film_id ";
        }
        $films = $this->paginate($sql, $query_values);
    
        
        foreach ($films['data'] as $key=>$value){
            $actor = $actor_model->get($value['film_id']);
            if ($actor){
                $value['actor'] = ["first_name"=>$actor['first_name'], "last_name"=>$actor['last_name']];
            } else {
                $value['actor'] = ["first_name"=>'', "last_name"=>''];
            }  
            $films['data'][$key] = $value;
        }

        return $films;

    }

    /**
     * Summary of updateFilms
     * @param array $data
     * @return void
     */
    public function updateFilms(array $data)
    {
        $film['title'] = $data['title'];
        $film['description'] = $data['description'] ?? '';
        $film['release_year'] = $data['release_year'] ?? '';
        $film['language_id'] = $data['language_id'];
        $film['original_language_id'] = $data['original_language_id'] ?? null;
        $film['rental_duration'] = $data['rental_duration'] ?? 3;
        $film['rental_rate'] = $data['rental_rate'] ?? 4.99;
        $film['length'] = $data['length'] ?? null;
        $film['replacement_cost'] = $data['replacement_cost'] ?? 19.99;
        $film['rating'] = $data['rating'] ?? 'G';
        $film['special_features'] = $data['special_features'] ?? '';
        
    
        $this->update('film',$film,["film_id"=>$data['film_id']] );
    }

    /**
     * Summary of deleteFilms
     * @param mixed $film_id
     * @return void
     */
    public function deleteFilms($film_id)
    {
        $this->delete('film',['film_id'=>$film_id]);
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