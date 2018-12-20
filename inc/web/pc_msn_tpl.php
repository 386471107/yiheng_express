<?php 

	global $_GPC, $_W;


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();

	$express_sms_setting='yiheng_express_sms_setting';

	$def_shop_id = $wopt->Get_web_def_shop();

	$act = $_GPC['act'];

	 if ($act=='copy_tpl')
	 {
		 
		 $paramsa = array(
				'uniacid' => $_W['uniacid'],
				'sms_area_id' => $def_shop_id,
			);
			$conditiona =" where uniacid =:uniacid and sms_area_id =:sms_area_id";
			$sql = "SELECT count(*) as cnt  from ".tablename($express_sms_setting).$conditiona;
			$exsit = pdo_fetch($sql,$paramsa);
			
			
			if ($exsit['cnt']==0)
			{
				$params = array(
				'uniacid' => $_W['uniacid'],
				);
				$condition =" where uniacid =:uniacid and sms_type in(1,2,3)";
				$limit =' limit 0,3';
				$select ="*";
				$sql = "SELECT $select from ".tablename($express_sms_setting).$condition.$limit;
				$msm_list = pdo_fetchall($sql,$params);
				// if($tmp == $v) unset($arr[$k]);
				foreach($msm_list as $ml)
				{
					unset($ml['id']);
					$ml['sms_area_id']=$def_shop_id;
					pdo_insert($express_sms_setting,$ml);
				}
				
			}
			
			
		
	 }
	 
	if(checksubmit('tpl_save'))
		{
			$data=array(
				'sms_message_id'=>trim($_GPC['sms_message_id']),
				'sms_message'=>trim($_GPC['sms_message']),
				'sms_com_type'=>trim($_GPC['sms_com_type']),
				'sms_accesskey'=>trim($_GPC['sms_accesskey']),
				'sms_accesssecret'=>trim($_GPC['sms_accesssecret']),
				'sms_message_tpl_id'=>trim($_GPC['sms_message_tpl_id']),
				'sms_signname'=>trim($_GPC['sms_signname']),
				
			);
			$result = pdo_update($express_sms_setting, $data,array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid'],'sms_area_id'=>$def_shop_id));
			if ($result) {
			message('修改短信模版成功', $this->createWebUrl('pc_msn_tpl'), 'success');
			} else {
			message('修改短信模版失败，请重试');
			}
			exit();
		}
        	
        	
	$my_msn_tpl=$wopt-> Get_my_msn_tpl($def_shop_id);

	if ($act =='edit')
	$item = $wopt->Get_msn_tpl_by_id($def_shop_id,intval($_GPC['id']));

	
	include $this->template('pc/pc_msn_tpl');  
	
	
	
?>