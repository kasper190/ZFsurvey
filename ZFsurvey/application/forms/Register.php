<?php

class Application_Form_Register extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $view = Zend_Layout::getMvcInstance()->getView();
        $url = $view->url(array(
            'controller' => 'auth', 'action' => 'register'
        ));
        $this->setAction($url);

        $this->addElement('text', 'username', array(
            'label'      => 'Login:',
            'class'      => 'form authform',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('Db_NoRecordExists', true, array('table' => 'user', 'field' => 'username')),
                array('NotEmpty', true)
        )));
        $this->username->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));

        $this->addElement('text', 'email', array(
            'label'      => 'E-mail:',
            'class'      => 'form authform',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            )
        ));
        $this->email->getValidator('NotEmpty')->setMessages(array(
            Zend_Validate_NotEmpty::IS_EMPTY => "Pole obowiązkowe"
        ));
        $this->email->getValidator('EmailAddress')->setMessage("Niepoprawny adres e-mail");

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

        $this->addElement('password', 'password2', array(
            'label'      => 'Powtórz hasło:',
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
            'label'  => 'Rejestruj',
            'class'  => 'button',
            'style'  => 'margin-left: 132px;'
        ));
    }
}
