<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Helper;


class Response
{
    public static function createResponse($data, int $code, array $errors = array()) : \Symfony\Component\HttpFoundation\Response
    {
        $response = new \stdClass();
        $response->meta = new \stdClass();

        $response->meta->code = $code;
        $response->meta->errors = $errors;

        $response->data = $data;

        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($response),
            $code,
            array('Content-Type' => 'application/json')
        );
    }
}