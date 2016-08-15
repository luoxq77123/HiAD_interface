<?php

class ConnectModeWidget extends CWidget {
	// 获取投放策略参数
	public $directional;
	
	public function init()
	{
		if($this->directional===null)
			$this->directional = array();
	}
	
	
    public function run() {
	
		$lists = SiteAdConnect::model()->getList();
		// 获取设置参数
		$connectSet = (!empty($this->directional['connect_set']))? explode("," ,$this->directional['connect_set']) : array();
		
		$list = array();
		$connect = array();
		foreach($lists as $key=>$val){
			$list[$key]['name'] = $val;
			$list[$key]['selected'] = false;
			if (in_array($key, $connectSet)) {
				$list[$key]['selected'] = true;
				$connect[$key] = $val;
			}
		}
		
        $set = array(
            'list' => $list,
			'connect' => $connect
        );

        $this->render('connectMode', $set);
    }
}