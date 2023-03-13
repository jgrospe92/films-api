<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;
use Vanier\Api\exceptions\HttpConflict;
use Vanier\Api\Models\CustomersModel;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\Validation\ValidateHelper;


/**
 * Summary of FilmsController
 * Support operations such as
 * handleGetAllCustomers 
 * handleGetFilmByCustomerId
 * handleUpdateCustomers
 * handleDeleteCustomer
 */
class CustomersController extends BaseController
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


    /**
     * Summary of handleGetAllCustomers
     * @param Request $request
     * @param Response $response
     * @throws HttpUnprocessableContent
     * @throws HttpBadRequest
     * @throws HttpNotFound
     * @return Response
     */
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
        $page = $filters["page"] ?? self::DEFAULT_PAGE;
        $pageSize = $filters["pageSize"] ?? self::DEFAULT_PAGE_SIZE;

        // check if the params is numeric, if not throw a bad request error
        if (!ValidateHelper::validatePageNumbers($page, $pageSize)) {
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
            throw new HttpNotFound($request);
        }
        // return parsed data
        return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_OK);
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

        $dataParams = ['id' => $customer_id, 'min' => 1, 'max' => 1000];
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
        if (!ValidateHelper::validatePageNumbers($page, $pageSize)) {
            throw new HttpBadRequest($request, "expected numeric but received alpha");
        }

        $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];

        if (!ValidateHelper::validatePagingParams($dataParams)) {
            throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual");
        }

        $hasKey['from_rentalDate'] = isset($filters['from_rentalDate']) ?? '';
        $hasKey['to_rentalDate'] = isset($filters['to_rentalDate']) ?? '';
        // validates date
        if ($hasKey['from_rentalDate'] && !$hasKey['to_rentalDate']) {
            throw new HttpBadRequest($request, "required parameter : to_rentalDate");
        } elseif (!$hasKey['from_rentalDate'] && $hasKey['to_rentalDate']) {
            throw new HttpBadRequest($request, "required parameter : from_rentalDate");
        } elseif ($hasKey['from_rentalDate'] && $hasKey['to_rentalDate']) {
            $date = ['from_rentalDate' => $filters['from_rentalDate'], 'to_rentalDate' => $filters['to_rentalDate']];
            if (!ValidateHelper::validateDateInput($date)) {
                throw new HttpBadRequest($request, "expected from_rentalDate and to_rentalDate dateformat 'yyyy-mm-dd' please check the documentation");
            }
        }
        // set pagination
        $this->customer_model->setPaginationOptions($page, $pageSize);
        // catch any DB exceptions
        try {
            $data = $this->customer_model->getFilmById($customer_id, $filters);
        } catch (Exception $e) {
            throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
        }

        if (!$data['films']['data']){
            throw new HttpNotFound($request);
        }

        // return parsed data
        return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_OK);
    }


    /**
     * Summary of handleUpdateCustomers
     * @param Request $request
     * @param Response $response
     * @throws HttpConflict
     * @return Response
     */
    public function handleUpdateCustomers(Request $request, Response $response)
    {

        // 1. ) to retrieve the data from the request
        $data = $request->getParsedBody();
        if (!isset($data)) {
            throw new HttpConflict($request);
        }

        // validate the body
        foreach ($data as $customer) {
            if (!ValidateHelper::validatePutCustomer($customer)) {
                throw new HttpConflict($request);
            } else {
                $this->customer_model->updateCustomer($customer);
            }
        }
        // return parsed data
        return $this->parsedResponseData($data, $response, StatusCodeInterface::STATUS_CREATED);
    }

    /**
     * Summary of handleDeleteCustomer
     * @param Request $request
     * @param Response $response
     * @param array $uri_args
     * @throws HttpUnprocessableContent
     * @throws HttpForbiddenException
     * @return Response
     */
    public function handleDeleteCustomer(Request $request, Response $response, array $uri_args)
    {
        $customer_id = $uri_args['customer_id'];
        $dataParams = ['id' => $customer_id, 'min' => 1, 'max' => 1000];
        $isValidated = ValidateHelper::validateInputId($dataParams);
        if (!$isValidated) {
            $msg = is_numeric($customer_id) ? "The provided ID : " . "{" . $customer_id . "} is out of range" : "Invalid input: " . "{" . $customer_id . "}, expecting a number ";
            throw new HttpUnprocessableContent($request, $msg);
        }
        try {
            $this->customer_model->deleteCustomer($customer_id);
        } catch (Exception $e)
        {
            throw new HttpForbiddenException($request, "Can't delete resource due to FK constraint");
        }
        
        return $this->parsedDeleteResponse($response);
    }


    /**
     * Summary of validateParams
     * @param mixed $param
     * @return bool
     * return true if the given parameter is supported otherwise false
     */
    function validateParams($param): bool
    {
        $params = [
            'first_name', 'last_name', 'city', 'country', 'page', 'pageSize', 'sort_by', 'rating',
            'special_features', 'category', 'rental_date', 'from_rentalDate', 'to_rentalDate'
        ];

        if (in_array($param, $params)) {
            return true;
        }
        return false;
    }
}
