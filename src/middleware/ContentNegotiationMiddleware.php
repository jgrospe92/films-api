<?php
namespace Vanier\Api\middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;



class  ContentNegotiationMiddleware implements MiddlewareInterface
{

    public function __construct(){

    }
    public function process(Request $request, RequestHandler $handler): Response
    {

        $accept = $request->getHeaderLine("Accept");
        
        if (!str_contains(APP_MEDIA_TYPE_JSON, $accept)){

            $errorHandling["404"] = array("Message"=>"Not Found", "Description"=>"Resource doesn't exists");

            //throw new HttpNotFoundException($request, "Invalid format" );
            $response = new \Slim\Psr7\Response();
            // print_r($errorHandling); exit();
            $response = json_encode($errorHandling["404"]);
            return $response;
        }
        $response = $handler->handle($request);
        return  $response;
        }
}