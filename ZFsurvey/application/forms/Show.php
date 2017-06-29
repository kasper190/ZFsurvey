<?php

class Application_Form_Show extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => $controller, 'action' => 'survey'
        ));

        $this->setAction($url);    
        $this->addElement('submit', 'submit', array(
            'label' => 'Wyślij',
            'style' => 'margin-left: 550px;',
            'order' => 1000
        ));
    }
    public function addQuestion($id, $label, $type, $required, $options = null)
    {
        $asterisk = '';
        if( $required ) {
            $asterisk = ' *';
        }
        if ( $type == 'form' ) {    
            $this->addElement('textarea', $id, array(
                'required'   => $required,
                'label'      => "$label $asterisk",
                'class'      => 'textarea',
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', true, array('min' => 1, 'max' => 2000)))
            ));
            $this->$id->addDecorators(array(
                'ViewHelper',
                'Errors',
                array('HtmlTag', array('class' => 'answer')),
                array('Label', array('class' => 'question')),
            ));
            $this->$id->getValidator('NotEmpty')->setMessages(array(
                Zend_Validate_NotEmpty::IS_EMPTY => "Pytanie jest obowiązkowe"
            ));
            $this->$id->getValidator('StringLength')->setMessages(array(
                Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
                Zend_Validate_StringLength::TOO_LONG  => "Odpowiedź jest zbyt długa",
            ));
        }
        if ( $type == 'radio' ||  $type == 'checkbox' || $type == 'select') {
            if( $type == 'checkbox') {
                $type = 'multiCheckbox';
            }
            $Answer = new Application_Model_DbTable_Answer();
            $sql = 'select answer_id, answer from answer where question_id = ?';
            $answers = $Answer->getAdapter()->fetchPairs($sql, $id);
            
            $this->addElement($type, $id, array(
                'required'      => $required,
                'label'         => "$label $asterisk",
                'multiOptions'  => $answers,
            ));
            $this->$id->addValidator('NotEmpty', $required,
                array('messages' => array('isEmpty' => 'Pytanie jest obowiązkowe'))
            );
            $this->$id->addDecorators(array(
                'ViewHelper',
                'Errors',
                array('HtmlTag', array('class' => 'answer')),
                array('Label', array('class' => 'question')),
            )); 
        }
    }
}

