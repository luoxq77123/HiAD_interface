<?php

class TimeDirectionalWidget extends CWidget {
	// 获取投放策略参数
	public $directional;
	
	public function init()
	{
		if($this->directional===null)
			$this->directional = array();
	}
	
	
    public function run() {
		$timeSet = (isset($this->directional['time_set']) && $this->directional['time_set']!=0)? explode(",", $this->directional['time_set']) : array();
		$weekList = AdTime::model()->weekList();
		$time = array();
		$week = array();
		foreach($timeSet as $val) {
			$arrTime = explode("-", $val);
			$wk = floor($arrTime[0]/100);
			$arrTime[1] = isset($arrTime[1])? $arrTime[1] : $wk*100+24;
			
			if($arrTime[1]-$arrTime[0]==24){
				$week[$wk] = true;
			}
			for($i=$arrTime[0]; $i<$arrTime[1]; $i++) {
				$time[] = $i;
			}
		}
        $set = array(
            'time' => $time,
			'week' => $week,
			'weekList' => $weekList
        );

        $this->render('timeDirectional', $set);
    }
}