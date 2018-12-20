<?php 
global $_GPC, $_W;

$title = '我要寄件';
$express_member='yiheng_express_member';
$express_reg='yiheng_express_reg';
$express_log_login='yiheng_express_log_login';
$express_recode='yiheng_express_recode';
$express_tpl='yiheng_express_tpl';


include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
$member = new yh_member();
$opt  = new yh_opt();
$mc_info=mc_oauth_userinfo();
$url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'none_per'));
$openid=$_W['openid'];


 
$act=trim($_GPC['act']);

$express_addr='yiheng_express_addr';

$orderby =" order by addr_default DESC , addr_id DESC";

$addr_rec_list = pdo_fetchall("SELECT *  FROM ".tablename($express_addr)."where addr_direction =0".$orderby);
$addr_send_list = pdo_fetchall("SELECT *  FROM ".tablename($express_addr)."where addr_direction =1".$orderby);





if ($_W['isajax'])
{

if ($act=='addr_del')
{
	$id = $_GPC['id'];
	$result=pdo_delete($express_addr,array('addr_id' =>$id));
	if($result)
		 {
		 	$params= array(
			 'tips' => "删除成功",
			 'dstype' => "success",
			);
			result_back($result,$params);
		 }
		 else
		 {
		 	$params= array(
			 'tips' => "删除失败！",
			 'dstype' => "error",
			);
			result_back($result,$params);
		 }

}

	if ($act=='addr_add')
{

	$rec_name=trim($_GPC['rec_name']);
	$rec_tel =trim($_GPC['rec_tel']);
	$rec_city=trim($_GPC['rec_city']);
	$rec_detail=trim($_GPC['rec_detail']);
	$rec_comp=trim($_GPC['rec_comp']);
	$rec_default_int=$_GPC['rec_default']=="true"?1:0;
	$addr_flag =intval($_GPC['addr_flag']);

	if (!empty($rec_name) && !empty($rec_tel) && !empty($rec_city) && !empty($rec_detail) )
	{

		$city_arr =explode(" ", $rec_city);
		$data= array(
		 'addr_province' => $city_arr[0],
		 'addr_city' => $city_arr[1],
		 'addr_district' => $city_arr[2],
		 'addr_tel' => $rec_tel,
		 'addr_name' => $rec_name,
		 'addr_addr' => $rec_detail,
		 'addr_openid' => $openid,
		 'addr_comp' => $rec_comp,
		 'addr_default' => $rec_default_int,
		 'addr_direction' => $addr_flag,
		 'uniacid' => $_W['uniacid'],
		);

		
		 $result=pdo_insert($express_addr, $data);

		 if($result)
		 {
		 	$params= array(
			 'tips' => "新增成功！",
			 'dstype' => "success",
			 'id' =>pdo_insertid(),
			);
			result_back($result,$params);
		 }
		 else
		 {
		 	$params= array(
			 'tips' => "新增失败！",
			 'dstype' => "error",
			);
			result_back($result,$params);

		 }



	}

	

}


}

include $this->template('send_express');  

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



exit();

?>