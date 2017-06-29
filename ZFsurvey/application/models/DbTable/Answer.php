<?php

class Application_Model_DbTable_Answer extends Zend_Db_Table_Abstract
{
    protected $_name = 'answer';
    protected $_dependentTables = array('Application_Model_DbTable_Response');
    protected $referenceMap = array(
        'Question' => array(
            'columns'       => array('question_id'),
            'refTableClass' => 'Application_Model_DbTable_Question',
            'reTableColumns'=> array('question_id') 
        )
    );
}

