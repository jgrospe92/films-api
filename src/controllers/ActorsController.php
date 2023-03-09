<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\ActorsModel;
use Vanier\Api\exceptions\HttpNotAcceptableException;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\Validation\ValidateHelper;


class ActorsController
{
   private $actor_model = null;

   const DEFAULT_PAGE = 1;
   const DEFAULT_PAGE_SIZE = 10;

   // Empty constructor
   public function __construct()
   {
      $this->actor_model = new ActorsModel();
   }

   public function handleCreateActors(Request $request, Response $response, array $uri_args)
   {
      // 1. ) to retrieve the data from the request

      // 2. ) validate
      // check if the body is empty
      // is_array, see if it's an array
      //check if the data is structured correctly

      // if all above condition is correct then you can proceed on handling the request

      // 3. ) Insert the new data into the db
      // validate actors

      // use foreach() to get all the individual data
      // 4. ) pass the actor element/item to the model
      // call the actors model function to create the actor

      // return with the right status code
      return $response;
   }

   public function handleGetAllActors(Request $request, Response $response)
   {

      $filters = $request->getQueryParams();

      if ($filters) {
         foreach ($filters as $key => $value) {
            if (!$this->validateParams($key)) {
               throw new HttpUnprocessableContent($request, "Invalid query parameter : " . "{" . $key . "}");
            } elseif (empty($value)) {
               throw new HttpUnprocessableContent($request, "Please provide query value for : " . "{" . $key . "}");
            }
         }
      }

      // verify if client added a page and pageSize params
      // if client didn't add a page and pageSize params, paginate using the default values
      $page = $filters["page"] ?? self::DEFAULT_PAGE;
      $pageSize = $filters["pageSize"] ?? self::DEFAULT_PAGE_SIZE;

      // check if the params is numeric, if not throw a bad request error
      if (!is_numeric($page) || !is_numeric($pageSize)) {
         throw new HttpBadRequest($request, "expected numeric but received alpha");
      }

      $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 50];

      if (!ValidateHelper::validatePagingParams($dataParams)) {
         throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual");
      }

      $this->actor_model->setPaginationOptions($page, $pageSize);
      // catch any DB exceptions
      try {
         $data = $this->actor_model->getAllActors($filters);
      } catch (Exception $e) {
         throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
      }

      if (!$data['data']) {
         throw new HttpNotFound($request);
      }

      // json
      $json_data = json_encode($data);
      // return the response;
      $response->getBody()->write($json_data);

      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
   }

   public function handleFilmByActorsId(Request $request, Response $response, array $uri_args)
   {
      $actor_id = $uri_args['actor_id'];
      $dataParams = ['id' => $actor_id, 'min' => 1, 'max' => 2000];
      $isValidated = ValidateHelper::validateInputId($dataParams);
      if (!$isValidated) {
         $msg = is_numeric($actor_id) ? "The provided ID : " . "{" . $actor_id . "} is out of range" : "Invalid input: " . "{" . $actor_id . "}, expecting a number ";
         throw new HttpUnprocessableContent($request, $msg);
      }
      $filters = $request->getQueryParams();
      if ($filters) {
         foreach ($filters as $key => $value) {
            if (!$this->validateParams($key)) {

               throw new HttpUnprocessableContent($request, "Invalid query parameter : " . "{" . $key . "}");
            } elseif (empty($value)) {
               throw new HttpUnprocessableContent($request, "Please provide query value for : " . "{" . $key . "}");
            }
         }
      }
      if (isset($filters['film_length'])){
         
         if ($filters['film_length']){
            $isValidNum = ValidateHelper::validateNumericInput(array("length"=>$filters['film_length']));
            if (!$isValidNum)
            {
               $msg = is_numeric($filters['film_length']) ? "The provided length is out of range" : "Invalid input: expecting a number ";
               throw new HttpUnprocessableContent($request, $msg);
            }
         } else {
            throw new HttpUnprocessableContent($request, "Please provide query value for : {film_length}");
         }
        
      }

      // verify if client added a page and pageSize params
      // if client didn't add a page and pageSize params, paginate using the default values
      $page = $filters["page"] ?? self::DEFAULT_PAGE;
      $pageSize = $filters["pageSize"] ?? self::DEFAULT_PAGE_SIZE;

      // check if the params is numeric, if not throw a bad request error
      if (!is_numeric($page) || !is_numeric($pageSize)) {
         throw new HttpBadRequest($request, "expected numeric but received alpha");
      }

      $paginateParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];
      if (!ValidateHelper::validatePagingParams($paginateParams)) {
         throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual");
      }

      // set pagination
      $this->actor_model->setPaginationOptions($page, $pageSize);
      // catch any DB exceptions
      try {
         $data = $this->actor_model->getFilmByActorId($actor_id, $filters);
      } catch (Exception $e) {
         throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
      }

      // if the returned data is empty
      if (!$data['data']) {
         throw new HttpNotFound($request);
      }

      // process the data and return the response as json format
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
   private function validateParams($param): bool
   {
      $params = [
         'first_name', 'last_name', 'page', 'pageSize', 'sort_by', 'category', 'rating', 'film_length'
      ];

      if (in_array($param, $params)) {
         return true;
      }
      return false;
   }
}
