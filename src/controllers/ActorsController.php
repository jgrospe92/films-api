<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\ActorsModel;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\exceptions\HttpConflict;
use Vanier\Api\Validation\ValidateHelper;


/**
 * Summary of ActorsController
 * handleCreateActors
 * handleGetAllACtors
 * handleFilmByActorsId
 * validateParams
 */
class ActorsController extends BaseController
{
   private $actor_model = null;

   const DEFAULT_PAGE = 1;
   const DEFAULT_PAGE_SIZE = 50;

   // Empty constructor
   public function __construct()
   {
      $this->actor_model = new ActorsModel();
   }

   /**
    * Summary of handleCreateActors
    * @param Request $request
    * @param Response $response
    * @param array $uri_args
    * @throws HttpConflict
    * @return Response
    */
   public function handleCreateActors(Request $request, Response $response, array $uri_args)
   {
      // 1. ) to retrieve the data from the request
      $data = $request->getParsedBody();
      // 2. ) validate
      // check if the body is empty
      if (!isset($data)) {
         throw new HttpConflict($request);
      }
      // validate the body
      foreach ($data as $actor) {
         if (!ValidateHelper::validatePostActor($actor)) {
            throw new HttpConflict($request);
         } else {
            // TODO create a mode function to create
            $this->actor_model->createActors($actor);
         }
      }
      // return parsed data
      return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_CREATED);
   }

   /**
    * Summary of handleGetAllActors
    * @param Request $request
    * @param Response $response
    * @throws HttpUnprocessableContent
    * @throws HttpBadRequest
    * @throws HttpNotFound
    * @return Response
    */
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
      if (!ValidateHelper::validatePageNumbers($page, $pageSize)) {
         throw new HttpBadRequest($request, "expected numeric but received alpha");
      }
      $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 100];

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

      // return parsed data
      return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_OK);
   }

   /**
    * Summary of handleFilmByActorsId
    * @param Request $request
    * @param Response $response
    * @param array $uri_args
    * @throws HttpUnprocessableContent
    * @throws HttpBadRequest
    * @throws HttpNotFound
    * @return Response
    */
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
      if (isset($filters['film_length'])) {

         if ($filters['film_length']) {
            $isValidNum = ValidateHelper::validateNumericInput(array("length" => $filters['film_length']));
            if (!$isValidNum) {
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
      if (!ValidateHelper::validatePageNumbers($page, $pageSize)) {
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

      if (!$data['films']['data']) {
         throw new HttpNotFound($request);
      }

      // return parsed data
      return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_OK);
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
