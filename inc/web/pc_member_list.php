<?php 

	global $_GPC, $_W;



	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();
	$def_shop_id = $wopt->Get_web_def_shop();
	$pindex = max(1, intval($_GPC['page']));
	$level=(int)($_GPC['level']);
	$express_member='yiheng_express_member';
	$act=$_GPC["act"];
	
	if($act=="export")
	{
		
		 exportExcel(array('姓名','年龄'), array(array('a',21),array('b',23)), '档案', './', true);
	}
	
	
	
		if(checksubmit('search'))
		{
			
			$tel=$_GPC['u_tel'];
			$name=$_GPC['u_name'];
			if (!empty($tel) && !empty($name) )
			{
				$params = array(
				'uniacid' => $_W['uniacid'],
				'm_tel' => $tel,
				'm_realname' => "%$name%",
				);
				$condition =" where uniacid =:uniacid and m_tel =:m_tel and m_realname like :m_realname";
			}
			if (!empty($tel))
			{
				$params = array(
				'uniacid' => $_W['uniacid'],
				'm_tel' => $tel,
				
				);
				$condition =" where uniacid =:uniacid and m_tel =:m_tel ";
			}
			if (!empty($name))
			{
				$params = array(
				'uniacid' => $_W['uniacid'],
				
				'm_realname' => "%$name%",
				);
				$condition =" where uniacid =:uniacid  and m_realname like :m_realname";
			}
			
			
			
			$sql = "SELECT * from ".tablename($express_member).$condition ;
			$mlist = pdo_fetchall($sql,$params);
			$search = 1;
			include $this->template('pc/pc_member_list');  

			exit();
		}
	
		
	
	if ($_W['isajax'])
		{
			if ($act=="fill_info")
			{
				$cur_id= intval($_GPC['cur_id']);
				$sql = "SELECT * from ".tablename('yiheng_express_tel_list')." where id = $cur_id and uniacid =".$_W['uniacid'];
				$res=pdo_fetch($sql);
				if (!empty($res))
				result_back(1,$res);
				result_back(0,$res);
			}

			if ($act=="save_info")
			{
				$cur_id= intval($_GPC['cur_id']);
				$uname= trim($_GPC['uname']);
				$uarea= trim($_GPC['uarea']);
				$udetail= trim($_GPC['udetail']);

				$udata=array(
					"tl_uname"=>$uname,
					"tl_area"=>$uarea,
					"tl_addr"=>$udetail,
					"tl_modify_ex"=>1,
				);
				$res=pdo_update('yiheng_express_tel_list',$udata,array('id' => $cur_id,'uniacid' => $_W['uniacid']));
				if ($res)
				result_back(1);
				result_back(0);
			}
			
			if ($act=="fill_info_by_tel")
			{
				$cur_tel= trim($_GPC['cur_tel']);
				$sql = "SELECT * from ".tablename('yiheng_express_tel_list')." where m_tel = $cur_tel and uniacid =".$_W['uniacid'];
				$res=pdo_fetch($sql);
				if (!empty($res))
				result_back(1,$res);
				result_back(0,$res);
			}
			
		}
	if ($level==-1)
	{
		$psize=50;
		$params = array(
						'uniacid' => $_W['uniacid'],
						'tl_shop_in' =>$def_shop_id,
		);
		$condition =" where uniacid =:uniacid and tl_shop_in =:tl_shop_in";
		$orderby =" order by tl_modify_ex ASC,id DESC";
		$select ="*";
		$sql = "SELECT $select from ".tablename('yiheng_express_tel_list').$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$mlist = pdo_fetchall($sql,$params);
		$sql = "SELECT COUNT(*) FROM " .tablename('yiheng_express_tel_list').$condition;
		$total = pdo_fetchcolumn($sql,$params);
		
		
		$condition =" where uniacid =:uniacid and tl_shop_in =:tl_shop_in and tl_modify_ex =1";
		$sqla = "SELECT COUNT(*) FROM " .tablename('yiheng_express_tel_list').$condition;
		$totala = pdo_fetchcolumn($sqla,$params);
		
		
		$pager = pagination($total, $pindex, $psize);
		include $this->template('pc/pc_unbind_member_list');  exit();
	}
	else
	{
		$list = $wopt->Get_web_member_list($def_shop_id,$level,$pindex,$psize=50);
		$mlist =$list['mlist'];
	}

	$pager=$list['pager'];

	include $this->template('pc/pc_member_list');  

	 function result_back($flag,$params =null)
		  {
		  	if (!empty($params))
		  	{
		  		foreach ($params as $key => $value) {
		  			$res[$key]=$value;	
		  		}
		  	}

		  	$res['success']=$flag;			
			$re=json_encode($res);
			exit($re);
		  }
		  
		  
		  /** 
 * 数据导出 
 * @param array $title   标题行名称 
 * @param array $data   导出数据 
 * @param string $fileName 文件名 
 * @param string $savePath 保存路径 
 * @param $type   是否下载  false--保存   true--下载 
 * @return string   返回文件全路径 
 * @throws PHPExcel_Exception 
 * @throws PHPExcel_Reader_Exception 
 */ 
function exportExcel($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false){  
    include_once MODULE_ROOT.'/lib/PHPExcel.php';
    $obj = new PHPExcel();  
   
    //横向单元格标识  
    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');  
       
    $obj->getActiveSheet(0)->setTitle('sheet名称');   //设置sheet名称  
    $_row = 1;   //设置纵向单元格标识  
    if($title){  
        $_cnt = count($title);  
        $obj->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //合并单元格  
        $obj->setActiveSheetIndex(0)->setCellValue('A'.$_row, '数据导出：'.date('Y-m-d H:i:s'));  //设置合并后的单元格内容  
        $_row++;  
        $i = 0;  
        foreach($title AS $v){   //设置列标题  
            $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);  
            $i++;  
        }  
        $_row++;  
    }  
   
    //填写数据  
    if($data){  
        $i = 0;  
        foreach($data AS $_v){  
            $j = 0;  
            foreach($_v AS $_cell){  
                $obj->getActiveSheet(0)->setCellValue($cellName[$j] . ($i+$_row), $_cell);  
                $j++;  
            }  
            $i++;  
        }  
    }  
       
    //文件名处理  
    if(!$fileName){  
        $fileName = uniqid(time(),true);  
    }  
   
    $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');  
   
    if($isDown){   //网页下载  
        header('pragma:public');  
        header("Content-Disposition:attachment;filename=$fileName.xls");  
        $objWrite->save('php://output');exit;  
    }  
   
    $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码  
    $_savePath = $savePath.$_fileName.'.xlsx';  
     $objWrite->save($_savePath);  
   
     return $savePath.$fileName.'.xlsx';  
}  
   
//


?>