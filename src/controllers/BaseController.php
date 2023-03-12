<?php
namespace Vanier\Api\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Fig\Http\Message\StatusCodeInterface;


class BaseController {

    public function parsedResponseData($data, Response $response) {

        $json_data = json_encode($data);
        $response->getBody()->write($json_data);
        return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader("Content-type", "application/json");
    }

}



?>