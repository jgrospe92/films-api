<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpConflict extends HttpSpecializedException
{

 
    protected $code = 409;
    protected $message = 'This post request is poorly structured, please check the documentations';
    protected $title = '409 Conflict';
    protected $description = 'Please provide all the required fields';
    
    
}
