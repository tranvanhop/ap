<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-without-legend');

        $this->add(array(
            'name' => 'user_name',
            'type' => 'text',
            'required' => true,
            'attributes' => array(
                'id' => 'user_name',
                'class' => 'form-control',
                'placeholder' => 'Nhập tên'
            ),
            'options' => array(
                'label' => 'Tên gì',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'text',
            'required' => true,
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Ví dụ : 01/02/1993'
            ),
            'options' => array(
                'label' => 'Ngày sinh',
                'label_attributes' => array(
                    'class'  => 'col-lg-4 control-label'
                ),
            )
        ));

        // Todo : add Csrf
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Csrf',
//            'name' => 'loginCsrf',
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
                'label' => 'Đăng nhập',
            )
        ));
    }
}
