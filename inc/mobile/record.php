<?php 

global $_GPC, $_W;


$id = intval($_GPC['id']);;
$title = '历史记录';
$op = in_array($_GPC['op'], array('display','check'))?$_GPC['op']:'display';
$express_member='yiheng_express_member';
$express_recode='yiheng_express_recode';
$express_reg='yiheng_express_reg';
//checkauth();
 $uniacid=$_W['uniacid'];

// if (!$_W['member']['uid'])
// {
	// include $this->template('404');
	// exit();		
// }



	

if ($op=='check')
{

	$data = array(
	'recoder_get_status' => 1,					
	);
	$result = pdo_update($express_recode, $data, array('id' => $id,'uniacid' =>$uniacid,'recoder_uid' =>$_W['member']['uid']));

}


$params=array(
	":uniacid" => $_W['uniacid'],
	":recoder_uid" => $_W['member']['uid'],
);	 
$where=' WHERE uniacid = :uniacid and recoder_uid=:recoder_uid';
$orderby=' order by `recoder_get_status` asc , `recoder_create` DESC';
$limit =" limit 0,30";
$list = pdo_fetchall("SELECT * FROM ".tablename($express_recode). $where.$orderby.$limit ,$params);



include $this->template('record');  
 ?>