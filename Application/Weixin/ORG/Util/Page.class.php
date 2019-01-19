<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 3;// 分页栏每页显示的页数
	public $lastSuffix = false; // 最后一页是否显示总页数

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    public $nowPage = 1;

	// 分页显示定制
    private $config  = array(
    	'wrap'		=>'li',
        'header'	=> '<span class="rows">共<b>%TOTAL_ROW% </b>条记录，每页<b>%LIST_ROWS%</b>条，共计<b>%TOTAL_PAGE%</b>页 </span>',
        'prev'		=> '<a href="$url">«</a>',
        'next'		=> '<a href="$url">»</a>',
    	'current'	=> '<li class="active"><span>$page</span></li>',//当前页(私有定义)
    	'page'		=> '<a href="$url">$page</a>',//普通页
        'first'		=> '<a href="$url">1</a>',//首页
        'last'		=> '<a href="$url">$total</a>',//尾页
    	'pagination'=> '<nav><ul class="pagination pagination-sm">$pagination</ul></nav>',//最外层
    	'theme'		=> '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%', //不要 %HEADER%

    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()){

        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
        if(isset($this->parameter['_URL_'])) unset($this->parameter['_URL_']);//修正处理
        $this->nowPage    = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);



    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($config, $value=''){
		if(is_string($config)) $config = array($config => $value);

		foreach($config as $name => $value){
			if(isset($this->config[$name])) {
				$this->config[$name] = $value;
			}
		}
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show($options=array()){

    	if(!empty($options)) $this->config = array_merge($this->config, $options);

    	$wrap = array('first', 'last', 'prev', 'next', 'page');
    	if(!empty($this->config['wrap'])){
    		 foreach($wrap as $keyname){
    		 	$this->config[$keyname] = '<'.$this->config['wrap'].'>' . $this->config[$keyname] . '</'.$this->config['wrap'].'>';
    		 }
    	}

        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页零时变量 */
        $now_cool_page      = $this->rollPage/2;
		$now_cool_page_ceil = ceil($now_cool_page);
		$this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ?  str_replace('$url', $this->url($up_row), $this->config['prev'])  : str_replace('href="$url"', '', $this->config['prev']);

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ?  str_replace('$url', $this->url($down_row), $this->config['next']) : str_replace('href="$url"', '', $this->config['next']);

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first = str_replace('$url', $this->url(1), $this->config['first']);
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end =  str_replace(array('$url', '$total'), array($this->url($this->totalPages), $this->totalPages), $this->config['last']);
        }

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
			if(($this->nowPage - $now_cool_page) <= 0 ){
				$page = $i;
			}elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
				$page = $this->totalPages - $this->rollPage + $i;
			}else{
				$page = $this->nowPage - $now_cool_page_ceil + $i;
			}
            if($page > 0 && $page != $this->nowPage){

                if($page <= $this->totalPages){
                    $link_page .=   str_replace(array('$url', '$page'), array($this->url($page), $page), $this->config['page']);
                }else{
                    break;
                }
            }else{
                if($page > 0 && $this->totalPages != 1){
                    $link_page .= str_replace('$page',  $page, $this->config['current']);
                }
            }
        }

        //替换分页内容
        $pagelink = str_replace(array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%', '%LIST_ROWS%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages, $this->listRows),
        	$this->config['theme']);

        return str_replace('$pagination', $pagelink, $this->config['pagination']);

    }


}
