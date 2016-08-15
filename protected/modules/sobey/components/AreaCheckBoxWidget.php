<?php

class AreaCheckBoxWidget extends CWidget {
	// 获取投放策略参数
	public $directional;
	
	public function init()
	{
		if($this->directional===null)
			$this->directional = array();
	}
	
	
    public function run() {
		// 获取设置参数
		$areaSet = (!empty($this->directional['area_set']))? explode("," ,$this->directional['area_set']) : array();
		// 获取省份
		$provice = Province::model()->getList();
		// 获取地级城市
		$city = array();
		$area = array();
		$province = array();
		foreach($provice as $key=>$val) {
			$name = $val;
			$province[$key]['name'] = $name;
			$province[$key]['selected'] = false;
			if (in_array($key, $areaSet)) {
				$province[$key]['selected'] = true;
				$area[$key] = $name;
			}
			$citys[$key] = City::model()->getListByProvince($key);
			// 直辖市
			if (count($citys[$key])==1) {
				$citys[$key] = District::model()->getListByCity(key($citys[$key]));
			}
			if ($province[$key]['selected']) {
				foreach($citys[$key] as $k=>$v){
					$cname = $v;
					$city[$key][$k]['name'] = $cname;
					$city[$key][$k]['selected'] = true;
				}
			} else {
				foreach($citys[$key] as $k=>$v){
					$cname = $v;
					$city[$key][$k]['name'] = $cname;
					$city[$key][$k]['selected'] = false;
					if (in_array($k, $areaSet)) {
						$province[$key]['selected'] = true;
						$city[$key][$k]['selected'] = true;
						$area[$k] = $cname;
					}
				}
			}
		}
        $set = array(
            'provice' => $province,
			'city' => $city,
			'area' => $area
        );

        $this->render('areaCheckBox', $set);
    }
}