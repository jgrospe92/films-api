<?php
namespace Vanier\Api\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;


/**
 * Summary of BaseController
 * parsedResponseData
 * parsedDeleteResponse
 */
class BaseController {

    /**
     * Summary of parsedResponseData
     * @param mixed $data
     * @param Response $response
     * @param mixed $status
     * @return Response
     */
    public function parsedResponseData($data, Response $response, $status) {

        $json_data = json_encode($data);
        $response->getBody()->write($json_data);
        return $response->withStatus($status)->withHeader("Content-type", "application/json");
    }

    /**
     * Summary of parsedDeleteResponse
     * @param Response $response
     * @return Response
     */
    public function parsedDeleteResponse(Response $response){

        $data = array("statusCode"=>StatusCodeInterface::STATUS_OK, "type"=>"Delete Request", "Description"=> "Resource successfully deleted");
        $parsedResponse = json_encode($data);
        $response->getBody()->write($parsedResponse);
        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
    }

}



?>