<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class CommentForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
            'attributes' => array(
                'id' => 'id'
            )
        ));

        $this->add(array(
            'type' => 'hidden',
            'name' => 'post_id',
            'attributes' => array(
                'id' => 'post_id'
            )
        ));

        $this->add(array(
            'type' => 'hidden',
            'name' => 'user_id',
            'attributes' => array(
                'id' => 'user_id'
            )
        ));

        $this->add(array(
            'type' => 'hidden',
            'name' => 'comment_id',
            'attributes' => array(
                'id' => 'comment_id'
            )
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => ''
            ),
            'options' => array(
                'label' => 'Name'
            )
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => ''
            ),
            'options' => array(
                'label' => 'Email',
            )
        ));
        $this->add(array(
            'name' => 'message',
            'type' => 'textarea',
            'attributes' => array(
                'id' => 'message',
                'class' => 'form-control',
                'placeholder' => 'Message',
                'rows' => 8
            ),
            'options' => array(
                'label' => 'Message',
                'label_attributes' => array(
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
                'label' => 'Post a comment',
            )
        ));
    }
}
