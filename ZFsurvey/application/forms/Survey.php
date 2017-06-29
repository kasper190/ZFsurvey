<?php

class Application_Form_Survey extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => 'survey', 'action' => 'addsurvey'
        ));

        $this->setAction($url);
        $this->addElement('text', 'title', array(
            'required'   => true,
            'label'      => 'Tytuł ankiety:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; margin-bottom: 20px; width: 450px;',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 3, 'max' => 120))
            )
        ));
        $this->title->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Tytuł nie może być pusty"
        ));
        $this->title->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_SHORT => "Tytuł '%value%' jest zbyt krótki",
            Zend_Validate_StringLength::TOO_LONG  => "Tytuł '%value%' jest zbyt długi",
        ));
             
        $this->addElement('textarea', 'start_msg', array(
            'required'   => false,
            'label'      => 'Wiadomość powitalna:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; margin-bottom: 20px; width: 450px; height: 70px;',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 0, 'max' => 2000))
            )
        ));
        $this->start_msg->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_LONG  => "Wiadomość powitalna jest zbyt długa",
        ));
                
        $this->addElement('textarea', 'end_msg', array(
            'required'   => true,
            'label'      => 'Wiadomość po wypełnieniu:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; margin-bottom: 20px; width: 450px; height: 70px;',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 0, 'max' => 2000))
            )
        ));
        $this->end_msg->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Wiadomość po wypełnieniu nie może być pusta"
        ));
        $this->end_msg->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_LONG  => "Wiadomość po wypełnieniu jest zbyt długa",
        ));
        
        $this->addElement('checkbox', 'ip_filter', array(
            'label'          => 'Filtrowanie adresów IP (1 adres = 1 odpowiedź)',
            'uncheckedvalue' => false,
            'checkedvalue'   => true,
            'style'          => 'float: left;'
        ));
        $this->getElement('ip_filter')->addDecorator('Label', array(
            'placement' => 'APPEND',
            'style'     => 'float: left; margin-top: 1px; margin-left: 7px;'
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Dalej',
            'class'  => 'button',
            'style'  => 'margin-left: 410px; margin-top: 10px;'
        ));
    }
}

