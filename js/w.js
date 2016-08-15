var HIMI_tout;
var HIMI = {
    HIMI_objs : new Array(),
    HIMI_preload_arr : new Array(),
    HIMI_load_arr : new Array(),
    HIMI_frame_html : new Array(),
    HIMI_client : {
        config:{
            //frame_url:'http://ad.dev/hiad-interface/Himi'
            frame_url:'http://localhost/hiad/hiad-interface/Himi',
            interface_url:'http://localhost/hiad/hiad-interface'
        },
        referer:function (){
            if(document.referrer == '' ){
                return 'null';
            }
            return document.referrer;
        },
        sWidth: function(){
            return screen.width
        },
        sHeight:function(){
            return screen.height
        },
        isFlash:function(){
            var hasFlash = false;
            var flashVersion = 0;
            if(window.ActiveXObject){
                var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
                if(swf){
                    hasFlash = true;
                    VSwf=swf.GetVariable("$version");
                    flashVersion=parseInt(VSwf.split(" ")[1].split(",")[0]);
                }
            }else{
                if(navigator.plugins && navigator.plugins.length > 0){
                    var swf = navigator.plugins["Shockwave Flash"];
                    if(swf){
                        hasFlash = true;
                        var words = swf.description.split(" ");
                        for(var i=0; i<navigator.plugins.length;i++) if(isNaN(parseInt(words[i]))) continue;
                        flashVersion = parseInt(words[i]);
                    }
                }
            }
            return hasFlash;
        }
    },
    setObj :   function (_obj,_w,_h,_t,_b,_l,_r,_p){
        if(_w){
            _obj.style.width = _w;
        }
        if(_h){
            _obj.style.height = _h;
        }
        if(_t){
            _obj.style.top = _t;
        }else if(_b){
            _obj.style.bottom = _b;
        }
        if(_l){
            _obj.style.left = _l;
        }else if(_r){
            _obj.style.right = _r;
        }
        if(_p){
            _obj.style.position = _p;
        }
    },
    scroll : function (){
        var IE6=(navigator.userAgent.indexOf("MSIE")>0 && navigator.appVersion.match(/6./i)=="6." ),hg = 0,o=true;
        var tops = new Array();
        var objs = this.HIMI_objs;
		
        window.onscroll = function (){
            hg = document.documentElement.scrollTop;	
            for(var i=0;i< objs.length;i++){
                if(o){
                    tops[i] = objs[i].style.top;
                }
                if(!IE6){
                    objs[i].style.position = "fixed";
                    objs[i].style.top = top;
                }else{
                    objs[i].style.position = "absolute";
                    objs[i].style.top = (parseInt(tops[i])+hg) +"px";
                }
            }
            o = false;
        };
    },
    type3 : function (pid,width,height,top,bottom,left,right){
        var str = '';
        if(top){
            str +='top='+top;
        }else{
            str +='bottom='+bottom;
        }
        if(left){
            str +=',left='+left;
        }else{
            str +=',right='+right;
        }
        var OpenWindow = window.open('', 'win', 'height='+height+', width='+width+','+str+',toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no');
        OpenWindow.document.write("<TITLE></TITLE>");

        OpenWindow.document.write("<BODYBGCOLOR=#ffffff>");

        OpenWindow.document.write(this.HIMI_frame_html[pid]);

        OpenWindow.document.write("</BODY>");

        OpenWindow.document.write("</HTML>");

        OpenWindow.document.close();
        return true;
    },
    process : function (obj,d){
        if(d > 0) {
            obj.style.filter='alpha(opacity='+(d*100)+')';
            obj.style.opacity = d;
            obj.style.MozOpacity = d
            d = d - 0.1;
            setTimeout(function(){
                process(obj,d);
            },200);
        } else {
            obj.style.display = 'none';
        }
    },
    setFrame : function (param){
        var json = eval(param);
        var obj = null;
        if(json.pId){
            obj =  document.getElementById(pId);
            if(json.height){
                obj.style.height = json.height;
            }
            if(json.position){
                obj.style.position = json.position;
            }
            if(json.positionTop){
                obj.style.top =  json.positionTop;
            }else if(json.positionBottom){
                obj.style.bottom = json.positionBottom;
            }
            if(json.positionLeft){
                obj.style.left = json.positionLeft;
            }else if(json.positionRight){
                obj.style.right = json.positionRight;
            }
            if(json.width){
                obj.style.width = json.width;
            }
        }
    },
    HIMI_CLB_renderFrame : function(id){
        var b=document.getElementById("HIMI_renderFrame"+id);
        try{
            var e=b.contentWindow.document;
            e.open("text/html","replace"); 
            if(this.HIMI_frame_html[id]){
                e.write(this.HIMI_frame_html[id]);
            }
            //e.close();
            e.body&&(e.body.style.backgroundColor="transparent",e.body.style.height='100%',e.body.style.width='100%',e.body.style.padding='0px',e.body.style.margin='0px');
        }catch(f){
        }
    },
    SETIFRAMEHTML : function (param){
        if(param.mrotate_mode){
            if(param.mrotate_mode==1 || param.mrotate_mode==2){
                if(param.mrotate[0].material_type_id == 1){                        
                    this.HIMI_frame_html[param.pId] = '<span style="word-wrap:break-word;"><a target="_blank" '+param.mrotate[0].style+' href="'+param.mrotate[0].link+'">'+param.mrotate[0].text+'</a></span>';
                }else if(param.mrotate[0].material_type_id == 2){
                    this.HIMI_frame_html[param.pId] = '<a href="'+param.mrotate[0].link+'" target="_blank" ><img src="'+param.mrotate[0].url+'" title="" alt="" border="0" height="100%" width="100%" /></a>';
                }else if(param.mrotate[0].material_type_id == 3){
                    this.HIMI_frame_html[param.pId] = '<div style="font-size: 0px;"><a href="'+param.mrotate[0].link+'" target="_blank" style="position:absolute;top:0;left:0;bottom:0;right:0;display:block;width:100%;height:expression(this.parentNode.scrollHeight);filter:alpha(opacity=0);opacity:0;background:#FFF;"></a><object width="'+param._w+'" height="'+param._h+'" align="middle"  codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="never" name="allowScriptAccess"><param value="high" name="quality"><param value="transparent" name="wmode"><param value="'+param.mrotate[0].url+'" name="movie"><embed width="'+param._w+'" height="'+param._h+'" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="never" quality="high" name="HIMI_CLB_FLASH_N1350463616093" src="'+param.mrotate[0].url+'" wmode="transparent"></embed></object></div>';
                }else if(param.mrotate[0].material_type_id == 4){
                    if (param.mrotate[0].template_mode==2) {
                        var objBody = document.getElementsByTagName('BODY').item(0);
                        var objDiv = document.createElement("ins");
                        var str = param.mrotate[0].content;
                        var arrStr = new Array();
                        arrStr = str.split('<script type="text/javascript">');
                        objDiv.innerHTML = arrStr[0];
                        if (typeof(objBody.childNodes[0])=='object'){
                            objBody.insertBefore(objDiv,objBody.childNodes[0]);
                        }else{
                            objBody.appendChild(objDiv);
                        }
                        eval(arrStr[1].replace('</script>', ''));
                        var oHead = document.getElementsByTagName('HEAD').item(0); 
                        var oScript= document.createElement("script"); 
                        oScript.type = "text/javascript"; 
                        oScript.charset = "utf-8";
                        oScript.text = arrStr[1].replace('</script>', ''); 
                        oHead.appendChild( oScript);
                    } else {
                        this.HIMI_frame_html[param.pId] = param.mrotate[0].content;
                    }
                }
                         
            }else if(param.mrotate_mode==3){
                var str = '';
                for(var i=0;i<param.mrotate.length;i++){                
                    if(param.mrotate[i].url){
                        if(param.mrotate[i].material_type_id == 1){
                            str += '<div style="word-wrap:break-word;"><a target="_blank" '+param.mrotate[i].style+' href="'+param.mrotate[i].link+'">'+param.mrotate[i].text+'</a></div>';
                        }else if(param.mrotate[i].material_type_id == 2){
                            str += '<div style="" > <a href="'+param.mrotate[i].link+'" target="_blank" ><img src="'+param.mrotate[i].url+'" title="" alt="" border="0" height="100%" width="100%" /></a></div>';
                        }               
                    }
                }
                this.HIMI_frame_html[param.pId] = '<div id="bd_ec_clb_asp" style="width: 100%; height: 100%; overflow: hidden;"> '+str+'  </div><script>(function(){var d = document;function G(id) {return d.getElementById(id);};var container = G("bd_ec_clb_asp");var pages = container.children;var pl = 0;for (var i = 0; i < container.children.length; i++) { if (container.children[i].nodeType === 1) {pl++; }}var cp = 0;function showPage(pn) {pages[pn].style.display = "";};function hidePages() {for (var i = 0; i < pl; i++) {pages[i].style.display = "none";}};function roll() {hidePages();showPage(cp);cp == (pages.length - 1) ? cp = 0 : cp++;};var autoRoll;function setRoll() {autoRoll = window.setInterval(function() {roll();}, '+(param.mrotate_time*1000)+');};roll();setRoll();container.onmouseover = function() {window.clearInterval(autoRoll);};container.onmouseout = function() {setRoll();}})();</script>';
            }
        }
    },
    SET_HIMI_POSITION : function (param){
        var json = eval(param);
        var html='<div style="height:15px;border:1px solid #e1e1e1;background:#f0f0f0;margin:0;padding:0;overflow:hidden;"><span style="float: right; clear: right; margin: 2px 5px 0px 0px; width: 39px; height: 13px; cursor: pointer; background-image: url('+this.HIMI_client.config.interface_url+'/js/close.png);background-attachment: scroll; background-position: 0px 0px; background-repeat: no-repeat no-repeat;" onmouseover="this.style.backgroundPosition=\'0 -20px\';"onmouseout="this.style.backgroundPosition=\'0 0\';"onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);"></span></div>';
        this.pId,this._url,this.type,this._h,this._w,this._t = null,this._l = null,this._r= null,this._b= null,this.stayTime,this.showTime;
        if(json.type){
            this.type = json.type;
        }
        if(json._h){
            this._h = json._h;
        }
        if(json.pId){
            this.pId = json.pId;
        }
        if(json._t){
            this._t =  json._t;
        }
        if(json._l){
            this._l = json._l;
        }
        if(json._r){
            this._r = json._r;
        }
        if(json._b){
            this._b = json._b;
        }
        if(json._w){
            this._w = json._w;
        }
        if(json.showTime){
            this.showTime = json.showTime;
        }
        if(json._url){
            this._url = json._url;
        }	
        if(json.mrotate){   
            this.SETIFRAMEHTML(json);
        }
        if(this.type == 1){
            if(this.pId){
                var obj = document.getElementById('himi_position_div_'+this.pId);
                if(obj||json.mrotate[0].material_type_id!=4||json.mrotate[0].template_mode!=2){
                    this.setObj(obj,this._w,this._h,this._t,this._b,this._l,this._r);
                    obj.innerHTML = "<iframe id='HIMI_renderFrame" + this.pId + "'  scrolling='no' frameborder='0'   src='about:blank' style='height:"+this._h+";width:"+this._w+"' onload='HIMI_CLB_renderFrame(" + this.pId + ");'></iframe>";
                }
            }
        }else if(this.type == 2){
            if(this.pId){
                var obj = document.getElementById('himi_position_div_'+this.pId); 
                if(obj){
                    if(param.scroll){
                        this.HIMI_objs.push(obj);
                    }                
                    this.setObj(obj,this._w,this._h,this._t,this._b,this._l,this._r,'absolute');
                    obj.innerHTML = "<iframe id='HIMI_renderFrame" + this.pId + "' scrolling='no' frameborder='0' src='about:blank' style='height:" + this._h + ";width:" + this._w + "' onload='HIMI_CLB_renderFrame(" + this.pId + ");'></iframe>" + html;
                    if(this.HIMI_objs.length>0){
                        this.scroll();
                    }
                }
            }
        }
        if(this.type == 3){
            this.type3(this.pId,this._w,this._h,this._t,this._b,this._l,this._r);
        }
        return false;
        if(json.stayTime){
            if(this.pId){
                var obj = document.getElementById('himi_position_div_'+this.pId);
                setTimeout(function (){
                    process(obj,1);
                },json.stayTime*1000);
            }
        }      
    },
    HIMI_PERLOAD : function (pIds){
        if(pIds.length > 0){
            for(var i=0; i<pIds.length; i++){
                if(!this.in_array(pIds[i], this.HIMI_load_arr)){
                    this.HIMI_load_arr.push(pIds[i]);
                    this.HIMI_preload_arr.push(pIds[i]);
                }
            }
            window.onload = function(){
                HIMI_tout = setInterval("HIMI.HIMI_PERLOAD_do()", 200);
            }
        }
    },
    HIMI_PERLOAD_do: function(){
        if(document.readyState=='complete'){
            this.LOAD_POSITION_AD_DATA(this.HIMI_preload_arr.join(','), true);
            clearInterval(HIMI_tout);
        }
    },
    HIMI_POSITION_INIT : function (pId){
        document.write('<div id="himi_position_div_'+pId+'"><\/div>');        
        var obj = document.getElementById('himi_position_div_'+pId);
        if(obj){         
            obj.style.display = 'block';
        }
        if(pId && !this.in_array(pId, this.HIMI_load_arr)){
            this.HIMI_load_arr.push(pId);
            this.LOAD_POSITION_AD_DATA(pId);
        }
    },
    HIMI_PLAYER_POSITION : function (pId, videoUrl, vwidth, vheight){
        vwidth = parseInt(vwidth)<=0? 632 : parseInt(vwidth);
        vheight = parseInt(vheight)<=0? 505 : parseInt(vheight);
        var interface_url = this.HIMI_client.config.interface_url;
        var css_url = interface_url+'/js/video-player/history/history.css';
        var js1_url = interface_url+'/js/video-player/history/history.js';
        var js2_url = interface_url+'/js/video-player/swfobject.js';
        document.write('<link rel="stylesheet" type="text/css" href="'+css_url+'" />');
        document.write('<script charset="utf-8" src="'+js1_url+'"></script>');
        document.write('<script charset="utf-8" src="'+js2_url+'"></script>');
        var html_js = '<script type="text/javascript">\
        	function getPlugins(){\
        	    return \'[{"source":"'+interface_url+'/js/video-player/com/sobey/player/plugin/modules/AdHiAdMsPlugin.swf","rc":"1","blockLoading":"true","positionId":"'+pId+'","host":"'+interface_url+'/dataService","blockPlaying":"true"}]\';\
			}\
            var swfVersionStr = "10.1.0";\
            var xiSwfUrlStr = "'+interface_url+'/js/video-player/playerProductInstall.swf";\
			var flashvars = {};\
            flashvars.logging=true;\
            flashvars.logLevel="all"; \
            flashvars.plugin=true;\
			flashvars.volume=50;\
			flashvars.loop=false;\
			flashvars.skin="'+interface_url+'/js/video-player/black2_0";\
			flashvars.host="http://127.0.0.1";\
			flashvars.autoLoad=true;\
			flashvars.autoPlay=true;\
			flashvars.bufferTime=5;\
			flashvars.mode=1;\
			flashvars.configable=true;\
	        flashvars.streamType="seekabelVod";\
			flashvars.seekParam="timecode=ms";\
			flashvars.smoothing=true;   \
			flashvars.initMedia="skins/8.jpg";\
			flashvars.isshowcontrol=true;\
			flashvars.loadinglogo="LOGO.png";\
			flashvars.isfullscreen=true;\
			flashvars.ispause=true;\
			flashvars.isfullscreen=true;\
			flashvars.url= "'+videoUrl+'";\
			flashvars.bottomrightlogo = "http://127.0.0.1/rightlogo.jpg";\
            var params = {};\
            params.quality = "high"; \
            params.bgcolor = "#ffffff";\
            params.allowscriptaccess = "always";\
            params.allowfullscreen = "true";\
            var attributes = {};\
            attributes.id = "SoPlayer";\
            attributes.name = "SoPlayer";\
            attributes.align = "middle";\
            swfobject.embedSWF(\
                "'+interface_url+'/js/video-player/SoPlayer.swf", \
                "HIMI_PLAYER_WARPPER_'+pId+'", \
                "'+vwidth+'", "'+vheight+'", \
                swfVersionStr, xiSwfUrlStr, \
                flashvars, params, attributes);\
            swfobject.createCSS("#HIMI_PLAYER_WARPPER_'+pId+'", "display:block;text-align:right;");\
        </script>';
        document.write(html_js);
    },
    HIMI_PLAYER_POSITION_HTML : function(pId){
        document.write('<div id="HIMI_PLAYER_WARPPER_'+pId+'" url=""></div>');
    },
    LOAD_POSITION_AD_DATA: function(pId_str, is_preload){
        var url = this.HIMI_client.config.frame_url+'?Pid='+pId_str+'&referer='+this.HIMI_client.referer()+'&sWidth='+this.HIMI_client.sWidth()+'&sHeight='+this.HIMI_client.sHeight()+'&isFlash='+this.HIMI_client.isFlash();
        if(is_preload){
            var oHead = document.getElementsByTagName('HEAD').item(0); 
            var oScript= document.createElement("script"); 
            oScript.type = "text/javascript"; 
            oScript.charset = "utf-8";
            oScript.src = url; 
            oHead.appendChild( oScript); 
        }else{
            document.write('<script charset="utf-8" src="'+url+'"></script>');
        }
    },
    in_array: function(o, arr){
        for(i=0;i<arr.length;i++){
            if(arr[i] == o)
                return true;
        }
        return false;
    }
};
HIMI_CLB_renderFrame = function (pId){
    HIMI.HIMI_CLB_renderFrame(pId);
}
HIMI_PERLOAD = function (){
    var pId_str = arguments;
    HIMI.HIMI_PERLOAD(pId_str);
}
HIMI_POSITION_INIT = function (pId){
    HIMI.HIMI_POSITION_INIT(pId);
}
SET_HIMI_POSITION = function (param){
    HIMI.SET_HIMI_POSITION(param);    
}
HIMI_PLAYER_POSITION = function (pId, videoUrl, vwidth, vheight){
    HIMI.HIMI_PLAYER_POSITION(pId, videoUrl, vwidth, vheight);
}
HIMI_PLAYER_POSITION_HTML = function (pId){
    HIMI.HIMI_PLAYER_POSITION_HTML(pId);
}
process = function(obj, index) {
    HIMI.process(obj, index);
}