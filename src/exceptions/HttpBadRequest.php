<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpBadRequest extends HttpSpecializedException
{

 
    protected $code = 400;
    protected $message = 'Invalid request syntax';
    protected $title = '400 BAD REQUEST';
    protected $description = 'Invalid request syntax';
    
    
}
