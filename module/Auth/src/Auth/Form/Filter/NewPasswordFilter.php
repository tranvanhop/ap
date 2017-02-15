<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;
use Auth\Utility\Confirm;

class NewPasswordFilter extends InputFilter
{

    public function __construct()
    {
        $isEmpty = \Zend\Validator\NotEmpty::IS_EMPTY;
        
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Password can not be empty.'
                        )
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 're_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Application\Utility\Confirm',
                    'options' => array(
                        'field' => 'password'
                    )
                )
            )
        ));
    }
}