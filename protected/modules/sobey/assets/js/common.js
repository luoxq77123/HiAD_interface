// JavaScript Document by xiaicong & feng


var $id = function (id) 
{
	return document.getElementById(id);
}


//导航切换
function  tab_nav(num) {
	for (var id=1; id<=16;id++){
		if(id==num){
				if($id("tab_nav"+id)){
					$id("tab_nav"+id).style.display="block";
				}
			}
			else{
					if($id("tab_nav"+id)){
						$id("tab_nav"+id).style.display="none";
					}
				}		
		}
}

//城市台导航切换
function  tab_pdnav(num,le){
	for (var id=1; id<=le;id++){
		if(id==num){
				if($id("tab_nav"+id)){
					$id("tab_nav"+id).style.display="block";
				}
		}else{
			if($id("tab_nav"+id)){
				$id("tab_nav"+id).style.display="none";
			}
		}		
	}
}



//主持人 tab切换
function  tab_zh(num,tid,bid,tol) {
	for (var id=1;id<=tol;id++){		
		if(id==num){
			if($id(tid+id)){
				$id(tid+id).className="fw700";
			}
			if($id(bid+id)){
				$id(bid+id).style.display="block";
			}
		}else{
			if($id(tid+id)){
				$id(tid+id).className="fw300";
			}
			if($id(bid+id)){
				$id(bid+id).style.display="none";
			}
		}
	}
}

//评论,推荐切换
function tab_qh(idt,idd){
	/*
	var zt=$id(idt).className;
	if(zt==idt){
		if($id(idt)){
			$id(idt).className=idt+"no";
		}
		if($id(idd)){
			$id(idd).className=idd;
		}
	}
	else{
		if($id(idt)){
			$id(idt).className=idt;
		}
		if($id(idd)){
			$id(idd).className=idd+"no" ;
		}
	}
	*/
}

var hdjshuqi = 1;

//视频滚动
function Vod(j)
{	
	if($id("hdleibiao")){
		var a = $id("hdleibiao"); 
		for(var i=1;i<=a.getElementsByTagName("span").length;i++)
		{
			if(i==j)
			{   
				if($id("hdleibiao"+i)){
					$id("hdleibiao"+i).className="ihdleibiao ihdleibiaonow";
				}
				if($id("hdtupian"+i)){
					$id("hdtupian"+i).style.display="block";
				}
				hdjshuqi = j;
			}
			else
			{
				if($id("hdleibiao"+i)){
					$id("hdleibiao"+i).className="ihdleibiao";
				}
				if($id("hdtupian"+i)){
					$id("hdtupian"+i).style.display="none";
				}
			}
		}
	}
}

function zdqhVod(){
	var lbtp = 0;
	if($id("hdleibiao")){
		lbtp = $id("hdleibiao").getElementsByTagName("span").length;
	}
	Vod(hdjshuqi);
	//alert(lbtp);
	hdjshuqi++
	if(hdjshuqi>lbtp){
		hdjshuqi = 1;	
	}		
}

/*
*tabid = 切换按钮ID
*tabname = 切换按钮子元素TAGName
*bodid = 切换内容ID
*bodname = 切换内容子元素TAGName
*shijian = 是否绑定事件,默认绑定onmouseover/onclick/onmousemove :参数:
*/


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

/*
*自运行幻灯
*listbod tab 区的一个切换子元素
*counter 计数器变量名
*/
function _autoplay(listbod,counter){
	if(listbod.tagName){
		this.numfu = listbod.parentNode.getElementsByTagName(listbod.tagName.toLowerCase());
	}
	if(window[counter] < this.numfu.length - 1 &&this.numfu.length>1){
		window[counter]++;		
	}else {
		window[counter] = 0	;
	}
	qh_newhd(this.numfu[window[counter]],counter);
	//alert(window[counter]);
}



$(document).ready(function(){
	var _indexhd = window.setInterval("zdqhVod();",5000);
	if(document.getElementById("inhuandeng")){
		document.getElementById("inhuandeng").onmouseover = function(){_indexhd = clearInterval(_indexhd);};
		document.getElementById("inhuandeng").onmouseout =function() {_indexhd = setInterval("zdqhVod();",5000);};
	}
});


//评论推荐
function Vod_paike(j)
{	
	if($id("dbtuijian")){
		var a = $id("dbtuijian");  
		for(var i=1;i<=a.getElementsByTagName("span").length;i++)
		{	
			if($id("dbtj"+i)){
				//if(!$id("dbtj"+i).className)
					//continue;
				if(i==j)
				{
					$id("dbtj"+i).className="dbtjnow";
				}
				else
				{
					$id("dbtj"+i).className="";
				}
			}
		}
	}
}


