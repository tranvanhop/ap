<?php

namespace Auth\Utility;

use Zend\Validator\AbstractValidator;

class Confirm extends AbstractValidator
{
    const DIFFERENT_FROM = 'DIFFERENT_FROM';

    protected $messageTemplates = array(
        self::DIFFERENT_FROM => 'Value different from original one'
    );

    private $field;

    public function __construct(array $options = array())
    {
        if (!isset($options['field'])) {
            throw new Exception\InvalidArgumentException('Field to check missing');
        }
        $this->field = $options['field'];
        parent::__construct($options);
    }

    public function isValid($value, $context = null)
    {
        if (!is_array($context) or !isset($context[$this->field])) {
            throw new Exception\RuntimeException(sprintf('Field "%s" missing in the context', $this->field));
        }
        $this->setValue($value);
        if ($value !== $context[$this->field]) {
            $this->error(self::DIFFERENT_FROM);
            return false;
        }
        return true;
    }

}