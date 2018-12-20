<?php 



	global $_GPC, $_W;

	// include MODULE_ROOT.'/inc/mobile/__init.php';

		
checklogin();

	$title = '快递设置';
    $op = in_array($_GPC['op'], array('display','add','del'))?$_GPC['op']:'display';
			 
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	$express_express_list='yiheng_express_express_list';
	

	
	if (checksubmit('submit')) {
			
			
			$express_name=!empty($_GPC['express_name'])?trim($_GPC['express_name']) :message('抱歉，输入快递名称！');
			
			$data = array(
					'uniacid' => $_W['uniacid'], 
					'express_name' => $express_name,
									
				);
				if (!empty($id)) {
					pdo_update($express_express_list, $data, array('id' => $id));
					message('快递更新成功！', $this->createWebUrl('express_setting'), 'success');
				} else {
					pdo_insert($express_express_list, $data);
					message('快递新增成功！', $this->createWebUrl('express_setting'), 'success');
				}

					
		}
		
		if ($op == 'del')
		{
			$id = intval($_GPC['id']);
			if($id)
			{
				$result = pdo_delete($express_express_list, array('id' => $id));
				$result? message('操作成功', referer(), 'success'):message('非法数据', referer(), 'error');
               
			}
			else
			{
			exit('非法请求');
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
		
		 $sql = "SELECT * from ".tablename($express_express_list).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		
		$list = pdo_fetchall($sql,$params);
		// print_r($list);exit;
		$sql = "SELECT COUNT(*) FROM " . tablename($express_express_list).$condition;
		
		$total = pdo_fetchcolumn($sql,$params);
		
		$pager = pagination($total, $pindex, $psize);
		}  
	
	



	 include $this->template('web/express_setting');  
	

	?>