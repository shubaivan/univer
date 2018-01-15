<?php

namespace AppBundle\Exception;

use Symfony\Component\Validator\ConstraintViolation;

class ValidatorException extends \Exception implements \JsonSerializable
{
    /**
     * @var array
     */
    private $errorsArray = [];

    /**
     * @param array           $errors
     * @param string          $message
     * @param int             $code
     * @param null|\Exception $previous
     */
    public function __construct(
        array $errors = [],
        $message = '',
        $code = 0,
        \Exception $previous = null
    ) {
        $this->errorsArray = $errors;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errorsArray;
    }

    /**
     * @return string
     */
    public function getErrorsMessage()
    {
        $response = $this->prepareResponseArray();
        if ($response) {
            return $this->implodeArrayError($response);
        }

        return '';
    }

    /**
     * @param array $message
     *
     * @return array
     */
    public function addError(array $message)
    {
        $this->errorsArray[] = $message;
    }

    public function jsonSerialize()
    {
        return [
            'errors' => $this->prepareResponseArray(),
        ];
    }

    /**
     * @return array
     */
    public function prepareResponseArray()
    {
        $response = [];

        foreach ($this->errorsArray as $errors) {
            foreach ($errors as $error) {
                /** @var ConstraintViolation $viol */
                foreach ($error as $viol) {
                    $response[$viol->getPropertyPath()] = $viol->getMessage();
                }
            }
        }

        return $response;
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function implodeArrayError(array $input)
    {
        $output = implode(', ', array_map(
            function ($v, $k) {
                return sprintf("%s='%s'", $k, $v);
            },
            $input,
            array_keys($input)
        ));

        return $output;
    }
}
