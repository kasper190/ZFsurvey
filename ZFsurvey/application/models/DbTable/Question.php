<?php

class Application_Model_DbTable_Question extends Zend_Db_Table_Abstract
{
    protected $_name = 'question';
    protected $_dependentTables = array('Application_Model_DbTable_Answer');
    protected $referenceMap = array(
        'Survey' => array(
            'columns'       => array('survey_id'),
            'refTableClass' => 'Application_Model_DbTable_Survey',
            'reTableColumns'=> array('survey_id') 
        )
    );
}

