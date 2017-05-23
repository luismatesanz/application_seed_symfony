<?php
namespace AppBundle\DependencyInjection;

use AppBundle\Serializer\FieldsListExclusionStrategy;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;

class SerializerCustomService
{
    /**
     * Serializer view with fields required
     */
    public function filterFields(View $view, $fields) : View
    {
        $fields = explode(',', $fields);
        $fieldsExists = reset($fields);
        if ($fieldsExists !== '') {
            // CREATE NEW SERIALIZATION CONTEXT
            $context = new Context();
            $context->addExclusionStrategy(
                new FieldsListExclusionStrategy($fields)
            );
            // ADD GROUP FIELDS IN SERIALIZER FOS REST
            $context->setGroups(array('Default', 'Fields'));
            $view->setContext($context);
        } else {
            $context = $view->getContext();
            $context->enableMaxDepth();
            $view->setContext($context);
        }
        return $view;
    }
}
