/*弹出层扩展库 基于 jquery UI*/

/*
* 取链接的地址 Ajax 获取;
* t = 整个页面中要绑定事件的jq dom
*/
function dialog_ajax(t){
	//url = "images/sss.html";
	//alert(typeof(url))
	var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			xile_loding($("#dialog-form"));
			$("#dialog-form").load($(this).attr("href"));
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			$( "#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:500,
				width:400,
				modal: true
			}).dialog("open");
			return false;
		});
	}			
}

/*小一点的弹窗*/
function dialog_ajax_notitle(t){
	var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			xile_loding($("#dialog-form"));
			$("#dialog-form").load($(this).attr("href"));
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			$( "#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:150,
				width:400,
				modal: true
			}).dialog("open");
			return false;
		});
	}			
}

/*图片库的弹窗*/
function dialog_ajax_picku(t){
	var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			xile_loding($("#dialog-form"));
			$("#dialog-form").load($(this).attr("href")); //内容待定义
			//alert($("#dialog-form").html())
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			$( "#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:530,
				width:785,
				modal: true
			}).dialog("open");
			return false;
		});
	}			
}

/*视频库的弹窗*/
function dialog_ajax_vodku(t){
	var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			xile_loding($("#dialog-form"));
			$("#dialog-form").load($(this).attr("href")); //内容待定义
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			$( "#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:500,
				width:785,
				modal: true
			}).dialog("open");
			return false;
		});
	}			
}

/*工作流的弹窗*/
function dialog_ajax_tch(t,w,h){
	w = w || 530;
	h = h || 400;
	var f = $(t);
	for(var i = 0 ; i < f.length ; i++){
		$($(f)[i]).click(function(){
			//alert($("#dialog-form").html());
			xile_loding($("#dialog-form"));
			$("#dialog-form").load($(this).attr("href")); //内容待定义
			//getdata();
			/*$( "#dialog-form").dialog("open");*/
			$( "#dialog-form").dialog({
				title:$(this).attr("title"),
				autoOpen:false,
				height:w,
				width:h,
				modal: true
			}).dialog("open");
			return false;
		});
	}			
}

/*通用弹窗 
传参
t = 需要遍历的 dom 数组, 或者 是 URL 
 t= 遍历的数组时会 自动绑定 click 事件并返回 false 阻止点击事件继续执行;
 t= url 传递 URL 不会自动onclick绑定事件,需要自行绑定事件. 无任何返回,需要自己根据需要自行阻止后续事件.
 t = 数组; 数组格式["我要输出的html"] 格式必须遵循
w = 弹出窗口宽度, 可选 默认400,
h = 弹出窗口高度, 可选 默认530, 如填写需补足w.
tit = 弹出窗口标题; 可选, 如 填写 则需要补足 w , h ; 如果默认值为 0 则也自己的title;
dom_id  = 新建弹出窗口的内容容器 id ,如页面中没有则自动补齐(设置该参数需要补齐其他参数);
默认取 this 的 title;
demo:

//传递dom
dialog_ajax_all($(".ajax")); 

//传递: URL
$(".delete").click(function(){
	dialog_ajax_all("del_gongzuoliu.html");
	return false;
});

//传递: html
var a = ["<a href='#'>我是一个链接</a> 3秒后关闭"];
$(".delete").click(function(){
	dialog_ajax_all(a,100,200,"提示框");
	setTimeout(function(){$('#dialog-form').dialog('close')},3000);
	return false;
});

// dom_id自建一个弹窗的div; 如填写此参数,需要补齐前面的参数
dom_id 
新建一个 div 用来作为内容容器;
默认dialog-form;
*/

/*数组方式传递弹窗值
list 必须的参数: 同dialog_ajax_all 的形参 t;
*/
function dialog_ajax_ko(sz){
	/*sz = {"list":$(".list"),"width":"400","height":"500","title":"我是弹窗","id":"dialog-form","modal":true}*/
	if(sz.list){
		var t = sz.list;
		var w = sz.width || 530;
		var h = sz.height || 400;
		var tit = sz.title|| false;
		var dom_id = sz.id || "dialog-form";
		var mod = sz.modal || false;
		if(sz.modal === undefined){mod = true;}
	}else{
		return ;	
	}
	dialog_ajax_all(t,w,h,tit,dom_id,mod);
}

function dialog_ajax_all(t,w,h,tit,dom_id,mod){
	w = w || 530;
	h = h || 400;
	tit = tit || 0;
	mod = mod || false;
	if(mod === undefined){mod = true;}
	dom_id = dom_id || "dialog-form";
	createDom(dom_id,"div");
	switch(Object.prototype.toString.apply(t)){
		case "[object Object]":
				var f = $(t);
				for(var i = 0 ; i < f.length ; i++){
					$($(f)[i]).click(function(){
						//alert($("#dialog-form").html());
						xile_loding($("#" + dom_id));
						$("#" + dom_id).load($(this).attr("href"),function(response,status,xhr){jq_load_er(response,status,xhr,dom_id)}); //内容待定义
						//getdata();
						/*$( "#dialog-form").dialog("open");*/
						var temptit =  tit || $(this).attr("title");
						dialog_do($("#" + dom_id),w,h,temptit,mod);
						return false;
					});
				}
				break ;
		case "[object String]":
					//alert($("#dialog-form").html());
					xile_loding($("#" + dom_id));
					$("#" + dom_id).load(t,function(response,status,xhr){jq_load_er(response,status,xhr,dom_id)}); //如过传进的参数是URL
					//getdata();
					/*$( "#dialog-form").dialog("open");*/
					var temptit =  tit || $(this).attr("title");
					dialog_do($("#" + dom_id),w,h,temptit,mod);
				break ;
		case "[object Array]" :
			  xile_loding($("#" + dom_id));
			  $("#" + dom_id).html(t[0]);
				//getdata();
				/*$( "#dialog-form").dialog("open");*/
				var temptit =  tit || $(this).attr("title");
				dialog_do($("#" + dom_id),w,h,temptit,mod);
			  break ;
		default :
			 return false;
	}
}

function jq_load_er(response,status,xhr,dom_id){
	if (status !=="success"){
		$("#" + dom_id).html("<font style='color:red;'>遇到错误了,请重试: <br/>An error occured: <br/>" + xhr.status + " " + xhr.statusText + "</font>");
	}	
}

/*弹窗函数
* d = 弹窗的盒子 JQuery 方式 dom
 w = 宽
 h = 高
 t = 标题
 mod  = 是否遮罩;
*/
function dialog_do(d,w,h,t,mod){
	if(mod === undefined){mod = true;}
	$(d).dialog({
		title:t,
		autoOpen:true,
		height:h,
		width:w,
		modal:mod
	}).dialog("open");
}

function xile_loding(t){
	$(t).html("加载中,请稍后........");
}

/*在 body后新增元素*/
function createDom(domId,tagname){
	if(document.getElementById(domId)){
		return ;		
	}
	this.domId = domId || "dialog-form";
	this.tagname = tagname || "div";
	if(!document.getElementById("#" + this.domId)){
		var dom_box = document.createElement("div") ;
		dom_box.id = this.domId;
		document.body.appendChild(dom_box);
		
	}	
}

function dialog_close(domid){
	$(domid).dialog('close');
}



