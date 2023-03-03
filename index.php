<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Vanier\Api\controllers\FilmsController;
use Vanier\Api\middleware\ContentNegotiationMiddleware;

require __DIR__ . '/vendor/autoload.php';
 // Include the file that contains the application's global configuration settings,
 // database credentials, etc.
require_once __DIR__ . '/src/config/app_config.php';

//--Step 1) Instantiate a Slim app.
$app = AppFactory::create();
//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();

// Test middleware


//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->getDefaultErrorHandler()->forceContentType(APP_MEDIA_TYPE_JSON);
//-- Step 4)
// TODO: change the name of the subdirectory here.
// You also need to change it in .htaccess
$app->setBasePath("/films-api");

$app->add(new ContentNegotiationMiddleware());


//-- Step 5)
// Here we include the file that contains the application routes. 
//require_once __DIR__ . './src/routes/api_routes.php';
// NOTE: Add your app routes here.
// The callbacks must be implemented in a controller class.
// The Vanier\Api must be used as namespace prefix.

//$app->get('/films', [FilmsController::class, 'getAllFilms']);

$app->get('/hello', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Reporting! Hello there!");    
    return $response;
});

// This is a middleware that should be disabled/enabled later. 
//$app->add($beforeMiddleware);
// Run the app.
$app->run();
