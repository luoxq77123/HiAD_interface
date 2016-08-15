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


/*
 树显示
*/

/*
  c = 绑定下面需要绑定的父元素id;
  d = 被操作元素的tag;
  a = 接受赋值 input 的id;
  b = 赋值当前操作元素 this ;
  *shijian = 是否绑定事件,默认绑定onmouseover/onclick/onmousemove :参数
  counter = 调用时最后回调的函数.;
*/
function _s_tab(tabid,tabname,a,shijian,counter,vtype){
	this.shijian = shijian || 0;
	a = a || 0;
	//alert(vtype)
	this.vtype = vtype || "value"
	var tabi,bodhtml;
	//alert(document.getElementById(tabid) + ":" + tabname);
	if(document.getElementById(tabid)){
		tabi = document.getElementById(tabid).getElementsByTagName(tabname);
	}else{return;}
	for(var i=0; i<tabi.length;i++){
		if(tabi[i]){
			tabi[i].leng = tabi.length;
			tabi[i].few = i;
			var fuhs = function(e){stopBubble(e);_s_tab_cl(tabid,tabname,a,this,counter,vtype);}; //执行函数
			if(tabi[i].getElementsByTagName("div")[0]){
				if(shijian == "onmouseover"){
				tabi[i].onmouseover = fuhs;
				}else if(shijian == "onclick"){
					tabi[i].onclick = fuhs;
				}else if(shijian == "onmousemove"){
					tabi[i].onmousemove = fuhs;
				}else{
					tabi[i].onclick = fuhs;	
				}
				if(tabi[i].getElementsByTagName("div")[0].getElementsByTagName("input")[0]){
					tabi[i].getElementsByTagName("div")[0].getElementsByTagName("input")[0].onclick = function(){inputall(this);};
				}
			}	
		}
	}
}

/*
  将当前元素的value 赋值给指定id值的input;
  c = 绑定下面需要绑定的父元素id;
  d = 被操作元素的tag;
  a = 接受赋值 input 的id;
  b = 赋值当前操作元素 this ;
  
*/
function _s_tab_cl(c,d,a,b,counter,vtype){
	var l = "";
	this.counter = counter || 0;
	this.vtype = vtype || "value";
	if(document.getElementById(c)){
		if(document.getElementById(c).getElementsByTagName(d)){
			l = document.getElementById(c).getElementsByTagName(d);
		}	
	}
	for( var i = 0; i < l.length ; i++ ){
		xile_removeClass(l[i],"now");
	}
	//alert(b.className)
	if(b.className != "now"){
		xile_addClass(b,"now");
		//b.className = "now";		
	}else{
		//xile_removeClass(b,"now")
		//b.className = "";
	}

	if(b.getElementsByTagName("ul") || !b.getElementsByTagName("ul").getElementsByTagName("ul")){
		var cl = b.getElementsByTagName("ul");
		//alert(b.childNodes[0])
		//var cn = b.childNodes.getElementsByTagName("ul");
		//alert(typeof(c));
		if(b.getElementsByTagName("ul")){
			if(cl[0]){
				if(cl[0].style.display != "block"){
					for( var i = 0; i < cl.length ; i++ ){
						if(cl[i].parentNode == b){
							cl[i].style.display = "block";
						}
					}
					_s_tab(c,"li",a,"onclick","");
				}else{
					if(cl[0].style.display == "block"){
						for( var i = 0; i < cl.length ; i++ ){
							cl[i].style.display = "none";
							/*
							var cl_v = 0;
							if(cl[i].getElementsByTagName("ul")){
								cl_v = cl[i].getElementsByTagName(d);
								for( var cl_vi = 0; cl_vi < cl_v.length ; cl_vi++ ){
									//if(l[lv_i].parentNode == b.parentNode){	
										cl_v[cl_vi].style.display = "none";		
									//}
								}
							}
							*/
						}
						//alert(cl[i])
						if(cl[i] && cl[i].getElementsByTagName("li")){
							var list = cl[i].getElementsByTagName("li");
							for( var li = 0; li < l.length ; li++ ){
								//l[i].className = "";
								xile_removeClass(l[i],"now")
							}	
						}
					}
				}
			}
		}
		
		for( var i = 0; i < l.length ; i++ ){
		//l[i].className = "";
		//xile_removeClass(l[i],"now");
		if(l[i].getElementsByTagName("ul")){
			//alert(l[i].getElementsByTagName("ul")[0].style)
		   try{
			   if(l[i].getElementsByTagName("ul")[0].style.display == "block"){
					if(l[i].getElementsByTagName("div") && l[i].getElementsByTagName("div")[0].getElementsByTagName("b")){
						var zkzy = l[i].getElementsByTagName("div")[0].getElementsByTagName("b")[0];
						xile_addClass(zkzy,"on");
					}   
			   }else{
				   if(l[i].getElementsByTagName("div") && l[i].getElementsByTagName("div")[0].getElementsByTagName("b")){
						var zkzy = l[i].getElementsByTagName("div")[0].getElementsByTagName("b")[0];
						xile_removeClass(zkzy,"on");
					}
			   }
			}
			catch(e){
					
			}
		   
		}
		
		/*
		if(l[i].parentNode == b.parentNode){
			l[i].className = "";	
			if(l[i].getElementsByTagName(d)){
				lv = l[i].getElementsByTagName(d);
			}
			for( var lv_i = 0; lv_i < lv.length ; lv_i++ ){
					//lv[lv_i].className = "";
					lv[lv_i].className = "";
			}
		}
		*/
	}	
	}
	if(b){
		//alert(this.vtype);
		_s_value(a,b,this.vtype);
	}
	if(this.counter){
		(this.counter)();
	}
	if(b.c){
		b.c ;	
	}
}
//阻止冒泡函数
function stopBubble(e){
	//如果传入了事件对象.那么就是非IE浏览器
	if(e){
		//因此它支持W3C的stopPropation()方法
		e.stopPropagation();
	}
	else{
		//否则,我们得使用IE的方式来取消事件冒泡
		window.event.cancelBubble = true;
	}
}

