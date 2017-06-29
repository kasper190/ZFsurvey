<?php

class My_Graph
{
    public function newGraph($question_id, $type)
    {        
        $Question = new Application_Model_DbTable_Question();
        $select = $Question->select()->where('question_id = ?', $question_id);
        $q = $Question->fetchRow($select);
        
	$Answer = new Application_Model_DbTable_Answer();
        $select = $Answer->select()->where('question_id = ?', $question_id);
        $answer = $Answer->fetchAll($select);
        $count = $Answer->fetchAll($select)->count();
        
        include "libchart/classes/libchart.php";
        if ( $type == 'horizontal' ) {
            $x = 500;
            $y = 150 + $count * 10;
            $chart = new HorizontalBarChart($x, $y);
        }
        if ( $type == 'vertical' ) {
            $x = 500;
            $y = 200;
            $chart = new VerticalBarChart($x, $y);
        }
        if ( $type == 'pie' ) {
            $x = 500;
            $y = 250 + $count * ($count * 0.6);
            $chart = new PieChart($x, $y);
        }
        
        $dataSet = new XYDataSet();
        $chart->setTitle($q['question']);
        foreach ($answer as $i => $a) {
            $Response = new Application_Model_DbTable_Response();
            $select = $Response->select()->where('answer_id = ?', $a['answer_id']);
            $count = $Response->fetchAll($select)->count();
            
            if ( $type != 'pie' ) {
                $dataSet->addPoint(new Point($a['answer'], $count));
            } else {
                $dataSet->addPoint(new Point('' . $a['answer'] . ' (' . $count . ')', $count));
            }   
        }
        
	$chart->setDataSet($dataSet);
        if ( $type == 'horizontal' ) {
            $chart->getPlot()->setGraphPadding(new Padding(5, 30, 20, 140));
        }
	$chart->render('images/charts/' . $question_id . '_' . $type . '.png');
    }
    public function delete($question_id, $type)
    {
        $chart = realpath(APPLICATION_PATH . '/../public/images/charts/' . $question_id . '_' . $type . '.png');
        unlink($chart);
    }
}