//滚动条

jQuery.fn.setScroll = function(_scroll,_scroll_up,_scroll_down,_scroll_bar){
    this.each(function(){
        var _bar_margin = 3;
        //create scroll dom
        var _scroll_control = jQuery('<div class="scroll_zone">').width(_scroll.width).css({'position':'absolute','float':'none',margin:0,padding:0}).css('background','url('+_scroll.img+')');
        var _scroll_control_up = jQuery('<img class="scroll_down">').attr('src',_scroll_up.img).width(_scroll.width).css({'z-index':'1000','position':'absolute', 'top':'0','float':'none',margin:0,padding:0});
        var _scroll_control_down = jQuery('<img class="scroll_down">').attr('src',_scroll_down.img).width(_scroll.width).css({'z-index':'1000','position':'absolute', 'bottom':'0','float':'none',margin:0,padding:0});
        var _scroll_control_bar =  jQuery('<img class="scroll_bar">').attr('src',_scroll_bar.img).width(_scroll.width).css({'z-index':'1500','position':'absolute','float':'none',margin:0,padding:0,height:_scroll_bar.height+'px'}).css('top',_scroll_up.height+_bar_margin+'px');
 
        _scroll_control.append(_scroll_control_up);
        _scroll_control.append(_scroll_control_bar);
        _scroll_control.append(_scroll_control_down);
 
        var _oheight = jQuery(this).css('height').substring(0,jQuery(this).css('height').indexOf('px'));
        var _owidth = jQuery(this).width();
        var _ocontent = jQuery(this).html();
 
        if(jQuery(this).attr('scrollHeight')<=_oheight) return;
 
        var _content_zone = jQuery('<div>').html(_ocontent).css({ width:_owidth-10+'px',height:_oheight+'px',overflow:'hidden','float':'none',margin:0,padding:0});
 
        jQuery(this).css({'overflow':'hidden'});
        jQuery(this).empty().append(_content_zone).css({position:'relative'}).append(_scroll_control.css({left:_owidth-_scroll.width+'px',top:'0',height:_oheight+'px',margin:0,padding:0}));
 
        //register drag event
        jQuery(this).find('.scroll_bar')
        .mousedown(
            function(){
                jQuery(document).mousemove(
                    function(e){
                      var _content = _content_zone.get(0);
                      var lastProgress = _scroll_control_bar.attr('progress');
                      _scroll_control_bar.attr('progress',e.pageY);
                      var nowProgress = _scroll_control_bar.css('top');
                      nowProgress = nowProgress.substring(0,nowProgress.indexOf('px'));
                      nowProgress = Number(nowProgress) + Number(e.pageY-lastProgress);
                      var preProgress = nowProgress/(_oheight-_scroll_up.height-_scroll_down.height-_scroll_bar.height-(2*_bar_margin));
                      _content.scrollTop = ((_content.scrollHeight - _content.offsetHeight) * preProgress);
                      if(nowProgress<(_scroll_up.height+_bar_margin) || nowProgress > (_oheight-(_scroll_down.height+_scroll_bar.height+_bar_margin))) return false;
                      try{_scroll_control_bar.css('top',nowProgress+'px');}catch(e){}
                      return false;
                    }
                );
                return false;
            }
        )
        .mouseout(
            function(){
                jQuery(document).mouseup(
                    function(){
                        jQuery(document).unbind('mousemove');
                     }
                )
            }
        )
 
    }); 
}


//copy

function dqurl(){
	  return window.location;
	}

var copytoclip=1;
function copyToClipboard(theField,isalert) {		
	var tempval=document.getElementById(theField);		
	if (navigator.appVersion.match(/\bMSIE\b/)){
		tempval.select();		
		if (copytoclip==1){
			therange=tempval.createTextRange();
			therange.execCommand("Copy");
			if(isalert!=false)alert("复制成功。现在您可以粘贴（Ctrl+v）到Blog 或BBS中,或者发送给QQ/MSN好友了。");
		}
		return;
	}else{
		alert("您使用的浏览器不支持此复制功能，请使用Ctrl+C或鼠标右键。");
		tempval.select();		
	}
}

