<?php
namespace Vanier\Api\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;


class BaseController {

    public function parsedResponseData($data, Response $response, $status) {

        $json_data = json_encode($data);
        $response->getBody()->write($json_data);
        return $response->withStatus($status)->withHeader("Content-type", "application/json");
    }

    public function parsedDeleteResponse(Response $response){

        $data = array("statusCode"=>200, "type"=>"Delete Request", "Description"=> "Resource successfully deleted");
        $parsedResponse = json_encode($data);
        $response->getBody()->write($parsedResponse);
        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
    }

}



?>