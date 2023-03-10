<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\FilmsModel;
use Vanier\Api\exceptions\HttpNotAcceptableException;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\exceptions\HttpConflict;
use Vanier\Api\Validation\ValidateHelper;


/**
 * Summary of FilmsController
 * Support operations such as getAllFilms
 */
class FilmsController
{

   private $film_model = null;

   // Empty constructor
   public function __construct()
   {
      $this->film_model = new FilmsModel();
   }

   public function handleCreateFilms(Request $request, Response $response, array $uri_args)
   {
      // 1. ) to retrieve the data from the request
      $data = $request->getParsedBody();

      //ValidateHelper::testIn(); exit;
      
      // 2. ) validate
      // check if the body is empty
      if (!isset($data)) {
         throw new HttpConflict($request);
      }
      // validate the body
      foreach ($data as $film) {
         if (!ValidateHelper::validatePostFilm($film)) {
            throw new HttpConflict($request);
         } else {
            if (isset($film['special_features'])){
               $sf = array_map('ucwords',$film['special_features']);
               $special_features = implode(',',$sf);
               $film['special_features'] = $special_features;
            }
            //$this->film_model->createFilm($film);
         }
      }

      // return with the right status code
      // json
      $json_data = json_encode($data);
      // return the response;
      $response->getBody()->write($json_data);
      return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader("Content-type", "application/json");
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
            elseif(empty($value)) {
               throw new HttpUnprocessableContent($request, "Please provide query value for : " . "{".$key."}");
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
           throw new HttpBadRequest($request, "expected numeric but received alpha");
        }

      $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];

      if (!ValidateHelper::validatePagingParams($dataParams))
      {
         throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual"); 
      }

      $this->film_model->setPaginationOptions($page, $pageSize);
      
      // catch any DB exceptions
      try 
      {
         $data = $this->film_model->getAll($filters);
      }
      catch (Exception $e)
      {
         throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
      }

      if (!$data['data']){
         throw new HttpNotFound($request, "please check your query parameter or consult the documentation");
      }
      // json
      $json_data = json_encode($data);
      // return the response;
      $response->getBody()->write($json_data);

      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
   }


   /**
    * Summary of handleGetFilmById
    * @param Request $request
    * @param Response $response
    * @param array $uri_args
    * @throws HttpUnprocessableContent
    * @return Response
    */
   public function handleGetFilmById(Request $request, Response $response, array $uri_args)
   {

      $film_id = $uri_args['film_id'];
      $dataParams = ['id'=>$film_id, "min"=>1,"max"=>1500];
      if (!ValidateHelper::validateInputId($dataParams)){
         $msg = is_numeric($film_id) ? "The provided ID : " . "{". $film_id . "} is out of range" : "Invalid input: " . "{". $film_id . "}, expecting a number ";
         throw new HttpUnprocessableContent($request, $msg);
      }

      $data =  $this->film_model->getFilmById($film_id);
      if (!$data){
         throw new HttpNotFound($request);
      }
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
      $params = ['language', 'category', 'title', 'description', 'special_features', 'rating', 'sort_by', 'pageSize', 'page',];

      if (in_array($param, $params)){
         return true;
      }
      return false;
   }


}




?>