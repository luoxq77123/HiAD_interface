<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<link href="<?php echo $this->module->assetsUrl; ?>/css/adctrlstyle.css" rel="stylesheet" />
<link href="<?php echo $this->module->assetsUrl; ?>/js/jQueryAlert/jquery.alerts.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->module->assetsUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo $this->module->assetsUrl; ?>/js/main.js" type="text/javascript"></script>
<script src="<?php echo $this->module->assetsUrl; ?>/js/helper.js" type="text/javascript"></script>
<?php
$form = $this->beginWidget('CActiveForm', array(
        'id' => 'position-form',
        'enableClientValidation' => true,
        'action' => array('position/add?token='.$_GET['token']),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions' => array()
    ));
?>
<div class="body" id="iframe_box" style="height:390px">
	<div class="ad-tit">
    	<span class="closebox" onclick="closeIframe()">关闭</span>
    	<h6>新建广告位</h6>
    </div>
    <div class="ad-bod">
    	<div class="tab-box tab-liubai" style="padding-bottom:0px">
            <table border="0"  cellpadding="8" class="one-table nofw">
                <tbody>
                    <tr>
                      <th><span class="notion">*</span><?php echo $form->label($position, 'sort');?></th>
                      <td><?php echo $form->textField($position, 'sort', array('class' => 'txt1','maxlength'=>10)) ;?><span>(数字越大,显示顺序越靠后)</span></td>
                    <tr>
                    <tr>
                      <th><span class="notion">*</span><?php echo $form->label($position, 'name');?></th>
                      <td><?php echo $form->textField($position, 'name', array('class' => 'txt1')) ;?></td>
                    </tr>
                    <tr valign="top">
                      <th><?php echo $form->label($position, 'description');?></th>
                      <td><?php echo $form->textArea($position, 'description', array('cols' => '80', 'rows' => '8', 'class' => 'txt1', 'style' => 'height:135px;width:450px;')) ;?>
                      </td>
                    </tr>
              </tbody>
            </table>
            </div>
            <div class="bott-f">
            	<input type="submit" value="完成" class="ml_20 butt2" />
                <input type="button" onclick="closeIframe()"  value="返回" class="ml_20 butt2" />
            </div>
    </div>    
</div>
<?php $this->endWidget(); ?>
<iframe id="frameC" style="height:1px;width:1px;display:none;"></iframe>  

<script type="text/javascript">
function showResponse(responseText, statusText)  {
    var data = $.parseJSON(responseText);
    if(data.code < 0){
        jAlert(data.message);
    }else{
        jAlert(data.message, '提示');
        //window.parent.closeDialog();
        var iframeTag = document.getElementById('frameC');
        iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
        iframeTag.src = iframeSrc;
        //parent.location.href='http://my.test.com/test1.html';
    }
}
      
        
// ajax提交
$.validator.setDefaults({
submitHandler: function() {
    banner_message('后台处理中，请稍后');
    $('#position-form').ajaxSubmit({success:showResponse});
    return false;
}
});

// 验证
$("#position-form").validate({
    rules: {
        'Position[sort]':{
            required: true,
            digits: true,
            min: 1,
            max: 10000
        },
        'Position[name]':'required'
    },
    messages: {
        'Position[sort]':{
            required: '&nbsp;排序请填入1-10000的数字',
            digits: '&nbsp;排序请填入1-10000的数字',
            min: '&nbsp;排序请填入1-10000的数字',
            max: '&nbsp;排序请填入1-10000的数字'
        },
        'Position[name]':'&nbsp;请填入广告位名称'
    }
});
 function closeIframe(){
    var iframeTag = document.getElementById('frameC');
    iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
    iframeTag.src = iframeSrc;
  }
</script>
</body>
</html>