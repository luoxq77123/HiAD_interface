<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>添加广告资源-第二步-投放策略</title>
    <link href="<?php echo $this->module->assetsUrl; ?>/js/sobey/magic/resources/default/common/common.css" rel="stylesheet" />
    <link href="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/main.css"/>
    <link href="<?php echo $this->module->assetsUrl; ?>/js/sobey/magic/resources/default/magic.control.ComboBox/magic.control.ComboBox.css" rel="stylesheet" />
    <link href="<?php echo $this->module->assetsUrl; ?>/css/adctrlstyle.css" rel="stylesheet" />
    <link href="<?php echo $this->module->assetsUrl; ?>/js/datepicker/jqueryui/jquery.ui.all.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->module->assetsUrl; ?>/css/manhua_hoverTips.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $this->module->assetsUrl; ?>/js/sobey/tangram/tangram-min.js"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/sobey/My97DatePicker/WdatePicker.js"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/main.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/helper.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/datepicker/jquery.ui.datepicker.my.js" type="text/javascript"></script> 
    <style>
        body { background-color: #FFF;}
        .w7Ttit2 input{width:54px;}
    </style>
</head>
<body>
<div class="body">
    <div class="ad-tit"> <span class="closebox" onclick="closeIframe()">关闭</span>
        <h6>添加广告资源</h6>
    </div>
    <div class="tang-tab ad-bod">
        <ul class="tang-title">
            <li class="tang-title-item"><a href="#" onclick="return false"><span>第一步:广告设置</span></a></li>
            <li class="tang-title-item tang-title-item-selected"><a href="#" onclick="return false"><span>第二步:投放策略</span></a></li>
            <li class="tang-title-item"><a href="#" onclick="return false"><span>第三步:上传素材</span></a></li>
      </ul>
        <form>
            <div class="tab-box tab-box2">
                <input type="hidden" name="ad_id" id="ad_id" value="<?php echo $policy['aid']; ?>" />
                <div>
                    <table border="0" cellpadding="0" cellspacing="0" width="954px;">
                        <tbody>
                            <tr>
                                <td width="72px;" height="34px;" style="padding-top:8px;" valign="top"><strong>投放时间：</strong></td>
                                <td class="ad_policy_frame">
                                    <table border="0" cellpadding="0" cellspacing="0" width="800px;">
                                        <tr>
                                            <td width="1200px;" valign="top">
                                                <input type="hidden" name="time_mode" id="time_mode" value="<?php echo $policy['time_mode']; ?>" /> 
                                                <div id="policy_default_time" <?php if($policy['time_mode']=="") {echo 'style="display:none;"';} else { echo 'style="width:300px;"';} ?>>
                                                    <ul>
                                                        <li class="w7Ttit" style="float:left;"><span class="notion">*</span><strong>开始</strong></li>
                                                        <li >
                                                            <input class="Wdate dateSle" type="text" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})" size="30" name="policy[start_time]" id="start_time" value="<?php echo $policy['start_time']; ?>" readonly="true">
                                                        </li>
                                                        <!--li><a href="#"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn9.gif" /></a></li-->
                                                        <br />
                                                        <div id="error_start_time" class="hide errmsg">提示：请选择投放开始时间。</div>
                                                    </ul>
                                                    <ul>
                                                        <li class="w7Ttit" style="float:left;"><span class="notion">*</span><strong>结束</strong></li>
                                                        <li class="help" id="tag_endtime1" <?php if($policy['set_endtime']!=0)echo 'style="display:none;"'; ?>>不限时间<a href="javascript:void(0);" class="w7Ttit1" onclick="changeEndTime(2)">设定时间</a></li>
                                                        <li id="tag_endtime2" class="help" <?php if($policy['set_endtime']==0)echo 'style="display:none;"'; ?>>
                                                            <input class="Wdate dateSle" type="text" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'start_time\')}'})" id="end_time" size="30" name="policy[end_time]" value="<?php echo $policy['end_time']; ?>" readonly="true"><a href="javascript:void(0);" class="w7Ttit1" onclick="changeEndTime(1)">不限时间</a>
                                                        </li>
                                                        <br />
                                                        <div id="error_end_time" class="hide errmsg">提示：请选择投放结束时间。</div>
                                                    </ul>
                                                    <div style="clear:both;" class="mgl5">
                                                        <div class="help">如果投放时间为不连续的多个时间段，<a href="javascript:void(0);" onclick="changeTimeMode('')">请点击这里选择</a></div>
                                                    </div>
                                                </div>
                                                <div id="policy_gap_time" <?php if($policy['time_mode']=="default")echo 'style="display:none;"'; ?>>
                                                     <ul>
                                                        <li><span class="notion">*</span>不连续时间段选择</li>
                                                        <li  class="help" id="tag_endtime1"><a href="javascript:void(0);" class="w7Ttit1" onclick="changeTimeMode('default')">返回默认选择</a></li>
                                                    </ul>
                                                    <ul style="height:90px;">
                                                        <li class="help">
                                                          <textarea id="timelist" style="width:240px; height:80px;border-color:#5794BF #CBE0E3 #C7E2F1 #C8DBE9;border-style: solid;border-width: 1px;" name="gap_time" readonly="readonly"><?php echo $policy['gap_time']; ?></textarea>
                                                          <span style="margin-right: 20px;">总天数：<font color="red" id="datenum"> <?php if(isset($policy['days'])) echo $policy['days'];else echo '0'; ?> </font>天</span><input id='datepicker_input' type='text' style=' width:1px; height:1px; border:0px; display:none;' /><span  id="datepicker"></span>
                                                        </li>
                                                        <br />
                                                        <div id="error_gap_time" class="hide errmsg">提示：请选择投放时间段</div>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td svalign="top" >
                                                <div class="tab-right-box" style=" margin-left:15px;">
                                                        <div > 
                                                            <strong class="mgl5">优先级</strong> <?php echo CHtml::dropDownList('priority_mode', @$policy['priority_mode'], $priorityMode, array('class' => 'select_box ml15', 'id' => 'priority_mode', 'onchange'=>'setPriority()')); ?> <?php echo CHtml::dropDownList('priority', @$policy['priority'], $priorityList, array('class' => 'select_box', 'id' => 'priority')); ?> <a  onmouseover="showMyToolTips(this, '什么是优先级？', '优先级决定广告的展现顺序。<br />优先级：独占>标准>补余>底层。<br />1.独占：支持按日计费。<br />2.标准：默认的优先级，细分为1-10级。<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 优先顺序依次为1级>2级>3级>...>10级。<br />3.补余：通常用于联盟广告。<br />4.底层：通常用于抄底广告，例如广告位招租等。')" onmouseout="hideMyToolTips()"  class="toolTips_tag " href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a>
                                                            <input type="checkbox" class="ml15" name="weights" id="weights" <?php if($policy['set_weights']) echo 'checked="true"';?> />
                                                            <label for="weights">&nbsp;设置权重</label>
                                                            <?php if($policy['set_weights']) { ?>
                                                            <?php echo CHtml::dropDownList('weight_list', @$policy['weights'], $weightList, array('class' => 'select_box ml15', 'id' => 'weight_list')); ?>
                                                            <?php } else { ?>
                                                            <?php echo CHtml::dropDownList('weight_list', @$policy['weight_list'], $weightList, array('class' => 'select_box ml15 hide', 'id' => 'weight_list')); ?>
                                                            <?php } ?>
                                                            <a class="toolTips_tag" onmouseover="showMyToolTips(this, '什么是权重？', '权重：优先级相同时，高权重的广告的展现概率更高。')" onmouseout="hideMyToolTips()"  href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a> 
                                                        </div>
                                                        <div class="mgl5 mt15">
                                                            <input id="w7check" type="checkbox" <?php if($policy['set_cost']) echo 'checked="true"';?> />
                                                            <label for="w7check" class="w7Ttit1">设置投放数量和价格</label>
                                                            <a class="toolTips_tag" onmouseover="showMyToolTips(this, '数量和价格是否必须设置？', '不是必须设置。如果不设置，广告将按计费方式为“每日费用”、价格为“0”、数量为“不限”进行投放。')" onmouseout="hideMyToolTips()"  href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a> 
                                                        </div>
                                                        <div id="cont_w7check" class="w7Ttit2 <?php if(!$policy['set_cost'])echo 'hide'; ?>">
                                                            <div class="fee"> 
                                                                <strong class="mgl5">计费方式 </strong>
                                                                <?php foreach($costMode as $key=>$val) { ?>
                                                                <input type="radio" name="cost_mode" id="fee<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(intval($key)==$policy['cost_mode'])echo 'checked="checked"'; ?> onclick="changeCostNumDisplay(this)" />
                                                                <label for="fee<?php echo $key; ?>"><?php echo $val; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                            <ul>
                                                              <li class="w7Ttit" style="float:left;"><strong class="mgl5">价格</strong></li>
                                                              <li class="help"> ￥
                                                                <input name="price" style="width:127px;" id="price" type="text" value="<?php echo $policy['price']; ?>" onblur="setTotalCost()" class="txt1 txt7" />
                                                                数量：
                                                                <?php if($policy['cost_mode']>1&&$policy['cost_num']>0){ ?>
                                                                <span id="cost_num_box">
                                                                <input type="text" name="cost_num" id="cost_num" class="select_box" onblur="setTotalCost()" value="<?php echo $policy['cost_num']; ?>" />
                                                                </span><a href="javascript:void(0);" id="but_modify_cost_num" onclick="modifyCostNum()">不限</a>
                                                                <?php } else if($policy['cost_mode']>1) { ?>
                                                                <span id="cost_num_box">不限</span> <a href="javascript:void(0);" id="but_modify_cost_num"  onclick="modifyCostNum()">更改数量</a>
                                                                <?php } else { ?>
                                                                <span id="cost_num_box">不限</span> <a href="javascript:void(0);" id="but_modify_cost_num" class="hide" onclick="modifyCostNum()">更改数量</a>
                                                                <?php } ?>
                                                                <div id="error_cost_set" class="hide errmsg">提示：数量请填写>0且≤1,000,000,000的数字。</div>
                                                              </li>
                                                              <div style="clear:both"></div>
                                                            </ul>
                                                            <ul>
                                                              <li class="w7Ttit"><strong class="mgl5">总费用</strong></li>
                                                              <li class="help"> ￥<span class="ml15" id="total_price">—</span> </li>
                                                            </ul>
                                                            <br/>
                                                        </div>
                                                  <div class="mgl5">
                                                    <input id="w7check1" type="checkbox" <?php if($policy['set_limit_day']) echo 'checked="true"';?> />
                                                    <label for="w7check1" class="w7Ttit1">限制每日投放数量</label>
                                                    <a class="toolTips_tag" onmouseover="showMyToolTips(this, '如何设置每天的投放数量？', '勾选后，您可以设置广告每天展现或点击的数量，到达该数量后，当天不再展现该广告。第二个投放日广告将再次展现，并从0开始重新计算当天的投放数量。')" onmouseout="hideMyToolTips()"  href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a> </div>
                                                  <div id="cont_w7check1" class="w7Ttit2 <?php if(!$policy['set_limit_day'])echo 'hide'; ?>"> <strong>每天</strong> <?php echo CHtml::dropDownList('limit_day_mode', @$policy['limit_day_mode'], $limitDayMode, array('class' => 'select_box ml15', 'id' => 'limit_day_mode')); ?>
                                                    <input type="text" id="limit_day_num" style = "width:54px;" class="txt1 txt8 ml15" value="<?php echo $policy['limit_day_num']; ?>"/>
                                                    <strong>次</strong>
                                                    <div id="error_cont_w7check1" class="hide errmsg">提示：数量请填写>0且≤1,000,000,000的整数。</div>
                                                  </div>
                                                  <div class="mgl5">
                                                    <input id="w7check2" type="checkbox" <?php if($policy['set_limit_one']) echo 'checked="true"';?> />
                                                    <label for="w7check2" class="w7Ttit1">限制对独立访客的展现次数</label>
                                                    <a class="toolTips_tag" onmouseover="showMyToolTips(this, '如何限制对独立访客的展现次数？', '勾选后，您可以从每天、每小时、每分钟多个维度限制广告对独立访客的展现次数。')" onmouseout="hideMyToolTips()"  href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a> </div>
                                                  <div id="cont_w7check2" <?php if(!$policy['set_limit_one']) echo 'class="hide"'; ?>>
                                                        <?php if($policy['set_limit_one']>0&&!empty($policy['limit_one'])) { ?>
                                                        <?php foreach($policy['limit_one'] as $key=>$val) { ?>
                                                        <div id="limit_one_<?php echo $key; ?>" class="w7Ttit2"> <?php echo CHtml::dropDownList('limit_one_mode_'.$key, @$val['mode'], $limitOneShowMode, array('class' => 'select_box', 'id' => 'limit_one_mode_'.$key, 'onchange'=>'checkLimitOneSet(this)')); ?> 最多展现
                                                          <input type="text" style = "width:54px" name="limit_one_shownum_<?php echo $key; ?>" id="limit_one_shownum_<?php echo $key; ?>" class="txt1 txt8" value="<?php echo $val['num']; ?>"/>
                                                          次 <span class="help"><a href="javascript:void(0);" onclick="removeCutDiv(this);">删除</a></span>
                                                            <div id="error_limit_one_<?php echo $key; ?>" class="hide errmsg">提示：已经设置此上限，请勿重复设置。</div>
                                                        </div>
                                                        <?php } ?>
                                                        <?php } else {?>
                                                        <div id="limit_one_0" class="w7Ttit2"> <?php echo CHtml::dropDownList('limit_one_mode_0', @1, $limitOneShowMode, array('class' => 'select_box', 'id' => 'limit_one_mode_0', 'onchange'=>'checkLimitOneSet(this)')); ?> 最多展现
                                                          <input type="text" style = "width:54px" name="limit_one_shownum_0" id="limit_one_shownum_0" class="txt1 txt8"/>
                                                          次 <span class="help"><a href="javascript:void(0);" onclick="removeCutDiv(this);">删除</a></span>
                                                            <div id="error_limit_one_0" class="hide errmsg">提示：已经设置此上限，请勿重复设置。</div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div id="add_limit_inputbox" class="w7Ttit2 <?php if(!$policy['set_limit_one']) echo 'hide'; ?>"> <span class="help"><a href="javascript:void(0);" onclick="addLimitOneInputBox();">添加多维</a></span> </div>
                                                    <input type="hidden" name="hiddenField" id="hiddenField" value="$_POST" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clear fgx pt_35"></div>
                <div class="bp-tab">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                              <td width="100px;" height="34"><strong style="width:90px;margin-right:45px;">精准定向：</strong></td>
                              <td><a href="javascript:void(0);" onClick="addDirectional();"><img src="<?php echo $this->module->assetsUrl; ?>/images/btn10.gif" /></a>&nbsp;&nbsp;<a class="toolTips_tag" onmouseover="showMyToolTips(this, '什么是精准定向？', '精准定向是从各个维度对网站访客的筛选，实现根据访客的不同特征来展现不同的广告。')" onmouseout="hideMyToolTips()" href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a></td>
                              </tr>
                            <tr>
                              <td width="84">&nbsp;</td>
                              <td width="1000" id="directional_value"><ul class="directional_list" id="directional_list">
                                  <?php if(isset($directional['area_set'])) { ?>
                                  <li id="area_set" class="help"> <img onClick="delAreaSet();" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 地域 = <?php echo $directional['area_set']['text'];?>
                                    <input id="area_set_text" type="hidden" value="<?php echo $directional['area_set']['value'];?>" name="area_set_text">
                                    [ <a onClick="showDirectional(1)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['connect_set'])) { ?>
                                  <li id="connect_set" class="help"> <img onClick="delConnectSet();" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 接入方式 = <?php echo $directional['connect_set']['text'];?>
                                    <input id="connect_set_text" type="hidden" value="<?php echo $directional['connect_set']['value'];?>" name="connect_set_text">
                                    [ <a onClick="showDirectional(2)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['time_set'])) { ?>
                                  <li id="time_set" class="help"> <img onClick="delTimeSet();" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 时间 = <?php echo $directional['time_set']['text'];?>
                                    <textarea id="time_set_text" name="time_set_text" style="display:none;"><?php echo $directional['time_set']['value'];?></textarea>
                                    [ <a onClick="showDirectional(3)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['btype_set'])) { ?>
                                  <li id="btype_set" class="help"> <img onClick="delContentSet('btype');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 浏览器类型 = <?php echo $directional['btype_set']['text'];?>
                                    <input id="btype_set_text" type="hidden" value="<?php echo $directional['btype_set']['value'];?>" name="btype_set_text">
                                    [ <a onClick="showDirectional(4)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['blanguage_set'])) { ?>
                                  <li id="blanguage_set" class="help"> <img onClick="delContentSet('blanguage');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 浏览器语言 = <?php echo $directional['blanguage_set']['text'];?>
                                    <input id="blanguage_set_text" type="hidden" value="<?php echo $directional['blanguage_set']['value'];?>" name="blanguage_set_text">
                                    [ <a onClick="showDirectional(5)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['osystem_set'])) { ?>
                                  <li id="osystem_set" class="help"> <img onClick="delContentSet('osystem');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 操作系统 = <?php echo $directional['osystem_set']['text'];?>
                                    <input id="osystem_set_text" type="hidden" value="<?php echo $directional['osystem_set']['value'];?>" name="osystem_set_text">
                                    [ <a onClick="showDirectional(6)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['resolution_set'])) { ?>
                                  <li id="resolution_set" class="help"> <img onClick="delContentSet('resolution');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 分辨率 = <?php echo $directional['resolution_set']['text'];?>
                                    <input id="resolution_set_text" type="hidden" value="<?php echo $directional['resolution_set']['value'];?>" name="resolution_set_text">
                                    [ <a onClick="showDirectional(7)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['formurl_set'])) { ?>
                                  <li id="from_url_set" class="help"> <img onClick="delUrlLimitSet('from_url');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 来源域 = <?php echo $directional['formurl_set']['value'];?>
                                    <textarea id="from_url_set_text" name="from_url_set_text" style="display: none;"><?php echo $directional['formurl_set']['value'];?></textarea>
                                    [ <a onClick="showDirectional(8)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                  <?php if(isset($directional['accessurl_set'])) { ?>
                                  <li id="access_url_set" class="help"> <img onClick="delUrlLimitSet('access_url');" src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif"> 被访url = <?php echo $directional['accessurl_set']['value'];?>
                                    <textarea id="access_url_set_text" name="access_url_set_text" style="display: none;"><?php echo $directional['accessurl_set']['value'];?></textarea>
                                    [ <a onClick="showDirectional(9)" href="javascript:void(0);">修改</a> ] </li>
                                  <?php } ?>
                                </ul></td>
                            </tr>
                            <tr>
                              <td width="84">&nbsp;</td>
                              <td width="1000" id="directional" class="hide"><?php $this->widget('AdDirectionalWidget', array()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bott-f">
              <a href="javascript:void(0);" id="step_prev"><input type="submit" value="上一步" class="butt5 butt-prve" /></a>
              <a href="javascript:void(0);" id="step_next"> <input type="button" value="下一步" class="butt5 butt-next" /></a>
              <!--<input type="submit" value="完成" class="butt5 butt-comp" />
              <input type="button" value="确认" class="butt5 butt-conf" />-->
            </div>
            <label for="textarea"></label>
            <textarea name="textarea" id="textarea" value="" cols="45" rows="5" style="display:none"><?php echo  $policy['adPosition']; ?></textarea>
        </form>
    </div>
</div>
<iframe id="frameC" style="height:1px;width:1px;display:none;"></iframe>  
<script type="text/javascript">
    $(function(){
        // 由于系统是ajax加载 所以在加载时间对象前需删除以前创建的时间对象
        if ($("#ui-datepicker-div").length==0) {
            $("#ui-datepicker-div").remove();
        }
        /**
         * 对日期控件初始化
         * datepicker: 按钮绑定id
         * target: 显示时间段textarea id
         * datepicker_input: 临时保存时间id
         * datenum：记录已经选择时间天数
         */
        $("#datepicker").datepickerRefactor({
            target : "#timelist",
            datepicker_input : "datepicker_input",
            datenum : "datenum"
        });
        // 上一步 
        $("#step_prev").click(function(){
            var url = '<?php echo Yii::app()->createURL('sobey/ad/setAd');?>?adPosition=<?php echo  $policy['adPosition']; ?>'
            window.location.href=url;
            return false;
        })
        // 下一步点击事件
        $("#step_next").click(function(){
            submitForm();
            //var url = "<?php echo Yii::app()->createURL('sobey/ad/setMaterial');?>";
            //window.location.href=url;
            //frame_load(url);
            return false;
        })
        // 权重
        $("#weights").change(function(){
            if ($("#weights").attr("checked")=="checked"){
                $("#weight_list").show();
            }else{
                $("#weight_list").hide();
            }
        })
        // 投放价格和数量
        $("#w7check").change(function(){
            if ($("#w7check").attr("checked")=="checked"){
                $("#cont_w7check").show();
            }else{
                $("#cont_w7check").hide();
            }
        })
        // 限制每日投放数量
        $("#w7check1").change(function(){
            if ($("#w7check1").attr("checked")=="checked"){
                $("#cont_w7check1").show();
            }else{
                $("#cont_w7check1").hide();
            }
        })

        // 限制对独立访客的展现次数
        $("#w7check2").change(function(){
            if ($("#w7check2").attr("checked")=="checked"){
                $("#cont_w7check2").show();
                $("#add_limit_inputbox").show();
            }else{
                $("#cont_w7check2").hide();
                $("#add_limit_inputbox").hide();
            }
        })
    })
    
    // 设置投放时间模式
    function changeTimeMode(mode) {
        if('default'==mode){
            $("#policy_gap_time").css("display","none");
            $("#policy_default_time").css("display","block");
            $("#time_mode").val("default");
        } else {
            $("#policy_default_time").css("display","none");
            $("#policy_gap_time").css("display","block");
            $("#time_mode").val("");
        }
    }
    
    // 在连续投放时间情况下 设置投放结束时间
    function changeEndTime(index) {
        if(1==index){
            $("#tag_endtime2").css("display","none");
            $("#tag_endtime1").css("display","block");
        } else {
            $("#tag_endtime1").css("display","none");
            $("#tag_endtime2").css("display","block");
        }
    }
    
    // 设置优先级
    function setPriority(){
        var mode = $("#priority_mode").val();
        if (mode==2){
            $("#priority").show();
        }else{
            $("#priority").hide();
        }
    }
    
    // 更加计费方式显示是否设置数量
    function changeCostNumDisplay(obj){
        if ($(obj).val()==1) {
            $("#but_modify_cost_num").hide();
            var str = '不限';
            $("#cost_num_box").html(str);
            $("#total_price").html("—");
        } else {
            var str = '不限';
            $("#cost_num_box").html(str);
            $("#but_modify_cost_num").html("更改数量");
            $("#but_modify_cost_num").show();
        }
    }
    
    // 设置投放数据量
    function modifyCostNum(){
        var html = $("#cost_num_box").html();
        if (html=="不限"){
            var str = '<input type="text" name="cost_num" id="cost_num" class="select_box" onchange="setTotalCost()" />';
            $("#cost_num_box").html(str);
            $("#but_modify_cost_num").html("不限");
        } else {
            var str = '不限';
            $("#cost_num_box").html(str);
            $("#but_modify_cost_num").html("更改数量");
        }
    }
    
    function setTotalCost(){
        var cost_mode = $("input:radio[name='cost_mode']:checked").val(),
            num = $("#cost_num").val(),
            price = $("#price").val(),
            total = '-';
        if (isNaN(price) || isNaN(num)) {
            $("#total_price").html(total);
            return false;
        } else if (cost_mode == 2) {
            total = (price/1000*num).toFixed(2);
        } else {
            total = (num*price).toFixed(2);
        }
        $("#total_price").html(total);
    }
    
    // 移除一个当前限制独立访问用户浏览次数设置
    function removeCutDiv(obj){
        $(obj).parent().parent().remove();
    }
    
    // 添加限制独立访问用户浏览次数设置框
    function addLimitOneInputBox(){
        var arrMode = getLimitOneShowMode();
        var mode = getLimitOneDefaultMode();
        for (var i=0; i<6; i++) {
            if ($("#limit_one_"+i).length==0) {
                var div = '<div id="limit_one_'+i+'" class="w7Ttit2">'+
                            '<select id="limit_one_mode_'+i+'" class="select_box" name="limit_one_mode_'+i+'" onchange="checkLimitOneSet(this)">';
                            for (var j=1; j<7; j++) {
                                if (mode == j) {
                                    div += '<option selected="selected" value="'+j+'">'+arrMode[j]+'</option>';
                                } else {
                                    div += '<option value="'+j+'">'+arrMode[j]+'</option>';
                                }
                                
                            }
                            div += '</select>'+
                            ' 最多展现 '+
                            '<input type="text" name="limit_one_shownum_'+i+'" id="limit_one_shownum_'+i+'" class="txt1 txt8"/> 次 '+
                            '<span class="help"><a href="javascript:void(0);" onclick="removeCutDiv(this);">删除</a></span>'+
                            '<div id="error_limit_one_'+i+'" class="hide errmsg">提示：已经设置此上限，请勿重复设置。</div>'+
                        '</div>';
                $("#cont_w7check2").append(div);
                return true;
            }
        }
    }
    
    // 检查限制独立访问用户浏览次数默认模式 是否冲入设置
    function checkLimitOneSet(obj){
        var id = $(obj).attr("id");
        var index = id.replace("limit_one_mode_","");
        var isSet = false;
        for (var i=0; i<6; i++) {
            if ($("#limit_one_"+i).length>0 && i!=index) {
                var mode = $("#limit_one_mode_"+i+" option:selected" ).val();
                if (mode==$("#"+id+" option:selected").val()){
                    isSet = true;
                    break;
                }
            }
        }
        if (isSet) {
            var errorId = "error_limit_one_"+index;
            setError(errorId, true);
        } else {
            var errorId = "error_limit_one_"+index;
            setError(errorId, false);
        }
    }
    
    // 获得一个限制独立访问用户浏览次数默认模式
    function getLimitOneDefaultMode(){
        var data = new Array();
        var rdata = 1;
        for (var i=0; i<6; i++) {
            if ($("#limit_one_"+i).length>0) {
                var mode = $("#limit_one_mode_"+i+" option:selected").val();
                data[mode] = 1;
            }
        }
        for (var i=1; i<7; i++) {
            if (typeof(data[i])=='undefined' || data[i]!=1) {
                rdata = i;
                return rdata;
            }
        }
        return rdata;
    }
    
    //限制对独立访客的展现次数方式
    function getLimitOneShowMode(){
        var data = new Array();
        data[1] = '每天';
        data[2] = '每小时';
        data[3] = '每30分钟';
        data[4] = '每20分钟';
        data[5] = '每10分钟';
        data[6] = '每分钟';
        return data;
    }
    
    // 提交数据
    function submitForm(){
        var aid = $("#ad_id").val(); 
        // 投放策略参数
        var time_mode = $("#time_mode").val();
        var start_time = $("#start_time").val();
        var set_endtime = ($("#tag_endtime1").is(":hidden"))? 1 : 0;
        var end_time = $("#end_time").val();
        var gap_time = $("#timelist").val();
        var priority_mode = $("#priority_mode option:selected").val();
        var priority = ($("#priority").length>0)? $("#priority option:selected").val() : "";
        var set_weights = ($("#weights").attr("checked")=="checked")? 1 : 0;
        var weights = $("#weight_list option:selected").val();
        var set_cost = ($("#w7check").attr("checked")=="checked")? 1 : 0;
        var cost_mode = $("input:radio[name='cost_mode']:checked").val();
        var price = $("#price").val();
        var set_cost_num = ($("#cost_num").length>0)? 1 : 0;
        var cost_num = 0;
        if (set_cost_num){
            cost_num = $("#cost_num").val();
        }
        var set_limit_day = ($("#w7check1").attr("checked")=="checked")? 1 : 0;
        var limit_day_mode = $("#limit_day_mode option:selected").val();
        var limit_day_num = $("#limit_day_num").val();
        var set_limit_one = ($("#w7check2").attr("checked")=="checked")? 1 : 0;
        var limit_one = "";
        for(var i=0,j=0; i<6; i++) {
            if ($("#limit_one_mode_"+i).length>0) {
                limit_one += $("#limit_one_mode_"+i+" option:selected").val();
                limit_one += "||";
                limit_one += $("#limit_one_shownum_"+i).val();
                limit_one += "&&";
            }
        }
        var limit_one_len = limit_one.length;
        limit_one = limit_one.substr(0, limit_one_len-2);
        // 播放器广告参数：播放器类型、广告尺寸和广告位置
        //var cushion = $("input:radio[name='playerCushion']").length>0? $("input:radio[name='playerCushion']:checked").val() : 0;
        var show_type = $("input:radio[name='show_type']").length>0? $("input:radio[name='show_type']:checked").val() : 0;
        var show_time = $("#show_time").length>0? $("#show_time").val() : 0;
        //var width = ($("#width").length>0)? $("#width").val() : 0;
        //var height = ($("#height").length>0)? $("#height").val() : 0;
        //var pos_x = ($("#pos_x").length>0)? $("#pos_x").val() : 0;
        //var pos_y = ($("#pos_y").length>0)? $("#pos_y").val() : 0;
        
        // 精准定向参数
        var areaSet = ($("#area_set_text").length>0)? $("#area_set_text").val() : 0;
        var connectSet = ($("#connect_set_text").length>0)? $("#connect_set_text").val() : 0;
        var timeSet = ($("#time_set_text").length>0)? $("#time_set_text").val() : 0;
        var btypeSet = ($("#btype_set_text").length>0)? $("#btype_set_text").val() : 0;
        var blanguageSet = ($("#blanguage_set_text").length>0)? $("#blanguage_set_text").val() : 0;
        var osystemSet = ($("#osystem_set_text").length>0)? $("#osystem_set_text").val() : 0;
        var resolutionSet = ($("#resolution_set_text").length>0)? $("#resolution_set_text").val() : 0;
        var fromurlSet = ($("#from_url_set_text").length>0)? $("#from_url_set_text").val() : "";
        var accessurlSet = ($("#access_url_set_text").length>0)? $("#access_url_set_text").val() : "";
        
        //检查内容
        var check = true;
        //时间
        if ("default"==time_mode) {
            if (start_time=="") {
                setError("error_start_time", true);
                check = false;
            } else {
                setError("error_start_time", false);
            }
            if (set_endtime) {
                if (end_time=="") {
                    setError("error_end_time", true);
                    check = false;
                } else {
                    if (!comptime(start_time, end_time)) {
                        setError("error_end_time", true, "提示：投放结束时间须大于开始时间。");
                        check = false;
                    } else {
                        setError("error_end_time", false);
                    }
                }
            } else {
                setError("error_end_time", false);
            }
        } else {
            if (gap_time=="") {
                setError("error_gap_time", true);
                check = false;
            } else {
                setError("error_gap_time", false);
            }
        }
        // 计费
        var re = /^\d+(\.\d{0,2})?$/;
        if (set_cost) {
            if (price!="" && !re.test(price)) {
                setError("error_cost_set", true);
                check = false;
            } else if (set_cost_num) {
                if (!re.test(cost_num) || cost_num<0 || cost_num>1000000000) {
                    setError("error_cost_set", true);
                    check = false;
                } else {
                    setError("error_cost_set", false);
                }
            } else {
                setError("error_cost_set", false);
            }
        }
        //每日限制
        if (set_limit_day) {
            if (limit_day_num=="" || !re.test(limit_day_num) || limit_day_num<1 || limit_day_num>1000000000) {
                setError("error_cont_w7check1", true);
                check = false;
            } else {
                setError("error_cont_w7check1", false);
            }
        }
        
        //独立用户访问限制
        if (set_limit_one) {
            for(var i=0; i<6; i++) {
                if ($("#limit_one_mode_"+i).length>0) {
                    var modei = $("#limit_one_mode_"+i+" option:selected").val();
                    var errExist = false;
                    for(var j=i+1; j<6; j++) {
                        if ($("#limit_one_mode_"+j).length>0) {
                            var modej = $("#limit_one_mode_"+j+" option:selected").val();
                            if (modei==modej) {
                                var errorId = "error_limit_one_"+i;
                                setError(errorId, true, "提示：已经设置此上限，请勿重复设置。");
                                errorId = "error_limit_one_"+j;
                                setError(errorId, true, "提示：已经设置此上限，请勿重复设置。");
                                check = false;
                                errExist = true;
                                break;
                            }
                        }
                    }
                    if (!errExist) {
                        var num = $("#limit_one_shownum_"+i).val();
                        var errorId = "error_limit_one_"+i;
                        if (!re.test(num) || num<1 || num>1000000000) {
                            setError(errorId, true, "提示：数量请填写>0且≤1,000,000,000的整数。");
                            check = false;
                        } else {
                            setError(errorId, false);
                        }
                    }
                }
            }
        }
        
        if (!check) {
            return false;
        }
        $.post(
            '<?php echo Yii::app()->createUrl("sobey/ad/setPolicy")?>?adPosition=<?php echo  $policy['adPosition']; ?>',
            {'do':'save','aid':aid,'time_mode':time_mode,'start_time':start_time,'set_endtime':set_endtime,'end_time':end_time,'gap_time':gap_time,'priority_mode':priority_mode,'priority':priority,'set_weights':set_weights,'weights':weights,'set_cost':set_cost,'cost_mode':cost_mode,'price':price,'set_cost_num':set_cost_num,'cost_num':cost_num,'set_limit_day':set_limit_day,'limit_day_mode':limit_day_mode,'limit_day_num':limit_day_num,'set_limit_one':set_limit_one,'limit_one':limit_one,'show_time':show_time,'show_type':show_type,'areaSet':areaSet,'connectSet':connectSet,'timeSet':timeSet,'btypeSet':btypeSet,'blanguageSet':blanguageSet,'osystemSet':osystemSet,'resolutionSet':resolutionSet,'fromurlSet':fromurlSet,'accessurlSet':accessurlSet}, 
            function(data){
                if(data.code < 1){
                    jAlert(data.message);
                }else{
                    var url = "<?php echo Yii::app()->createURL('sobey/ad/setMaterial?aid=');?>"+aid+'&adPosition='+data.adPosition;
                    window.location.href=url;
                }
            },
            'json'
        );
    }
    
    function setShowAttr(){
        var cushion = $("input[name='playerCushion']:checked").val();
        if (cushion == 4) {
            $(".cut-in-attr").removeClass("hide");
        }else{
            $(".cut-in-attr").addClass("hide");
        }
    }
    
    // 设置错误显示
    // isErr 为true时 显示id错误信息，为false屏蔽
    function setError(errId, isErr) {
        if (isErr) {
            $("#"+errId).parent().css("border", "2px solid #F0DDA5");
            $("#"+errId).parent().css("background-color", "#FEF7DB");

            $("#"+errId).parent().css("padding", "4px");
            var errStr = (arguments.length==3)? arguments[2] : "";
            if (errStr!="") {
                $("#"+errId).html(errStr);
            }
            $("#"+errId).show();
        } else {
            $("#"+errId).parent().css("border", "");
            $("#"+errId).parent().css("background-color", "");
            $("#"+errId).parent().css("padding", "");
            $("#"+errId).hide();
        }
    }
    
    // 比较时间
    function comptime(beginTime, endTime) {
        var bTime   =   new   Date(Date.parse(beginTime.replace(/-/g,   "/")));
        var beginTimes = bTime.getTime();
        var eTime   =   new   Date(Date.parse(endTime.replace(/-/g,   "/")));
        var endTimes = eTime.getTime();

        if (beginTimes >= endTimes) {
            return false;
        } else {
            return true;
        } 
    }
    
    /*设置精准定向*/
    function addDirectional(){
        if ($("#directional").html()=="") {
            $("#directional").load("<?php echo Yii::app()->createURL('ad/directional');?>");
        } else {
            if ($("#directional").is(":hidden")){
                $("#directional").show();
            } else {
                $("#directional").hide();
            }
        }
    }
    
    function hideDirectional(){
        $("#directional").hide();
    }
    
    function showDirectional(){
        var param = (arguments[0]==null)? 1 : arguments[0];
        //alert(param);
        //$("#directional_type").find("option[value='"+param+"']").attr("selected",true);
        setTimeout(function() {
            var selSorts = $("#directional_type");
            $.each(selSorts, function(index, sort) {
                var ope = $(sort).find("option[value='" + param + "']");
                if (ope.length > 0)
                ope[0].selected = true;
            });
            selectDirectionalMode();
            }, 1);
    }
    
    function completeDirectional(){
        var type = $("#directional_type option:selected").val();
        switch(type){
        case '1':
            saveAreaSet();
            hideDirectional();
            return;
        case '2':
            saveConnectSet();
            hideDirectional();
            return;
        case '3':
            saveTimeSet();
            hideDirectional();
            return;
        case '4':
            saveSContentSet(4);
            hideDirectional();
            return;
        case '5':
            saveSContentSet(5);
            hideDirectional();
            return;
        case '6':
            saveSContentSet(6);
            hideDirectional();
            return;
        case '7':
            saveSContentSet(7);
            hideDirectional();
            return;
        case '8':
            saveUrlLimitSet(8);
            hideDirectional();
            return;
        case '9':
            saveUrlLimitSet(9);
            hideDirectional();
            return;
        }
    }
    
    // 保存地址设置
    function saveAreaSet(){
        var mode = ($("#directional_mode option:selected").val()==1)? "=" : "≠";
        var html = "";
        var selectNum = 0;
        if ($("#area_set").length>0) {
            html = '<img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delAreaSet();"/>地域 '+mode;
            var pText = "";
            $("#address_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                selectNum ++;
                pText += (pText=="")? tmpid.replace("address_select_", "") : ","+tmpid.replace("address_select_", "");
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="area_set_text" id="area_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional(1)">修改</a>]';
            
            $("#area_set").html(html);
        } else {
            html = '<li id="area_set" class="help"><img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delAreaSet();"/>地域 '+mode;
            var pText = "";
            $("#address_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                selectNum ++;
                pText += (pText=="")? tmpid.replace("address_select_", "") : ","+tmpid.replace("address_select_", "");
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="area_set_text" id="area_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional(1)">修改</a>]</li>';
            
            $("#directional_list").append(html);
        }
        return true;
    }
    
    // 保存接入设置
    function saveConnectSet(){
        var selectNum = 0;
        var mode = ($("#directional_mode option:selected").val()==1)? "=" : "≠";
        var html = "";
        if ($("#connect_set").length>0) {
            html = '<img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delConnectSet();"/>接入方式 '+mode+' ';
            var pText = "";
            $("#connect_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                selectNum ++;
                pText += (pText=="")? tmpid.replace("conncet_select_", "") : ","+tmpid.replace("conncet_select_", "");
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="connect_set_text" id="connect_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional(2)">修改</a>]';
            
            $("#connect_set").html(html);

        } else {
            html = '<li id="connect_set" class="help"><img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delConnectSet();"/>接入方式 '+mode+' ';
            var pText = "";
            $("#connect_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                selectNum ++;
                pText += (pText=="")? tmpid.replace("conncet_select_", "") : ","+tmpid.replace("conncet_select_", "");
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="connect_set_text" id="connect_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional(2)">修改</a>]</li>';
            
            $("#directional_list").append(html);
        }
        return true;
    }
    
    // 保存时间设置
    function saveTimeSet(){
        var isSelected = false;
        var week = new Array('星期一','星期二','星期三','星期四','星期五','星期六','星期日');
        var mode = ($("#directional_mode option:selected").val()==1)? "=" : "≠";
        var html = "";
        var pText = "";
        var isSelectAll = true;
        if ($("#time_set").length>0) {
            var html = '<img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delTimeSet();"/>时间 '+mode+' ';
            var timeEnd = "";
            for(var i=1; i<8; i++) {
                html += "<span>"+week[i-1]+'：';
                if ($("#week_"+i).attr("checked")=="checked") {
                    html += '全天投放';
                    isSelected = true;
                    pText += (pText=="")? ((i*100)+'-'+(i*100+24)):','+((i*100)+'-'+(i*100+24));
                } else {
                    isSelectAll = false;
                    var selectNum = 0;
                    for(var j=0; j<24; j++) {
                        var tid = i*100+j;
                        var tmpid = "timebox_"+tid;
                        if ($("#"+tmpid).attr("class")=="td_time_box_select") {
                            if (timeEnd=="") {
                                html += j+":00";
                                pText += (pText=="")? (i*100+j):','+(i*100+j);
                            }
                            timeEnd = "--"+(j+1)+":00,";
                            selectNum ++;
                            isSelected = true;
                        } else if (timeEnd!=""){
                            html += timeEnd;
                            timeEnd = "";
                            pText += '-'+(i*100+j);
                        }
                    }
                    if (timeEnd!="") {
                        html += timeEnd;
                        pText += '-'+(i*100+24);
                        timeEnd = "";
                    }
                    if (0 == selectNum) {
                        html += '全天暂停';
                    }
                }
                html += "</span>";
            }
            if (!isSelected) {
                return false;
            }
            if (isSelectAll){
                html += '<input type="hidden" name="time_set_text" id="time_set_text" value="0" />';
            } else {
                html += '<textarea style="display:none;" name="time_set_text" id="time_set_text">'+pText+'</textarea>';
            }
            
            html += '[<a href="javascript:void(0);" onclick="showDirectional(3)">修改</a>]';
            
            $("#time_set").html(html);
        } else {
            var html = '<li id="time_set" class="help"><img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delTimeSet();"/>时间 '+mode+' ';
            var timeEnd = "";
            for(var i=1; i<8; i++) {
                html += "<span>"+week[i-1]+'：';
                if ($("#week_"+i).attr("checked")=="checked") {
                    html += '全天投放';
                    isSelected = true;
                    pText += (pText=="")? ((i*100)+'-'+(i*100+24)):','+((i*100)+'-'+(i*100+24));
                } else {
                    isSelectAll = false;
                    var selectNum = 0;
                    for(var j=0; j<24; j++) {
                        var tid = i*100+j;
                        var tmpid = "timebox_"+tid;
                        if ($("#"+tmpid).attr("class")=="td_time_box_select") {
                            if (timeEnd=="") {
                                html += j+":00";
                                pText += (pText=="")? (i*100+j):','+(i*100+j);
                            }
                            timeEnd = "--"+(j+1)+":00,";
                            selectNum ++;
                            isSelected = true;
                        } else if (timeEnd!=""){
                            html += timeEnd;
                            timeEnd = "";
                            pText += '-'+(i*100+j);
                        }
                    }
                    if (timeEnd!="") {
                        html += timeEnd;
                        timeEnd = "";
                    }
                    if (0 == selectNum) {
                        html += '全天暂停';
                    }
                }
                html += "</span>";
            }
            
            if (!isSelected) {
                return false;
            }
            if (isSelectAll){
                html += '<input type="hidden" name="time_set_text" id="time_set_text" value="0" />';
            } else {
                html += '<textarea style="display:none;" name="time_set_text" id="time_set_text">'+pText+'</textarea>';
            }
            html += '[<a href="javascript:void(0);" onclick="showDirectional(3)">修改</a>]</li>';
            
            $("#directional_list").append(html);
        }
        return true;
    }
    
    
    // 保存浏览器类型 浏览器语言 操作系统 分辨率设置
    function saveSContentSet(index){
        var arrType = new Array();
        var arrTypeName = new Array();
        arrType[4] = 'btype';
        arrType[5] = 'blanguage';
        arrType[6] = 'osystem';
        arrType[7] = 'resolution';
        arrTypeName[4] = '浏览器类型';
        arrTypeName[5] = '浏览器语言';
        arrTypeName[6] = '操作系统';
        arrTypeName[7] = '分辨率';
        var tag = arrType[index];
        var selectNum = 0;
        var mode = ($("#directional_mode option:selected").val()==1)? "=" : "≠";
        var html = "";
        var pText = "";
        if ($("#"+tag+"_set").length>0) {
            var html = '<img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delContentSet(\''+tag+'\');"/>'+arrTypeName[index]+' '+mode+' ';
            $("#"+tag+"_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                pText += (pText=="")? tmpid.replace(tag+"_select_", "") : ','+tmpid.replace(tag+"_select_", "");
                selectNum ++;
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="'+tag+'_set_text" id="'+tag+'_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional('+index+')">修改</a>]';
            
            $("#connect_set").html(html);
        } else {
            var html = '<li id="'+tag+'_set" class="help"><img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delContentSet(\''+tag+'\');"/>'+arrTypeName[index]+' '+mode+' ';
            $("#"+tag+"_select_box li").each(function(){
                var tmpid = $(this).attr("id");
                html += $("#"+tmpid+" span").html()+",";
                selectNum ++;
                pText += (pText=="")? tmpid.replace(tag+"_select_", "") : ','+tmpid.replace(tag+"_select_", "");
            });
            if (selectNum == 0) {
                return false;
            }
            html += '<input type="hidden" name="'+tag+'_set_text" id="'+tag+'_set_text" value="'+pText+'" />';
            html += '[<a href="javascript:void(0);" onclick="showDirectional('+index+')">修改</a>]</li>';
            
            $("#directional_list").append(html);
        }
        return true;
    }
    
    // 来源域设置
    function saveUrlLimitSet(index){
        var arrType = new Array();
        var arrTypeName = new Array();
        arrType[8] = 'from_url';
        arrType[9] = 'access_url';
        arrTypeName[8] = '来源域';
        arrTypeName[9] = '被访url';
        var tag = arrType[index];
        var text = $("#"+tag).val();
        if (text.replace(/(^\s*)|(\s*$)/g, "") == ""){
            return false;
        }
        var pText = "";
        pText = text.replace(new RegExp("\n","gm"), ",");
        var mode = ($("#directional_mode option:selected").val()==1)? "=" : "≠";
        if ($("#"+tag+"_set").length>0) {
            var html = '<img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delUrlLimitSet(\''+tag+'\');"/>'+arrTypeName[index]+' '+mode+' ';
            html += pText;
            html += '<textarea style="display:none;" name="'+tag+'_set_text" id="'+tag+'_set_text">'+pText+'</textarea>';
            html += '[<a href="javascript:void(0);" onclick="showDirectional('+index+')">修改</a>]';
            
            $("#"+tag+"_set").html(html);
        } else {
            var html = '<li id="'+tag+'_set" class="help"><img src="<?php echo $this->module->assetsUrl; ?>/images/deltit.gif" onclick="delUrlLimitSet(\''+tag+'\');"/>'+arrTypeName[index]+' '+mode+' ';
            html += pText;
            html += '<textarea style="display:none;" name="'+tag+'_set_text" id="'+tag+'_set_text">'+pText+'</textarea>';
            html += '[<a href="javascript:void(0);" onclick="showDirectional('+index+')">修改</a>]</li>';
            
            $("#directional_list").append(html);
        }
        return true; 
    }
    
    function selectDirectionalMode(){
        var mode = $("#directional_type option:selected").val();
        for(var i=1; i<10; i++) {
            $("#directional_cont_"+i).hide();
        }
        $("#directional_cont_"+mode).show();
        $("#directional").show();
    }
    
    /*
     * 设置地区 
     */
    // 显示子地名
    function showCitys(provinceid) {
        if ($("#province_city_"+provinceid).length>0) {
            var position = $("#mainCon1").offset();
            var top = position.top+10;
            $("#province_city_"+provinceid).css("top",top+"px");
            $("#province_city_"+provinceid).show();
        }
    }
    
    function hideCitys(provinceid) {
        if ($("#province_city_"+provinceid).length>0) {
            $("#province_city_"+provinceid).hide();
        }
    }
    
    function  selectProvinceAddr(provinceid) {
        if ($("#provice_"+provinceid).attr("checked")=="checked") {
            $("input[name='"+provinceid+"_city[]']").each(function(){
                $(this).attr("checked", true);
            });
            var addrName = $("#addr_name_"+provinceid).html();
            var addr = '<li id="address_select_'+provinceid+'"><span>'+addrName+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurAddr(\''+provinceid+'\');"/></li>';
            $("#address_select_box").append(addr);
        } else {
            $("input[name='"+provinceid+"_city[]']").each(function(){
                $(this).attr("checked", false);
                var kcity=$(this).val();
                if ($("#address_select_"+kcity).length>0) {
                    $("#address_select_"+kcity).remove();
                }
            });
            $("#address_select_"+provinceid).remove();
        }
    }
    
    function selectCityAddr(cityid){
        if ($("#city_"+cityid).attr("checked")=="checked") {
            var addrName = $("#addr_name_"+cityid).html();
            var addr = '<li id="address_select_'+cityid+'"><span>'+addrName+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurAddr(\''+cityid+'\');"/></li>';
            $("#address_select_box").append(addr);
            
            //勾选上级节点
            var parentId = $("#city_"+cityid).parent().parent().attr("id");
            var provinceid = parentId.replace("province_city_", "");
            $("#provice_"+provinceid).attr("checked", true);
            
            //判断是否选择上级菜单下所有节点 如果选择 则合并所有子节点用上级节点代替
            var selectAll = true;
            $("input[name='"+provinceid+"_city[]']").each(function(){
                if ($(this).attr("checked")!='checked') {
                    selectAll = false;
                    return;
                }
            });
            if (selectAll) {
                $("input[name='"+provinceid+"_city[]']").each(function(){
                    var cid = $(this).val();
                    $("#address_select_"+cid).remove();
                });
                var addrName = $("#addr_name_"+provinceid).html();
                var addr = '<li id="address_select_'+provinceid+'"><span>'+addrName+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurAddr(\''+provinceid+'\');"/></li>';
                $("#address_select_box").append(addr);
            }
        } else {
            // 检查该省份下所有的城市 是否都取消 如果是 则取消省份勾选
            var parentId = $("#city_"+cityid).parent().parent().attr("id");
            var provinceid = parentId.replace("province_city_", "");
            var selectNull = true;
            $("input[name='"+provinceid+"_city[]']").each(function(i,n){
                if ($(this).attr("checked")=='checked') {
                    selectNull = false;
                    return;
                }
            });
            if (selectNull) {
                $("#provice_"+provinceid).attr("checked", false);
            }
            
            // 删除右边选择的当前市
            $("#address_select_"+cityid).remove();

            // 当前是省级第一个被取消的市 则把选择的省级名称改成其下没有被取消的市级名称
            var noSelectNum = 0;
            $("input[name='"+provinceid+"_city[]']").each(function(i,n){
                if ($(this).attr("checked")!='checked') {
                    noSelectNum ++;
                }
            });
            if (!selectNull && noSelectNum==1) {
                $("#address_select_"+provinceid).remove();
                $("input[name='"+provinceid+"_city[]']").each(function(){
                    if ($(this).attr("checked")=="checked") {
                        var cid = $(this).val();
                        var addrName = $("#addr_name_"+cid).html();
                        var addr = '<li id="address_select_'+cid+'"><span>'+addrName+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurAddr(\''+cid+'\');"/></li>';
                        $("#address_select_box").append(addr);
                    }
                });
            }
            
        }
    }
    
    function deleteCurAddr(addrid){
        $("#address_select_"+addrid).remove();
        if ($("#provice_"+addrid).length>0) {
            $("#provice_"+addrid).attr("checked", false);
            $("input[name='"+addrid+"_city[]']").each(function(){
                $(this).attr("checked", false);
            });
        } else if ($("#city_"+addrid).length>0) {
            $("#city_"+addrid).attr("checked", false);
            // 检查该省份下所有的城市 是否都取消 如果是 则取消省份勾选
            var parentId = $("#city_"+addrid).parent().parent().attr("id");
            var provinceid = parentId.replace("province_city_", "");
            var selectNull = true;
            $("input[name='"+provinceid+"_city[]']").each(function(){
                if ($(this).attr("checked")=='checked') {
                    selectNull = false;
                    return;
                }
            });
            if (selectNull) {
                $("#provice_"+provinceid).attr("checked", false);
            }
        }
    }
    
    function cleanAllAddr(){
        $("#address_select_box li").each(function(){
            var selectid= $(this).attr("id");
            var addrid = selectid.replace("address_select_", "");
            deleteCurAddr(addrid);
        });
    }
    
    function selectAllAddr(){
        cleanAllAddr();
        $("input[name='province[]']").each(function(){
            $(this).attr("checked", true);
            var pid=$(this).attr("id");
            var addrid = pid.replace("provice_", "");
            selectProvinceAddr(addrid);
        });
    }
    
    function delAreaSet(){
        $("#area_set").remove();
        cleanAllAddr();
    }
    
    /*
     * 设置链接模式 
     */
    // 添加链接模式
    function addConnectMode(mode){
        var name = $("#connect_name_"+mode).html();
        var html = '<li id="conncet_select_'+mode+'"><span>'+name+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurConncet(\''+mode+'\');"/></li>';
        $("#connect_select_box").append(html);
        $("#but_connect_"+mode).html("已添加");
    }
    
    function deleteCurConncet(mode){
        $("#conncet_select_"+mode).remove();
        var html = '<a href="javascript:void(0);" onclick="addConnectMode(\''+mode+'\');">添加</a>';
        $("#but_connect_"+mode).html(html);
    }
    
    function selectAllConnect(){
        $("#connect_list .help").each(function(){
            var lid = $(this).attr("id");
            var mode = lid.replace("but_connect_", "");
            if ($("#conncet_select_"+mode).length==0) {
                addConnectMode(mode);
            }
        });
    }
    
    function cleanAllConnect(){
        $("#connect_list .help").each(function(){
            var lid = $(this).attr("id");
            var mode = lid.replace("but_connect_", "");
            if ($("#conncet_select_"+mode).length>0) {
                deleteCurConncet(mode);
            }
        });
    }
    
    function delConnectSet(){
        $("#connect_set").remove();
        cleanAllConnect();
    }
    
    
    /*
     * 设置时间段 
     */
    function setCurTimeBoxStyle(timeTag){
        var obj = $("#timebox_"+timeTag);
        if (obj.attr("class")!="td_time_box_select")
            obj.attr("class", "td_time_box_hover");
    }
    
    function revertCurTimeBoxStyle(timeTag){
        var obj = $("#timebox_"+timeTag);
        if (obj.attr("class")!="td_time_box_select")
            obj.attr("class", "td_time_box");
    }
    
    function selectCurTime(timeTag){
        var obj = $("#timebox_"+timeTag);
        if (obj.attr("class")=="td_time_box_select") {
            obj.attr("class", "td_time_box");
            var weekid = timeTag.toString().substring(0, 1);
            $("#week_"+weekid).attr("checked", false);
        } else {
            obj.attr("class", "td_time_box_select");
            var weekid = parseInt(timeTag.toString().substring(0, 1));
            var isAllDay = true;
            for(var i=0; i<24; i++) {
                var timeboxId = weekid*100+i;
                if ($("#timebox_"+timeboxId).attr("class")!="td_time_box_select"){
                    isAllDay = false;
                    return ;
                }
            }
            if (isAllDay){
                $("#week_"+weekid).attr("checked", true);
            }
        }
    }
    
    function selectCurWeek(weekid){
        var objId = "week_"+weekid;
        if ($("#"+objId).attr("checked")=="checked") {
            for(var i=0;i<24;i++) {
                var timeTag = weekid*100+i;
                $("#timebox_"+timeTag).attr("class", "td_time_box_select");
            }
        } else {
            for(var i=0;i<24;i++) {
                var timeTag = weekid*100+i;
                $("#timebox_"+timeTag).attr("class", "td_time_box");
            }
        }
    }
    
    function fastSelectTime(type){
        switch(type) {
        case 'all':
            for(var j=1; j<8; j++) {
                $("#week_"+j).attr("checked", true);
                selectCurWeek(j);
            }
            return;
        case 'workday':
            for(var j=1; j<6; j++) {
                $("#week_"+j).attr("checked", true);
                selectCurWeek(j);
            }
            for(var j=6; j<8; j++) {
                $("#week_"+j).attr("checked", false);
                selectCurWeek(j);
            }
            return;
        case 'weekend':
            for(var j=1; j<6; j++) {
                $("#week_"+j).attr("checked", false);
                selectCurWeek(j);
            }
            for(var j=6; j<8; j++) {
                $("#week_"+j).attr("checked", true);
                selectCurWeek(j);
            }
            return;
        }
    }
    
    function delTimeSet(){
        $("#time_set").remove();
        for(var j=1; j<8; j++) {
            $("#week_"+j).attr("checked", false);
            selectCurWeek(j);
        }
    }
    
    /*
     * 浏览器类型 浏览器语言 操作系统 分辨率设置
     */
    function addSelectContent(mode, prefix){
        var name = $("#"+prefix+"_name_"+mode).html();
        var html = '<li id="'+prefix+'_select_'+mode+'"><span>'+name+'</span> <img class="mt7" src="<?php echo $this->module->assetsUrl; ?>/images/view_clean.gif" onclick="deleteCurContent(\''+mode+'\', \''+prefix+'\');"/></li>';
        $("#"+prefix+"_select_box").append(html);
        $("#but_"+prefix+"_"+mode).html("已添加");
    }
    
    function deleteCurContent(mode, prefix){
        $("#"+prefix+"_select_"+mode).remove();
        var html = '<a href="javascript:void(0);" onclick="addSelectContent(\''+mode+'\', \''+prefix+'\');">添加</a>';
        $("#but_"+prefix+"_"+mode).html(html);
    }
    
    function selectAllContent(prefix){
        $("#"+prefix+"_list .help").each(function(){
            var lid = $(this).attr("id");
            var mode = lid.replace("but_"+prefix+"_", "");
            if ($("#"+prefix+"_select_"+mode).length==0) {
                addSelectContent(mode, prefix);
            }
        });
    }
    
    function cleanAllContent(prefix){
        $("#"+prefix+"_list .help").each(function(){
            var lid = $(this).attr("id");
            var mode = lid.replace("but_"+prefix+"_", "");
            if ($("#"+prefix+"_select_"+mode).length>0) {
                deleteCurContent(mode, prefix);
            }
        });
    }
    
    function delContentSet(prefix){
        $("#"+prefix+"_set").remove();
        cleanAllContent(prefix);
    }
    
    // url限制设置
    function delUrlLimitSet(tag){
        $("#"+tag+"_set").remove();
    }
    
     function closeIframe(){
        var iframeTag = document.getElementById('frameC');
        iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
        iframeTag.src = iframeSrc;
      }
</script>
</body>
</html>
