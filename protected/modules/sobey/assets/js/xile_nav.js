/*
*导航切换显示
*喜乐导航
*Author:xiaocong
*Email:xiepinxi@gmail.com
*/

//Document ready
function __clear(timer){
    clearTimeout(timer);
    clearInterval(timer);
    return null;
};

function __attach_event(evt, callback) {
    if (window.addEventListener) {
        window.addEventListener(evt, callback, false); 
    } else if (window.attachEvent) {
        window.attachEvent("on" + evt, callback);
    }
}

function __domReady(f) {
    // 假如 DOM 已经加载，马上执行函数
    if (__domReady.done) return f();
    // 假如我们已经增加了一个函数
    if (__domReady.timer) {
        // 把它加入待执行函数清单中
        __domReady.ready.push(f);
    } else {
        // 为页面加载完毕绑定一个事件，
        // 以防它最先完成。使用addEvent(该函数见下一章)。
        __attach_event("load", __isDOMReady);
        // 初始化待执行函数的数组
        __domReady.ready = [f];
        // 尽可能快地检查DOM是否已可用
        __domReady.timer = setInterval(__isDOMReady, 100);
    }
}
function __isDOMReady() {
    // 如果我们能判断出DOM已可用，忽略
    if (__domReady.done) return false;
    // 检查若干函数和元素是否可用
    if (document && document.getElementsByTagName && document.getElementById && document.body) {
        // 如果可用，我们可以停止检查
        __clear(__domReady.timer);
        __domReady.timer = null;
        // 执行所有正等待的函数
        for ( var i = 0; i < __domReady.ready.length; i++ ) {
            __domReady.ready[i]();
        }
        // 记录我们在此已经完成
        __domReady.ready = null;
        __domReady.done = true;
    }
}


// 页面加载完成后使用
/* code

__domReady(function() {   
	//qlPicListWidth() 
});

*/



/*
*tabid = 切换按钮ID
*tabname = 切换按钮子元素TAGName
*bodid = 切换内容ID
*bodname = 切换内容子元素TAGName
*shijian = 是否绑定事件,默认绑定onmouseover/onclick/onmousemove :参数:
*/

function _ntabqh(tabid,tabname,bodid,bodname,shijian,counter){
	this.shijian = shijian || 0;
	this.counter = counter || 0;
	var tabi,bodhtml;
	if(document.getElementById(tabid)){
		tabi = document.getElementById(tabid).getElementsByTagName(tabname);
	}else{return ;}
	if(document.getElementById(bodid)){
		bodhtml = document.getElementById(bodid).getElementsByTagName(bodname);
	}else{
		
	}
	for(var i=0; i<tabi.length;i++){
		if(tabi[i]){
			if(bodhtml){
				if(bodhtml[i]){
					tabi[i].bod = bodhtml[i];
					bodhtml[i].pr = tabi[i];
				}	
			}
			tabi[i].leng = tabi.length;
			tabi[i].few = i;
			var fuhs = function(){qh_newhd(this,this.counter);}; //执行函数
			if(shijian == "onmouseover"){
				tabi[i].onmouseover = fuhs;
			}else if(shijian == "onclick"){
				tabi[i].onclick = fuhs;
			}else if(shijian == "onmousemove"){
				tabi[i].onmousemove = fuhs;
			}else{
				tabi[i].onmouseover = fuhs;	
			}
		}
	}
}

/*
*需要先运行一次_ntabqh()
*num = 传递值可以是切换子元素的this 或序列号(从0开始);
*counter = 计数器变量名. 如果传递的是序列号 必须同时传递counter 否则不会给序列号赋值.
*/
function qh_newhd(num,counter){
	this.counter = counter || 0;
	//alert(this.counter);
	//alert(typeof(num));
	if(num.tagName){
		var parN = num.parentNode.getElementsByTagName(num.tagName.toLowerCase());
	}else{return ;}
	//alert(num.tagName);
	//var bodparN = num.bod.parentNode.getElementsByTagName(num.bod.tagName.toLowerCase());
	//alert(num.bod.innerHTML)
	for(var i = 0; i<num.leng;i++){
		if(i == num.few){
			if(parN[i]){
				parN[i].className = "now";	
			}
			if(num.bod){
				//alert(num.few+"<br />"+parN[i].bod.style.display);
				parN[i].bod.style.display = "block";	
			}
			if(this.counter){
				window[this.counter] = i;
			}
		}else{
			if(parN[i]){
				parN[i].className = "";	
			}
			if(num.bod){
				parN[i].bod.style.display = "none";	
			}
		}
	}
}

//简单隐藏弹出
function xile_show_hide(id,nid){
	this.nid = nid || "";
	if(document.getElementById(nid)){document.getElementById(nid).style.display = "none";}
	if(!document.getElementById(id)){ return ;}
	var a = document.getElementById(id);
	a.style.display = ((a.style.display != "none") ? "none" : "block");
	if(a.style.display != "none"){
		//$(document).click(function(){xile_show_hide(id,nid);$(document).click(null)});
		//alert(1);
		document.onclick = close_class_treebox;
		//close_class_treebox();
	}
	//return false;	
}



function close_class_treebox(){
	//alert(2);
		//alert(3);
		var clnv = $(".treebox");
		for(var i = 0 ; i < clnv.length ; i++){
			clnv[i].style.display = "none";	
			//alert(clnv[i].style.display);
		}
	document.onclick = null;
}