//分享
function fxceng(onid,bt){
	var xsq = "fenxiangk"; //要显示的区块ID
	var xsbt = "fxkdt"; //要显示的标题ID
	var xsnr = "fenxiangkbod"; //要内容盒子ID
    var neirong = $id(onid).innerHTML;
	if($id(xsq)){
		$id(xsq).style.display="block";
	}
	if($id(xsbt)){
		$id(xsbt).innerHTML = bt;
	}
	if($id(xsnr)){
		$id(xsnr).innerHTML = neirong;	
	}
	
}
function fxcengoff(){
	if($id("fenxiangk")){
		$id("fenxiangk").style.display="none";
	}
	if($id("dbbofangqi")){
		$id("dbbofangqi").style.display="block";
	}
}
	
	
//收藏
function addBookmark(title,url) {
	if (window.sidebar) { 
		window.sidebar.addPanel(title, url,""); 
	} else if( document.all ) {
		window.external.AddFavorite( url, title);
	} else if( window.opera && window.print ) {
		return true;
	}
}

//详细信息展开收起

	 function xZhankai(onid,offid){
		 	if($id(onid)){
				$id(onid).style.display = "none";
			}
			if($id(offid)){
				$id(offid).style.display = "inline";
			}

		 }
		 
//栏目最新最热显示

function  lmQh(num) {
	for (var id=1; id<=4;id++){
		if(id==num){
			if(num==1){
				if(document.getElementById("lmQhBt1")){
					document.getElementById("lmQhBt1").setAttribute("href","index.shtml");	
				}
				$id("fyBq").style.display="block";
				$id("qhNext").style.display="block";
				}else{
					$id("fyBq").style.display="none";
				    $id("qhNext").style.display="none";
					}
			$id("jmzhanshi"+id).style.display="block";
			$id("lmQhBt"+id).className="jmlbtitle4";
			}
			else{
				$id("jmzhanshi"+id).style.display="none";
			$id("lmQhBt"+id).className="";
				}		
		}
}


//开关灯
function kaiGuanDeng(bgid,niuid){
	var text=$id(niuid).title;
	if(text == "开灯"){
		if($id(bgid)){
			$id(bgid).style.display="none";
			$id(bgid).style.opacity=0.6;
		}
		if($id(niuid)){
			$id(niuid).title="关灯";
			$id(niuid).innerHTML="关灯";
			$id(niuid).className="";
		}
	}else{
		if($id(bgid)){
			$id(bgid).style.display="block";
			$id(bgid).style.opacity=0.6;
			$id(bgid).style.height=document.body.clientHeight+"px";
		}
		if($id(niuid)){
			$id(niuid).title="开灯";
			$id(niuid).innerHTML="开灯";
			$id(niuid).className="kaidniu";
		}
	}
}

/*弹出层*/
var TINY={};

function T$(i){return document.getElementById(i)}

