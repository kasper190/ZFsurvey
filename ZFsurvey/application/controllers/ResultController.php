<?php

class ResultController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return $this->_helper->redirector(
                'index', 'auth', 'default'
            );
        }
        
        $survey_id = $this->getRequest()->getParam('survey_id');      
        if($survey_id) {
            $User = new Application_Model_DbTable_User();
            $select = $User->select()->where('username = ?', $auth->getIdentity());
            $u = $User->fetchRow($select);

            $Survey = new Application_Model_DbTable_Survey();
            $select = $Survey->select()->where('survey_id = ?', $survey_id)
                                       ->where('user_id = ?', $u['user_id']);
            if(!$row = $Survey->fetchrow($select)) {
                $this->view->identity = $auth->getIdentity();
                $this->_helper->viewRenderer('accessdenied');
            }  
        }      
        $this->view->identity = $auth->getIdentity();
    }
    public function indexAction()
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $Survey = new Application_Model_DbTable_Survey();       
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $this->view->survey = $Survey->fetchRow($select);
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $this->view->question = $Question->fetchAll($select);
        
        $Respondent = new Application_Model_DbTable_Respondent();
        $select = $Respondent->select()->where('survey_id = ?', $survey_id);
        $this->view->respondent = $Respondent->fetchAll($select)->count();
    }
    public function surveyformAction()
    {
        $auth = Zend_Auth::getInstance();
        $survey_id = $this->getRequest()->getParam('id');      
        
        $User = new Application_Model_DbTable_User();
        $select = $User->select()->where('username = ?', $auth->getIdentity());
        $u = $User->fetchRow($select);

        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->select()->where('survey_id = ?', $survey_id)
                                   ->where('user_id = ?', $u['user_id']);
        if(!$row = $Survey->fetchrow($select)) {
            $this->_helper->viewRenderer('accessdenied');
        } else {
            include 'ShowController.php';
            $survey = new ShowController($this->_request, $this->_response);
            $survey->surveyformAction();
            $survey->renderScript('show/surveyform.phtml');
        }
    }
    public function surveyAction()
    {
        $auth = Zend_Auth::getInstance();
        $survey_id = $this->getRequest()->getParam('id');      
        
        $User = new Application_Model_DbTable_User();
        $select = $User->select()->where('username = ?', $auth->getIdentity());
        $u = $User->fetchRow($select);

        $Survey = new Application_Model_DbTable_Survey();
        $select = $Survey->select()->where('survey_id = ?', $survey_id)
                                   ->where('user_id = ?', $u['user_id']);
        if(!$row = $Survey->fetchrow($select)) {
            $this->_helper->viewRenderer('accessdenied');
        } else {
            include 'ShowController.php';
            $survey = new ShowController($this->_request, $this->_response);
            $survey->surveyAction();
            $survey->renderScript('show/surveyform.phtml');
        }
    }
    public function respondentsAction()
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $this->view->survey_id = $survey_id;
        
        $Respondent = new Application_Model_DbTable_Respondent();
        $select = $Respondent->select()->where('survey_id = ?', $survey_id);
        $this->view->respondent = $Respondent->fetchAll($select);        
    }
    public function responseAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('response', 'html')->initContext();

        $respondent_id = $this->_getParam('respondent_id', null);
        $survey_id = $this->getRequest()->getParam('survey_id');
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $question = $Question->fetchAll($select);
        $this->view->question = $question;
        
        $select = $Question->select('question_id')->where('survey_id = ?', $survey_id);
        $question_id = $Question->fetchAll($select)->toArray();
        
        $Response = new Application_Model_DbTable_Response();
        $select = $Response->select()->where('respondent_id = ?', $respondent_id)->where('question_id IN(?)', $question_id);
        $this->view->response = $Response->fetchAll($select);
    }
    public function deleterespondentAction() 
    {
        $this->_helper->viewRenderer('respondents');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('deleterespondent', 'json')->initContext();
        
        $respondent_id = $this->_getParam('respondent_id', null);
        $Respondent = new Application_Model_DbTable_Respondent();
        $select = $Respondent->getAdapter()->quoteInto('respondent_id = ?', $respondent_id);
        $r = $Respondent->fetchRow($select);
        $Respondent->delete($select);
        
        return $this->_helper->redirector(
            'respondents', 'result', null, array('survey_id' => $r['survey_id'])
        );
    }
    public function questionallAction()
    {
        $question_id = $this->getRequest()->getParam('question_id');
        
	$Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('question_id = ?', $question_id);
        $q = $Question->fetchRow($select);
        $this->view->question = $q['question'];
        
        $Response = new Application_Model_DbTable_Response();
        $select = $Response->select()->where('question_id = ?', $question_id);
        $this->view->response = $Response->fetchAll($select);
    }
    public function questionAction() 
    {  
        $question_id = $this->getRequest()->getParam('question_id');
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('question_id = ?', $question_id);
        $q = $Question->fetchRow($select);
        $this->view->question = $q;
        
        $Response = new Application_Model_DbTable_Response();
        $select = $Response->select('response')->where('question_id = ?', $question_id);
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($select); 
        $paginator = new Zend_Paginator($adapter);  
        $paginator->setItemCountPerPage(10);  
        $page = $this->_request->getParam('page', 1);
        $this->view->page = $page;
        $paginator->setCurrentPageNumber($page);  
        $this->view->paginator = $paginator;
    }
    public function xlsAction()
    {
        $survey_id = $this->getRequest()->getParam('survey_id');
        $this->_helper->layout->disableLayout();
        $this->view->content_type = header('Content-type: application/vnd.ms-excel');
        
        $Survey = new Application_Model_DbTable_Survey();       
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $survey = $Survey->fetchRow($select);
        $this->view->survey = $survey;
        $this->view->content_disposition = header('Content-Disposition: attachment; filename="' . $survey['title'] . '.xls"');
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $this->view->question = $Question->fetchAll($select);
        
        $Respondent = new Application_Model_DbTable_Respondent();
        $select = $Respondent->select()->where('survey_id = ?', $survey_id);
        $this->view->respondent = $Respondent->fetchAll($select)->count();
    }
    public function chartAction()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('chart', 'html')->initContext();
        
        $question_id = $this->_getParam('question_id', null);
        $type = $this->_getParam('type', null);
        
        $chart = new My_Graph();
        $chart->newGraph($question_id, $type);
        
        $data = file_get_contents('images/charts/' . $question_id . '_' . $type . '.png');
        $image = base64_encode($data);
        $this->view->image = $image;
        
        $chart->delete($question_id, $type);
    }
    public function questionnaireAction()
    {  
        $this->_helper->viewRenderer('index');
        $survey_id = $this->getRequest()->getParam('survey_id');
        
        $Survey = new Application_Model_DbTable_Survey();       
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $question = $Question->fetchAll($select);
                
        $this->_helper->layout->disableLayout();
        require_once('tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetFont('FreeSans', '', 12);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle($s['title']);
        
        $html ='<table border="0">
                    <tr><td style="width: 540px; height: 300px;"></td></tr>
                    <tr>
                    <td style="width: 540px; height: 30px; font-size: 30px; text-align: center;">
                       ' . $s['title'] . '
                    </td>
                    </tr>
                    <tr><td style="width: 540px; height: 40px;"></td></tr>
                    <tr>
                    <td style="width: 540px; height: 375px; font-size: 20px; '; 
                    if( strlen($s['start_msg']) < 50 ) { 
                        $html .= 'text-align: center;';
                    }   $html .= ' ">
                    ' . $s['start_msg'] . '
                    </td>
                    </tr>
                </table>';
        $pdf->AddPage();
        $pdf->writeHTML($html, true, 0, true, 0);
        
        $html = '';
        foreach ($question as $k => $q) {
            $html .= '<h3 style="font-size: 17px;">' . $q['question'] . '</h3>';
            if( $q['type'] != 'form' ) { 
                $Answer = new Application_Model_DbTable_Answer();
                $select = $Answer->select()->where('question_id = ?', $q['question_id']);
                $answer = $Answer->fetchAll($select);
                
                $html .='<table border="0">';
                foreach ($answer as $i => $a) {
                $html .='
                            <tr>
                            <td border="1" style="width: 20px; height: 20px;"></td>
                            <td style="height: 20px;">
                                ' . $a['answer'] . '
                            </td>
                        </tr>
                        <tr><td style="height: 1px;"></td></tr>';
                }
                $html .='</table>';
            } else {
                $html .='<table border="0">
                            <tr>
                            <td style="width: 500px;">';
                            for($i=0 ; $i<830 ; $i++) { 
                                $html .= '.'; 
                            }
                    $html .='</td>
                            </tr>
                            <tr><td style="height: 1px;"></td></tr>
                       </table>';
            }
        }
        $html .='<table border="0">
                    <tr>
                    <td style="width: 540px; font-size: 20px; '; 
                    if( strlen($s['end_msg']) < 50 ) { 
                        $html .= 'text-align: center;';
                    }   $html .= ' ">
                    ' . $s['end_msg'] . '
                    </td>
                    </tr>
                </table>';
        $pdf->AddPage();
        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->Output($s['title'] . '.pdf', 'I');
    }
    public function reportAction()
    {  
        $this->_helper->viewRenderer('index');
        $survey_id = $this->getRequest()->getParam('survey_id');
        
        $Survey = new Application_Model_DbTable_Survey();       
        $select = $Survey->select()->where('survey_id = ?', $survey_id);
        $s = $Survey->fetchRow($select);
        
        $Template = new Application_Model_DbTable_Template();
        $select = $Template->select()->where('survey_id = ?', $survey_id);
        $t = $Template->fetchRow($select);
        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('survey_id = ?', $survey_id);
        $question = $Question->fetchAll($select);
        
        $User = new Application_Model_DbTable_User();
        $select = $User->select()->where('user_id = ?', $s['user_id']);
        $user = $User->fetchRow($select);
        
        $Respondent = new Application_Model_DbTable_Respondent();
        $select = $Respondent->select()->where('survey_id = ?', $survey_id);
        $respondent = $Respondent->fetchAll($select)->count();
        
        $this->_helper->layout->disableLayout();
        require_once('tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetFont('FreeSans', '', 12);
        $pdf->setPrintHeader(false);
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetTitle($s['title']);
        $html ='<h2 style="font-size: 30px; text-align: center;">' . $s['title'] . '</h2>';
        $html .='<p></p>';
        $html .='<table border="1" cellpadding="5">
                    <tr>
                    <td style="width: 200px; height: 20px;">Administrator</td>
                    <td style="width: 340px; height: 20px;">' . $user['username'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Tytuł ankiety</td>
                    <td style="width: 340px; height: 20px;">' . $s['title'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Wiadomość powitalna</td>
                    <td style="width: 340px; height: 20px;">' . $s['start_msg'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Wiadomość po wypełnieniu</td>
                    <td style="width: 340px; height: 20px;">' . $s['end_msg'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Data utworzenia</td>
                    <td style="width: 340px; height: 20px;">' . $s['date'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Filtr adresów IP</td>
                    <td style="width: 340px; height: 20px;">';
                    if($s['ip_filter']) { 
                        $html .= 'Tak';
                    } else {
                        $html .= 'Nie';
                    }   $html .= '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Kolor czcionki nagłówkowej</td>
                    <td style="width: 80px; height: 20px; background-color: ' . $t['titlefont'] . ';"></td>
                    <td style="width: 260px; height: 20px;">' . $t['titlefont'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Kolor czcionki głównej</td>
                    <td style="width: 80px; height: 20px; background-color: ' . $t['mainfont'] . ';"></td>
                    <td style="width: 260px; height: 20px;">' . $t['mainfont'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Kolor tła</td>
                    <td style="width: 80px; height: 20px; background-color: ' . $t['background'] . ';"></td>
                    <td style="width: 260px; height: 20px;">' . $t['background'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Kolor obramowania</td>
                    <td style="width: 80px; height: 20px; background-color: ' . $t['frame'] . ';"></td>
                    <td style="width: 260px; height: 20px;">' . $t['frame'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Kolor tła zawartości</td>
                    <td style="width: 80px; height: 20px; background-color: ' . $t['main'] . ';"></td>
                    <td style="width: 260px; height: 20px;">' . $t['main'] . '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px; height: 20px;">Obramowanie ankiety</td>
                    <td style="width: 340px; height: 20px;">';
                    if($t['radius']) { 
                        $html .= 'Rogi zaokrąglone';
                    } else {
                        $html .= 'Rogi niezaokrąglone';
                    }   $html .= '</td>
                    </tr>
                    <tr>
                    <td style="width: 200px;">Logo</td>';
                    if($t['logo']) {
                        $html .= '<td style="width: 340px;" align="center"><img src="uploads/' . $s['survey_id'] . '.' . $t['logo'] . '" width="200px;" /></td>';
                    } else {
                        $html .= '<td style="width: 340px;">Nie</td>';
                    }    
                    $html .= '</tr>
                </table>
                <p>Liczba odpowiedzi: ' . $respondent . '</p>';               
        $pdf->AddPage();
        $pdf->writeHTML($html, true, 0, true, 0);
        
        foreach ($question as $k => $q) {
    		$html ='<h3 style="font-size: 15px;">' . $q['question'] . '</h3>';
    		
            if( $q['type'] != 'form' ) {
    			$html .= '
    			<table border="1" cellpadding="5">
    			<thead>
    			<tr style="background-color: #B2B2B2;">
    			    <th style="width: 35px;">lp.</th>
    			    <th style="width: 385px; text-align: center;">Odpowiedź</th>
    			    <th style="width: 60px; text-align: center;">Procent</th>
    			    <th style="width: 60px; text-align: center;">Głosów</th>
    			</tr>
    			</thead>
    			<tbody>';
    			$Answer = new Application_Model_DbTable_Answer();
    			$select = $Answer->select()->where('question_id = ?', $q['question_id']);
    			$answer = $Answer->fetchAll($select);
    			foreach ($answer as $i => $a) {
    			    $Response = new Application_Model_DbTable_Response();
    			    $select = $Response->select()->where('answer_id = ?', $a['answer_id']);
    			    $count = $Response->fetchAll($select)->count();

    			    $select = $Response->select()->where('question_id = ?', $q['question_id']);
    			    $all = $Response->fetchAll($select)->count();
    			    if($all) {
    				$procent = round(($count / $all * 100), 2);
    			    } else {
    				$procent = 0;
    			    }
    			    $html .= '
    			    <tr>
    				<td style="width: 35px;">' . ($i + 1) . '.</td>
    				<td style="width: 385px;">' . $a['answer'] . '</td>
    				<td style="width: 60px; text-align: right;">' . $procent . '%</td>
    				<td style="width: 60px; text-align: center;">' . $count . '</td>                   
    			    </tr>';
    				
    			}
    			$html .= '
    			</tbody>
    			</table>';
    				           
    			$chart = new My_Graph();
    			$chart->newGraph($q['question_id'], 'pie');
    			$html .= '
    			    <table>
    			    <tr><td style="height: 20px;"></td></tr>
    			    <tr>
    				<td style="width: 540px; text-align: center;">
    				    <img src="images/charts/' . $q['question_id'] . '_pie.png"/>
    				</td>
    			    </tr>
    			    </table>';   
        	} else {
    		    $Response = new Application_Model_DbTable_Response();
    		    $select = $Response->select()->where('question_id = ?', $q['question_id']);
    		    $response = $Response->fetchAll($select);
    		    $html .= '
    		    <table border="1" cellpadding="5">
    		        <thead>
    		            <tr style="background-color: #B2B2B2;">
    		                <th style="width: 35px;">lp.</th>
    		                <th style="width: 505px; text-align: center;">Odpowiedź</th>
    		            </tr>
    		        </thead>
    		        <tbody>';
    		    foreach ($response as $i => $a) {
    		    $html .= '
    		        <tr>
    		            <td style="width: 35px;">'. ($i + 1) .'.</td>
    		            <td style="width: 505px;">'. $a['response'] .'</td>
    		        </tr>';
    		    }
    		    $html .= '
    		        </tbody>
    		    </table>';
        	}
                    
            $pdf->AddPage();
            $pdf->writeHTML($html, true, 0, true, 0);
        }

        $pdf->Output($s['title'] . '_wyniki.pdf', 'I');
        
        foreach ($question as $q) {
            $chart->delete($q['question_id'], 'pie');
        }   
    }
}

