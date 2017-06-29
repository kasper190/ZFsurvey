<?php

class My_Validate_Password extends Zend_Validate_Abstract
{
    const DONT_MATCH = 'dontMatch';  
    protected $_messageTemplates = array(
        self::DONT_MATCH => 'Podano dwa różne hasła'
    );

    public function isValid($password1, $password2 = null)
    {
        $this->_setValue($password1);
        if ( is_array($password2) && isset($password2['password']) && ($password1 === $password2['password']) ) {
            return true;
        }
        
        $this->_error(self::DONT_MATCH);
        return false;
    }
}
