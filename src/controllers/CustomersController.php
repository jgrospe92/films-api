<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\CustomersModel;
use Vanier\Api\exceptions\HttpNotAcceptableException;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\Validation\ValidateHelper;


/**
 * Summary of FilmsController
 * Support operations such as getAllFilms, 
 */
class CustomersController
{

    private $customer_model = null;

    // default constant value for page & pageSize
    const DEFAULT_PAGE = 1;
    const DEFAULT_PAGE_SIZE = 10;

    // Empty constructor
    public function __construct()
    {
        $this->customer_model = new CustomersModel();
    }
    // Callback - need to return response
    public function handleGetAllCustomers(Request $request, Response $response)
    {

        // filter by title 
        $filters = $request->getQueryParams();

        if ($filters) {
            foreach ($filters as $key => $value) {
                if (!$this->validateParams($key)) {
                    throw new HttpUnprocessableContent($request, "Invalid query parameter : " . "{" . $key . "}");
                } elseif (empty($value)) {
                    throw new HttpUnprocessableContent($request, "Please provide query value for : " . "{" . $key . "}");
                }
            }
        }
        // verify if client added a page and pageSize params
        // if client didn't add a page and pageSize params, paginate using the default values
        $page = $filters["page"] ?? DEFAULT_PAGE;
        $pageSize = $filters["pageSize"] ?? DEFAULT_PAGE_SIZE;

        // check if the params is numeric, if not throw a bad request error
        if (!is_numeric($page) || !is_numeric($pageSize)) {
            throw new HttpBadRequest($request, "expected numeric but received alpha");
        }

        $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];

        if (!ValidateHelper::validatePagingParams($dataParams)) {
            throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual");
        }

        $this->customer_model->setPaginationOptions($page, $pageSize);
        // catch any DB exceptions
        try {
            $data = $this->customer_model->getAllCustomers($filters, $request);
        } catch (Exception $e) {
            throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
        }

        if (!$data['data']) {
            throw new HttpUnprocessableContent($request, "Unable to process your request, please check you query parameter");
        }
        // json
        $json_data = json_encode($data);
        // return the response;
        $response->getBody()->write($json_data);

        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
    }


    /**
     * Summary of handleGetFilmByCustomerId
     * @param Request $request
     * @param Response $response
     * @param array $uri_args
     * @throws HttpUnprocessableContent
     * @return Response
     */
    public function handleGetFilmByCustomerId(Request $request, Response $response, array $uri_args)
    {


        $customer_id = $uri_args['customer_id'];
       
        $dataParams = ['id'=> $customer_id, 'min'=>1, 'max'=>1000];
        $isValidated = ValidateHelper::validateInputId($dataParams);
        if (!$isValidated) {
            $msg = is_numeric($customer_id) ? "The provided ID : " . "{" . $customer_id . "} is out of range" : "Invalid input: " . "{" . $customer_id . "}, expecting a number ";
            throw new HttpUnprocessableContent($request, $msg);
        }

        $filters = $request->getQueryParams();
        if ($filters) {
            foreach ($filters as $key => $value) {
                if (!$this->validateParams($key)) {

                    throw new HttpUnprocessableContent($request, "Invalid query parameter : " . "{" . $key . "}");
                } elseif (empty($value)) {
                    throw new HttpUnprocessableContent($request, "Please provide query value for : " . "{" . $key . "}");
                }
            }
        }
        // verify if client added a page and pageSize params
        // if client didn't add a page and pageSize params, paginate using the default values
        $page = $filters["page"] ?? self::DEFAULT_PAGE;
        $pageSize = $filters["pageSize"] ?? self::DEFAULT_PAGE_SIZE;

        // check if the params is numeric, if not throw a bad request error
        if (!is_numeric($page) || !is_numeric($pageSize)) {
            throw new HttpBadRequest($request, "expected numeric but received alpha");
        }

        $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];

        if (!ValidateHelper::validatePagingParams($dataParams))
        {
           throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual"); 
        }
  
        // set pagination
        $this->customer_model->setPaginationOptions($page, $pageSize);
        // catch any DB exceptions
        try {
            $data = $this->customer_model->getFilmById($customer_id, $filters);
        } catch (Exception $e) {
            echo $e;
            throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
        }

        // if the returned data is empty
        if (!$data['data']){
            throw new HttpUnprocessableContent($request, "Unable to process your request, please check your query parameter or consult the documentation");
         }
         // 

        // process the data and return the response as json format
        $json_data = json_encode($data);
        $response->getBody()->write($json_data);
        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader("Content-type", "application/json");
    }

    /**
     * Summary of validateParams
     * @param mixed $param
     * @return bool
     * return true if the given parameter is supported otherwise false 
     */
    function validateParams($param): bool
    {
        $params = ['first_name', 'last_name', 'city', 'country', 'page', 'pageSize', 'sort_by'];

        if (in_array($param, $params)) {
            return true;
        }
        return false;
    }


}
