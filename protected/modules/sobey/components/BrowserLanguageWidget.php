<?php

class BrowserLanguageWidget extends CWidget {
	// 获取投放策略参数
	public $directional;
	
	public function init()
	{
		if($this->directional===null)
			$this->directional = array();
	}
	
	
    public function run() {	
		// 获取定向类型
		$lists = SiteAdLanguage::model()->getList();
		
		$arrSelect = (!empty($this->directional['resolution_set']))? explode(",", $this->directional['resolution_set']) : array();
		
		$list = array();
		$select = array();
		foreach($lists as $key=>$val){
			$list[$key]['name'] = $val;
			$list[$key]['selected'] = false;
			if (in_array($key, $arrSelect)) {
				$list[$key]['selected'] = true;
				$select[$key] = $val;
			}
		}
		
        $set = array(
            'list' => $list,
			'select' => $select
        );

        $this->render('browserLanguage', $set);
    }
}