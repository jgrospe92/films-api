<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\ServerRequestCreatorFactory;
use Vanier\Api\exceptions\HttpErrorHandler;
use Slim\Factory\AppFactory;
use Vanier\Api\controllers\ActorsController;
use Vanier\Api\controllers\FilmsController;
use Vanier\Api\controllers\CustomersController;
use Vanier\Api\controllers\CategoriesController;
use Vanier\Api\middleware\ContentNegotiationMiddleware;
use Vanier\Api\models\CustomersModel;

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

// Films routing 
$app->get('/films', [FilmsController::class, 'handleGetAllFilms']);
$app->get('/films/{film_id}', [FilmsController::class, 'handleGetFilmById']);
// post - create films
$app->post('/films',[FilmsController::class,'handleCreateFilms']);
// update
$app->put('/films', [FilmsController::class, 'handleUpdateFilms']);

// customer routing
$app->get('/customers',[CustomersController::class, 'handleGetAllCustomers']);
$app->get('/customers/{customer_id}/films',[CustomersController::class, 'handleGetFilmByCustomerId']);
// update
$app->put('/customers', [CustomersController::class, 'handleUpdateCustomers']);
// Delete
$app->delete('/customers/{customer_id}', [CustomersController::class, 'handleDeleteCustomer']);

// Actor routing
$app->get('/actors', [ActorsController::class, 'handleGetAllActors']);
$app->get('/actors/{actor_id}/films', [ActorsController::class, 'handleFilmByActorsId']);
// post - create actors
$app->post('/actors', [ActorsController::class, 'handleCreateActors']);

// categories
$app->get('/categories/{category_id}/films', [CategoriesController::class, 'handleGetAllFilmsByCategory']);


//This is a middleware that should be disabled/enabled later.
//$app->add($beforeMiddleware);
// Run the app.
$app->run();
