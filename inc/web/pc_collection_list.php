<?php 

global $_GPC, $_W;

	$express_member='yiheng_express_member';
	$express_recode='yiheng_express_recode';
	
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();

	$nav = $wopt->page_info();

	$def_shop_id = $wopt->Get_web_def_shop();
	
	$recive=(int)($_GPC['recive']);
	
	
		if(checksubmit('search'))
		{
			
			$barcord=$_GPC['barcord'];
			
			if (!empty($barcord))
			{
				$params = array(
					'uniacid' => $_W['uniacid'],
					'recoder_barcode' => $barcord,
					'recoder_shop_id' => $def_shop_id,
				);
				$condition =" where uniacid =:uniacid and recoder_barcode =:recoder_barcode  and recoder_shop_id =:recoder_shop_id";
				
				$sql = "SELECT * from ".tablename($express_recode).$condition ;
				$list = pdo_fetchall($sql,$params);
				include $this->template('pc/pc_collection_list');  exit();
			}
			
		
		}
		
		
		if(checksubmit('dsearch'))
		{ 
		
			$sdate=$_GPC['sdate'];
			$sltime = explode("/",$sdate);
			$s_year=$sltime[2]; 
			$s_month=$sltime[0]; 
			$s_day=$sltime[1]; 
			if (!empty($sdate))
			{
				$params = array(
					'uniacid' => $_W['uniacid'],
					'recoder_create_year' => $s_year,
					'recoder_create_month' => $s_month,
					'recoder_create_day' => $s_day,
					'recoder_shop_id' => $def_shop_id,
					
					
				); 
				$orderby = " order by recoder_get_status ASC,id DESC";
				$condition =" where recoder_create_year =:recoder_create_year and recoder_create_month =:recoder_create_month and recoder_create_day=:recoder_create_day and  uniacid =:uniacid and recoder_shop_id =:recoder_shop_id";
				
				$sql = "SELECT * from ".tablename($express_recode).$condition.$orderby ;
				$list = pdo_fetchall($sql,$params);
				include $this->template('pc/pc_collection_list');  exit();
			}
			
		
		}
		
		
	
		
		

	$page_index = max(1, intval($_GPC['page']));
	if ($recive ==1 || $recive==0)
		$all_info= $wopt->Get_web_collection_list($def_shop_id,$recive,$retention=0,$page_index,$psize=50);
	else
		$all_info= $wopt->Get_web_collection_list($def_shop_id,0,$retention=1,$page_index,$psize=50);
	
	$total=$all_info['total'];
	$totaltd=$all_info['totaltd'];
	$total_other =$total-$totaltd;
	$total_tel = $all_info['total_tel'];
	$total_weixin =$total-$total_tel;


	$list=$all_info['lst'];
	$pager=$all_info['pager'];
	
	
	
	

	include $this->template('pc/pc_collection_list');  

	
?>