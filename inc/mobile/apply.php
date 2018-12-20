<?php 

global $_GPC, $_W;





include_once MODULE_ROOT.'/inc/func/yh_member.func.php';

$member = new yh_member();

$openid=$_W['openid']; 

$title="快递点操作申请";
$express_apply="yiheng_express_apply";

	if (empty($openid))
	{
		$userinfo= json_decode($member->Get_Userinfo());
		$openid = $userinfo->openid;
		//判断用户是否存在于
		$exsit = $member->Check_Member($userinfo->openid);
		if (!$exsit)
		{
			
			$member->Insert_memberinfo($userinfo->openid,$userinfo->nickname,$userinfo->headimgurl);
			header("Location: $url");
		} 
	}
	else
	{
		 $exsit = $member->Check_Member($openid);
		if (!$exsit)
		{
			mc_oauth_userinfo();
			$member->Insert_memberinfo($openid,$_W['fans']['nickname'],$_W['fans']['avatar']);
		}

	}

	if (empty($openid)) die();

	$act=$_GPC['act'];
	$u_shop_id = $member->Get_shop_id($openid);
	
	if (!$u_shop_id)
	message('请在周边网点页面选择你要加入的网点!',$this->createMobileUrl('my'),'warning');
	if ($act =='to_apply')
	{	
			
				 $res = pdo_delete($express_apply,array('apply_openid' => $openid,'uniacid' =>$_W['uniacid']));
				 $data = array(		 
					'uniacid' => $_W['uniacid'],
					'apply_ip' =>  $_W['clientip'],
					'apply_os' =>  $_W['os'],
					'apply_openid' => $openid,
					'apply_shop_id' =>  $u_shop_id ,
					'apply_createtime' =>  time(),
					);
				 $result=pdo_insert($express_apply, $data);

	}

$manage=$member->is_shop_manage($openid);
if ($manage)
message('已拥有店铺管理权限,无法申请！',$this->createMobileUrl('my'),'warning');

 include $this->template('apply');  
 exit();
 ?>
