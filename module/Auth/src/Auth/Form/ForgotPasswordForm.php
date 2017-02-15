<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class ForgotPasswordForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-without-legend');

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'required' => true,
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'Enter Email'
            ),
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        // Todo : add Csrf
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Csrf',
//            'name' => 'forgotPasswordCsrf',
//            'options' => array(
//                'csrf_options' => array(
//                    'timeout' => 3600
//                )
//            )
//        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-primary'
            ),
            'options' => array(
                'label' => 'Send',
            )
        ));
    }
}
