<?php

class SoftLayer_Http_Adapter_Curl implements SoftLayer_Http_Adapter_Interface
{
    public function call(SoftLayer_Http_Request &$request, SoftLayer_Http_Response &$response)
    {
        $curl = curl_init();

        $headers = array();

        foreach($request->getHeaders() as $header => $value) {
            $headers[] = "{$header}: {$value}";
        }

        $url  = "";
        $url .= $request->getBaseUrl();
        $url .= $request->getPath();
        $url .= "?".http_build_query($request->getParams());

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $body = curl_exec($curl);

        if($body === false) {
            throw new Exception(curl_error($curl));
        }

        $info = curl_getinfo($curl);

        $response->setHeader('Content-Type', $info['content_type']);
        $response->setStatus($info['http_code']);
        $response->setBody($body);

        curl_close($curl);
    }
}
