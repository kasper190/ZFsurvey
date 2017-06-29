<?php

class Application_Model_DbTable_Survey extends Zend_Db_Table_Abstract
{
    protected $_name = 'survey';
    protected $_dependentTables = array('Application_Model_DbTable_Question');
    protected $referenceMap = array(
        'User' => array(
            'columns'       => array('user_id'),
            'refTableClass' => 'Application_Model_DbTable_User',
            'reTableColumns'=> array('user_id') 
        )
    );
}

