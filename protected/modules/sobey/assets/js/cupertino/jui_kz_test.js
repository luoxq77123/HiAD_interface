/*弹出层扩展库 基于 jquery UI*/

/*
* 取链接的地址 Ajax 获取;
* t = 整个页面中要绑定事件的jq dom
*/
function dialog_ajax(t){
	//var f = $(t);
	
}

/*小一点的弹窗*/
function dialog_ajax_notitle(t){
	//var f = $(t);
	
}

/*图片库的弹窗*/
function dialog_ajax_picku(t){
	//var f = $(t);
	
}

/*工作流的弹窗*/
function dialog_ajax_tch(t,w,h){
	w = w || 530;
	h = h || 400;
	//var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			//$("#dialog-form").load($(this).attr("href")); //内容待定义
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			
			$("#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:w,
				width:h,
				modal: true
			});
			$( "#dialog-form").dialog("open");
			//;
			//return false;
		});
	}			
}