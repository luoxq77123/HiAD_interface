<?php

class MaterialTemplate extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_template}}';
    }
    
    public function rules() {
        return array(
            array('name, code', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('code', 'validateCode', 'on' => 'add,edit')
        );
    }
    
    /**
     * 验证代码
     * @param type $attribute
     * @param type $params 
     * 规则：所有变量需定义在html前面
     */
    public function validateCode($attribute, $params) {
        if (!$this->hasErrors()) {
            $code = trim($this->code);
            $arrCode = explode("\n", $code);
            if (empty($arrCode)) {
                $this->addError('code', '代码不能为空。');
            }
            $rowNum = 1;
            $isdefineVar = false;
            $defineComplete = false;
            $isUsedVar = false;
            $varArray = array();
            $pattern = "/var(\s+)([a-z]+):(media|link|text|number)=[0|1];\/\/(.+)/i";
            $patternVar = "/(var(\s+))?([^:]+)/i";
            $patternHtml = '/\${(.*?)}/';
            foreach($arrCode as $row) {
                if (preg_match($pattern, $row)) {
                    if ($defineComplete) {
                        $this->addError('code', '系统检测到你第'.$rowNum."行前定义有无效参数，请将广告物料html放在所有参数之后。");
                        return false;
                    }
                    if (preg_match($patternVar, $row, $match)) {
                        if (in_array($match[3], $varArray)) {
                            $this->addError('code', '系统检测到你第'.$rowNum."行前定义的参数，与其上面的参数有重复。");
                            return false;
                        }
                        $varArray[] = $match[3];
                        $isdefineVar = true;
                    }
                } else {
                    if (empty($varArray)) {
                        $this->addError('code', '系统检测到广告物料模板定义的参数无效或未定义参数。');
                        return false;
                    }
                    $defineComplete = true;
                    if (preg_match_all($patternHtml, $row, $matches)) {
                        foreach($matches[1] as $val) {
                            if (!in_array($val, $varArray)) {
                                $this->addError('code', '系统检测到你第'.$rowNum."行，广告物料html使用未定义参数。");
                                return false;
                            } else
                                $isUsedVar = true;
                        }
                    }
                }
                $rowNum ++;
            }
            if (!$isdefineVar) {
                $this->addError('code', '系统检测到广告物料模板定义的参数无效或未定义参数。');
                return false;
            } else if (!$isUsedVar) {
                $this->addError('code', '系统检测到广告物料html未使用参数，请至少使用一个参数。');
                return false;
            }
            return true;
        }
    }

    /**
     * 解析代码
     * @code type $code
     * @param type $params 
     * 规则：所有变量需定义在html前面
     */
    public function parseCode($code) {
        $code = trim($code);
        $arrCode = explode("\n", $code);
        $index = 0;
        $arrData = array('params'=>array(), 'html'=>'');
        $pattern = "/var(\s+)([a-z]+):(media|link|text|number)=[0|1];\/\/(.+)/i";
        // 涉及单项解析 添加推荐模板时使用
        //$pattern = "/var(\s+)(.+):(media|link|text|number|radio)=[0|1];\/\/(.+)/i";
        $patternVar = "/var\s+(.*?):(.*?)=(.*?);\/\/(.*?)$/i";
        foreach($arrCode as $row) {
            if (preg_match($pattern, $row)) {
                if (preg_match($patternVar, $row, $match)) {
                    $arrData['params'][$index]['name'] = $match[1];
                    $arrData['params'][$index]['description'] = $match[4];
                    $arrData['params'][$index]['type'] = reset(array_keys($this->getDataType(), $match[2]));
                    $arrData['params'][$index]['required'] = $match[3];
                    $arrData['params'][$index]['options'] = "";
                    // 单项选择
                    if($arrData['params'][$index]['type']==5) {
                        $tempStr = substr($match[1], 0, -1);
                        $tempArr = explode("[", $tempStr);
                        $arrSelection = explode(",", $tempArr[1]);
                        $arrOpt = array();
                        if (!empty($arrSelection)) {
                            foreach($arrSelection as $key=>$val){
                                $opt = explode("->", $val);
                                $arrOpt[$key]['value'] = $opt[0];
                                $arrOpt[$key]['desc'] = $opt[1];
                            }
                        }
                        $arrData['params'][$index]['name'] = $tempArr[0];
                        $arrData['params'][$index]['options'] = $arrOpt;
                    }
                    $index ++;
                }
            } else {
                $arrData['html'] .= $row;
            }
        }
        return $arrData;
    }

    public function attributeLabels() {
        return array(
            'name' => '广告物料模板名称:',
            'code' => '代码:',
            'description' => '说明:'
        );
    }
    
    // 获取内容 根据id
    public function getOneById($id) {
        $user = Yii::app()->session['user'];
        return $this->find('id=:id and com_id=:com_id', array(':id'=>$id, ':com_id'=>$user['com_id']));
    }

    // 获取所有内容 根据名称 或者 id
    public function getDataByNameOrIds($ids=array(), $name="") {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        // 附加搜索条件
        if (is_array($ids)&&!empty($ids)) {
            $criteria->addInCondition('id',$ids);
        }
        if (isset($name) && $name) {
            $criteria->addSearchCondition('name', $name);
        }
        $data = $this->findAll($criteria);
        return $data;
    }

    // get template data type
    public function getDataType(){
        return array(
            '1' => 'media',
            '2' => 'link',
            '3' => 'text',
            '4' => 'number',
            '5' => 'radio'
        );
    }
}