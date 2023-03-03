<?php
namespace Vanier\Api\exceptions;
// declare(strict_types=1);
use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException
{


    public function __construct()
    {
        
    }
    /**
     * @var int
     */
    protected $code = 404;

    /**
     * @var string
     */
    protected $message = 'Not found.';

    protected $title = '404 Not Found';
    protected $description = 'The requested resource could not be found. Please verify the URI and try again.';
    
}
