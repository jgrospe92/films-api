<?php

namespace Vanier\Api\controllers;
// imports
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\FilmsModel;
use Vanier\Api\exceptions\HttpNotAcceptableException;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;


/**
 * Summary of FilmsController
 * Support operations such as getAllFilms, 
 */
class FilmsController
{

   private $film_model = null;

   // Empty constructor
   public function __construct()
   {
      $this->film_model = new FilmsModel();
   }
   // Callback - need to return response
   public function handleGetAllFilms(Request $request, Response $response)
   {
      // constant values
      define('DEFAULT_PAGE', 1);
      define("DEFAULT_PAGE_SIZE", 10);

      // filter by title 
      $filters = $request->getQueryParams();
      
      if ($filters){
         foreach($filters as $key => $value){
       
            if(!$this->validateParams($key)){
               throw new HttpUnprocessableContent($request, "Invalid query parameter : " . "{".$key."}");
            }
         }
      }
      // verify if client added a page and pageSize params
      // if client didn't add a page and pageSize params, paginate using the default values
      $page = $filters["page"] ?? DEFAULT_PAGE;
      $pageSize = $filters["pageSize"] ?? DEFAULT_PAGE_SIZE;
     
      // check if the params is numeric, if not throw a bad request error
      if (!is_numeric($page) || !is_numeric($pageSize))
      {
         throw new HttpBadRequest($request);
      }

      $this->film_model->setPaginationOptions($page, $pageSize);
      
      $data = $this->film_model->getAll($filters);

      if (!$data['data']){
         throw new HttpUnprocessableContent($request, "Unable to process your request, please check you query parameter");
      }
      // json
      $json_data = json_encode($data);
      // return the response;
      $response->getBody()->write($json_data);

      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
   }


   public function handleGetFilmById(Request $request, Response $response, array $uri_args)
   {

      $film_id = $uri_args['film_id'];
      if (!$this->validateInputId($film_id)){
         $msg = is_numeric($film_id) ? "The provided ID : " . "{". $film_id . "} is out of range" : "Invalid input: " . "{". $film_id . "}, expecting a number ";
         throw new HttpUnprocessableContent($request, $msg);
      }

      $data =  $this->film_model->getFilmById($film_id);
      $json_data = json_encode($data);
      $response->getBody()->write($json_data);

      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
   }

   /**
    * Summary of validateParams
    * @param mixed $param
    * @return bool
    * return true if the given parameter is supported otherwise false 
    */
   function validateParams($param) : bool {
      $params = ['language', 'category', 'title', 'description', 'special_features', 'rating', 'sort_by', 'pageSize', 'page'];

      if (in_array($param, $params)){
         return true;
      }
      return false;
   }

   private function validateInputId($id)
   {
      return filter_var($id, FILTER_VALIDATE_INT, ['options'=> ['min_range' =>1, 'max_range'=>1000]]);
   }

}




?>