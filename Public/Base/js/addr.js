/**
 * 载入XML文件并解析
 * @param  string xmlFile 文件路径
 * @return {[type]}         [description]
 */
function loadXML(xmlFile){
	var xmlDoc=null;
    //判断浏览器的类型
    //支持IE浏览器
    if(!window.DOMParser && window.ActiveXObject){
        var xmlDomVersions = ['MSXML.2.DOMDocument.6.0','MSXML.2.DOMDocument.3.0','Microsoft.XMLDOM'];
        for(var i=0;i<xmlDomVersions.length;i++){
            try{
                xmlDoc = new ActiveXObject(xmlDomVersions[i]);
                break;
            }catch(e){
            }
        }
    }
    //支持Mozilla浏览器
    else if(document.implementation && document.implementation.createDocument){
        try{
            /* document.implementation.createDocument('','',null); 方法的三个参数说明
             * 第一个参数是包含文档所使用的命名空间URI的字符串； 
             * 第二个参数是包含文档根元素名称的字符串； 
             * 第三个参数是要创建的文档类型（也称为doctype）
             */
            xmlDoc = document.implementation.createDocument('','',null);
        }catch(e){
        }
    }
    else{
        return null;
    }

    if(xmlDoc!=null){

      try{

        xmlDoc.async = false;
        xmlDoc.load(xmlFile);

      }catch(e){  //chrome的xmlDoc没有load属性,异步加载兼容

        try{

          var xmlhttp = new window.XMLHttpRequest();
          xmlhttp.open("GET",xmlFile,false);
          xmlhttp.send(null);
          xmlDoc = xmlhttp.responseXML;

        }catch(e){  //chrome的xmlDoc没有load属性,异步加载兼容

        }

      }

    }

    return xmlDoc;
}

//此函数已作废
function loadXML_chrome(fileRoute){
  xmlDoc=null;
  if (window.ActiveXObject){
      xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
      xmlDoc.async=false;
      xmlDoc.load(fileRoute);
  }
  else if (document.implementation && document.implementation.createDocument){
      var xmlhttp = new window.XMLHttpRequest();
      xmlhttp.open("GET",fileRoute,false);
      xmlhttp.send(null);
      var xmlDoc = xmlhttp.responseXML.documentElement;
  }
  else {xmlDoc=null;}
  return xmlDoc;
}


function checkXMLDocObj(xmlFile){
	var xmlDoc = loadXML(xmlFile);
  if(xmlDoc==null){

    alert('您的浏览器不支持xml文件读取,于是本页面禁止您的操作,推荐使用IE5.0以上可以解决此问题!');

  }
  return xmlDoc;
}

/**
 * 获取下级地区，以对象nodeList形式返回
 * @param  int pid 父级地区id
 * @return {[type]}     [description]
 */
function get_addr_next(pid){
  var cons = thinkphp_path_parse();

  var addr = loadXML(cons.__PUBLIC__+"/xml/region.xml");  //"/wulv/Public/xml/region.xml"

  if(pid === undefined || pid == 0){
    var next = addr.documentElement.childNodes;
  }else{
    var next = addr.getElementsByTagName("_"+pid)[0].childNodes;
  } 
  //alert(next[2].getAttribute("name"));
  return next;
}

/**
 * 获取地区id对应的地区名称
 * @param  int id 地区id
 * @return {[type]}    [description]
 */
function get_addr_name_byid(id){
  var cons = thinkphp_path_parse();

  var addr = loadXML(cons.__PUBLIC__+"/xml/region.xml");  //"/wulv/Public/xml/region.xml"

  var name = addr.getElementsByTagName("_"+id)[0].getAttribute('name');
  //alert(next);
  return name;

}

/**
 * 仿thinkphp的js路径解析
 * @return {[type]} [description]
 */
function thinkphp_path_parse(){

  var url= window.location.href;
  var reg = /(((.*)\/[a-zA-Z]+\.php)(\/*[a-zA-Z]*))(\/*[a-zA-Z]*)/;
  var match = url.match(reg);

  var __ROOT__ = match[3];
  var __APP__ = match[2];
  var __URL__ = match[4] ? match[1] : match[1] + "/Index";
  var __ACTION__ = match[5] ? match[0] : __URL__ + "/index";
  var __PUBLIC__ = __ROOT__ + "/Public";

  return {"__ROOT__":__ROOT__, "__APP__":__APP__, "__URL__":__URL__, "__ACTION__":__ACTION__, "__PUBLIC__":__PUBLIC__};

}

/**
 * 使用后台admin
 * @param  {[type]} pid [description]
 * @return {[type]}     [description]
 */
function admin_get_addr_next(pid){
    var cons = admin_thinkphp_path_parse();
    var addr = loadXML(cons.__PUBLIC__+"/xml/region.xml");
    if(pid === undefined || pid == 0){
      var next = addr.documentElement.childNodes;
    }else{
      var next = addr.getElementsByTagName("_"+pid)[0].childNodes;
    } 
    return next;
}

function admin_thinkphp_path_parse(){
    var url= window.location.href;
    if(url.indexOf('#')!=-1){
        url = url.substring(0,url.indexOf('#'));
    }
    var reg = /(((.*)\/[a-zA-Z]+(\.php)?)(\/*[a-zA-Z]*))(\/*[a-zA-Z]*)/;
    var match = url.match(reg);
    if(match[4]=='.php'){
        match[3] = match[3].substring(0,match[3].lastIndexOf('/'));
    }
    var __ROOT__ = match[3];
    var __PUBLIC__ = __ROOT__ + "/Public";
    
    return {"__ROOT__":__ROOT__, "__PUBLIC__":__PUBLIC__};

}
