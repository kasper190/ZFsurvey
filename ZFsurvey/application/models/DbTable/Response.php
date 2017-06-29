<?php

class Application_Model_DbTable_Response extends Zend_Db_Table_Abstract
{
    protected $_name = 'response';   
    protected $referenceMap = array(
        'Answer' => array(
            'columns'       => array('answer_id'),
            'refTableClass' => 'Application_Model_DbTable_Answer',
            'reTableColumns'=> array('answer_id') 
        ),
        'Respondent' => array(
            'columns'       => array('respondent_id'),
            'refTableClass' => 'Application_Model_DbTable_Respondent',
            'reTableColumns'=> array('respondent_id') 
        )
    );
}

