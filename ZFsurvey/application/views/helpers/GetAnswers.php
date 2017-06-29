<?php

class Zend_View_Helper_GetAnswers extends Zend_View_Helper_Abstract
{
    public function getAnswers($question_id)
    {
        $Answer = new Application_Model_DbTable_Answer();
        $select = $Answer->select()->where('question_id = ?', $question_id);
        $this->view->answer = $Answer->fetchAll($select);
    }
}

