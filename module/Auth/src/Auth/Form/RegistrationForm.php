<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class RegistrationForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
            'attributes' => array(
                'id' => 'id'
            )
        ));
        $this->add(array(
            'name' => 'first_name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'first_name',
                'class' => 'form-control',
                'placeholder' => 'Enter First Name'
            ),
            'options' => array(
                'label' => 'First Name',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'last_name',
                'class' => 'form-control',
                'placeholder' => 'Enter First Name'
            ),
            'options' => array(
                'label' => 'Last Name',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'Enter Email',
                'onkeyup' => 'isExistsEmail()'
            ),
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'text',
            'attributes' => array(
                'id' => 'phone',
                'class' => 'form-control',
                'placeholder' => 'Enter Phone',
                'onkeyup' => 'isExistsPhone()'
            ),
            'options' => array(
                'label' => 'Phone',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'avatar',
            'type' => 'file',
            'attributes' => array(
                'id' => 'file',
                'class' => 'form-control',
                'onkeyup' => 'isExistsPhone()'
            ),
            'options' => array(
                'label' => 'Avatar',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
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
            'name' => 'confirm_password',
            'type' => 'password',
            'attributes' => array(
                'id' => 'confirm_password',
                'class' => 'form-control',
                'placeholder' => 'Enter Confirm Password'
            ),
            'options' => array(
                'label' => 'Confirm Password',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));
        // Todo : add Csrf
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Csrf',
//            'name' => 'registrationCsrf',
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
                'label' => 'Create an account',
            )
        ));
    }
}
