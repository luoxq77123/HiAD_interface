<?php

class BrowserTypeWidget extends CWidget {
	// 获取投放策略参数
	public $directional;
	
	public function init()
	{
		if($this->directional===null)
			$this->directional = array();
	}
	
	
    public function run() {		
		// 获取定向类型
		$lists = SiteAdBrowser::model()->getList();
		
		$select = (isset($this->directional['btype_set']) && $this->directional['btype_set']!=0)? explode(",", $this->directional['btype_set']) : array();
		
		$list = array();
		$btype = array();
		foreach($lists as $key=>$val){
			$list[$key]['name'] = $val;
			$list[$key]['selected'] = false;
			if (in_array($key, $select)) {
				$list[$key]['selected'] = true;
				$btype[$key] = $val;
			}
		}
		
        $set = array(
            'list' => $list,
			'btype' => $btype
        );

        $this->render('browserType', $set);
    }
}