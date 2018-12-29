<?php 
global $_GPC, $_W;


include_once MODULE_ROOT.'/inc/mobile/common.php';
	$title = '我要寄件';
	
	if ($myinfo['m_level']==4)
	{
		$url=$this->createMobileUrl('employee');
		header("Location: $url");exit();
	
	}
	
	//走权限管理
	$page="send_express";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
$express_delivery='yiheng_express_delivery';
 
$act=trim($_GPC['act']);

	if(checksubmit('submit'))
	{


		$s_name=empty(trim($_GPC['s_name']))?message("寄件人姓名不能不空",refresh,"error"):trim($_GPC['s_name']);
		$s_tel=empty(trim($_GPC['s_tel']))?message("寄件人电话不能不空",refresh,"error"):trim($_GPC['s_tel']);
		$s_addr=empty(trim($_GPC['s_addr']))?message("寄件人地址不能不空",refresh,"error"):trim($_GPC['s_addr']);
		$r_name=empty(trim($_GPC['r_name']))?message("收件人姓名不能不空",refresh,"error"):trim($_GPC['r_name']);
		$r_tel=empty(trim($_GPC['r_tel']))?message("收件人电话不能不空",refresh,"error"):trim($_GPC['r_tel']);
		$r_addr=empty(trim($_GPC['r_addr']))?message("收件人地址不能不空",refresh,"error"):trim($_GPC['r_addr']);
 		$express_info=empty(trim($_GPC['express_info']))?message("请选择快递公司",refresh,"error"):trim($_GPC['express_info']);
		
		$params= array(
				 'uniacid' =>  $_W['uniacid'],
				 'dlv_create_openid' => $openid,
				 'dlv_fetch_status' => 0,
				);
		$where = " where uniacid =:uniacid and dlv_create_openid like :dlv_create_openid and dlv_fetch_status =:dlv_fetch_status";	
		$sql = "SELECT count(*) as cnt from ".tablename($express_delivery).$where;
		$item = pdo_fetch($sql,$params);

		if($item['cnt']<15)
		{	

			$arr_express_info=explode("&",$express_info);
			$rand=rand(10000,99999);
			$data=array(
				 'dlv_from_name' =>$s_name,
				 'dlv_from_tel' =>$s_tel,
				 'dlv_from_addr' =>$s_addr,
				 'dlv_to_name' =>$r_name,
				 'dlv_to_tel' =>$r_tel,
				 'dlv_to_addr' =>$r_addr,
				 'dlv_remark' =>trim($_GPC['remark']),
				 'dlv_createtime' =>time(),
				 'dlv_express_company' =>$arr_express_info[0],
				 'dlv_express_poster' =>$arr_express_info[1],
				 'dlv_rand_code' =>$rand,
				 'dlv_good_type' =>trim($_GPC['express_goods']),
				 'dlv_get_type' =>trim($_GPC['post_type']),
				 'dlv_create_openid' =>$openid,
				  'uniacid' => $_W['uniacid'],
			);
			$result=pdo_insert($express_delivery, $data);
			if($result)
				message("快递新增成功并通知快递人员!",refresh,"success");
		}
		else
		{
			message("未处理件达15件，无法新增!",refresh,"error");
		}

		exit();

	}

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



$addr_rec_list = $opt->Get_addr_by_openid($openid,$direction=0);
$addr_send_list = $opt->Get_addr_by_openid($openid,$direction=1);

$goods_list= $opt->Get_express_goods_list();

$goods_list_str=json_encode($goods_list);

$couriers_list= $opt->Get_express_couriers_list();


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