<?php

class Zend_View_Helper_GetResponse extends Zend_View_Helper_Abstract
{
    public function getResponse($question_id, $answer_id, $limit = null)
    {
        if ($answer_id == null) {
            $Response = new Application_Model_DbTable_Response();
            $select = $Response->select()->where('question_id = ?', $question_id);
            $count = $Response->fetchAll($select)->count();
            $this->view->count = $count;
            
            $select = $Response->select()->where('question_id = ?', $question_id)->limit($limit);
            $this->view->response = $Response->fetchAll($select);  
        } else {
            $Response = new Application_Model_DbTable_Response();
            $select = $Response->select()->where('answer_id = ?', $answer_id);
            $count = $Response->fetchAll($select)->count();
            $this->view->count = $count;

            $select = $Response->select()->where('question_id = ?', $question_id);
            $all = $Response->fetchAll($select)->count();
            if ($all) {
                $this->view->procent = round(($count / $all * 100), 2);
            } else {
                $this->view->procent = 0;
            }
        }
    }
}

