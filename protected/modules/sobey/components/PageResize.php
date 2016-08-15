<?php

class PageResize extends CWidget {

    public $pages;
    public $refreshArea;
	public $arrPageSize;
	
	public function init(){
		if($this->arrPageSize===null)
			$this->arrPageSize = array(10 => 10, 20 => 20, 50 => 50);
	}

    public function run() {
        $this->render('pageresize');
    }

}