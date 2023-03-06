<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpUnprocessableContent extends HttpSpecializedException
{
    protected $code = 422;
    protected $message = 'Incorrect query parameter';
    protected $title = '422 Unprocessable Content';
    protected $description = 'The requested content is not supported';

    
    
}