TINY.box=function(){
	var p,m,b,fn,ic,iu,iw,ih,ia,f=0;
	return{
		show:function(c,u,w,h,a,t){
			if(!f){
				p=document.createElement('div'); p.id='tinybox';
				m=document.createElement('div'); m.id='tinymask';
				b=document.createElement('div'); b.id='tinycontent';
				document.body.appendChild(m); document.body.appendChild(p); p.appendChild(b);
				m.onclick=TINY.box.hide; window.onresize=TINY.box.resize; f=1
			}
			if(!a&&!u){
				p.style.width=w?w+'px':'auto'; p.style.height=h?h+'px':'auto';
				p.style.backgroundImage='none'; b.innerHTML=c
			}else{
				b.style.display='none'; p.style.width=p.style.height='100px'
			}
			this.mask();
			ic=c; iu=u; iw=w; ih=h; ia=a; this.alpha(m,1,10,3);
			if(t){setTimeout(function(){TINY.box.hide()},1000*t)}
		},
		fill:function(c,u,w,h,a){
			if(u){
				p.style.backgroundImage='';
				var x=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject('Microsoft.XMLHTTP');
				x.onreadystatechange=function(){
					if(x.readyState==4&&x.status==200){TINY.box.psh(x.responseText,w,h,a)}
				};
				x.open('GET',c,1); x.send(null)
			}else{
				this.psh(c,w,h,a)
			}
		},
		psh:function(c,w,h,a){
			if(a){
				if(!w||!h){
					var x=p.style.width, y=p.style.height; b.innerHTML=c;
					p.style.width=w?w+'px':''; p.style.height=h?h+'px':'';
					b.style.display='';
					w=parseInt(b.offsetWidth); h=parseInt(b.offsetHeight);
					b.style.display='none'; p.style.width=x; p.style.height=y;
				}else{
					b.innerHTML=c
				}
				this.size(p,w,h,4)
			}else{
				p.style.backgroundImage='none'
			}
		},
		hide:function(){
			TINY.box.alpha(p,-1,0,5)
		},
		resize:function(){
			TINY.box.pos(); TINY.box.mask()
		},
		mask:function(){
			m.style.height=TINY.page.theight()+'px';
			m.style.width=''; m.style.width=TINY.page.twidth()+'px'
		},
		pos:function(){
			var t=(TINY.page.height()/2)-(p.offsetHeight/2); t=t<10?10:t;
			p.style.top=(t+TINY.page.top())+'px';
			p.style.left=(TINY.page.width()/2)-(p.offsetWidth/2)+'px'
		},
		alpha:function(e,d,a,s){
			clearInterval(e.ai);
			if(d==1){
				e.style.opacity=0; e.style.filter='alpha(opacity=0)';
				e.style.display='block'; this.pos()
			}
			e.ai=setInterval(function(){TINY.box.twalpha(e,a,d,s)},20)
		},
		twalpha:function(e,a,d,s){
			var o=Math.round(e.style.opacity*100);
			if(o==a){
				clearInterval(e.ai);
				if(d==-1){
					e.style.display='none';
					e==p?TINY.box.alpha(m,-1,0,3):b.innerHTML=p.style.backgroundImage=''
				}else{
					e==m?this.alpha(p,1,100,5):TINY.box.fill(ic,iu,iw,ih,ia)
				}
			}else{
				var n=o+Math.ceil(Math.abs(a-o)/s)*d;
				e.style.opacity=n/100; e.style.filter='alpha(opacity='+n+')'
			}
		},
		size:function(e,w,h,s){
			e=typeof e=='object'?e:T$(e); clearInterval(e.si);
			var ow=e.offsetWidth, oh=e.offsetHeight,
			wo=ow-parseInt(e.style.width), ho=oh-parseInt(e.style.height);
			var wd=ow-wo>w?-1:1, hd=(oh-ho>h)?-1:1;
			e.si=setInterval(function(){TINY.box.twsize(e,w,wo,wd,h,ho,hd,s)},20)
		},
		twsize:function(e,w,wo,wd,h,ho,hd,s){
			var ow=e.offsetWidth-wo, oh=e.offsetHeight-ho;
			if(ow==w&&oh==h){
				clearInterval(e.si); p.style.backgroundImage='none'; b.style.display='block'
			}else{
				if(ow!=w){e.style.width=ow+(Math.ceil(Math.abs(w-ow)/s)*wd)+'px'}
				if(oh!=h){e.style.height=oh+(Math.ceil(Math.abs(h-oh)/s)*hd)+'px'}
				this.pos()
			}
		}
	}
}();

TINY.page=function(){
	return{
		top:function(){return document.body.scrollTop||document.documentElement.scrollTop},
		width:function(){return self.innerWidth||document.documentElement.clientWidth},
		height:function(){return self.innerHeight||document.documentElement.clientHeight},
		theight:function(){
			var d=document, b=d.body, e=d.documentElement;
			return Math.max(Math.max(b.scrollHeight,e.scrollHeight),Math.max(b.clientHeight,e.clientHeight))
		},
		twidth:function(){
			var d=document, b=d.body, e=d.documentElement;
			return Math.max(Math.max(b.scrollWidth,e.scrollWidth),Math.max(b.clientWidth,e.clientWidth))
		}
	}
}();

function qiehuan(n){ 
	for(i=0;i<=8;i++){ 
		if(n==i){ 
			document.getElementById(i).className = "on";
			document.getElementById("p"+i).style.display = "block"; 
		} 
		else{ 
			document.getElementById(i).className = "out";
			document.getElementById("p"+i).style.display = "none";  
		} 
	} 
}

function userBrowser(){
	var browserName=navigator.userAgent.toLowerCase();
	if(/msie/i.test(browserName) && !/opera/.test(browserName)){
		return "IE";
	}else if(/firefox/i.test(browserName)){
		return "Firefox";
	}else if(/chrome/i.test(browserName) && /webkit/i.test(browserName) && /mozilla/i.test(browserName)){
		return "Chrome";
	}else if(/opera/i.test(browserName)){
		return "Opera";
	}else if(/iphone|ipad|ipod/i.test(browserName)){
		return "iphone";
	}else if(/webkit/i.test(browserName) &&!(/chrome/i.test(browserName) && /webkit/i.test(browserName) && /mozilla/i.test(browserName))){
		return "Safari";
	}else{
		return "unKnow";
	}
}
