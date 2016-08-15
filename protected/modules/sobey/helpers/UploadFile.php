<?php
Class UploadFile{
		/**
	 * 允许上传文件的大小
	 * 设置为 -1 代表没有限制
	 */
	public $max_size = 100000000;
	
	/**
	 * 允许上传文件的类型
	 */
	public $allow_types = 'zip|rar|txt|pdf|jpg|jpeg|png|gif';

	/*
	 * 文件类型
	 */
	public $file_type = array();

	/**
	 * 上传文件存放的目录
	 */
	public $save_path = '';
	
	
	public $save_path_root = '';
	/**
	 * 文件名命名方式date time 都代表以当前时间命名 其他都代表使用愿文件名
	 */
	public $file_name = '';

	/**
	 * 错误信息
	 */
	public $errmsg = '';
	
	/**
	 * 文件保存地址
	 */
	public $file_path = '';
	
  
	/**
	 * 开始上传文件
	 * $access public
	 * @param object - $file 文件域对象
	 * @return array - $file_info 文件信息
	 */
	public function upLoad($file){
		//$user = $this->user;
		$name = $file['name']; //文件名
		$name_type = explode('.', $name);
		$type = $file['type']; //获取文件类型
		$size = $file['size']; //获取上传文件大小
		$tmp_name = $file['tmp_name']; //临时文件地址文件名
		$error = $file['error']; //获得文件上传信息
		if($error == 0 ){ // Go If
			//如果错误代码为0代表没有错误
			if(is_uploaded_file($tmp_name)){ // Go IF
				//检测文件是否通过 http post 上传
				$allow_types = explode('|',$this->allow_types); //分割允许上传的文件类型
				//$type = $this->getFileType($type,$name_type[1]); //获取上传文件的类型
				$type = $name_type[1];
				//if(in_array($type,$allow_types)){
					//如果上传的文件类型在用户允许的类型中
					if($size < $this->max_size){
						 //如果上传文件大小不超过允许上传的大小
						 $this->setSavaPath(); //设置上传文件保存路径
						 $new_name = $this->save_path . $this->file_name . '.' . $type;
						 if($this->move_file($tmp_name, $new_name)){
							if($this->checkFile($new_name,$type)){
							 	$this->setErrMsg('上传成功');
								$file_info = array(
									'size'=>$size,
									'type'=>$type,
									'name'=>$new_name,
									'error'=>$error
								);
								$this->setFileUploadedPath($new_name);
								return $file_info;
							}else{
								@unlink($new_name);
								$this->setErrMsg('文件的类型有问题！！！系统已删除！！！');
							}
						 }else{
							$this->setErrMsg('文件移动失败！');
							return false;
						 }

					}else{
						$this->setErrMsg('文件过大，最多可上传' . $this->max_size . 'k的文件！');
						return false;
					}
				//}else{
					//如果没在提示错误
				//	$this->setErrMsg('只支持上传' . $this->allow_types . '等文件类型！不允许' . $type);
				//	return false;
				//}
			}else{ //Else
				//如果不是设置错误信息
				$this->setErrMsg('文件不是通过HTTP POST方式上传的！');
				return false;
			} // End If is_uploaded_file
		} // End If	 error = 0
	}

	
	public function setSavePathAndName($path,$name){
		$this->save_path = $path;
		$this->file_name = $name;
	}

	/**
	 * 设置文件上传后的保存路径
	 */
	public function setSavaPath(){
		$this->save_path = (preg_match('/\/$/',$this->save_path)) ? $this->save_path : $this->save_path . '/';
		if(!is_dir($this->save_path )){
			//如果目录不存在，创建目录
			$this->makeDir();
		}
	}
	
	/**
	 * 创建目录
	 * @access public
	 * @param  string - $dir 目录名
	 */
	public function makeDir($dir = null){
		if(!$dir){
			$dir = $this->save_path;
		}
		if(is_dir($dir)){
			$this->setErrMsg('需要创建的文件夹已经存在！');
		}
		$dir = explode('/', $dir);
		$d = !$dir[0] ? '/' : '';
		foreach($dir as $v){
			if($v){
				$d .= $v . '/';
				if(!is_dir($d)){
					$state = mkdir($d, 0777);
					if(!$state)
						$this->setErrMsg('在创建目录' . $d . '时出错！');
				}
			}
		}
		return true;
	}

	/**
	 * 移动文件
	 * @access public
	 * @param string - $tmp_name 原文件路径加文件名
	 * @param string - $new_name 新文件路径加文件名
	 * @return 返回文件是否移动成功
	 */
	public function move_file($tmp_name, $new_name){
		if(!file_exists($tmp_name)){
			$this->setErrMsg('需要移动的文件不存在');
		}
		if(file_exists($new_name)){
			$this->setErrMsg('文件' . $new_name . '已经存在！');
		}
		if(function_exists('move_uploaded_file')){
			$state =  move_uploaded_file($tmp_name, $new_name);
		}else if(function_exists('rename')){
			$state = rename($tmp_name, $new_name);
		}  
		return $state;
	}
	
	public function setFileUploadedPath($path){
		$this->file_path = $path;
	}
	/**
	 * 检验上传文件的后缀与文件头的类型是否一致
	 * @access public
	 */
	
	public function checkFile($file,$type){
        $fp = fopen($file, "rb");
        $bin = fread($fp, 2); //只读2字节
        fclose($fp);
        $str_info  = @unpack("C2chars", $bin);
        $type_code = intval($str_info['chars1'].$str_info['chars2']);
        $file_type = '';
        switch ($type_code) {
            case 7790:
                $file_type = 'exe';
                break;
            case 7784:
                $file_type = 'midi';
                break;
            case 8075:
                $file_type = 'zip';
                break;
            case 8297:
                $file_type = 'rar';
                break;
            case 255216:
                $file_type = 'jpg';
                break;
            case 7173:
                $file_type = 'gif';
                break;
            case 6677:
                $file_type = 'bmp';
                break;
            case 13780:
                $file_type = 'png';
                break;
            case 3780:
            	$file_type = 'pdf';
            	break;
            default:
                $file_type = 'unknown';
                break;
        }
        if($file_type == $type || $type='mp4'){
        	return true;
        }else{
        	return false;
        }
        //return 'this file '.$file.' de type is '.$type.' and type_code is '.$type_code;
	}

	/**
	 * 设置错误信息
	 * $access public
	 * $param string - $msg 错误信息字符串
	 */
	public function setErrMsg($msg){
		$this->errmsg = $msg;
	}
}