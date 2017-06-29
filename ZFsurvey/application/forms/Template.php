<?php

class Application_Form_Template extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');     
        $this->addElement('text', 'titlefont', array(
	    'label'    => 'Kolor czcionki nagłówkowej:',
	    'required' => false,
	    'class'    => "color {hash:true, pickerClosable:true} templateform",
	    'value'    => '#000000',
	    'onchange' => "document.getElementById('exampletitlefont').style.color = '#'+this.color",
        ));
        
        $this->addElement('text', 'mainfont', array(
            'label'    => 'Kolor czcionki głównej:',
            'required' => false,
            'class'    => "color {hash:true, pickerClosable:true} templateform",
            'value'    => '#000000',
            'onchange' => "document.getElementById('examplemainfont').style.color = '#'+this.color",
        ));
        
        $this->addElement('text', 'background', array(
            'label'    => 'Kolor tła:',
            'required' => false,
            'class'    => "color {hash:true, pickerClosable:true} templateform",
            'value'    => '#E0E0E0',
            'onchange' => "document.getElementById('examplebg').style.backgroundColor = '#'+this.color",
        ));

        $this->addElement('text', 'frame', array(
            'label'    => 'Kolor obramowania:',
            'required' => false,
            'class'    => "color {hash:true, pickerClosable:true} templateform",
            'value'    => '#368DE3',
            'onchange' => "document.getElementById('exampleframe').style.backgroundColor = '#'+this.color",
        ));

        $this->addElement('text', 'main', array(
            'label'    => 'Kolor tła zawartości:',
            'required' => false,
            'class'    => "color {hash:true, pickerClosable:true} templateform",
            'value'    => '#FFFFFF',
            'onchange' => "document.getElementById('examplemain').style.backgroundColor = '#'+this.color",
        ));
        
        $this->addElement('radio', 'radius', array(
            'label'         => 'Obramowanie ankiety:',
            'required'      => false,
            'multiOptions'  => array(
                '1'         => 'Rogi zaokrąglone',
                '0'         => 'Rogi niezaokrąglone'
            ),
            'value'         => '1',
            'description'   => '<div class="blank" style="margin-bottom: 1px;"></div>'
        ));
        $this->radius->addDecorator('description', array('escape' => false));
        
        $element = new Zend_Form_Element_File('filename', array('required' => false));
        $element
            ->setLabel('Dodaj logo (opcjonalne):')
            ->setDestination(realpath(APPLICATION_PATH . '/../public/uploads'))
            ->addValidator('NotEmpty', false)
            ->addValidator('Count', true, 1)
            ->addValidator('Size', true, 302400)
            ->setAutoInsertNotEmptyValidator(false)
            ->addValidator('Extension', false, 'jpg,png,gif');
        $this->addElement($element, 'filename');

        $this->filename->getValidator('Upload')->setMessages(array(
            Zend_Validate_File_Upload::NO_FILE => "Nazwa pliku nie może być pusta!",
        ));
        $this->filename->getValidator('Size')->setMessages(array(
            Zend_Validate_File_Size::TOO_BIG => "Maksymalny rozmiar pliku to '%max%'. Przesłany plik '%value%' ma rozmiar '%size%'.",
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Zapisz',
            'class' => 'button'
        ));
    }
}
