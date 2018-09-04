<?php

namespace Tool\Utils;
/**

* Project: 分页类

* Sub Project: 分页类
* 例子
* $pages= & get_singleton("Service_Page");
$pages->_page_no=$page_no;
$pages->_total=1000;
$pages->_url=url("Default","Index");
$conent=$pages->page();

* Author: libin

* Date: 2009年08月05日

* File: Page.php

* Version: 1.0

*/
class UtilPage {
    
	private static $_page_no=1;
	private static $_page_size=10;
	private static $_page_num=0;
	private static $_total=0;
	private static $_parm=array();
	private static $_url="";
	//大的分页
	private static $_big_page=10;
	/**
	 * 初始化分页类
	 * 
	 * @param unknown $total
	 * @param unknown $url
	 * @param unknown $page_no
	 * @param unknown $parm
	 * @param number $page_size
	 * @return string
	 */
	public static function page($total,$url,$page_no,$page_size=10,$parm=array()){
	    $res="";
	    self::$_page_size=$page_size;
	    self::$_total=$total;
	    
	    $pages = ceil(self::$_total/self::$_page_size);
	    if($pages >1 ){
	        self::$_page_num=$pages;
	        if($page_no > $pages){
	            $page_no=$pages;
	        }
	        if($page_no < 1){
	            $page_no=1;
	        }
	        self::$_page_no=$page_no;
	        self::$_parm=$parm;
	        self::$_url= self::cleanUrl($url);
	        $res=self::createPage();
	    }
	    return $res;
	}
    /**
	 * 清除url中的参数
	 *
	 */
    public static function cleanUrl($url){
        $arr_url = explode('?',$url);// 清除参数
        $pattern = '/(\w+)\/p(\d+)(\/)?/i';
        $replacement = '$1/';//清除原分页即链接中/p(\d+)/ 的字符去掉
        return preg_replace($pattern, $replacement, $arr_url[0]);
    }
	/**
	 * Enter description here...
	 *
	 */
	public static function createPage() {
	    $url=self::$_url;
	    $pageno=self::$_page_no;
	    $pagenum=self::$_page_num;
	    $pagelist=array();
	    $buttonnum=2;
	    $prevmorefalg=false;
	    $nextmoreflag=false;
	    
	    
	    if($pagenum < 5){
	        for($i=1;$i<=$pagenum;$i++){
	            $pagelist[]=$i;
	        }
	    }else{
	        $leftnum=$pageno - 1;//左面的空白链接
	        $rightnum=$pagenum-$pageno;//右边的空白链接
	
	        if($leftnum < $buttonnum){
	            $rightnum=$buttonnum+($buttonnum-$leftnum);
	            $nextmoreflag=true;
	        }elseif($rightnum < $buttonnum){
	            $leftnum=$buttonnum+($buttonnum-$rightnum);
	            $prevmorefalg=true;
	        }else{
	            $leftnum=$buttonnum;
	            $rightnum=$buttonnum;
	            $nextmoreflag=true;
	            $prevmorefalg=true;
	        }
	        
	        $leftnum=abs($leftnum);
	        $rightnum=abs($rightnum);
	        
	        
	        for($i=$pageno-$leftnum;$i<$pageno;$i++){
	            $pagelist[]=$i;
	        }
	        $pagelist[]=$pageno;
	        
	        for($i=$pageno+1;$i<=$pageno+$rightnum;$i++){
	            $pagelist[]=$i;
	        }
	    }
	    
	    
	    
	    $pagecontent='<ul class="pagecontent">';
	    if($pageno >1){
	        $firstpageurl=self::createUrlParm(1);
	        $prevpageurl=self::createUrlParm($pageno-1);
	        $pagecontent.='<li class="page_first"><a href="'.$firstpageurl.'">首页</a></li><li class="page_prev"><a  href="'.$prevpageurl.'">&lt;</a></li>';
	    }
	    if($prevmorefalg){
	        $pagecontent.='<li class="pagemore"><a>...</a></li>';
	    }
	    foreach ($pagelist as $v){
	        $pageurl=self::createUrlParm($v);
            
	        if($v==$pageno){
	            $pagecontent.='<li class="active" ><a class="on" href="'.$pageurl.'">'.$v.'</a></li>';
	        }else{
	            $pagecontent.='<li><a  href="'.$pageurl.'">'.$v.'</a></li>';
	        }
	    }
	    if($nextmoreflag){
	        $pagecontent.='<li class="pagemore"><a>...</a></li>';
	    }
	    
	    if($pageno < $pagenum){
	        $lastpageurl=self::createUrlParm($pagenum);
	        $nextpageurl=self::createUrlParm($pageno+1);
	        
	        $pagecontent.='<li class="page_next"><a href="'.$nextpageurl.'">&gt;</a></li><li class="page_last"><a href="'.$lastpageurl.'">末页</a></li>';
	    }
	    $gotopageurl=self::createUrlParm("pagecontsenfasdfasdf");
		$to='<li class="topage">跳转到 <input type="text" name="goto_pagenum" value="" id="goto_pagenum"><a href="javascript:topage(\''.$gotopageurl.'\')"  class="pageTo" />GO</a></li>';
		$script='<script>
		function topage(url){
			var pagenum=document.getElementById("goto_pagenum").value;
			if(isInt(pagenum)){
		        url=url.replace("pagecontsenfasdfasdf",pagenum);
				location.href=url;
			}
		}
		function isInt(value)
		{
			var patrn= /^[0-9]?[0-9]*$/
			if (patrn.exec(value))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		</script>';
		$to .=$script;
		$pagecontent .=$to;
		$pagecontent .="</ul>";
		
		return $pagecontent;
	}
	/**
	 * 构造参数
	 * @author libin 2008-10-13
	 * @param array() $var
	 * @return unknown
	 */
	public static function createUrlParm($page_no) {
		$url=self::$_url;
		$parm=self::$_parm;
		$laststr=substr($url, -1,1);
		if($page_no !=1){
		    if($laststr=='/'){
		        $url.="p".$page_no."/";
		    }else{
		        $url.="/p".$page_no."/";
		    }
		}
		if(count($parm)>0 && is_array($parm)){
		    $i=0;
		    foreach ($parm as $k=>$v){
		      if($i==0){
		          $url.="?".$k."=".$v;
		      }else{
		          $url.="&".$k."=".$v;
		      }
		    }
		}
		return $url;
	}
}
?>