<?php

namespace Vanier\Api\Validation;

require_once("validations/Validator.php");

use Vanier\Api\Validations\Validator;

/**
 * Summary of ValidateHelper
 */
class ValidateHelper
{

    public static function validatePagingParams(array $dataParams)
    {

        // The array containing the data to be validated.
        $data = array(
            "page" => $dataParams['page'],
            "page_size" => $dataParams['pageSize'],
        );
        // An array element can be associated with one or more validation rules. 
        // Validation rules must be wrapped in an associative array where:
        // NOTE: 
        //     key => must be an existing key  in the data array to be validated. 
        //     value => array of one or more rules.    
        $rules = array(
            'page' => [
                'required',
                'numeric',
                ['min', $dataParams['pageMin']]
            ],
            'page_size' => [
                'required',
                'integer',
                ['min', $dataParams['pageSizeMin']],
                ['max', $dataParams['pageSizeMax']]
            ]
        );

        // Create a validator and override the default language used in expressing the error messages.
        $validator = new Validator($data, [], 'en');
        // Important: map the validation rules before calling validate()
        $validator->mapFieldsRules($rules);
        if ($validator->validate()) {
            return true;
        } else {
            return false;
        }
    }

    
   /**
    * Summary of validateInputId
    * @param mixed $id
    * @return mixed
    */
   public static function validateInputId(array $dataParams)
   {
      return filter_var($dataParams['id'], FILTER_VALIDATE_INT, ['options'=> ['min_range' =>$dataParams['min'], 'max_range'=>$dataParams['max']]]);
   }



   /**
    * Summary of validateDateInput
    * @param mixed $date
    * @return bool 
    * Validate date input
    */
   public static function validateDateInput(array $date){

    $data = array('from_rentalDate'=> $date['from_rentalDate'], 'to_rentalDate'=>$date['to_rentalDate']);

    $rules = array(
                    'from_rentalDate' => [['dateFormat', 'Y-m-d']],
                    'to_rentalDate' => [['dateFormat', 'Y-m-d']]
                    );
    $validator = new Validator($data, [], 'en');
     // Important: map the validation rules before calling validate()
    $validator->mapFieldsRules($rules);
    if ($validator->validate()) {
        return true;
    } else {
        return false;
    }

   }

    function testValidatePersonInfo()
    {
        // The array containing the data to be validated.
        $data = array(
            "fist_name" => "Ladybug",
            "last_name" => "Bumblebee",
            "age" =>  '9',
            "price" =>  '389.53',
            "oi" =>  '5',
            "dob" =>  '1-2022-05',
        );
        // An array element can be associated with one or more validation rules. 
        // Validation rules must be wrapped in an associative array where:
        // key => must be an existing key  in the data array to be validated. 
        // value => array of one or more rules.    
        $rules = array(
            'fist_name' => array(
                'required',
                array('lengthMin', 4)
            ),
            'last_name' => array(
                'required',
                array('lengthBetween', 1, 4)
            ),
            'age' => [
                'required',
                'integer',
                ['min', 18]
            ],
            'dob' => [
                'required',
                ['dateFormat', 'Y-m-d']
            ],
            'oi' => [
                'required',
                ['equals', 'Oye']
            ]
        );

        $validator = new Validator($data);
        // Important: map the validation rules before calling validate()
        $validator->mapFieldsRules($rules);
        if ($validator->validate()) {
            echo "<br> Valid data!";
        } else {
            //var_dump($validator->errors());
            //print_r($validator->errors());
            echo $validator->errorsToString();
            echo $validator->errorsToJson();
        }
    }
    public static function validateNumericInput(array $data)
    {
        // Validate a single value.
        // The value must be passed as an array. 
        $value = $data['length'];
        
        $validator = new Validator(['length' => $value]);
        $validator->rule('min','length',1);
        if ($validator->validate()) {
            return true;
        } else {
           return false;
        }
    }

    public static function validatePostActor(array $data)
    {
        $first_name = $data['first_name'] ?? '';
        $last_name = $data['last_name'] ?? '';

        $data = array(
            "fist_name" => $first_name,
            "last_name" => $last_name
        );

        $rules = [
            'alpha' => [
                'fist_name','last_name'
            ],
            // We can apply the same rule to multiple elements.
            'required' => [
                'fist_name', 'last_name'
            ],
            // Validate the max length of list of elements.
            'lengthMax' => array(
                array('fist_name', 20),
                array('last_name', 20)
            )
        ];
        // Change the default language to French.
        //$validator = new Validator($data, [], "fr");
        $validator = new Validator($data);
        $validator->rules($rules);

        if ($validator->validate()) {
            return true;
        } else {
            return false;
        }
    }

    public static function validatePostFilm(array $data)
    {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $release_year = $data['release_year'] ?? '';
        $language_id = $data['language_id'] ?? '';
        $original_language_id = $data['original_language_id'] ?? '';
        $rental_duration = $data['rental_duration'] ?? '';
        $rental_rate = $data['rental_rate'] ?? '';
        $length = $data['length'] ?? '';
        $replacement_cost = $data['replacement_cost'] ?? '';
        $rating = $data['rating'] ?? '';
        $special_features = $data['special_features'] ?? '';


        $data = array(
            "title" => $title,
            "description" => $description,
            "release_year" => $release_year,
            "language_id" => $language_id,
            "original_language_id" => $original_language_id,
            "rental_duration" => $rental_duration,
            "rental_rate" => $rental_rate,
            "length" => $length,
            "replacement_cost" => $replacement_cost,
            "rating" => $rating,
            "special_features" => $special_features,
        );

        $rules = [
            'numeric' => [
                'release_year','language_id', 'original_language_id', 'rental_duration', 
            ],
            // We can apply the same rule to multiple elements.
            'required' => [
                'fist_name', 'last_name'
            ],
            // Validate the max length of list of elements.
            'lengthMax' => array(
                array('fist_name', 20),
                array('last_name', 20)
            )
        ];
        // Change the default language to French.
        //$validator = new Validator($data, [], "fr");
        $validator = new Validator($data);
        $validator->rules($rules);

        if ($validator->validate()) {
            return true;
        } else {
            return false;
        }
    }
}
