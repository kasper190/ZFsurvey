# ZFsurvey

The web application for generating, analyzing and visualizing surveys written in PHP5.5, MySQL  and JavaScript. 

<br />

<b>Main functionality:</b>

| Action | Description |
| ------ | ----------- |
| Create surveys | Adding new surveys to the system, defining layout and settings |
| Filling out surveys | Retrieving responses from respondents |
| Analysis of results | Visualization of the results of pre-filled surveys |
| Survey management | Edit, delete, activate or deactivate surveys |

<br />

<b>To see all functionality</b>, the system model, database schema, UML diagrams, use case scenarios and application screenshots, go to the [Wiki page] (polish language).

<br />

Framework and libraries used in the project:
- [Zend Framework] (1.12.9)
- [jQuery] (1.9.1)
- [Libchart] (1.3)
- [TCPDF] (6.2.6)
- [JSColor] (1.4.4)
- [jQuery Corner] (2.13)

***

## Installation

The application contains all required Zend Framework files. First, go through the installation process described in the Zend documentation. Then you need to setup the database and edit some files.

### ZFsurvey.sql
Execute included sql file to create MySQL database (/ZFsurvey/sql/ZFsurvey.sql):
```sh
mysql> source ZFsurvey.sql
```

### application.ini
Edit application.ini file to setup a database connection (/ZFsurvey/application/configs/application.ini):
```php
resources.db.adapter         = "pdo_mysql"
resources.db.params.host     = "localhost"
resources.db.params.username = "username"
resources.db.params.password = "password"
resources.db.params.dbname   = "ZFsurvey"
resources.db.params.charset = "utf8"
```

### Mail.php
To handle password reset action you need to edit e-mail configuration in Mail.php file (/ZFsurvey/library/My/Mail.php). Pass your smtp server adress, port number, e-mail adress, username and password:
```php
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
```

<p align="center">
<img src="https://lh3.googleusercontent.com/twXrD8fqn5DNOjc3H2gA6rl6GKDJGoGCYdI9fISdYYeeBTQT3pqUwdyE8nOAjXhw2TvbEXPhSPQqUjK-blEknHHCuaKMKBENl9KFjWHmoKE59DJ2eFKCm-21ZUVUoqDs7elAVl6CAgrLKVE9If1qpDBlE8wyYgXrGMNJQulcW2F_sFKtnykWIbPq0QS3c0EeURxh_4P2LooeR4UM2XbyfSlY6Old3ribkBUwoQZTdD1sBa0zemwOSfSzbzyym-MzW51P_hRImS3xMBGw05vHDulOKE__mU1OY5eUomPBXn6NNcQ9SC-VMqMlSlATuEkaehz_ikUO9jYN8zMQ60NVbP4J1f6eM2kGMyMzwjIO29rPSmYoWEmi3jN58Deij50mEoo_fcXFDhTKqjxiYYE-yfGcA_hU6r3lC_cjXk2R9jCOIPfw533BPH6q9KAzPEhvuepyhFApJEDMtH4GFDfLbTeJRYpwXZLS17K_0aMeIp7jtdZLzUHMAsJFkBW0eRDQtxTeCR-7wi8w1bsyWnsHWurtsO6RF7M74Rv0IOe4lHc7GcsZ65HT71FBnyn8C67iG1g4ILH59xL2sM0y_MR4-t5mqngoUh_NqPZ6gfNcVVprcBf5kR5k6OJTPLoEXzX4-oRLBpEjyvTsMKMm-qEAu1wZJjPjMxjqegQLrfGv=w982-h399-no" alt="Ankiety">
</p>

[Zend Framework]: <https://framework.zend.com/manual/1.12/en/manual.html>
[jQuery]: <https://jquery.com/>
[Libchart]: <https://naku.dohcrew.com/libchart/pages/introduction/>
[TCPDF]: <https://tcpdf.org/>
[JSColor]: <http://jscolor.com/>
[jQuery Corner]: <http://malsup.com/jquery/corner/>

[Wiki page]: <https://github.com/kasper190/ZFsurvey/wiki>
