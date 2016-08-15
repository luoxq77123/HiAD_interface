<?php

class UploadController extends CController {

    public function actionUploadPic() {
        $upload = new UploadFile;
        $www_root = dirname(Yii::app()->BasePath);
        //$user = Yii::app()->session['user'];
        $image = getimagesize($_FILES["Filedata"]['tmp_name']);
        $path = '/material/image/' . date('Y/md/', time());
        $upload->setSavePathAndName($www_root . $path, uniqid());
        if ($info = $upload->upLoad($_FILES["Filedata"])) {
            $info['name'] = str_replace($www_root, '', $info['name']);
            $info['width'] = $image[0];
            $info['height'] = $image[1];
            $return = array('code' => 1, 'value' => $info);
        } else {
            $return = array('code' => -1, 'value' => 'error');
        }
        die(json_encode($return));
    }

    public function actionUploadFlash() {
        $upload = new UploadFile;
        $www_root = dirname(Yii::app()->BasePath);
        //$user = Yii::app()->session['user'];
        $flash = getimagesize($_FILES["Filedata"]['tmp_name']);
        $path = '/material/flash/' . date('Y/md/', time());
        $upload->setSavePathAndName($www_root . $path, uniqid());
        if ($info = $upload->upLoad($_FILES["Filedata"])) {
            $info['name'] = str_replace($www_root, '', $info['name']);
            $info['width'] = $flash[0];
            $info['height'] = $flash[1];
            $return = array('code' => 1, 'value' => $info);
        } else {
            $return = array('code' => -1, 'value' => 'error');
        }
        die(json_encode($return));
    }
    
    public function actionUploadVideo() {
        $upload = new UploadFile;
        $www_root = dirname(Yii::app()->BasePath);
        //$user = Yii::app()->session['user'];
        $flash = getimagesize($_FILES["Filedata"]['tmp_name']);
        $path = '/material/video/' . date('Y/md/', time());
        $upload->setSavePathAndName($www_root . $path, uniqid());
        if ($info = $upload->upLoad($_FILES["Filedata"])) {
            $info['name'] = str_replace($www_root, '', $info['name']);
            $info['width'] = $flash[0];
            $info['height'] = $flash[1];
            $return = array('code' => 1, 'value' => $info);
        } else {
            $return = array('code' => -1, 'value' => 'error');
        }
        die(json_encode($return));
    }

}