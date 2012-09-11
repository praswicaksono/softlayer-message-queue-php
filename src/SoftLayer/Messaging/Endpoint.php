<?php

class SoftLayer_Messaging_Endpoint
{
    public static function endpointByType($type)
    {
        switch(strtolower($type)) {
            case 'http': return new SoftLayer_Messaging_Endpoint_Http(); break;
            case 'queue': return new SoftLayer_Messaging_Endpoint_Queue(); break;
        }
    }
}
