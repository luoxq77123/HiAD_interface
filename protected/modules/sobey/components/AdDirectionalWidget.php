<?php

class AdDirectionalWidget extends CWidget {
		
    public function run() {
	
		// 获取定向类型
		$directionalType = SiteAd::model()->getDirectionalType();		
		// 获取定向模式
		$directionalMode = SiteAd::model()->getDirectionalMode();
		// 投放策略参数
		$adInfo = Yii::app()->session['create_ad_info'];
		$directional = array();
		if (!empty($adInfo['policy']['directional_info'])) {
			$directional = $adInfo['policy']['directional_info'];
			if (!is_numeric($directional['formurl_set'])&&$directional['formurl_set']!="") {
				$directional['formurl_set'] = str_replace(",", "\n", $directional['formurl_set']);
			} else {
				$directional['formurl_set'] = "";
			}
			if (!is_numeric($directional['accessurl_set'])&&$directional['accessurl_set']!="") {
				$directional['accessurl_set'] = str_replace(",", "\n", $directional['accessurl_set']);
			} else {
				$directional['accessurl_set'] = "";
			}
		} else {
			$directional = array(
				'formurl_set' => "",
				'accessurl_set' => ""
			);
		}
        $set = array(
            'directionalType' => $directionalType,
            'directionalMode' => $directionalMode,
			'directional' => $directional,
            'asset_url' => Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('application.modules.sobey.assets'))
        );

        $this->render('adDirectional', $set);
    }
}