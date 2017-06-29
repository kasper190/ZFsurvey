<?php

class Application_Form_QuestionType extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->addElement('select', 'questiontype', array(
            'multiOptions' => array(
                'message'  => 'WYBIERZ TYP PYTANIA',
                'radio'    => 'Pytanie jednokrotnego wyboru',
                'checkbox' => 'Pytanie wielokrotnego wyboru',
                'select'   => 'Lista rozwijana',
                'form'     => 'Pytanie otwarte'
            ),
            'class' => 'select'
        ));
    }
}

