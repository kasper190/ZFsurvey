<?php

class My_Validate_User extends Zend_Validate_Abstract
{
    const NOT_FOUND = 'notFound';
    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Brak użytkownika o podanym loginie i adresie email'
    );

    public function isValid($email, $username = null)
    {
        $this->_setValue($email);
        if ( is_array($username) && isset($username['username']) ) {
            $User = new Application_Model_DbTable_User();
            $select = $User->select()->where('username = ?', $username['username'])->where('email = ?', $email);
            $u = $User->fetchRow($select);
            if ($u) {
                return true;
            }
        }
        
        $this->_error(self::NOT_FOUND);
        return false;
    }
}