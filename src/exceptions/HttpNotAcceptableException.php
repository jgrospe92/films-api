<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException
{

 
    protected $code = 406;
    protected $message = 'Requested resource does not match the provided criteria';
    protected $title = '406 Not Acceptable';
    protected $description = 'Requested resource does not match the provided criteria';
    
}
