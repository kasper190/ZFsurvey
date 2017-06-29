<?php

class Application_Form_Question extends Zend_Form
{	
    public function init()
    {
        $this->addElement('textarea', 'question', array(
            'required'   => true,
            'label'      => 'Treść pytania:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; width: 380px; height: 70px;',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 3, 'max' => 2000))
            )
        ));
        $this->question->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Treść pytania jest wymagana"
        ));
        $this->question->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_LONG  => "Treść pytania jest zbyt długa",
        ));
        
      
        $this->addElement('hidden', 'id', array(
            'value' => 1
        ));
        
        $this->addElement('text', 'answer', array(
            'required'   => true,
            'label'      => 'Odpowiedzi:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; width: 380px;',
            'filters'    => array('StringTrim'),
            'order'      => 2,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 1, 'max' => 500))
            )
        ));
        $this->answer->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pierwsza odpowiedź jest wymagana"
        ));
        $this->answer->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_SHORT => "Odpowiedź jest zbyt krótka",
            Zend_Validate_StringLength::TOO_LONG  => "Odpowiedź jest zbyt długa",
        ));           
    
        $this->addElement('button', 'addElement', array(
            'label' => 'Dodaj',
            'class' => 'button',
            'style' => 'float: left; margin-right: 10px; width: 60px; text-align: center;',
            'order' => 91
        ));

        $this->addElement('button', 'removeElement', array(
            'label' => 'Usuń',
            'class' => 'button',
            'style' => 'width: 60px; margin-bottom: 20px; text-align: center;',
            'order' => 92
        ));
        $this->removeElement->removeDecorator('DtDdWrapper');
        
        $this->addElement('checkbox', 'required', array(
            'label'          => 'Pytanie obowiązkowe',
            'uncheckedvalue' => false,
            'checkedvalue'   => true,
            'style'          => 'float: left;',
            'order'          => 93
        ));
        $this->getElement('required')->addDecorator('Label', array(
            'placement' => 'APPEND',
            'style'     => 'float: left; margin-top: 1px; margin-left: 7px;'
        ));
        
        $this->addElement('hidden', 'qtype', array(
	    'filters' => array('StringTrim'),
	    'order'   => 94
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Dodaj pytanie',
            'class' => 'button',
            'style' => 'margin-left: 281px; margin-bottom: 26px;',
            'order' => 95
        ));         
    }
    public function addNewAnswer($name, $value, $order)
    {
        $this->addElement('text', $name, array(
            'required' => false,
            'class'    => 'form',
            'style'    => 'width: 380px;',
            'value'    => $value,
            'order'    => $order
        ));
    }
    public function addfirstAnswer()
    {
        $this->addElement('text', 'answer', array(
            'required'   => true,
            'label'      => 'Odpowiedzi:',
            'class'      => 'form',
            'style'      => 'margin-top: 10px; width: 380px;',
            'filters'    => array('StringTrim'),
            'order'      => 2,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array('min' => 1, 'max' => 500))
            )
        ));
        $this->answer->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pierwsza odpowiedź jest wymagana"
        ));
        $this->answer->getValidator('StringLength')->setMessages(array(
            Zend_Validate_StringLength::INVALID   => "Niepoprawny napis",
            Zend_Validate_StringLength::TOO_SHORT => "Odpowiedź jest zbyt krótka",
            Zend_Validate_StringLength::TOO_LONG  => "Odpowiedź jest zbyt długa",
        ));
    }
    public function addAnswer($obj)
    {
        $question_id = $obj['question_id'];
        $Answer = new Application_Model_DbTable_Answer();
        $select = $Answer->select()->where('question_id = ?', $question_id);   
        
        foreach( $Answer->fetchAll($select) as $i => $answer ) {
            if( $i == 0 ) { 
                $this->answer->setValue($answer['answer']);
            } else {
                $this->addNewAnswer("newAnswer$i", $answer['answer'], $i+2);
            }
            $this->id->setValue($i+1);
        }
    }
}

