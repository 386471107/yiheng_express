<?php 

	global $_GPC, $_W;
	
	$title = '我的待收列表';
  


include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';

$member = new yh_member();
$opt  = new yh_opt();

$url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'none_per'));

$openid=$_W['openid']; 

	if (empty($openid))
	{
		$userinfo= json_decode($member->Get_Userinfo());
		$openid = $userinfo->openid;
		//判断用户是否存在于
		$exsit = $member->Check_Member($userinfo->openid);
		if (!$exsit) $member->Insert_memberinfo($userinfo->openid,$userinfo->nickname,$userinfo->headimgurl);
		
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
	
	if ($_W['isajax'])
		{

			if ($act == 'done')
						{
							$id= $_GPC['id'];
							$done_res=$opt->Done_record($id,$openid,"user");	

									if ($done_res)
									{	
										$opt ->Stats_out_add($member->Get_shop_id($openid));
										$res['rid']=$id;
										result_back(1,$res);	
									}

						}
		}


$mitem= $member->Get_member($openid);

$shop=$member->Get_shop_name($mitem['m_defaut_area']);


$sms_list= $opt->Get_shop_msm_list($shop_id=$member->Get_shop_id($openid),2,$openid,$page_index=1,$psize=100) ;	

$done=empty($sms_list['staff'])?0:1;


include $this->template('my_sms');  
exit();
 
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


	 ?>