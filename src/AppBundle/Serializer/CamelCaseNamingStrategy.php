<?php

namespace AppBundle\Serializer;

use JMS\Serializer\Metadata\PropertyMetadata;

class CamelCaseNamingStrategy extends \JMS\Serializer\Naming\CamelCaseNamingStrategy
{
    public function translateName(PropertyMetadata $property) : string
    {
        $property = parent::translateName($property);
        return lcfirst($property);
    }
}
