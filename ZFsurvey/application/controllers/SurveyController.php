<?php

class SurveyController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return $this->_helper->redirector(
                'index',
                'auth',
                'default'
            );
        }
        
        $survey_id = $this->getRequest()->getParam('survey_id');      
        if($survey_id) {
            $User = new Application_Model_DbTable_User();
            $select = $User->select()->where('username = ?', $auth->getIdentity());
            $u = $User->fetchRow($select);

            $Survey = new Application_Model_DbTable_Survey();
            $select = $Survey->select()->where('survey_id = ?', $survey_id)
                                       ->where('user_id = ?', $u['user_id']);
            if(!$row = $Survey->fetchrow($select)) {
                $this->view->identity = $auth->getIdentity();
                $this->_helper->viewRenderer('accessdenied');
            }  
        }      
        $this->view->identity = $auth->getIdentity();
    }
    public function indexAction()
    {
        $User = new Application_Model_DbTable_User();
        $auth = Zend_Auth::getInstance()->getIdentity();
        $select = $User->select()->where('username = ?', $auth);
        $u = $User->fetchRow($select);
        
        $Survey = new Application_Model_DbTable_Survey();       
        $select2 = $Survey->select()->where('user_id = ?', $u['user_id']);
        $this->view->surveys = $Survey->fetchAll($select2);       
    }
    public function addsurveyformAction()
    {
        $this->view->form = new Application_Form_Survey();
    }
    public function addsurveyAction()
    {
        $this->_helper->viewRenderer('addsurveyform');
        $form = new Application_Form_Survey();     
        
        $User = new Application_Model_DbTable_User();
        $auth = Zend_Auth::getInstance()->getIdentity();
        $select = $User->select()->where('username = ?', $auth);
        $u = $User->fetchRow($select);

        if ($form->isValid($this->getRequest()->getPost())) {
            $Survey = new Application_Model_DbTable_Survey();          
            $data = array(
                'title' => $form->getValue('title'),
                'start_msg' => $form->getValue('start_msg'),
                'end_msg' => $form->getValue('end_msg'),
                'date' => date("Y-m-d"),
                'ip_filter'  => $form->getValue('ip_filter'),
                'status' => false,
                'user_id' => $u['user_id']
            );
            $survey_id = $Survey->createRow($data)->save();
            
            $Template = new Application_Model_DbTable_Template(); 
            $data = array(
                'logo' => '',
                'radius' => true,
                'titlefont' => '#000000',
                'mainfont' => '#000000',
                'background'  => '#E0E0E0',
                'frame' => '#368DE3',
                'main' => '#FFFFFF',
                'survey_id' => $survey_id
            );
            $Template->createRow($data)->save();
            
            return $this->_helper->redirector(
                'template', 'survey', null, array('survey_id' => $survey_id, 'case' => 'create')
            );
        }
        $this->view->form = $form;
    }
    public function editsurveyAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $Survey = new Application_Model_DbTable_Survey();
        $obj = $Survey->find($survey_id)->current();
        if (!$obj) {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        }
        $this->view->survey_id = $survey_id;
        $this->view->form = new Application_Form_Survey();
        $this->view->form->populate($obj->toArray());
        $url = $this->view->url(array('action' => 'updatesurvey', 'survey_id' => $survey_id));
        $this->view->form->setAction($url);
        $this->view->object = $obj;
    }
    public function updatesurveyAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $Survey = new Application_Model_DbTable_Survey();
        $obj = $Survey->find($survey_id)->current();
        if (!$obj) {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        }

        if ($this->getRequest()->isPost()) {
            $form = new Application_Form_Survey();
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $form->getValues();
                $obj->setFromArray($data);
                $obj->save();
                return $this->_helper->redirector(
                    'edit', 'survey', null, array('survey_id' => $survey_id)
                );
            }
            $this->view->form = $form;
        } else {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        } 
    }
    public function deletesurveyAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('deletesurvey', 'json')->initContext();
        $survey_id = $this->_getParam('survey_id', null);

        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->getAdapter()->quoteInto('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        
        $Template = new Application_Model_DbTable_Template();
        $select2 = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select2);
        
        $logo = realpath(APPLICATION_PATH . '/../public/uploads/' . $survey_id . '.' . $t['logo']. '');
        unlink($logo);
        $Survey->delete($select);        
        
        return $this->_helper->redirector(
            'index',
            'survey',
            'default'
        );
    }
    public function templateAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $case = $this->getRequest()->getParam('case');
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $obj = $Template->fetchRow($select);

        $this->view->form = new Application_Form_Template();
        $this->view->form->populate($obj->toArray());
        
        if( $case == 'create') {
            $url = $this->view->url(array('action' => 'updatetemplate', 'controller' => 'survey', 'survey_id' => $survey_id, 'case' => 'create'));
        }
        if( $case == 'edit' ) {
            $url = $this->view->url(array('action' => 'updatetemplate', 'controller' => 'survey', 'survey_id' => $survey_id, 'case' => 'edit'));
        }

        $this->view->form->setAction($url);
        $this->view->object = $obj;
        $this->view->survey_id = $survey_id;        
    } 
    public function updatetemplateAction() 
    {
        
        $survey_id = $this->getRequest()->getParam('survey_id');
        $case = $this->getRequest()->getParam('case');
        $this->_helper->viewRenderer('template');
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $obj = $Template->fetchRow($select);
        if (!$obj) {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        }

        if ($this->getRequest()->isPost()) {
            $form = new Application_Form_Template();
            if ($form->isValid($this->getRequest()->getPost())) {
                $name = pathinfo($form->filename->getFileName());
                $newAnswer = $survey_id . '.' . $name['extension'];
                $form->filename->addFilter('Rename', $newAnswer);
                $data = $form->getValues();
                
                if( $name['extension'] ) {
                    $update = $Template->update(
                        array(
                            'logo' => $name['extension']
                        ),
                        array(
                            'survey_id = ?' => $survey_id
                        )
                    );
                }
                
                $fld = realpath(APPLICATION_PATH . '/../public/uploads');
                My_ScaleImage::scale($fld . '/' . $survey_id . '.' . $name['extension']);
                
                $update2 = $Template->update(
                    array(
                        'radius' => $form->getValue('radius'),
                        'titlefont' => $form->getValue('titlefont'),
                        'mainfont' => $form->getValue('mainfont'),
                        'background'  => $form->getValue('background'),
                        'frame' => $form->getValue('frame'),
                        'main' => $form->getValue('main')
                    ),
                    array(
                        'survey_id = ?' => $survey_id
                    )
                );
                
                if( $case == 'create' ) {
                    return $this->_helper->redirector(
                        'addquestion', 'survey', null, array('survey_id' => $survey_id, 'case' => 'create')
                    );
                }
                if( $case == 'edit' ) {
                    return $this->_helper->redirector(
                        'edit', 'survey', null, array('survey_id' => $survey_id)
                    );
                }
            }
            $this->view->form = $form;
        } else {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        } 
    }
    public function deletelogoAction()
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        
        $logo = realpath(APPLICATION_PATH . '/../public/uploads/' . $survey_id . '.' . $t['logo']. '');
        unlink($logo);
        $update = $Template->update(array('logo' => 0), array('survey_id = ?' => $survey_id));
                
        return $this->_helper->redirector(
            'template', 'survey', null, array('survey_id' => $survey_id)
        );
    }
    public function addquestionAction() 
    {
        $this->_helper->viewRenderer('editquestion');
        $survey_id = $this->getRequest()->getParam('survey_id');
        $case = $this->getRequest()->getParam('case');
        $this->view->case = $case;
        $id = $this->getRequest()->getParam('id');
        
        $form = new Application_Form_Question();
        $form3 = new Application_Form_QuestionType();

        if (!$this->getRequest()->isPost()) {
            $this->view->survey_id = $survey_id;
            $this->view->form = $form;
            $this->view->form3 = $form3;
            return;
        }
        
        $data = $form->getValidValues($_POST);
        if( $data['qtype'] != "form" ) {
            $form->addfirstAnswer();
            for( $i=1 ; $i < $id ; $i++ ) {
                $form->addNewAnswer("newAnswer$i", null, $i + 2);
            }
        } else {
            $form->answer->setRequired(false);
        }
        
        if (!$form->isValid($_POST)) {

            $this->view->survey_id = $survey_id;
            $this->view->form = $form;
            $form3->setDefault('questiontype', $form->getValue('qtype'));
            $this->view->form3 = $form3;
            return;
        }
                
        $Question = new Application_Model_DbTable_Question();
        if($form->getValue('question')) {
            $data = array(
                'question'  => $form->getValue('question'),
                'type'      => $form->getValue('qtype'),
		'required'  => $form->getValue('required'),
                'survey_id' =>  $survey_id
            );
            $question_id = $Question->createRow($data)->save();
        }
        
        if( $form->getValue('qtype') != "form" ) {
            $Answer = new Application_Model_DbTable_Answer();
            if($form->getValue('answer')) {
                $data = array(
                    'answer' => $form->getValue('answer'),
                    'question_id' => $question_id
                );
                $Answer->createRow($data)->save();
            }
            for( $i=1 ; $form->getValue("newAnswer$i") ; $i++ ) {
                $data = array(
                    'answer' => $form->getValue("newAnswer$i"),
                    'question_id' => $question_id
                );
                $Answer->createRow($data)->save();
            }
        } else {
            $form->answer->setRequired(false);
        }
        
        if( $case == 'create' ) {
            return $this->_helper->redirector(
                'addquestion', 'survey', null, array('survey_id' => $survey_id, 'case' => 'create')
            );
        }
        if( $case == 'edit' ) {
            return $this->_helper->redirector(
                'questionlist', 'survey', null, array('survey_id' => $survey_id)
            );
        }
    }
    public function newanswerAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('newanswer', 'html')->initContext();

        $id = $this->_getParam('id', null);
        $element = new Zend_Form_Element_Text("newAnswer$id");
        $this->view->newanswer = $element->__toString();
    }
    public function editquestionAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $question_id = $this->getRequest()->getParam('question_id');
        $Question = new Application_Model_DbTable_Question();
        $obj = $Question->find($question_id)->current();
        if (!$obj) {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        }
     
        $form3 = new Application_Form_QuestionType();
        $form3->setDefault('questiontype', $obj['type']);
        $this->view->form3 = $form3;

        $form = new Application_Form_Question();
        $this->view->form = $form;
        $this->view->form->setDefault('qtype', $obj['type']);
        
        if( $obj['type'] != "form" ) {
            $this->view->form->addAnswer($obj);
        }

        $this->view->form->populate($obj->toArray());
        $url = $this->view->url(array('action' => 'updatequestion', 'survey_id' => $survey_id, 'question_id' => $question_id));
        $this->view->form->setAction($url);
        $this->view->object = $obj;
        $this->view->survey_id = $survey_id;            
    }
    public function updatequestionAction() 
    {
        $this->_helper->viewRenderer('editquestion');
        $survey_id = $this->getRequest()->getParam('survey_id');
        $question_id = $this->getRequest()->getParam('question_id');
        $id = $this->getRequest()->getParam('id');
        $Question = new Application_Model_DbTable_Question();   

        $obj = $Question->find($question_id)->current();
        if (!$obj) {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        }
        
        if ($this->getRequest()->isPost()) {
            $form = new Application_Form_Question();
            $form->addAnswer($obj);
            $form3 = new Application_Form_QuestionType();
            if( $form->getValue('qtype') != "form" ) {
                $form->addfirstAnswer();
                for( $i=1 ; $i < $id ; $i++ ){
                    $form->addNewAnswer("newAnswer$i", null, $i + 2);
                }
            } else {
                $form->answer->setRequired(false);
            }

            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $form->getValues();
                $obj->setFromArray($data);
                $obj->save();
                $update = $Question->update(array('type' => $data['qtype']), array('question_id = ?' => $question_id));

                if( $data['qtype'] != "form" ) {
                    $Answer = new Application_Model_DbTable_Answer();
                    $select = $Answer->select()->where('question_id = ?', $question_id);
                    $c = 0;
                    foreach( $Answer->fetchAll($select) as $i => $answer ) {
                        if( $i == 0 ) {
                            $update = $Answer->update(
                                array(
                                    'answer' => $form->getValue('answer')
                                ),
                                array(
                                    'answer_id = ?' => $answer['answer_id']
                                )
                            );
                        } else {
                            if(!$form->getValue("newAnswer$i")) {
                                $select = $Answer->getAdapter()->quoteInto('answer_id = ?', $answer['answer_id']);
                                $Answer->delete($select);
                            }
                            $update = $Answer->update(
                                array(
                                    'answer' => $form->getValue("newAnswer$i")
                                ),
                                array(
                                    'answer_id = ?' => $answer['answer_id']
                                )
                            );
                        }
                        $c = $i;
                    }

                    if($obj['type'] == 'form') {
                        if($form->getValue("answer")) {
                            $data = array(
                                'answer' => $form->getValue("answer"),
                                'question_id' => $question_id
                            );
                            $Answer->createRow($data)->save();
                        }
                    }
                    for ( ++$c ; $c < $id ; $c++ ) {
                        if($form->getValue("newAnswer$c")) {
                            $data = array(
                                'answer' => $form->getValue("newAnswer$c"),
                                'question_id' => $question_id
                            );
                            $Answer->createRow($data)->save();
                        }
                    }                  
                } else {
                    $Answer = new Application_Model_DbTable_Answer();
                    $select = $Answer->getAdapter()->quoteInto('question_id = ?', $question_id);
                    $Answer->delete($select);
                }
                return $this->_helper->redirector(
                    'questionlist', 'survey', null, array('survey_id' => $survey_id)
                );
            }

            $this->view->survey_id = $survey_id;
            $this->view->form = $form;
            $form3->setDefault('questiontype', $form->getValue('qtype'));
            $this->view->form3 = $form3;
        } else {
            throw new Zend_Controller_Action_Exception('Błędny adres!', 404);
        } 
    }
    public function deletequestionAction() 
    {
        $this->_helper->viewRenderer('edit');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('deletesurvey', 'json')->initContext();

        $survey_id = $this->getRequest()->getParam('survey_id');
        $question_id = $this->_getParam('question_id', null);

        $Question = new Application_Model_DbTable_Question();
        $select = $Question->getAdapter()->quoteInto('question_id = ?', $question_id);
        $Question->delete($select);
        
        return $this->_helper->redirector(
                'questionlist', 'survey', null, array('survey_id' => $survey_id)
        );
    }
    public function questionlistAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $this->view->survey_id = $survey_id;
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $this->view->question = $Question->fetchAll($select);    
    }
    public function editAction() 
    { 
        $survey_id = $this->getRequest()->getParam('survey_id');
        $this->view->survey_id = $survey_id;
        
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        $this->view->status = $s['status'];
        
    }    
    public function addressAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $Survey = new Application_Model_DbTable_Survey();
        $status = $Survey->update(
                array(
                    'status' => true
                ),
                array(
                    'survey_id = ?' => $survey_id
                )
        );
        $this->view->survey_id = $survey_id;
    }
    public function changestatusAction() 
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $action = $this->getRequest()->getParam('actionname');
        
        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        $status = $s['status'];
        
        if ( $s['status'] ) {
            $change = $Survey->update(
                array(
                    'status' => false
                ),
                array(
                    'survey_id = ?' => $survey_id
                )
            );
        } else {
            $change = $Survey->update(
                array(
                    'status' => true
                ),
                array(
                    'survey_id = ?' => $survey_id
                )
            );
        }
        
        if( $action == 'index' ) {
            return $this->_helper->redirector(
               'index', 'survey', null
            );
        }
        if( $action == 'edit' && $status == true) {
            return $this->_helper->redirector(
                'edit', 'survey', null, array('survey_id' => $survey_id)
            );
        }
        if( $action == 'edit' && $status == false) {
            return $this->_helper->redirector(
                'address', 'survey', null, array('survey_id' => $survey_id)
            );
        } 
    }       
}
