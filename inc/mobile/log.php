<?php 



	global $_GPC, $_W;

	$title = '后台管理';
   
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	$express_recode='yiheng_express_recode';
	
	checkauth();
	
	if ($_W['fans']['uid'])
	{
		
		$params = array(
					'm_fansid' => $_W['fans']['fanid'],
					'm_openid' => $_W['fans']['openid'],
					
					);
		
		$where=' WHERE m_fansid = :m_fansid and m_openid = :m_openid';		
		$item = pdo_fetch("SELECT *  FROM ".tablename($express_member). $where.$orderby,$params);
		
		if ($item['m_is_manage']!=1)
		{			
			include $this->template('404');  	
			exit();
		}
		else
		 {
			 
		 $params = array(
				'uniacid' => $_W['uniacid'],
				
				
				);
		
		$where=' WHERE uniacid = :uniacid ';	
//$orderby =" order by recoder_get_status asc";		
		$limit =' limit 0,50 ';
		$list = pdo_fetchall("SELECT *  FROM ".tablename($express_recode). $where.$orderby.$limit,$params);
		
			 
			include $this->template('log');  
			exit();
			
		}
		
	}
	 ?>