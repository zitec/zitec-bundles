<?php

namespace Zitec\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SettingsAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        // to remove a single route
        $collection->remove('create');
        $collection->remove('delete');
        $collection->add('generate');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'Parameter name', 'attr' => array('readonly' => true)))
            ->add('description', 'text', array('label' => 'Parameter description', 'attr' => array('readonly' => true)))
            ->add('value', 'textarea', array('label' => 'Parameter value'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Parameter name'))
            ->add('value', null, array('label' => 'Parameter value'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Parameter name'))
            ->add('description', null, array('label' => 'Parameter description'))
            ->add('value', null, array('label' => 'Parameter value'));
    }

}