function addusertabs(t){
	for(var i = 0 ; i < $(".tabs .tabt").length ; i++ ){
		if( i == $(t).index() ){
			$($(".tabs .tabt")[i]).addClass("now");	
		}else{
			$($(".tabs .tabt")[i]).removeClass("now");	
		}
	}
	for(var i = 0 ; i < $(".tabs .tabb").length ; i++ ){
		if( i == $(t).index() ){
			$($(".tabs .tabb")[$(t).index()]).show();
		}else{
			$($(".tabs .tabb")[i]).hide();
		}
	}
}

/**/

//直播页滑动显示直播频道列表
var x__timeout = 500;    //定时器
var x__closetimer = 0; //定时后执行函数
var x__ddmenuitem = 0; //存储执行函数
function x__openbox(tid,did){
	x__cbox();
	x__jsddm_canceltimer();
	//alert(did);
	if(document.getElementById(did)){
		//alert(1);
		document.getElementById(did).style.display = "block";	
	}
	//$("#"+did).show();
	//$("#"+tid).addClass("now");
}
function x__cbox(){
	//关闭函数
	//jsddm_canceltimer();
	if(document.getElementById("bod_new_model")){
		document.getElementById("bod_new_model").style.display = "none";
	}
	//$("#zbqhlbid > dd").hide();
	//$("#zbqhlbid > dt").removeClass("now");
}
function x__jsddm_timer(){
	x__closetimer = window.setTimeout(x__cbox,x__timeout);
}
function x__jsddm_canceltimer(){
	if(x__closetimer){
		window.clearTimeout(x__closetimer);
		x__closetimer = null;
	}
}


/*
工作流管理
*/
//增加步骤
function step_add(){
	$("#roles").html($("#roles").html() + step_tpl($("#roles > li").length+1));
}

//删除步骤
function step_delete(num){
	$($("#roles > li")[num-1]).remove();
	for(var i = (num-1);i < $("#roles > li").length ; i++){
		$($("#roles > li")[i]).attr( "id" ,( "step_" + ( i + 1) ) );
		$($($("#roles > li")[i]).children("span")[0]).html(i+1);
		$($($("#roles > li")[i]).children("select")[0]).attr("name",("roleid[" + i + 1 + "]" ) );
		$($($("#roles > li")[i]).children("img")[0]).click(function(){
			step_delete(i-1);	
		})
	}
}

//工作流步骤模板
function step_tpl(num){
	var tplhtml;
	tplhtml = "<li id='step_ " + num + " '> " ;
	tplhtml += "第<span>" + num + "</span>步： " ;
	tplhtml += "<select name='roleid[" + num + "]' id='roleid[" + num + "]'>";
	tplhtml += $("#step_roleid").html();
	tplhtml += "</select>";
	tplhtml += " <img src='" + $("#step_del_img").attr("src") + "' alt='删除' width='16' height='16' class='hand' onclick='step_delete(\" " + num + " \");'>";
	tplhtml += "</li>";
	return tplhtml;
}

/*图片库相关函数*/

var tupianku = function(t){
	this.t = t;
	this.inputall = function(){
		$(this.t).find("input[type=checkbox]").attr("checked","checked");
	}
	this.removechecked = function(){
		$(this.t).find("input[type=checkbox]").attr("checked",false);
	}
	this.Negated = function(){
		var inputlist = $(this.t).find("input[type=checkbox]");
		for(var i = 0 ; i < inputlist.length ; i++ ){
			$( inputlist[i] ).attr("checked") ? $( inputlist[i] ).attr("checked",false) : $( inputlist[i] ).attr("checked","checked") ;
		}
	}
	this.tumb = function(){
		$(this.t).removeClass("imglist");
		$(this.t).addClass("imgthumlist");
	}
	this.list = function(){
		$(this.t).removeClass("imgthumlist");
		$(this.t).addClass("imglist");
	}
}


/*一级列表全选*/
function listinputall(t,box){
	 var thf =  $(box).find("input[type=checkbox]");						
	 for( var i = 0 ; i < thf.length ; i++ ){
		 if($(t).attr("checked")){
			 $(thf[i]).attr("checked","checked");
		 }else{
			 $(thf[i]).attr("checked",false);
		 }
	 }
}

/*
页面ajax 刷新;
*/

function ajax_load(boxid,url){    
    jLoadingAlert();
	$("#" + boxid).load(url,function(response,status,xhr){jq_load_erore(response,status,xhr,boxid)})
}

function frame_load(url, refresh){
    // 链接锚点
    if(refresh){
        ajax_load('frame_container',url);
    }else{
        window.location = location.pathname + "#" + url;
        return false;
    }
}

function jq_load_erore(response,status,xhr,dom_id){
    jLoadingAlert(true);
	if (status !=="success"){
		$("#" + dom_id).html("<font style='color:red;'>遇到错误了,请重试: <br/>An error occured: <br/>" + xhr.status + " " + xhr.statusText + "</font>");
	}	
}

function xl_loding(t){
	$(t).html("加载中,请稍后........")
}

var now_nav_num = 0;
function now_nav(i){
	if(document.getElementById("#vnavtab")){
			
	}		
}

/*批量添加关联导航
	.tool_20_link   关联 #tool_20;
	plgl_nav(["tool_20","tool_42"]);
*/
function plgl_nav(d){
	for(var i in d){
		var iddd = String(d[i])
		$("." + iddd + "_link").attr("i",iddd);
		$("." + iddd + "_link").click(function(){
			$("#"+$(this).attr("i")).click();
			return false;
		});	
	}
}

/*左右隐藏*/
function hide_left(l,r){
	if($(l).css("display")=="none"){
		$(r).css("padding","0px").css("width","85%");
		$(l).css("display","block");
		$("#hideleft").removeClass("l_h");
	}else{
		$(l).css("display","none");	
		$(r).css("width","100%");
		$("#hideleft").addClass("l_h");
	}
}