//选中赋值
function _la_value(a){
	/*
	if( document.getElementById("s_search_lb")){
		 document.getElementById("s_search_lb").value = a.value;
	}
	*/
	_s_value("s_search_lb",a.value);
	if(ttang_dt){
		ttang_dt.innerHTML = a.innerHTML;
	}
	var r = ttang_dd.onmouseout;
	//alert(document.getElementById("s_search_lb").value);
}

/*为input.value 赋值
a = input ID;
b = 赋值的this;
c = 被赋值类型;
*/
function  _s_value(a,b,c){
	this.c = c || "c";
	//alert(c);
	//alert(document.getElementById(a).innerHTML + ":" + b.innerHTML);
	if(a && document.getElementById(a)){
		var cidname = "请选择",adom = document.getElementById(a);
		if(b.getElementsByTagName("label") && b.getElementsByTagName("label")[0].getElementsByTagName("span") && !b.getElementsByTagName("ul")[0]){
			 cidname = b.getElementsByTagName("label")[0].getElementsByTagName("span")[0].innerHTML;
			 adom.innerHTML = cidname;
		}
		/*
		switch(this.c){
			case "innerHTML" :
				adom.innerHTML = cidname;
				break;
			case "value" :
				adom.value = b.value;
				//alert(this.c);
				break;
			default:
				adom.value = b.value;
				//alert(document.getElementById(a).innerHTML);
				break;
		}
		*/
	}	
}

/*定义一个表单提交函数*/
//function _s_bt(){document.search_from.submit();}
__domReady(function() {   
	//_s_tab("nav","li",0,"onclick",""); //为第一层赋值;
});

/*读取内容并输出
dom 需要赋值的对象
box 需要展示的盒子
prevent 可选 是否阻止默认 onclick 事件, 默认是阻止
*/
function loadhtml(dom,box,prevent){
	for(var i = 0 ; i < $(dom).length ; i++){
		$($(dom)[i]).click(function(){
			$(box).load($(this).attr("href"));
			return prevent =  prevent || false ;
		});
	}	
}

