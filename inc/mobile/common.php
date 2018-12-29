<?php 


include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';

$express_member='yiheng_express_member';
$express_reg='yiheng_express_reg';
$express_log_login='yiheng_express_log_login';

//======================
$express_recode='yiheng_express_recode';
$express_tpl='yiheng_express_tpl';
$express_tel_list='yiheng_express_tel_list';
$express_sms_setting='yiheng_express_sms_setting';
$express_area='yiheng_express_area';
$express_employee='yiheng_express_employee';
$recode_attimg='yiheng_express_recode_attimg';
$express_addr='yiheng_express_addr';
$express_goods_list='yiheng_express_goods_list';

 

//======================

$member = new yh_member();
$opt  = new yh_opt();

$mc_info=mc_oauth_userinfo();
$openid=$_W['openid'];
$member->judge_db_member($openid);

if (empty($openid)) die();

$myinfo = $member->Get_member($openid);
$has_shop= $myinfo['m_defaut_area'];
if ($has_shop==0)
{
	$my_url=$this->createMobileUrl('my');
	header("Location: $my_url");exit();
}
$refuse_url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'reject'));

//关店检测
if(!$opt->Get_shop_open_status($myinfo['m_defaut_area']))
	{
		$close_url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'close'));
		header("Location: $close_url");exit();
	}
	
?>