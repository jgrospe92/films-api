<?php

namespace Vanier\Api\controllers;
// imports

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Vanier\Api\Models\CategoriesModel;
use Vanier\Api\exceptions\HttpNotAcceptableException;
use Vanier\Api\exceptions\HttpBadRequest;
use Vanier\Api\exceptions\HttpUnprocessableContent;
use Vanier\Api\exceptions\HttpNotFound;
use Vanier\Api\Validation\ValidateHelper;


/**
 * Summary of FilmsController
 * Support operations such as getAllFilms, 
 */
class CategoriesController
{

    private $categories_model = null;

    // default constant value for page & pageSize
    const DEFAULT_PAGE = 1;
    const DEFAULT_PAGE_SIZE = 10;

    // Empty constructor
    public function __construct()
    {
        $this->categories_model = new CategoriesModel();
    }
    // Callback - need to return response
    public function handleGetAllFilmsByCategory(Request $request, Response $response, array $uri_args)
    {
        // category id
        $category_id = $uri_args['category_id'];
        $dataParams = ['id' => $category_id, 'min' => 1, 'max' => 16];
        $isValidated = ValidateHelper::validateInputId($dataParams);
        if (!$isValidated) {
            $msg = is_numeric($category_id) ? "The provided ID : " . "{" . $category_id . "} is out of range" : "Invalid input: " . "{" . $category_id . "}, expecting a number ";
            throw new HttpUnprocessableContent($request, $msg);
        }
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
        if (!is_numeric($page) || !is_numeric($pageSize)) {
            throw new HttpBadRequest($request, "expected numeric but received alpha");
        }

        $dataParams = ['page' => $page, 'pageSize' => $pageSize, 'pageMin' => 1, 'pageSizeMin' => 5, 'pageSizeMax' => 10];

        if (!ValidateHelper::validatePagingParams($dataParams)) {
            throw new HttpUnprocessableContent($request, "Out of range, unable to process your request, please consult the manual");
        }

        $this->categories_model->setPaginationOptions($page, $pageSize);
        // catch any DB exceptions
        try {
            $data = $this->categories_model->getAllFilmsByCategory($category_id,$filters);
        } catch (Exception $e) {
            throw new HttpBadRequest($request, "Invalid request Syntax, please refer to the manual");
        }

        // json
        $json_data = json_encode($data);
        // return the response;
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
        $params = [
            'film_length', 'rating', 'sort_by'
        ];

        if (in_array($param, $params)) {
            return true;
        }
        return false;
    }
}
