<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Validator;

/**
 * Class ViolationHelper
 * @package App\Validator
 */
class ViolationHelper
{
    /**
     * @param $violations
     * @return array
     */
    public static function mapViolationsToArray($violations): array
    {
        $errors = array();
        foreach ($violations as $violation) {
            $errors[] = 'Field \'' . $violation->getPropertyPath() . '\': ' . $violation->getmessage();
        }

        return $errors;
    }
}
