
<td><div class="step1 fl">
    <div class="title">
      <ul class="w8Tbletit">
        <li class="w8">接入方式</li>
        <li class="w8">&nbsp;</li>
        <li class="w5"><a href="javascript:void(0);" onclick="selectAllConnect();">全部添加&gt;&gt;</a></li>
      </ul>
    </div>
    <div class="mainCon">
      <ul id="connect_list" class="tvProvince">
        <?php foreach($list as $k=>$v) {?>
        <table style="width:487px;">
          <tr>
            <td><li  id="connect_name_<?php echo $k; ?>"><?php echo $v['name']; ?></li></td>
            <td style="width: 400px;">&nbsp;</td>
            <td>&nbsp;</td>
            <td><li class="help" id="but_connect_<?php echo $k; ?>">
                <?php if($v['selected']) { ?>
                已添加
                <?php } else { ?>
                <a href="javascript:void(0);" onclick="addConnectMode('<?php echo $k; ?>');">添加</a>
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
        <li class="w6">已选接入方式</li>
        <li class="w7"><a href="javascript:void(0);" onclick="cleanAllConnect();">全部删除&gt;&gt;</a></li>
      </ul>
    </div>
    <div class="mainCon" id="mainCon2">
      <ul id="connect_select_box" class="addr_select_list">
        <?php foreach($connect as $key=>$val) { ?>
        <li id="conncet_select_<?php echo $key; ?>"><span><?php echo $val; ?></span> <img class="mt7" src="<?php echo Yii::app()->request->baseUrl; ?>/images/view_clean.gif" onclick="deleteCurConncet('<?php echo $key; ?>');"/></li>
        <?php } ?>
      </ul>
    </div>
  </div></td>
