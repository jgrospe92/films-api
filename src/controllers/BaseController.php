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

}



?>