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


class ActorsController{
    private $actor_model = null;

    // Empty constructor
   public function __construct() {
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

   public function handleGetAllActors(Request $request, Response $response){
      echo "actors"; exit;
   }
}


?>