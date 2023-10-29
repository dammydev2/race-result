<?php

namespace App\Validator;

use App\Entity\Race;
use App\Validator\Constraints\CsvFile;
use App\Validator\Constraints\DateFormat;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


class RaceValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data, UploadedFile $file = null)
    {
        $race = new Race();

        $fileError = [];
        if(!$file){
            $fileError['file'] = 'please upload a csv file.';
        }
        else{
            $fileError['file'] = $this->validateCSV($file);
            if($fileError['file'] == null){
                $fileError = [];
            }
        }

        if(isset($data['title'])){
            $race->setTitle($data['title']);
        }

        $errors = $this->validator->validate($race);

        $dateResponse =[];

        if (!array_key_exists('date', $data)) {
            $dateResponse['date'] = 'date field is missing in the request.';
        }
        else {
            
            $dateError = $this->validator->validate(
                $data['date'],
                new DateFormat()
            );

            if (count($dateError) > 0) {
                $dateResponse['date'] = $dateError[0]->getMessage();
            }
        }

        $errorResponse = [];
        foreach ($errors as $error) {
            $fieldName = $error->getPropertyPath();
            $errorMessage = $error->getMessage();
            $errorResponse[$fieldName] = $errorMessage;
        }
        
        // $combinedErrors = array_merge($errorResponse, $dateResponse);
        $combinedErrors = $errorResponse + $dateResponse + $fileError;
        
        return $combinedErrors;
    }

    public function validateCSV(UploadedFile $file) {
        
        $errors = $this->validator->validate($file, new CsvFile());
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $errorMessages;
        }
        return null;
    }

}