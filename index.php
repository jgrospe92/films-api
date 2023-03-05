<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\ServerRequestCreatorFactory;
use Vanier\Api\exceptions\HttpErrorHandler;
use Slim\Factory\AppFactory;
use Vanier\Api\controllers\ActorsController;
use Vanier\Api\controllers\FilmsController;
use Vanier\Api\middleware\ContentNegotiationMiddleware;
use Vanier\Api\models\ActorsModel;
use Vanier\Api\models\FilmsModel;

require __DIR__ . '/vendor/autoload.php';
 // Include the file that contains the application's global configuration settings,
 // database credentials, etc.
require_once __DIR__ . '/src/config/app_config.php';


//--Step 1) Instantiate a Slim app.
$app = AppFactory::create();
// add callable
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
// add server request
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();

// Test middleware


//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);
$errorMiddleware->getDefaultErrorHandler()->forceContentType(APP_MEDIA_TYPE_JSON);
$app->add(new ContentNegotiationMiddleware());
//-- Step 4)
// TODO: change the name of the subdirectory here.
// You also need to change it in .htaccess
$app->setBasePath("/films-api");

//-- Step 5)
// Here we include the file that contains the application routes. 
//require_once __DIR__ . './src/routes/api_routes.php';
// NOTE: Add your app routes here.
// The callbacks must be implemented in a controller class.
// The Vanier\Api must be used as namespace prefix.

$app->get('/films', [FilmsController::class, 'handleGetAllFilms']);

$app->get('/hello', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Reporting! Hello there!");    
    return $response;
});

$app->get('/films/{film_id}', [FilmsController::class, 'handleGetFilmById']);

// post - create a film
$app->post('/actors', [ActorsController::class, 'handleCreateActors']);


// This is a middleware that should be disabled/enabled later. 
//$app->add($beforeMiddleware);
// Run the app.
$app->run();
