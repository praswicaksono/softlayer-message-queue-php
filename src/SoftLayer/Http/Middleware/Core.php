<?php

class SoftLayer_Http_Middleware_Core implements SoftLayer_Http_Middleware_Interface
{
    public function filterRequest(SoftLayer_Http_Request &$request)
    {
        /* ... */
    }

    public function filterResponse(SoftLayer_Http_Response &$response)
    {
        $status = $response->getStatus();

        if($status >= 400) {
            $body = $response->getBody();
            $message = $body->message?$body->message:"";
            $errors = "";
            $exception = "[{$status}]";

            if(property_exists($body, 'errors')) {
                foreach($body->errors as $category => $collection) {
                    $errors .= "{$category}: " . implode(", ", $collection);
                }
            }

            if($message) $exception .= " - {$message}";
            if($errors) $exception .= " - {$errors}";

            throw new Exception($exception);
        }
    }
}