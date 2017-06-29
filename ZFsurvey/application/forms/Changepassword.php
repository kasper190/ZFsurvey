<?php

class Application_Form_Changepassword extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => 'auth', 'action' => 'changepassword'
        ));

        $this->setAction($url);
        $this->addElement('password', 'password', array(
            'label'      => 'Nowe hasło:',
            'class'      => 'form authform',
            'required'   => true,
            'validators' => array(
                array('NotEmpty', true)
            )
        ));
        $this->password->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));

        $this->addElement('password', 'password2', array(
            'label'      => 'Powtórz nowe hasło:',
            'class'      => 'form authform',
            'required'   => true,
            'validators' => array(
                array('NotEmpty', true)
            )
        ));
        $this->password2->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));
        $this->password2->addValidator(new My_Validate_Password());

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Zmień hasło',
            'class'  => 'button',
            'style'  => 'margin-left: 111px;'
        ));
    }
}

