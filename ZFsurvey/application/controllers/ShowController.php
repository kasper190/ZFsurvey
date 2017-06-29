<?php

class ShowController extends Zend_Controller_Action
{
    public function preDispatch()
    {                
        $survey_id = $this->getRequest()->getParam('id');
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);

        if ( $s['status'] == false ) {
            $this->view->message = 'Ankieta jest nieaktywna';
            $this->_helper->viewRenderer('accessdenied');
        }
        if ( $s['ip_filter'] == true ) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
              $ip=$_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
              $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
              $ip=$_SERVER['REMOTE_ADDR'];
            }
            
            $Respondent = new Application_Model_DbTable_Respondent();
            $select = $Respondent->select()->where('survey_id = ?', $survey_id)->where('ip = ?', $ip);                                
            $r = $Respondent->fetchRow($select);
            
            $action = $this->getRequest()->getActionName();
            if ($r && $action != 'endmsg') {
                $this->view->message = 'Wypełniłeś już ankietę, dziękujemy';
                $this->_helper->viewRenderer('accessdenied');
            }
        }            
    }
    public function init()
    {
        $this -> _helper->layout()->setLayout("survey");
    }
    public function surveyformAction()
    {
        $this -> _helper->layout()->setLayout("survey");
        $survey_id = $this->getRequest()->getParam('id');
        $form = new Application_Form_Show();     
        
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);        
        $this->view->s = $s;
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        $this->view->t = $t;
        $this->view->asterisk = '* Pytanie obowiązkowe';
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $question = $Question->fetchAll($select); 
        
        foreach ($question as $q) {
            $select = $Question->select()->where('question_id = ?', $q['question_id']);
            $q = $Question->fetchRow($select);
            $form->addQuestion( $q['question_id'], $q['question'], $q['type'], $q['required'] );
        }
        
        $this->view->form = $form;
    }
    public function surveyAction()
    {
        $survey_id = $this->getRequest()->getParam('id');
        $this -> _helper->layout()->setLayout("survey");
        $this->_helper->viewRenderer('surveyform');
        $form = new Application_Form_Show();     
        
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        $this->view->s = $s;
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        $this->view->t = $t;
        $this->view->asterisk = '* Pytanie obowiązkowe';
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $question = $Question->fetchAll($select); 
        
        foreach ($question as $q) {
            $select = $Question->select()->where('question_id = ?', $q['question_id']);
            $q = $Question->fetchRow($select);
            $form->addQuestion( $q['question_id'], $q['question'], $q['type'], $q['required'] );
        }
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
                      
        if ($form->isValid($this->getRequest()->getPost())) {
            $Respondent = new Application_Model_DbTable_Respondent();          
            $data = array(
                'ip' => $ip,
                'date' => date("Y-m-d H:i:s"),
                'survey_id' => $survey_id
            );
            $respondent_id = $Respondent->createRow($data)->save();
                                   
            $data = $form->getValues();
            foreach ($data as $q => $a) {
                $select = $Question->select()->where('question_id = ?', $q);
                $question = $Question->fetchRow($select);
                
                if ( $question['type'] == 'checkbox' ) {
                    foreach ($a as $i => $k) {
                        $Answer = new Application_Model_DbTable_Answer();
                        $select = $Answer->select()->where('answer_id = ?', $k);
                        $answer = $Answer->fetchRow($select);
                        $response = $answer['answer'];
                        
                        $Response = new Application_Model_DbTable_Response;
                        $data = array(
                            'response' => $response,
                            'respondent_id' => $respondent_id,
                            'question_id' => $q,
                            'answer_id' => $k
                        );
                        $Response->createRow($data)->save();
                    }
                } else { 
                    if ($question['type'] == 'form') {
                        $response = $a;
                        $a = null;
                    } else {
                        if( $a ) {
                            $Answer = new Application_Model_DbTable_Answer();
                            $select = $Answer->select()->where('answer_id = ?', $a);
                            $answer = $Answer->fetchRow($select);
                            $response = $answer['answer'];
                        } else {
                            $a = null;
                            $response = null;
                        }
                    }
                    $Response = new Application_Model_DbTable_Response;
                    $data = array(
                        'response' => $response,
                        'respondent_id' => $respondent_id,
                        'question_id' => $q,
                        'answer_id' => $a
                    );
                    $Response->createRow($data)->save();
                }    
            }
            
            $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
            if( $controller == 'show' ) {
                return $this->_helper->redirector(
                    'endmsg', 'show', null, array('id' => $survey_id)
                );
            }
            if ( $controller == 'result' ) {
                return $this->_helper->redirector(
                    'index', 'survey', null, array('survey_id' => $survey_id)
                );
            }
        }
        $this->view->form = $form;
    }
    public function startmsgAction()
    {
        $survey_id = $this->getRequest()->getParam('id');
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        if( !$s['start_msg'] ) {
            return $this->_helper->redirector(
                'surveyform', 'show', null, array('id' => $s['survey_id'])
            );
        }
        $this->view->s = $s;
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        $this->view->t = $t;
    }
    public function endmsgAction()
    {
        $survey_id = $this->getRequest()->getParam('id');
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        $this->view->s = $s;
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        $this->view->t = $t;
    }
}

