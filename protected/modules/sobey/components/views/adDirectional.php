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
			<?php $this->widget('ConnectModeWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_3" class="hide">
			<?php $this->widget('TimeDirectionalWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_4" class="hide">
			<?php $this->widget('BrowserTypeWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_5" class="hide">
			<?php $this->widget('BrowserLanguageWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_6" class="hide">
			<?php $this->widget('OperateSystemWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_7" class="hide">
			<?php $this->widget('ResolutionWidget', array('directional'=>$directional)); ?>
		  </tr>
		  <tr id="directional_cont_8" class="hide">
			<td>
			<div class="fl" style="width:780px;">
				<div class="title">
					<ul class="w8Tbletit">
						<li class="help">每行输入一个值
						</li>
					</ul>
				</div>
				<div class="mainCon">
					<table border="0">
					  <tr>
					    <td width="300"><textarea id="from_url" name="from_url" class="dtext_box"><?php echo $directional['formurl_set']; ?></textarea></td>
						<td width="300" class="tatop">
							说明信息：<br />
							来源域用来定位来自特定域名的访客。<br />
							每行输入一个域名，以回车换行。<br />
							例如：输入baidu.com，则只对从baidu.com来的访客展现广告；输入(direct)表示直达。<br />
						</td>
					  </tr>
					</table>
				</div>
			</div>
			</td>
		  </tr>
		  <tr id="directional_cont_9" class="hide">
			<td>
			<div class="fl" style="width:780px;">
				<div class="title">
					<ul class="w8Tbletit">
						<li class="help">每行输入一个值
						</li>
					</ul>
				</div>
				<div class="mainCon">
					<table border="0">
					  <tr>
					    <td width="300"><textarea id="access_url" name="access_url" class="dtext_box"><?php echo $directional['accessurl_set']; ?></textarea></td>
						<td width="300" class="tatop">
							说明信息：<br />
							被访url用来定位访问特定页面的访客。<br />
							每行输入一个url，以回车换行。<br />
							例如：输入www.baidu.com/news，则只在url中含有www.baidu.com/news的页面上展现广告。<br />
						</td>
					  </tr>
					</table>
				</div>
			</div>
			</td>
		  </tr>
		  
		  <tr>
		    <td colspan="3" style="height:58px;">
				<div class="tableFooter tableFooter1">
					<div class="in in3">
					   <a href="javascript:void(0);" onclick="completeDirectional()" id="btn11"><img src="<?php echo $asset_url; ?>/images/btn11.gif" /></a>
					   <a href="javascript:void(0);" onclick="hideDirectional()" id="btn12"><img src="<?php echo $asset_url; ?>/images/btn12.gif" /></a>    
					</div>
				</div>
			</td>
		  </tr>
		</table>
   	</div>