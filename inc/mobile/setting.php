<?php 


	global $_GPC, $_W;

	$title = '基本设置';
   
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	
	$express_sms='yiheng_express_sms';
	
	
	
	checkauth();
	//判断是否有权限加入
	if ($_W['fans']['uid'])
	{
		$params = array(
					'm_fansid' => $_W['fans']['fanid'],
					'm_openid' => $_W['fans']['openid'],
					
					);
		
		$where=' WHERE m_fansid = :m_fansid and m_openid = :m_openid';		
		$item = pdo_fetch("SELECT *  FROM ".tablename($express_member). $where.$orderby,$params);
		
		if (empty($item) || $item['m_is_manage']!=1)
		{
			exit();
			
		}
	
		
	}
	//判断是否有权限加入
	
	
	
	$params = array(
					'id' => 1,
				);
		$where=' WHERE id = :id ';		
		$item = pdo_fetch("SELECT *  FROM ".tablename($express_sms). $where.$orderby,$params);
		
	if ($_GPC['op']=='change')
	{	
		if ($item['sms_sned']==1)
		{
			$data = array(
					'sms_sned' => 0,			
					);
		}
		else
		{
			$data = array(
					'sms_sned' => 1,			
					);
			
		}
		
		$result = pdo_update($express_sms, $data, array('id' => 1));
		
		$redata['success']=1;			
		$re=json_encode($redata);
		exit($re);					
	}
	

	 include $this->template('setting');  

	  ?>
	
	