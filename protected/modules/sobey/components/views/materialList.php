<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<div class="hezi-tit">
    <div class="taskbar">
        <form onsubmit="return user_search();" method="get" class="list_search_form">
            <div class="fl">
                <div class="line line2 line21 titBar" style="background: none repeat scroll 0 0 #F7F7F7;">
                    <div class="mgl38 fl">类型： <?php echo CHtml::dropDownList('search_type', @$_GET['type'], $materialType, array('class' => 'sle', 'id' => 'search_type')); ?>
                    </div>
                    <div class="mgl38 fl">尺寸： <?php echo CHtml::dropDownList('search_size', @$_GET['size'], $usedSize, array('class' => 'sle', 'id' => 'search_size')); ?> 
                    </div>
                </div>
            </div>
            <div class="search-box fr mr_20" style = "width:180px; margin-top:11px;">
                <div class="search search1 mgl17"> <?php echo CHtml::textField('search_name', @$_GET['name'], array('class' => 'txt1 txt3 fl', 'id' => 'search_name')); ?> <a href="javascript:void(0);" onclick="user_search()" class="fr"><input type="button" class="butt"></a>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="w4Table w5Table">
  <table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tbody>
    <thead>
      <tr>
        <th scope="col" width="5%" class="tpboder tx_c"><label>
            <input type="checkbox" onchange="opAllCheckbox()" name="checkall" id="all_checkbox" />
          </label></th>
        <th>物料名称</th>
        <th>状态</th>
        <th>尺寸</th>
        <th>类型</th>
        <th width="25%">操作</th>
      </tr>
    </thead>
    <?php if($materiallist):?>
    <?php foreach($materiallist as $key=>$one):?>
    <tr <?php echo ($key%2==0)? '' : ''; ?>  onmouseout="this.style.backgroundColor='' " onmouseover="this.style.backgroundColor='#ccc'">
      <td ><input type="checkbox" class="checkbox_material" id="wuliaoId[]" name="material[]" value="<?php echo $one->id;?>" /></td>
      <td ><?php echo $one->name;?></td>
      <td ><?php echo $status[$one->status];?></td>
      <td ><?php if($one->material_size) echo $one->material_size;else echo '--';?></td>
      <td ><?php if(isset($materialType[$one->material_type_id]))echo $materialType[$one->material_type_id];else echo '--'?></td>
      <td ><div class="ico-box"> <a class="ico-eye" title="预览" target="_blank" href="<?php echo Yii::app()->createUrl('sobey/client/cbad', array('val' => $one->id, 'ad_type'=>$adType, 'type'=>$one->material_type_id));?>" onclick="">预览</a> 
          <!--<a href="#" class="ico-search" title="查找">查找</a>
                <a href="#" class="ico-stat" title="流量">流量</a>--> 
          <a  onclick="xinjian()"  class="ico-add" title="新建">新建</a> <a  onClick="bianji(<?php echo  $one->id; ?>)" class="ico-edit" title="编辑">编辑</a> <a  onclick="material_status(-1,<?php echo $one->id; ?>);" class="ico-dele" title="删除">删除</a> </div></td>
      <td >&nbsp;</td>
    </tr>
    <?php endforeach;?>
    <?php else:?>
    <tr>
      <td></td>
      <td colspan="5"><span>没有查到相关的内容！</span></td>
    </tr>
    <?php endif;?>
      </tbody>
    
  </table>
  <div class="pl_30 adbox">
    <?php
		$this->widget('HmLinkPager',array(
			'header'=>'',
			'firstPageLabel' => '首页',
			'lastPageLabel' => '末页',
			'prevPageLabel' => '<上一页',
			'nextPageLabel' => '下一页>',
			'refreshArea' => 'ggw_box',
			'pages' => $pages,
			'selectedPageCssClass' => 'current',
			'maxButtonCount'=>6,
			'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
			)
		);
		?>
    <!--end page--> 
    <!--page info-->
    <?php $this->widget('PageResize', array('pages' => $pages, 'refreshArea' => 'ggw_box', 'arrPageSize'=>$this->arrPageSize)); ?>
    <!--end page info--> 
  </div>
</div>
<div class="tableFooter tableFooter1">
  <div class="in in3"> <a href="javascript:void(0);" onclick="completeMaterial()" id="btn11"><img src="<?php echo $asset_url; ?>/images/btn11.gif" /></a> <a href="javascript:void(0);" onclick="hideMaterial()" id="btn12"><img src="<?php echo $asset_url; ?>/images/btn12.gif" /></a> </div>
</div>
</body>
</html>
