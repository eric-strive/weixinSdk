function upload(id,preview_id,input_name,buttonText,upload_url,delete_path){
    $("#"+id).uploadify({
        //开启调试
        'debug' : false,
        //是否自动上传
        'auto':true,
        //超时时间
        'successTimeout':99999,
        //flash
        'swf': "./Public/static/uploadify3_2_1/uploadify.swf",
        //文件选择后的容器ID
        'queueID':'uploadfileQueue',
        //服务器端脚本使用的文件对象的名称 $_FILES个['upload']
        'fileObjName':'upload',
        //上传处理程序
        'uploader':upload_url,
        //浏览按钮的背景图片路径
        'buttonImage':'',
        //浏览按钮的宽度
        'width':'100',
        //浏览按钮的高度
        'height':'32',
        'buttonText':buttonText,
        //在浏览窗口底部的文件类型下拉菜单中显示的文本
        'fileTypeDesc':'支持的格式：',
        //允许上传的文件后缀
        'fileTypeExts':'*.jpg;*.jpge;*.gif;*.png',
        //上传文件的大小限制
        'fileSizeLimit':'500kb',
        //上传数量
        'queueSizeLimit' : 1,
        //每次更新上载的文件的进展
        'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
            //有时候上传进度什么想自己个性化控制，可以利用这个方法
            //使用方法见官方说明
        },
        //选择上传文件后调用
        'onSelect' : function(file) {

        },
        //返回一个错误，选择文件的时候触发
        'onSelectError':function(file, errorCode, errorMsg){
            switch(errorCode) {
                case -100:
                    alert("上传的文件数量已经超出系统限制的"+$('#file_upload').uploadify('settings','queueSizeLimit')+"个文件！");
                    break;
                case -110:
                    alert("文件 ["+file.name+"] 大小超出系统限制的"+$('#file_upload').uploadify('settings','fileSizeLimit')+"大小！");
                    break;
                case -120:
                    alert("文件 ["+file.name+"] 大小异常！");
                    break;
                case -130:
                    alert("文件 ["+file.name+"] 类型不正确！");
                    break;
            }
        },
        //检测FLASH失败调用
        'onFallback':function(){
            alert("您未安装FLASH控件，无法上传图片！请安装FLASH控件后再试。");
        },
        //上传到服务器，服务器返回相应信息到data里
        'onUploadSuccess':function(file, data, response){
            var image_info = JSON.parse(data);
            var html = '<img src="'+image_info.url+'" alt="">';
            if(image_info.status){
                /*删除被替换的图片*/
                var old_path =  $("input[name='"+input_name+"']").val();
                if(old_path&& $.trim(old_path)!=""){
                    $.ajax({
                        type:"GET",
                        dataType:"JSON",
                        url:delete_path,
                        data:{delete_path:old_path},
                        success:function(data){
                            $("#"+preview_id).html(html);
                            $("input[name='"+input_name+"']").val(image_info.url);
                        }
                    });
                }else{
                    $("#"+preview_id).html(html);
                    $("input[name='"+input_name+"']").val(image_info.url);
                }

            }else{
                alert("上传失败，请重试！");
            }
        }
    });
}