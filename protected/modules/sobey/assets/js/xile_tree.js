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

/* end 函数类库
xiaocong
*/


