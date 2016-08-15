<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>物料预览</title>
        <link href="<?php echo $this->module->assetsUrl; ?>/css/main.css" rel="stylesheet" type="text/css" />
    </head>
    <body  style="background:#eee; width:100%;">
    <!--面包屑-->
	<div class="tpboder pl_35" id="pt">
        <div class="z"><a href="#">预览</a></div>
        <div>注：此页面中点击和展现将不被记录在统计中</div>
    </div>
    <!--end 面包屑-->
    <!--操作按钮-->
    <div class="tpboder adbox pl_35" width="96%">
         <?php if($type == 1):?>
            <div class="lxr_sx preview_box"><span><?php if (!empty($data)) echo $data['text'];?></span></a></div>
         <?php elseif($type == 2):?>
            <div class="lxr_sx preview_box"><?php if (!empty($data)): ?><img style="cursor:pointer;" src="<?php echo Yii::app()->request->baseUrl.$data['url']; ?>" onerror="this.src='<?php echo $this->module->assetsUrl; ?>/images/none.png';" width="<?php echo $data['pic_x']; ?>" height="<?php echo $data['pic_y']; ?>" /><?php endif; ?></div>
         <?php elseif($type == 3):?>
            <div class="lxr_sx preview_box"><p><?php if (!empty($data)): ?><embed src="<?php echo Yii::app()->request->baseUrl.$data['url']; ?>" type="application/x-shockwave-flash" width="<?php echo $data['flash_x']; ?>" height="<?php echo $data['flash_y']; ?>" fullscreen="yes"><?php endif; ?></p></div>
         <?php elseif($type == 4):?>
            <?php if (isset($_GET['is_template'])&&$_GET['is_template']):?>
            <?php echo $data['html'];?>
            <?php else:?>
            <?php echo isset($data['content'])? $data['content'] : '';?>
            <?php endif;?>
         <?php elseif($type == 5):?>
            <script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/player/jwplayer.js"></script>
            <link href="<?php echo $this->module->assetsUrl; ?>/js/player/webwidget_slideshow_dot.css" rel="stylesheet" type="text/css"/>
            <div class="lxr_sx preview_box" id="video_box">
            <p>
            <script type="text/javascript">
                jwplayer("video_box").setup({
                    flashplayer: "<?php echo $this->module->assetsUrl; ?>/js/player/playerdiy.swf",
                    file: "<?php echo $this->module->assetsUrl.$data['url']; ?>",
                    width: '<?php echo $data['width']; ?>',
                    height: '<?php echo $data['height']; ?>',
                    image:"<?php echo $this->module->assetsUrl.$data['pic']; ?>",
                    controlbar: "over",
                    screencolor :"#CCC",
                    autostart : true
                });
            </script>
            </p>
            </div>
         <?php endif;?>
    </div>
   </body>
</html>
<!--end 操作按钮-->