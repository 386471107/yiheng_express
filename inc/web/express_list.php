<?php 



	global $_GPC, $_W;

	// include MODULE_ROOT.'/inc/mobile/__init.php';

		
checklogin();

	$title = '会员管理';
    
	$op = in_array($_GPC['op'], array('display','set','del'))?$_GPC['op']:'display';
	
	
	$express_member='yiheng_express_member';
	$express_recode='yiheng_express_recode';
	
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	
	$id=$_GPC['id'];
	if($op=='set')
	{
		
	$params = array(
					'id' => $id,
					);
		
		$where=' WHERE id = :id ';		
		$item = pdo_fetch("SELECT * FROM ".tablename($express_member). $where.$orderby,$params);
		if (!empty($item))	
		{
			$is_manage= ($item['m_is_manage']==1)?0:1;
				$data = array(
					'm_is_manage' => $is_manage,
					
					);
			$result = pdo_update($express_member, $data, array('id' => $item['id']));
		 	$redata['success']=1;
			$re=json_encode($redata);
			exit($re);
			
		}
		
	}
	
	
	if($op=='display')
	{	
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$condition =" where uniacid =:uniacid";
		$orderby=" order by id DESC";
		$params = array(
					'uniacid' => $_W['uniacid'],								
			);
		
		 $sql = "SELECT * from ".tablename($express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		
		$list = pdo_fetchall($sql,$params);
		// print_r($list);exit;
		$sql = "SELECT COUNT(*) FROM " . tablename($express_recode).$condition;
		
		$total = pdo_fetchcolumn($sql,$params);
		
		
		$pager = pagination($total, $pindex, $psize);
	}
		
		
	 include $this->template('web/express_list');  
	

	?>