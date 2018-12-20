<?php 

	global $_GPC, $_W;



	$express_delivery='yiheng_express_delivery';
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 30;
	$condition =" where uniacid =:uniacid";
	$orderby=" order by dlv_id DESC";
	$params = array(
				'uniacid' => $_W['uniacid'],								
		);
	
	 $sql = "SELECT * from ".tablename($express_delivery).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	
	$list = pdo_fetchall($sql,$params);
	// print_r($list);exit;
	$sql = "SELECT COUNT(*) FROM " . tablename($express_delivery).$condition;
	
	$total = pdo_fetchcolumn($sql,$params);
	
	
	$pager = pagination($total, $pindex, $psize);

	
	include $this->template('pc/pc_wait_receive_list');  
	
	
	
?>