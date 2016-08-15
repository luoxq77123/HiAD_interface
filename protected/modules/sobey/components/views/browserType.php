
<td><div class="step1 fl">
    <div class="title">
      <ul class="w8Tbletit">
        <li class="w8">浏览器类型</li>
        <li class="w8">&nbsp;</li>
        <li class="w5"><a href="javascript:void(0);" onclick="selectAllContent('btype');">全部添加&gt;&gt;</a></li>
      </ul>
    </div>
    <div class="mainCon">
      <ul id="btype_list" class="tvProvince">
        <?php foreach($list as $k=>$v) { ?>
        <table style="width:487px;">
          <tr>
            <td><li id="btype_name_<?php echo $k; ?>"><?php echo $v['name']; ?></li></td>
            <td style="width: 400px;">&nbsp;</td>
            <td>&nbsp;</td>
            <td><li class="help" id="but_btype_<?php echo $k; ?>">
                <?php if($v['selected']) { ?>
                已添加
                <?php } else { ?>
                <a href="javascript:void(0);" onclick="addSelectContent('<?php echo $k; ?>','btype');">添加</a>
                <?php } ?>
              </li></td>
          </tr>
        </table>
        <?php } ?>
      </ul>
    </div>
  </div></td>
<td><div class="stepArral fl">&nbsp;</div></td>
<td class="align_top"><div class="step1 step2 fl">
    <div class="select_box_bar"></div>
    <div class="title">
      <ul>
        <li class="w6">已选浏览器类型</li>
        <li class="w7"><a href="javascript:void(0);" onclick="cleanAllContent('btype');">全部删除&gt;&gt;</a></li>
      </ul>
    </div>
    <div class="mainCon">
      <ul id="btype_select_box" class="addr_select_list">
        <?php foreach($btype as $key=>$val) { ?>
        <li id="btype_select_<?php echo $key; ?>"><span><?php echo $val; ?></span> <img class="mt7" src="<?php echo Yii::app()->request->baseUrl; ?>/images/view_clean.gif" onclick="deleteCurContent('<?php echo $key; ?>', 'btype');"/></li>
        <?php } ?>
      </ul>
    </div>
  </div></td>
