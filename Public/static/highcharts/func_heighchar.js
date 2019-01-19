
/**
 * js动态传参,可点击
 * title:categories数组，X轴的数组
 * text_left:左边y轴的文字显示。text_right:右边y轴的文字显示。
 * data_left:左边y轴对应数据显示。data_right:右边y轴对应数据显示。
 * data_left_name:左边y轴对应数据名称;data_right_name:右边y轴对应数据名称;
 * 
 */
function drawChart_click(data){
    var id,title,text_left,text_right,data_left,data_right,data_left_name,data_right_name,color_y1,color_y2;
    if(typeof(data) == "undefined"){
        id         = '';
        text_lab   = '';
        title      = '';
        text_left  ='';
        text_right ='';
        data_left  ='';
        data_right ='';
        data_left_name  ='';
        data_right_name = '';
        color_y1        = '#f29700';
        color_y2        = '#000000';
    }else{
        id            = data['id'];
        text_lab      = data['text_lab'];
        title         = data['title'];
        text_left     = data['text_left'];
        text_right    = data['text_right'];
        data_left     = data['data_left'];
        data_right    = data['data_right'];
        data_left_name   = data['data_left_name'];
        data_right_name  = data['data_right_name'];
        color_y1        = data['color_y1'][0];
        color_y2        = data['color_y2'][0];
    }

    $("#"+id+"").highcharts({
        chart: {
            renderTo:'container',
            type: 'line',
        },
        title: {
            text: text_lab
        },
        xAxis: {
            categories: title,
            labels : {
                rotation : -20,
                align : 'center'
            },
            tickmarkPlacement : 'on'
        },
        yAxis: [{
            title: {
                text: text_left
            },
            allowDecimals:false //是否允许刻度有小数
        },{
            title: {
                text: text_right
           },
           allowDecimals:false, //是否允许刻度有小数
           opposite:true
        }],
          /*工具提示*/
        tooltip: {
            /*如果你需要给你图表的某一部分添加某些功能的话，就可以去查找Highcharts的函数库，像这样添加就可以了*/
            formatter: function () {
                if(this.series.name == data_left_name){ 
                    return '<b>' + this.series.name + '</b><br/>' +this.y+'元';
                }else{
                    return '<b>' + this.series.name + '</b><br/>' +this.y+'笔';
                }
             }
         },
        series: [{
            name: data_left_name,
            yAxis:0,
            data: data_left,
            color: color_y1
        }, {
            name: data_right_name,
            yAxis:1,
            data: data_right,
            color: color_y2
        }],
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: true
            },
           series : {
               point:{
                    events : {
                        click: function(events) {
                            //y1的点击事件
                            if(this.series.name == data_left_name){
                                point_click_y1();
                            }
                            //y2的点击事件
                            if(this.series.name == data_right_name){
                                point_click_y2();
                            }
                        }
                    }
                }
            }

        },
    });
};    


/**
 * js动态传参，不可点击
 * title:categories数组，X轴的数组
 * text_left:左边y轴的文字显示。text_right:右边y轴的文字显示。
 * data_left:左边y轴对应数据显示。data_right:右边y轴对应数据显示。
 * data_left_name:左边y轴对应数据名称;data_right_name:右边y轴对应数据名称;
 * 
 */
function drawChart_unclick(data){
    var id,title,text_left,text_right,data_left,data_right,data_left_name,data_right_name;
    if(typeof(data) == "undefined"){
        id         = '';
        text_lab   = '';
        title      = '';
        text_left  ='';
        text_right ='';
        data_left  ='';
        data_right ='';
        data_left_name  ='';
        data_right_name = '';
        color_y1        = '#f29700';
        color_y2        = '#000000';
    }else{
        id            = data['id'];
        text_lab      = data['text_lab'];
        title         = data['title'];
        text_left     = data['text_left'];
        text_right    = data['text_right'];
        data_left     = data['data_left'];
        data_right    = data['data_right'];
        data_left_name   = data['data_left_name'];
        data_right_name  = data['data_right_name'];
        color_y1        = data['color_y1'][0];
        color_y2        = data['color_y2'][0];
    }

    $("#"+id+"").highcharts({
        chart: {
            renderTo:'container',
            type: 'line',
        },
        title: {
            text: text_lab
        },
        xAxis: {
            categories: title,
            labels : {
                rotation : -20,
                align : 'center'
            },
            tickmarkPlacement : 'on'
        },
        yAxis: [{
            title: {
                text: text_left
            },
            allowDecimals:false //是否允许刻度有小数
        },{
            title: {
                text: text_right
           },
           allowDecimals:false, //是否允许刻度有小数
           opposite:true
        }],
          /*工具提示*/
        tooltip: {
            /*如果你需要给你图表的某一部分添加某些功能的话，就可以去查找Highcharts的函数库，像这样添加就可以了*/
            formatter: function () {
                if(this.series.name == data_left_name){ 
                    return '<b>' + this.series.name + '</b><br/>' +this.y+'元';
                }else{
                    return '<b>' + this.series.name + '</b><br/>' +this.y+'笔';
                }
             }
         },
        series: [{
            name: data_left_name,
            yAxis:0,
            data: data_left,
            color: color_y1
        }, {
            name: data_right_name,
            yAxis:1,
            data: data_right,
            color: color_y2
        }],
    });
};    



/**
 *双y轴，点击弹窗的方法
 *y1点击函数封装 
 */
function point_click_y2(){
     alert('y2');
}

/**
 *双y轴，点击弹窗的方法
 *y1点击函数封装 
 */
function point_click_y1(){
     alert('y1');
}

