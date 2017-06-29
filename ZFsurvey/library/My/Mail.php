<?php

class My_Mail
{
    public function newPassword($username, $email, $password)
    {
        $config = array(
            'ssl'      => 'ssl',
            'port'     => 465,
            'auth'     => 'login',
            'username' => 'example@example.com',
            'password' => 'example'
        );
        
        $transport = new Zend_Mail_Transport_Smtp('smtp.example.com', $config);
        Zend_Mail::setDefaultTransport($transport);
        
        $mail = new Zend_Mail('UTF-8');
        $mail->addTo($email);
        $mail->setFrom('example@example.com', 'Administrator serwisu Ankiety');
        $mail->setSubject('Nowe hasło');
        $mail->setBodyText("Witaj {$username}.\nOto nowe hasło w serwisie Ankiety:\n\n{$password}\n\n");
        $mail->send();
    }
}