/*选中赋值 使用于 栏目选择管理中
 t 是 input 的选项
*/
function inputall(t){
	if($(t).attr("type") == "checkbox"){
		$tr = $(t).parents("div").siblings("ul").find("input[type=checkbox]");
			for(var i = 0 ; i < $tr.length ; i ++ ){
				if( $(t).attr( "checked")){
					$($tr[i]).attr( "checked",$(t).attr("checked"));
					
					//$($tr[i]).attr("disabled","disabled");
					//$($tr[i]).attr("readonly","readonly");
					/*选复层不能操作下层*/
				}else{
					//$($tr[i]).attr("disabled",false);
					//$($tr[i]).attr("readonly",false);
					/*选复层不能操作下层*/
					$($tr[i]).attr( "checked",false);
				}
		 	}
		if( $(t).parents("div").siblings("ul").length && $(t).parents("div").siblings("ul").css("display") == "block"){
			stopBubble();	
		}	
	}
}


/*
 选中赋值 使用于 栏目选择管理中
 t 是 input 的选项
 bod 盒子id
*/
function xile_input_all(t,bod){
	if($(t).attr("type") == "checkbox"){
		$tbod = $(bod).find("input[type=checkbox]");
		for(var i = 0 ; i < $tbod.length ; i ++ ){
			if( $(t).attr( "checked")){
				$($tbod[i]).attr( "checked",$(t).attr("checked"));
			}else{
				$($tbod[i]).attr( "checked",false);
			}
		}
		if($(t).attr( "checked")){
			return 1; //全选
		}else{
			return 0; //取消全选
		}
	}
}


/*
	权限管理,部门管理展开收起函数
	t 是传递 this 
*/
function bumen_tree(t,Prefix){
	Prefix = Prefix || "bumen_";
	var parent = $(t).parents("table").find("tr");
	var tid = Number( $(t).attr("id").replace(Prefix,"") );
	var zt = $(t).hasClass("collapsed") && $(t).css("display") != "none" ? "block" : "none";
	for(var i = 0 ; i < $(parent).length ; i ++ ){
		//alert( $( $(parent)[i] ).attr("parentid") + ":" + zt );
		if( tid == $( $(parent)[i] ).attr( "parentid" ) ){
			if(zt == "block"){
				$( $(parent)[i] ).show();
				$(t).removeClass("collapsed");
			}else{
				$( $(parent)[i] ).hide();
				$(t).addClass("collapsed");
				if( $( $(parent)[i] ).hasClass("parent") ){
					$($(parent)[i] ).click();	
				}
			}
		}
	}
}

/*选中赋值 使用于 栏目选择管理中
 t 是 input 的选项
*/
function inputall(t){
	if($(t).attr("type") == "checkbox"){
		$tr = $(t).parents("div").siblings("ul").find("input[type=checkbox]");
			for(var i = 0 ; i < $tr.length ; i ++ ){
				if( $(t).attr( "checked")){
					$($tr[i]).attr( "checked",$(t).attr("checked"));
					
					//$($tr[i]).attr("disabled","disabled");
					//$($tr[i]).attr("readonly","readonly");
					/*选复层不能操作下层*/
				}else{
					//$($tr[i]).attr("disabled",false);
					//$($tr[i]).attr("readonly",false);
					/*选复层不能操作下层*/
					$($tr[i]).attr( "checked",false);
				}
		 	}
		if( $(t).parents("div").siblings("ul").length && $(t).parents("div").siblings("ul").css("display") == "block"){
			stopBubble();	
		}	
	}
}

