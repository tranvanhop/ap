<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class ContactFilter extends InputFilter
{

    public function __construct()
    {
        $isEmpty = \Zend\Validator\NotEmpty::IS_EMPTY;

        $this->add(array(
            'name' => 'name',
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
                            $isEmpty => 'Name can not be empty or too short.'
                        ),
                        'min' => 1,
                        'max' => 128
                    ),
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'email',
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
                            $isEmpty => 'Email can not be empty.'
                        ),
                        'min' => 3,
                        'max' => 128
                    ),
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'message',
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
                            $isEmpty => 'Message can not be empty or too short.'
                        ),
                        'min' => 1,
                        'max' => 1024
                    )
                )
            )
        ));
    }
}