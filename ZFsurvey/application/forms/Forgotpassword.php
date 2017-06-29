<?php

class Application_Form_Forgotpassword extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => 'auth', 'action' => 'forgotpassword'
        ));

        $this->setAction($url);
        $this->addElement('text', 'username', array(
            'label'      => 'Login:',
            'class'      => 'form authform',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true)
            )
        ));
        $this->username->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));

        $this->addElement('text', 'email', array(
            'label'      => 'E-mail:',
            'class'      => 'form authform',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true)
            )
        ));
        $this->email->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));
        $this->email->addValidator(new My_Validate_User());

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Zresetuj hasło',
            'class'  => 'button',
            'style'  => 'margin-left: 96px;'
        ));
    }
}

