<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class NewPasswordForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-without-legend');

        $this->add(array(
            'name' => 'token',
            'type' => 'hidden',
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'required' => true,
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter Password'
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        $this->add(array(
            'name' => 're_password',
            'type' => 'password',
            'required' => true,
            'attributes' => array(
                'id' => 're-password',
                'class' => 'form-control',
                'placeholder' => 'Enter Re-Password'
            ),
            'options' => array(
                'label' => 'Re-Password',
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
