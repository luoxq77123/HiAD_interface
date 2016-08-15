<?php

class StyleWidget extends CWidget {
	
	public $id_bg = array();

    public function run() {
		$style = '';
        if(count($this->id_bg)){
			$style = $this->id_bg_style($this->id_bg);
		}
		echo $style;
    }

	private function id_bg_style($ids){
        $style  = "<style type='text/css'>\n";
        foreach($ids as $one){
            $color = $this->get_id_color($one);
            $style .= ".bg_$one{background-color:$color !important}\n";
        }
        $style .= "</style>\n";
		return $style;
	}
    
    private function get_id_color($id){
		$cache_name = md5('StyleWidget_get_id_color'.$id);
        $color = Yii::app()->memcache->get($cache_name);
        if (!$color) {
            $color = $this->get_rand_color();
            Yii::app()->memcache->set($cache_name, $color, 600);
        }
        return $color;
    }

	private function get_rand_color(){
		$str = '#';
		for($i = 0 ; $i < 6 ; $i++) {
			$randNum = rand(0 , 15);
			switch ($randNum) {
				case 10: $randNum = 'A'; break;
				case 11: $randNum = 'B'; break;
				case 12: $randNum = 'C'; break;
				case 13: $randNum = 'D'; break;
				case 14: $randNum = 'E'; break;
				case 15: $randNum = 'F'; break;
			}
			$str .= $randNum;
		}
		return $str;
	}
}