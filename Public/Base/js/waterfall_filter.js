//该js文件统一整合瀑布流与筛选功能（黄开旺）

//筛选函数
function filter_form(){	
	//点击筛选页面重新置1；
	$(".cur_page").val("1");
	//滚动条到顶部
	$(window).scrollTop(0);
	//获取form表单的所有数据
	var data = $(".filter_form").serialize();
	//获取form表单的提交页面
	var form_url = $(".filter_form").attr("action");
	if("#loading"){
		$("#loading").show();
	}

	$.ajax({
	  data:data,
	  type:"POST",
	  url:form_url,
	  dataType:"html",
	  success:function(data){
	  	 if("#loading"){
			$("#loading").hide();
		 }
	     $(".filter_container").html(data);
	  }
	})
	
}
//瀑布流函数
function water_fall(){
    //页面初始化时执行瀑布流
    //给定一个隐藏域用于存放当前页面页码
    if($(".cur_page").length == 0){
    	$(".filter_form").append('<input type="hidden" class="cur_page" value="1" name="p">');
    }  
     //用户拖动滚动条，达到底部时ajax加载一次数据
    var loading = $("#loading").data("on", false);//通过给loading这个div增加属性on，来判断执行一次ajax请求
    $(window).scroll(function(){

        if(loading.data("on")) return;
        if($(document).scrollTop() >= $(document).height()-$(window).height()){
            //页面拖到底部了
            //加载更多数据
            var form_url = $(".filter_form").attr("action");
            var cur_page = $(".filter_form").find(".cur_page").val();
            var total_page = $(".filter_form").find(".total_page").val();
            if(parseInt(total_page)>parseInt(cur_page)){
            	loading.data("on", true).fadeIn();       //在这里将on设为true来阻止继续的ajax请求
	            cur_page++;

	            if($("#loading")){
	            	$("#loading").show();
	            }
	            $(".filter_form").find(".cur_page").val(cur_page);
	            var data = $(".filter_form").serialize();
        		$.ajax({
				  data:data,
				  type:"POST",
				  url:form_url,
				  dataType:"html",
				  success:function(data){
				  	if("#loading"){
						$("#loading").hide();
					}
				    if(data != "0"){                      
                        //获取到了数据data,后面用JS将数据新增到页面上                       
                        $(".filter_container").append(data);
                    }
                    //一次请求完成，将on设为false，可以进行下一次的请求
                    loading.data("on", false);
                    loading.fadeOut();
				  }
				})
            }

        }
    });
}
//创建隐藏表单函数
function create_form(){
	//判断form表单是否存在，不存在则创建
	if($(".filter_form").length == 0){
		var cur_action = window.location.href;
		var form_html = '<form action="'+window.location.href+'" method="post" class="filter_form"><input type="hidden" class="cur_page" value="1" name="p"></form>'
		$(".filter_container").after(form_html);
	}
}

function show_none(){
	$(".filter_container").find(".filter_tips").show().delay(2000).hide(0);
}

function get_total_page(){
    var data = $(".filter_form").serialize();
    var form_url = $(".filter_form").attr("action");
    $("#loading").show();
    $.ajax({
        data:data,
        type:"POST",
        url:form_url+"/get_total_page/1",
        dataType:"json",
        success:function(data){
            $("#loading").hide();
            $(".total_page").val(data.total_page);
            filter_form();
            /*if(parseInt(data.total_page) > 0){
                filter_form();
            }else{
                show_none();
            }*/
        }
    })
}

//验证信息是否全面
function check_use(){
	var can_use = true;
	if($(".filter_container").length == 0){
		alert("没有filter_container");
		can_use = false;
		return false;
	}
	if($(".filter_form").length == 0){
		alert("没有filter_form");
		can_use = false;
		return false;
	}else{
		if($(".filter_form").attr("action") == ""){
			alert("没有action");
			can_use = false;
			return false;
		}
		if($(".filter_form").find(".cur_page").val() == "" || $(".filter_form").find(".cur_page").length == 0){
			alert("没有cur_page");
			can_use = false;
			return false;
		}
		if($(".filter_form").find(".total_page").val() == "" || $(".filter_form").find(".total_page").length == 0){
			alert("没有total_page");
			can_use = false;
			return false;
		}
	}
	if($("#loading").length == 0){
		alert("没有loading");
	}
	return can_use;
	
}
	
//主函数
function show_more(){
	if(check_use){
		create_form();
		water_fall();
		if($(".filter_submit").length > 0){
			$(".filter_submit").click(function(){
				filter_form();
			})			
		}		
	}
}
