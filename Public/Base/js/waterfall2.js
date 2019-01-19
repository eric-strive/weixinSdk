/**
 *  瀑布流封装
 *  黄开旺
 *   参数:container:存放新数据的容器；url:请求数据的链接,total_page:总页数
 *   
 *   使用规则：后台代码结合thinkphp分页，组装页面的html代码返回(若没有数据了，则输出0)，当前页面只显示第一页数据即可,该函数在页面加载完毕即可调用
 */

 function show_more(container,url,total_page){
    //页面初始化时执行瀑布流
    //给定一个隐藏域用于存放当前页面页码
    if($(".cur_page").length == 0){
    	container.append('<input type="hidden" class="cur_page" value="1">');
    }  
     //用户拖动滚动条，达到底部时ajax加载一次数据
    var loading = $("#loading").data("on", false);//通过给loading这个div增加属性on，来判断执行一次ajax请求
    $(window).scroll(function(){

        if(loading.data("on")) return;
        if($(document).scrollTop() >= $(document).height()-$(window).height()){
            //页面拖到底部了

            //加载更多数据
            
            var cur_page = container.find(".cur_page").val();
            if(parseInt(total_page)>parseInt(cur_page)){
            	loading.data("on", true).fadeIn();       //在这里将on设为true来阻止继续的ajax请求
	            cur_page++;

	            if($("#loading")){
	            	$("#loading").show();
	            }
	            $.get(
	            	url+"/p/"+cur_page,
	                function(data){
	                		if($("#loading")){
				            	$("#loading").hide();
				            }
	                        if(data != "0"){                      
	                            //获取到了数据data,后面用JS将数据新增到页面上
	                            container.find(".cur_page").val(cur_page);
	                            container.append(data);
	                        }
	                        //一次请求完成，将on设为false，可以进行下一次的请求
	                        loading.data("on", false);
	                        loading.fadeOut();
	                },
	                "html"
	            );
            }

        }
    });
 }