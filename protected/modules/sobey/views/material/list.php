<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="#">物料库</a> &gt; <a href="#">站点物料库</a></div>
</div>
<!--end 面包屑-->
<!--按钮-->
<div class="tpboder pl_20 adbox">
    <div class="lxr_sx"><a id="material_add" href="<?php echo $this->createUrl('material/add'); ?>" title="新建物料" class="iscbut cbut_jia"><span>新建物料</span></a>		</div>
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
                <?php echo CHtml::dropDownList('search_status', @$_GET['status'], array(0 => '-请选择-', 1 => '启用', -1 => '禁用'), array('class' => 'txt1', 'id' => 'search_status')); ?>
            </label>&nbsp;

            <label>类型:
                <?php echo CHtml::dropDownList('search_type', @$_GET['type'], $materialType, array('class' => 'txt1', 'id' => 'search_type')); ?>
            </label>&nbsp;

            <label>尺寸:
                <?php echo CHtml::dropDownList('search_size', @$_GET['size'], $usedSize, array('class' => 'txt1', 'id' => 'search_size')); ?>
            </label>
        </div>
        <div class="fr sz_sc"><span></span>物料名称：&nbsp;<?php echo CHtml::textField('name_search', @$_GET['name'], array('class' => 'txt1', 'id' => 'name_search')); ?>&nbsp;<input type="button" class="iscbut_4" value="搜索" onclick="material_search()" /></div>
    </form>
</div>
<!--end 表单-->
<!--操作按钮-->
<div class="tpboder pl_30 adbox">
    <div class="butgn nobutgn" id="butgn">
        <input type="button" onclick="material_status(1);" value="启用">
        <input type="button" onclick="material_status(-1);" value="禁用">
      <!--  <input type="button" onclick="order_delete();" value="禁用">-->
    </div>
</div>
<!--end 操作按钮-->
<!-- 用户列表 -->
<div class="tpboder adbox">
    <table border="0" cellspacing="0" cellpadding="0" class="list_table" id="list_table">
        <tr>
            <th scope="col" width="5%" class="tpboder tx_c"><label><input type="checkbox" id="lxr_qx" /></label></th>
            <th scope="col" width="45%" class="tpboder">物料名称</th>
            <th scope="col" width="10%" class="tpboder">状态</th>
            <th scope="col" width="10%" class="tpboder">尺寸</th>
            <th scope="col" width="10%" class="tpboder">类型</th>
            <th scope="col" width="20%" class="tpboder">操作</th>
        </tr>
       <?php if($materiallist):?>
        <?php foreach ($materiallist as $one): ?>
            <tr>
                <td><input type="checkbox" class="checkbox_material" name="material[]" value="<?php echo $one->id; ?>" /></td>
                <td><?php echo $one->name; ?></td>
                <td><?php echo $status[$one->status]; ?></td>
                <td><?php if ($one->material_size) echo $one->material_size;else echo '--'; ?></td>
                <td><?php if (isset($materialType[$one->material_type_id])) echo $materialType[$one->material_type_id];else echo '--' ?></td>
                <td>
                    <a href="<?php echo $this->createUrl('material/edit', array('id' => $one->id)); ?>" title="修改物料信息" class="load_frame">修改</a> |
                    <!--<a href="#">广告</a> | 
                    <a href="#">报告</a>-->
                    <a target="_blank" href="<?php echo Yii::app()->createUrl('client/cbad', array('val' => $one->id, 'ad_type' => 1, 'type'=>$one->material_type_id));?>" onclick="">预览</a>
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
    $(document).ready(function() {
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
        var size = $('#search_size').val();
        var type = $('#search_type').val();
        var name = $.trim($('#name_search').val());
        //var url = '<?php echo $this->createUrl('material/list') ?>?status='+status+'&type='+type+'&size='+encodeURIComponent(size)+'&name='+encodeURIComponent(name);
        <?php $get = $_GET; unset($get['status']); unset($get['name']); unset($get['type']); unset($get['size']);?>
        var url = '<?php echo Yii::app()->createUrl('material/list', $get) ?>&status='+status+'&type='+type+'&size='+encodeURIComponent(size)+'&name='+encodeURIComponent(name);
        //alert(url);
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
        '<?php echo $this->createUrl('material/status'); ?>',
        {'ids[]':ids, status:status}, 
        function(data){
            if(data.code < 0){
                banner_message(data.message);
            }else{
                jAlert(data.message, '提示');
                setTimeout('frame_load("<?php echo $this->createUrl('material/list'); ?>", true)', 1000);
                //setTimeout('ajax_load("ggw_box", "<?php echo Yii::app()->request->getUrl(); ?>", true);', 1000);
            }
        },
        'json'
    );
    }

    function material_delete(id){
        material = new Array();
        if(id){
            material.push(id);
        }else{
            $('.checkbox_material:checked').each(function(){
                material.push($(this).val());
            });
        }
        if(material.length < 1){
            return;
        }
        
        jConfirm('是否删除用户？', '提示', function(r){
            if(r){
                banner_message('后台处理中，请稍后');
                $.post(
                '<?php echo $this->createUrl('material/del'); ?>', 
                {'material[]':material}, 
                function(data){
                    if(data.code < 0){
                        banner_message(data.message);
                    }else{
                        jAlert(data.message, '提示');
                        setTimeout('frame_load("<?php echo $this->createUrl('material/list'); ?>", true);', 1000);
                    }
                },
                'json'
            );
            }
        });
    }
</script>
