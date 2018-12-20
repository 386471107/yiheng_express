<?php 

global $_GPC, $_W;
include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';

$express_area='yiheng_express_area';
$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';


$member = new yh_member();
$opt = new yh_opt();
$url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'none_per'));
$openid=$_W['openid']; 

$member->judge_db_member($openid);

$mc_info=mc_oauth_userinfo();

$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");

if (empty($openid)) die();



	/*
	此处以后会修改成多店模式，
	1.查询微信加入的所有店铺并链接展示
	2.如果加入，则显示最近位置快递点信息，需做LBS定位
	 */
	$be_shop=$member->Get_shop_id($openid);

	if ($be_shop)
	{
		$params=array(
			":area_code" => $be_shop,
				
			);
			$where=' WHERE area_code = :area_code';
			$shop_item = pdo_fetch("SELECT *  FROM ".tablename($express_area). $where.$orderby,$params);
			if (!empty($shop_item))
			{
				if(strpos($shop_item['area_image'],'http://') == false && strpos($shop_item['area_image'],'https://') == false)
					{ 
					 $shop_item['area_image']=$_W[attachurl].'/'.$shop_item['area_image']; 
					}
				if(strpos($shop_item['area_logo'],'http://') == false && strpos($shop_item['area_logo'],'https://') == false)
					{ 
					 $shop_item['area_logo']=$_W[attachurl].'/'.$shop_item['area_logo']; 
					}	

					

			}
	}
	else
	{
		;
	}
	
	

include $this->template('shop_info');  
exit();	



?>