<?php

class Application_Model_DbTable_Template extends Zend_Db_Table_Abstract
{
    protected $_name = 'template';
    protected $referenceMap = array(
        'Survey' => array(
            'columns'       => array('survey_id'),
            'refTableClass' => 'Application_Model_DbTable_Survey',
            'reTableColumns'=> array('survey_id') 
        )
    );
}

