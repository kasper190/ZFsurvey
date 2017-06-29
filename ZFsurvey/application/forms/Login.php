<?php

class Application_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => 'auth', 'action' => 'login'
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

        $this->addElement('password', 'password', array(
            'label'      => 'Hasło:',
            'class'      => 'form authform',
            'required'   => true,
            'validators' => array(
                array('NotEmpty', true)
            )
        ));
        $this->password->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Login',
            'class'  => 'button',
            'style'  => 'margin-left: 156px;'
        ));
    }
}
