<?php
namespace Vanier\Api\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class FilmsController{

    // Empty constructor
   public function __construct() {
      
   }
   // Callback - need to return response
   public function getAllFilms(Request $request, Response $response)
   {

        #throw new HttpNotFoundException($request, "something went wrong while processing the request, invalid data");
          
        // filter by title 
        $filters = $request->getQueryParams();

        $filmsModel = new \Vanier\Api\models\FilmsModel();
        $data = $filmsModel->getAll($filters);
        
        // json
        $json_data = json_encode($data);
        // return the response;
        $response->getBody()->write($json_data);


        return $response->withStatus(200)->withHeader("Content-type", "application/json");
   }




} 