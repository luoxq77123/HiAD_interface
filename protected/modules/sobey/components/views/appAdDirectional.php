    <div class="in">
        <div class="step1 fl">
            <div>
                <strong>定位</strong>
                 <?php echo CHtml::dropDownList('directional_type', @1, $directionalType, array('class' => 'text2 text22 mgl15', 'id' => 'directional_type', 'onchange'=>'selectDirectionalMode()')); ?>
                 <?php echo CHtml::dropDownList('directional_mode', @1, $directionalMode, array('class' => 'text2 text22 mgl15', 'id' => 'directional_mode')); ?>
            </div>
        </div>
        <table>
          <tr id="directional_cont_1">
            <?php $this->widget('AreaCheckBoxWidget', array('directional'=>$directional)); ?>
          </tr>
          <tr id="directional_cont_2" class="hide">
            <?php $this->widget('AppAdSingleDirectionalWidget', array('directional'=>$directional, 'directionalType'=>'connect')); ?>
          </tr>
          <tr id="directional_cont_3" class="hide">
            <?php $this->widget('TimeDirectionalWidget', array('directional'=>$directional)); ?>
          </tr>
          <tr id="directional_cont_4" class="hide">
            <?php $this->widget('AppAdSingleDirectionalWidget', array('directional'=>$directional, 'directionalType'=>'brand')); ?>
          </tr>
          <!--tr id="directional_cont_5" class="hide">
            <?php //$this->widget('BrowserLanguageWidget', array('directional'=>$directional)); ?>
          </tr-->
          <tr id="directional_cont_6" class="hide">
            <?php $this->widget('AppAdSingleDirectionalWidget', array('directional'=>$directional, 'directionalType'=>'platform')); ?>
          </tr>
          <tr id="directional_cont_7" class="hide">
            <?php $this->widget('ResolutionWidget', array('directional'=>$directional)); ?>
          </tr>

          <tr>
            <td colspan="3" style="height:58px;">
                <div class="tableFooter tableFooter1">
                    <div class="in in3">
                       <a href="javascript:void(0);" onclick="completeDirectional()" id="btn11"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn11.gif" /></a>
                       <a href="javascript:void(0);" onclick="hideDirectional()" id="btn12"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn12.gif" /></a>    
                    </div>
                </div>
            </td>
          </tr>
        </table>
    </div>