<!--导航-->
<div class="san_nav">
    <ul class="fl san_list" id="san_list">
        <li><a href="javascript:void(0);" class="now">广告位</a></li>
        <li><a href="<?php echo Yii::app()->createUrl('app/list');?>" class="load_frame">应用</a></li>
    </ul>
    <a href="<?php echo Yii::app()->createUrl('appPosition/index', array('a' => 'table'));?>" style="line-height: 30px;" class="fr mr_40 load_frame">查看广告位投放视图&gt;&gt;</a>
</div>
<!--end 导航-->

<div class="taskbar">
    <div class="line4" id="banner_message" style="display: none;">
        <div class="line41 fr">
            <a href="#" class="close_message"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/deltit.gif" /></a>
        </div>
        <div class="message_area"></div>
    </div>
</div>

<!--生成代码-->
<div class="bgline add"><div style="float:left; margin-top:10px;">广告位：</div><a href="<?php echo Yii::app()->createUrl('appPosition/add');?>" id="add_position" title="新建广告位" class="iscbut cbut_jia"><span>新建广告位</span></a></div>

<div class="tpboder pl_30 adbox">
    <form onsubmit="return user_search();" method="get" class="list_search_form">
        <div class="fl shaixuan">
            <label>状态:
                <?php echo CHtml::dropDownList('search_status', @$_GET['status'], array('' => '-请选择-', 1 => '启用', -1 => '禁用'), array('class' => 'txt1', 'id' => 'search_status')); ?>
            </label>
            <label class="pl_20">类型:
                <?php echo CHtml::dropDownList('search_type', @$_GET['type'], array(0 => '-请选择-') + $adShows, array('class' => 'txt1', 'id' => 'search_type')); ?>
            </label>
            <label class="pl_20">尺寸:
                <?php echo CHtml::dropDownList('search_size', @$_GET['size'], array('' => '-请选择-') + $usedSize, array('class' => 'txt1', 'id' => 'search_size')); ?>
            </label>
        </div>
        <div class="fr sz_sc">
            广告位名称：&nbsp;<?php echo CHtml::textField('search_name', @$_GET['name'], array('class' => 'txt1', 'id' => 'search_name')); ?>
            <input type="button" onclick="user_search()" value="搜索" class="iscbut_4">
        </div>
    </form>
</div>
<!--操作按钮-->
    <div class="tpboder pl_30 adbox">
      <div class="butgn nobutgn" id="butgn">
      	<input type="button" onclick="position_status(1);" value="启用">
      	<input type="button" onclick="position_status(-1);" value="禁用">
        <!--<input type="button" onclick="position_delete();" value="删除">-->
      </div>
    </div>
<!--end 操作按钮-->

<div class="tpboder adbox">
    <table border="0" cellspacing="0" cellpadding="0" class="list_table" id="list_table">
        <tbody><tr>
        <th scope="col" width="5%" class="tpboder tx_c"><label><input type="checkbox" id="lxr_qx" /></label></th>
        <th scope="col" width="15%" class="tpboder">广告位名称</th>
        <th scope="col" width="15%" class="tpboder">状态</th>
        <th scope="col" width="15%" class="tpboder">类型</th>
        <th scope="col" width="15%" class="tpboder">应用</th>
        <th scope="col" width="15%" class="tpboder">尺寸</th>
        <th scope="col" width="25%" class="tpboder">操作</th>
        </tr>
                   <?php if($spList):?>
        <?php foreach($spList as $one):?>
        <tr>
        <td><input type="checkbox" name="ids[]" class="checkbox_ids" value="<?php echo $one->Position->id;?>" /></td>
        <td><?php echo $one->Position->name;?></td>
        <td><?php echo $one->Position->status == 1 ? '启用' : '禁用';?></td>
        <td><?php if(isset($adShows[$one->Position->ad_show_id])) echo $adShows[$one->Position->ad_show_id];else echo '--';?></td>
        <td><?php echo isset($apps[$one->app_id]) ? $apps[$one->app_id] : '-'; ?></td>
        <td><?php echo $one->Position->position_size;?></td>
        <td>
            <a href="<?php echo Yii::app()->createUrl('appPosition/edit', array('id' => $one->Position->id));?>" class="edit_position" title="修改广告位 <?php echo $one->Position->name;?>">修改</a> |
            <a href="<?php echo Yii::app()->createUrl('appAd/list', array('positionid' => $one->Position->id, 'position_name' => urlencode($one->Position->name)));?>" title="查看广告" class="load_frame">广告</a>  |
            <a href="<?php echo Yii::app()->createUrl('statistics/app', array('type' => 'position', 'ad_id' => $one->Position->id, 'ad_name' => urlencode($one->Position->name)));?>" class="load_frame">统计</a>
        </td>
        </tr>
        <?php endforeach;?>
          <?php else:?>
            <tr>
                <td></td>
               <td colspan="6"><span>没有查到相关的内容！</span></td>
            </tr>
          <?php endif;?>
    </tbody></table>
</div>
<div class="pl_30 adbox">
    <!--page-->
    <?php
    $this->widget('HmLinkPager',array(
        'header'=>'',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
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
    <?php $this->widget('PageResize', array('pages' => $pages, 'refreshArea' => 'ggw_box')); ?>  
    <!--end page info-->
</div>


<script type="text/javascript">
    $(document).ready(function() {
        dialog_ajax_ko({"list":$("#add_position"),"width":630,"height":600});
        dialog_ajax_ko({"list":$(".edit_position"),"width":630,"height":600});
    
        $("#lxr_qx").change(function(){
            if(xile_input_all($(this),$("#list_table"))){
                if($('.checkbox_ids:checked').length > 0)
                    $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });

        $('.checkbox_ids').click(function(){
            if($('.checkbox_ids:checked').length > 0){
                $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });
    });

    function user_search(){
        var search_status = $('#search_status option:selected').val();
        var search_type = $('#search_type option:selected').val();
        var search_size = $('#search_size option:selected').val();
        var search_name = $.trim($('#search_name').val());
        var url = '<?php echo Yii::app()->createUrl('appPosition/list')?>?appGroupId=<?php echo @$_GET["appGroupId"];?>&appId=<?php echo @$_GET["appId"];?>&status='+search_status+'&type='+search_type+'&size='+search_size+'&name='+encodeURIComponent(search_name);
        if(typeof(ajax_load) == 'function')
            ajax_load("ggw_box", url);
        else
            window.location = url;
        return false;
    }

	function position_status(status, uid){
            ids = new Array();
            if(uid){
                ids.push(uid);
            }else{
                $('.checkbox_ids:checked').each(function(){
                    ids.push($(this).val());
                });
            }
            if(ids.length < 1){
                return;
            }
            banner_message('后台处理中，请稍后');
            $.post(
                '<?php echo Yii::app()->createUrl('appPosition/status');?>', 
                {'ids[]':ids, status:status}, 
                function(data){
                    if(data.code < 0){
                        banner_message(data.message);
                    }else{
                        jAlert(data.message, '提示');
                        setTimeout('ajax_load("ggw_box", "<?php echo Yii::app()->getController()->getAction()->id == 'index' ? Yii::app()->createUrl('appPosition/list') : Yii::app()->request->getUrl();?>", true);', 1000);
                    }
                },
                'json'
            );
        }

</script>