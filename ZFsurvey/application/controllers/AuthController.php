<?php

class AuthController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->form = new Application_Form_Login();
    }
    public function loginAction()
    {
        $this->_helper->viewRenderer('index');
        $form = new Application_Form_Login();
        if ($form->isValid($this->getRequest()->getPost())) {
            $adapter = new Zend_Auth_Adapter_DbTable(
                null,
                'user',
                'username',
                'password',
                'MD5(CONCAT(?, salt))'
            );

            $adapter->setIdentity($form->getValue('username'));
            $adapter->setCredential($form->getValue('password'));
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($adapter);

            if ($result->isValid()) {
                return $this->_helper->redirector(
                    'index', 'survey', 'default'
                );
            }
            $form->password->addError('Błędna próba logowania');
        }
        $this->view->form = $form;
    }
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector(
            'index', 'auth', 'default'
        );
    }
    public function registerformAction()
    {
        $this->view->form = new Application_Form_Register();
    }
    public function registerAction()
    {
        $this->_helper->viewRenderer('registerform');
        $form = new Application_Form_Register();

        if ($form->isValid($this->getRequest()->getPost())) {
            $User = new Application_Model_DbTable_User();
            $salt = md5(time());

            $data = array(
                'username' => $form->getValue('username'),
                'password' => md5($form->getValue('password') . $salt),
                'salt'     => $salt,
                'email'    => $form->getValue('email')
            );

            $User->createRow($data)->save();

            return $this->_helper->redirector(
                'index', 'index', 'default'
            );
        }
        $this->view->form = $form;
    }
    public function forgotpasswordformAction()
    {
        $this->view->form = new Application_Form_Forgotpassword();
    }
    public function forgotpasswordAction()
    {
        $this->_helper->viewRenderer('forgotpasswordform');
        $form = new Application_Form_Forgotpassword();

        if ($form->isValid($this->getRequest()->getPost())) {
            $User = new Application_Model_DbTable_User();
            $select = $User->select()->where('username = ?', $form->getValue('username'));
            $u = $User->fetchRow($select);

            $password = '';
            for ($i = 1; $i <= 5; $i++) {
                $password .= chr(rand(ord('a'), ord('z')));
            }

            $salt = md5(time());
            $u['salt']     = $salt;
            $u['password'] = md5($password . $salt);
            $u->save();
            
            $email = $u['email'];
            $mail = new My_Mail();
            $mail->newPassword($u['username'], $email, $password);

            return $this->_helper->redirector(
                'index', 'index', 'default'
            );
        }
        $this->view->form = $form;
    }
    public function changepasswordformAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return $this->_helper->redirector(
                'index', 'auth', 'default'
            );
        }
        $this->view->identity = $auth->getIdentity();
        $this->view->form = new Application_Form_Changepassword();
    }
    public function changepasswordAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return $this->_helper->redirector(
                'index', 'auth', 'default'
            );
        }

        $User = new Application_Model_DbTable_User();
        $select = $User->select()->where('username = ?', $auth->getIdentity());
        $u = $User->fetchRow($select);

        $this->_helper->viewRenderer('changepasswordform');
        $form = new Application_Form_Changepassword();

        if ($u && $form->isValid($this->getRequest()->getPost())) {

            $password = $form->getValue('password');
            $salt = md5(time());
            $u['salt']     = $salt;
            $u['password'] = md5($password . $salt);
            $u->save();

            $email = $u['email'];
            $mail = new My_Mail();
            $mail->newPassword($u['username'], $email, $password);

            return $this->_helper->redirector(
                'index',
                'index',
                'default'
            );
        }
        $this->view->identity = $auth->getIdentity();
        $this->view->form = $form;
    }
}


