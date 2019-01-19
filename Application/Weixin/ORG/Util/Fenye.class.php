<?php

	class Fenye{
		
		public $pageNo=1;		//页码
		public $pageSize=10;	//页尺寸
		public $count=0;		//数据总数
		public $pageCount=0;	//总页数
		public $pageNext=0;		//下一页
		public $pagePrev=0;		//上一页
		
		
		public function init($data){
			
			$this->count=count($data);	//数据总数
			$this->pageCount=ceil($this->count/$this->pageSize);	//总页数
			$this->pageNo=isset($_GET['pageNo'])?$_GET['pageNo']:1;	//页码
			$this->pageNext=$this->pageNo+1;	//下一页
			$this->pagePrev=$this->pageNo-1;	//上一页
			//判断页码越界
			if($this->pageNext>$this->pageCount) $this->pageNext=$this->pageCount;
			if($this->pagePrev<1) $this->pagePrev=1;	
			if($this->pageNo>$this->pageCount) $this->pageNo=$this->pageCount;
			if($this->pageNo<1) $this->pageNo=1;	
			
			$offset=($this->pageNo-1)*$this->pageSize;	//偏移量
			$data= array_slice($data,$offset,$this->pageSize);


			return $data;
			
		}
		
		//获取分页信息
		public function getPager($url){
			$str='共'.$this->pageCount.'页 ';
			$str.='共'.$this->count.'条数据 ';
			$str.='当前第'.$this->pageNo.'页 ';
			$str.='每页'.$this->pageSize.'条 ';
			$str.="<a href=".$url."/pageNo/1>第一页</a>";
			$str.="<a href=".$url."/pageNo/{$this->pagePrev}>上一页</a>";
			$str.="<a href=".$url."/pageNo/{$this->pageNext}>下一页</a>";
			$str.="<a href=".$url."/pageNo/{$this->pageCount}>最末页</a>";
			return $str;
		}
		
		
		
		
	}