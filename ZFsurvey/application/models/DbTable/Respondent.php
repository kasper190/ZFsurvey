<?php

class Application_Model_DbTable_Respondent extends Zend_Db_Table_Abstract
{
    protected $_name = 'respondent';
    protected $_dependentTables = array('Application_Model_DbTable_Response');
}

