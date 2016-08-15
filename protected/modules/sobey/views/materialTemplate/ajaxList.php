<!--表单-->
<div class="pop_topfoot_banner">
    <form method="get" onsubmit="return material_search();" class="list_search_form">
        <div class="fl shaixuan ml_4 bold">广告物料模板</div>
        <div class="fr sz_sc"><span></span><?php echo CHtml::textField('name', @$_GET['name'], array('class' => 'txt1', 'id' => 'name_search')); ?>&nbsp;<input type="button" class="iscbut_4" value="搜索" onclick="material_search()" /></div>
    </form>
</div>
<!--end 表单-->
<!-- 用户列表 -->
<div>
    <table border="0" width="100%" cellspacing="1" cellpadding="1" class="table_pop_wrapper">
        <tr class="tr_title">
            <td scope="col" width="10%" class="tpboder">&nbsp;</td>
            <td scope="col" width="45%" class="tpboder">物料模板名称</td>
            <td scope="col" width="45%" class="tpboder">说明</td>
        </tr>
        <?php if($materiallist):?>
        <?php foreach ($materiallist as $one): ?>
        <tr>
            <td class="tx_c"><input type="radio" class="checkbox_material" name="MaterialTemplateId" value="mtId_<?php echo $one->id; ?>" onclick="setMaterialTemplate('<?php echo $one->name; ?>')" /></td>
            <td><?php echo $one->name; ?></td>
            <td><?php echo $one->description; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php else:?>
        <tr>
            <td></td>
           <td colspan="3"><span>没有查到相关的内容！</span></td>
        </tr>
        <?php endif;?>
    </table>
</div>
<!--end 用户列表-->

<!-- 分页-->
<div style="background:none repeat scroll 0 0 #DFEDF7; height:22px; border:solid 1px #ccc;">
    <!--page-->
    <?php
    $this->widget('HmLinkPager', array(
        'header' => '',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'refreshArea' => 'media_template_list',
        'pages' => $pages,
        'selectedPageCssClass' => 'current',
        'maxButtonCount' => 6,
        'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
            )
    );
    ?>
    <!--end page-->
    <!--page info-->
    <?php $this->widget('PageResize', array('pages' => $pages, 'refreshArea' => 'media_template_list')); ?>  
    <!--end page info-->
</div>
<!-- end 分页-->
<div class="pop_topfoot_banner">
    <div class="fr sz_sc mt7"><input type="button" class="iscbut_4" value="取消" onclick="hideTemplateList();" /></span>
</div>