/*选中赋值 使用于 权限和角色等的管理中
t = 操作dom 本身 input
Prefix = 可选 需要匹配的前缀 默认 bumen_
parentid = 父亲 tr 的 Prefix + ID;
*/
function bumen_inputall(t,Prefix){
	Prefix = Prefix || "bumen_";
	if($(t).attr("type") == "checkbox"){
		$tr = $(t).parents("tr").siblings("tr");
		var tid = Number( $(t).parents("tr").attr("id").replace(Prefix,"") );
		//$tr = $(t).parents("tr").siblings("tr").find("input[type=checkbox]");
			for(var i = 0 ; i < $tr.length ; i ++ ){
				//alert(tid + ":" + $( $tr[i] ).attr( "parentid" ));
				if( tid == $( $tr[i] ).attr( "parentid" ) ){
					if( $(t).attr("checked") ){
						$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr( "checked",$(t).attr("checked"));
						$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr("disabled","disabled");
						//$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr("readonly","readonly");
						/*选复层不能操作下层*/
					}else{
						$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr("disabled",false);
						//$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr("readonly",false);
						/*选复层不能操作下层*/
						$( $( $tr[i] ).find("input[type=checkbox]")[0] ).attr( "checked",false);						
					}
					if( $( $tr[i] ).hasClass("parent") ){
						bumen_inputall( $( $( $tr[i] ).find( "input[type=checkbox]")[0] ));	//自循环向下
					}
				}
		 	}
	}
}

/*函数类库
xiaocong
*/
/*addclass*/
var xile_addClass = function addClass(node,className){
	if(!xile_hasClass(node,className)){
		return node.className += " " + className;	
	}
} 

/*hasclass*/
var xile_hasClass = function(element,className){
     return new RegExp("(^|\\s)"+className+"(\\s|$)").test(element.className);
}

/*removeClass*/
var xile_removeClass = function(node,className){
    eles = node.className.split(/\s+/);        //先将已有的class放进数组
    for(var i = 0,l = eles.length; i < l; i++){
        if(eles[i] == className){
            eles.splice(i,1);                          //再遍历删除指定的class
        }
    }
    node.className = eles.join(" ");          //最后将新的数组用空格隔开重新添加回className
    return node;
}

var getElementsByClassName = function (searchClass,node,tag) {
  if(document.getElementsByClassName){
    var nodes =  (node || document).getElementsByClassName(searchClass),result = [];
      for(var i=0 ;node = nodes[i++];){
        if(tag && tag !== "*" && node.tagName === tag.toUpperCase()){
          result.push(node)
        }else{
          result.push(node)
        }
      }
      return result
    }else{
      node = node || document;
      tag = tag || "*";
      var classes = searchClass.split(" "),
      elements = (tag === "*" && node.all)? node.all : node.getElementsByTagName(tag),
      patterns = [],
      current,
      matchp;
	  //alert(node.all);
      var i = classes.length;
      while(--i >= 0){
        patterns.push(new RegExp("(^|\\s)" + classes[i] + "(\\s|$)"));
      }
      var j = elements.length;
	   //alert(j);
      while(--j >= 0){
        current = elements[j];
        matchp = false;
        for(var k=0, kl=patterns.length; k<kl; k++){
		 //alert(current.className);
          matchp = patterns[k].test(current.className);
          if (!matchp)  break;
        }
        if (matchp) result.push(current);	
      }
      return result;
    }
}
  
function showMyToolTips(obj, title, description) {
	var btip = 'div_tooltips_box';
    if ($("#"+btip).length==0) {
        $("body").prepend('<div class="docBubble" id="'+btip+'"><i class="triangle-l"></i><div class="tl"><div class="inner"><div class="box_hoverTips"><div class="box_top_hoverTips"><span><var></var></span></div> <div class="tips_cont"><dl><dt id="tooltips_title">'+title+'</dt><dd id="tooltips_description">'+description+'</dd></dl></div> <div class="box_bottom_hoverTips"><span><var></var></span></div></div></div></div><div class="tr"></div><div class="bl"></div></div>');
    }
	$("#tooltips_title").html(title);
	$("#tooltips_description").html(description);
	var offset,h ,w ;
	offset = $(obj).offset();
	h = $(obj).height();
	w = $(obj).width();
	$("#"+btip).css({ "left":offset.left+w+10  ,  "top":offset.top+h/2-23 }).show();
}

function hideMyToolTips() {
	var btip = 'div_tooltips_box';
	if ($("#"+btip).length>0) {
		$("#"+btip).hide();
	}
}

function banner_message(message){
    $("#banner_message .message_area").html(message);
    $("#banner_message").slideDown();
}
    
