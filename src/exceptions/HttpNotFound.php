<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpNotFound extends HttpSpecializedException
{

 
    protected $code = 404;
    protected $message = 'please check your query parameter or consult the documentations';
    protected $title = '404 Not Found';
    protected $description = 'The requested resource could not be found. Please verify the URI and try again.';
    
    
}
