<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="#">物料库</a> &gt; <a href="#">广告物料模板</a></div>
</div>
<!--end 面包屑-->
<!--按钮-->
<div class="tpboder pl_20 adbox">
    <div class="lxr_sx"><a id="material_add" href="<?php echo $this->createUrl('materialTemplate/add'); ?>" title="新建广告物料模板" class="iscbut cbut_jia"><span>新建模板</span></a></div>
</div>
<!--end 按钮-->
<!--提示-->
<div class="taskbar">
    <div class="line4" id="banner_message" style="display: none;">
        <div class="line41 fr">
            <a href="javascript:void(0);" class="close_message"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/deltit.gif" /></a>
        </div>
        <div class="message_area"></div>
    </div>
</div>
<!--end 提示-->
<!--表单-->
<div class="tpboder pl_30 adbox">
    <form method="get" onsubmit="return material_search();" class="list_search_form">
        <div class="fl shaixuan">
            <label>状态:
                <?php echo CHtml::dropDownList('status', @$_GET['status'], array(0 => '-请选择-', 1 => '启用', -1 => '删除'), array('class' => 'txt1', 'id' => 'search_status')); ?>
            </label>
        </div>
        <div class="fr sz_sc"><span></span>模板名称：&nbsp;<?php echo CHtml::textField('name', @$_GET['name'], array('class' => 'txt1', 'id' => 'name_search')); ?>&nbsp;<input type="button" class="iscbut_4" value="搜索" onclick="material_search()" /></div>
    </form>
</div>
<!--end 表单-->
<!--操作按钮-->
<div class="tpboder pl_30 adbox">
    <div class="butgn nobutgn" id="butgn">
        <input type="button" onclick="material_status(1);" value="启用">
        <input type="button" onclick="material_status(-1);" value="删除">
      <!--  <input type="button" onclick="order_delete();" value="禁用">-->
    </div>
</div>
<!--end 操作按钮-->
<!-- 用户列表 -->
<div class="tpboder adbox">
    <table border="0" cellspacing="0" cellpadding="0" class="list_table" id="list_table">
        <tr>
            <th scope="col" width="5%" class="tpboder tx_c"><label><input type="checkbox" id="lxr_qx" /></label></th>
            <th scope="col" width="45%" class="tpboder">物料模板名称</th>
            <th scope="col" width="10%" class="tpboder">状态</th>
            <th scope="col" width="10%" class="tpboder">说明</th>
            <th scope="col" width="20%" class="tpboder">操作</th>
        </tr>
                   <?php if($materiallist):?>
        <?php foreach ($materiallist as $one): ?>
            <tr>
                <td><input type="checkbox" class="checkbox_material" name="material[]" value="<?php echo $one->id; ?>" /></td>
                <td><?php echo $one->name; ?></td>
                <td><?php echo $status[$one->status]; ?></td>
                <td><?php echo $one->description; ?></td>
                <td>
                    <a href="<?php echo $this->createUrl('materialTemplate/edit', array('id' => $one->id)); ?>" title="修改物料模板" class="load_frame">修改</a> |
                    <a target="_blank" href="<?php echo Yii::app()->createUrl('client/cbad', array('val' => $one->id, 'ad_type'=>1, 'type'=>4, 'is_template'=>1));?>" onclick="">预览</a>
                </td>
            </tr>
        <?php endforeach; ?>
          <?php else:?>
            <tr>
                <td></td>
               <td colspan="5"><span>没有查到相关的内容！</span></td>
            </tr>
          <?php endif;?>
    </table>
</div>
<!--end 用户列表-->

<!-- 分页-->
<div class="pl_30 adbox">
    <!--page-->
    <?php
    $this->widget('HmLinkPager', array(
        'header' => '',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'pages' => $pages,
        'selectedPageCssClass' => 'current',
        'maxButtonCount' => 6,
        'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
            )
    );
    ?>
    <!--end page-->
    <!--page info-->
    <?php $this->widget('PageResize', array('pages' => $pages)); ?>  
    <!--end page info-->
</div>
<!-- end 分页-->
<script type="text/javascript">
    $(document).ready(function(e) {
        <?php if (isset($_GET['aid']) && $_GET['aid']): ?>
                    banner_message('广告&nbsp;“<span><?php echo urldecode($_GET['ad_name']) ?></span>”&nbsp;中的物料');
        <?php endif; ?>

        $("#lxr_qx").change(function(){
            if(xile_input_all($(this),$("#list_table"))){
                if($('.checkbox_material:checked').length > 0)
                    $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });
			
        $('.checkbox_material').click(function(){
            if($('.checkbox_material:checked').length > 0){
                $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });

        //页面跳转
        $("#material_add").click(function(){
            frame_load($(this).attr("href"));
            return false;
        });
    });

    function material_search(){
        var status = $('#search_status').val();
        var name = $.trim($('#name_search').val());
        <?php $get = $_GET; unset($get['status']); unset($get['name']);?>
        var url = '<?php echo Yii::app()->createUrl('materialTemplate/list', $get) ?>&status='+status+'&name='+encodeURIComponent(name);
        if(typeof(ajax_load) == 'function')
            frame_load(url);
        else
            window.location = url;
        return false;
    }
		
    function material_status(status, uid){
        var ids = new Array();
        if(uid){
            ids.push(uid);
        }else{
            $('.checkbox_material:checked').each(function(){
                ids.push($(this).val());
            });
        }
        if(ids.length < 1){
            return;
        }
        banner_message('后台处理中，请稍后');
        $.post(
            '<?php echo $this->createUrl('materialTemplate/status'); ?>', 
            {'ids[]':ids, status:status}, 
            function(data){
                if(data.code < 0){
                    banner_message(data.message);
                }else{
                    jAlert(data.message, '提示');
                    setTimeout('frame_load("<?php echo $this->createUrl('materialTemplate/list'); ?>", true);', 1000);
                }
            },
            'json'
        );
    }
</script>
