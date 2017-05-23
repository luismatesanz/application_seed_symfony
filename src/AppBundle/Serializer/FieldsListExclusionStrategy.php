<?php

namespace AppBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class FieldsListExclusionStrategy implements ExclusionStrategyInterface
{
    private $fields = array();
    private $parentFields = array();
    private $parentArrays = 0;
    private $depthLast = 1;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
        $this->depthLast = 1;
        $this->parentArrays = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext) : bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext) : bool
    {
        if (empty($this->fields)) {
            return false;
        }

        if ( $this->depthLast-$navigatorContext->getDepth() > 1 && $navigatorContext->getDepth() > 1 ) $this->parentArrays--;
        if ( $navigatorContext->getDepth() === 1 )  $this->parentArrays = 0;

        $depth = $navigatorContext->getDepth()-$this->parentArrays;

        // DELETE FIELDS ARRAY
        if($this->depthLast > $depth || $depth <= 1) {
            for ( $i = $this->depthLast; $i >= $depth; $i-- ) {
                unset($this->parentFields[$i-1]);
            }
        }

        $nameProperty = $property->serializedName ?: $property->name;

        $vistingSet=$navigatorContext->getVisitingSet();
        //iterate over object to get last object
        foreach ($vistingSet as $v){
            $currentObject=$v;
        }

        $propertyValue = $property->getValue($currentObject);
        $relation = $this->checkRelation($propertyValue);

        if ( sizeof($this->parentFields) > 0 ) {
            $name = implode('.',$this->parentFields).".".$nameProperty;
        } else {
            $name = $nameProperty;
        }

        $exist = false;

        if ($depth >= 1) {
            foreach ($this->fields as $field){
                $findValues = explode('.', $field);
                $values = explode('.', $name);
                $valuesDepth = array_slice($values, 0, $depth);
                $findValuesDepth = array_slice($findValues, 0, $depth);
                if(serialize($valuesDepth) === serialize($findValuesDepth)){
                    $exist = true;
                    if ( $relation ) {
                        if (($propertyValue instanceof PersistentCollection || $propertyValue instanceof ArrayCollection ) && sizeof($propertyValue) > 0 ) {
                            $this->parentArrays++;
                        }
                        $index = $depth-1;
                        $this->parentFields[$index] = $nameProperty;
                    }
                    break;
                }
            }

        }
        $this->depthLast = $navigatorContext->getDepth();

        return !$exist;
    }

    private function checkRelation($propertyValue) : bool
    {
        if ($propertyValue instanceof Proxy){
            // skip not loaded one association
            if ($propertyValue->__isInitialized__){
                return true;
            }
        }
        if ($propertyValue instanceof PersistentCollection){
            return true;
        }
        if ($propertyValue instanceof ArrayCollection ) {
            return true;
        }
        if ($propertyValue instanceof Collection ) {
            return true;
        }

        if (gettype($propertyValue) === "array") {
            return true;
        }

        if (gettype($propertyValue) === "object") {
            return true;
        }

        return false;
    }
}