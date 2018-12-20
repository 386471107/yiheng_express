<?php 



	global $_GPC, $_W;

	// include MODULE_ROOT.'/inc/mobile/__init.php';

		
	checklogin();

	$title = '会员权限管理';
    
	$act = in_array($_GPC['act'], array('display','displaya','cancel','edit','del','area','area_add','area_edit','area_del'))?$_GPC['act']:'display';
	

$solution_module_list='yiheng_e_solution_module_list';
$solution_module_page='yiheng_e_solution_module_page';



$e_solution_area='yiheng_e_solution_area';
$e_solution_member='yiheng_e_solution_member';

$tsml=tablename($solution_module_list);
$tsmp=tablename($solution_module_page);


	
	if(checksubmit('m_save'))
		{

		$have=$_GPC['tmcheck'];
		$chk=array();
		foreach ($have as $key => $value) {
			array_push($chk,$key);
		}
		$json_chk=(json_encode($chk));

			$m_area_code =trim($_GPC['m_area_code']);
			empty($m_area_code)?message("企业信息不允许为空"):'';


			$m_id = (int)($_GPC['id']);
			
			$m_status=	$_GPC['m_status'];

			$m_area_code = $m_status==1?trim($_GPC['m_area_code']):0;

         	$m_data=array(	
				'm_area_code'=>$m_area_code ,
				'm_realname'=>trim($_GPC['m_realname']),
				'm_tel'=>trim($_GPC['m_tel']),
				'm_status'=>$m_status,
				'm_op_module_page'=>$json_chk,
				'uniacid' => $_W['uniacid'],
			);

        	 if ($m_id)
        	 {
        	 		if (pdo_update($e_solution_member,$m_data,array('m_id' =>$m_id))) {
					message('修改店员信息成功', referer(), 'success');
					} else {
					message('修改店员信息失败，请重试');
					}
					exit();
        	 }
        	

	
		}
		
	
if(checksubmit('area_save'))
		{

			$area_code =trim($_GPC['area_code']);
			empty($area_code)?message("企业信息不允许为空"):'';

			$params = array(
							'area_code' => $area_code,
							'uniacid' => $_W['uniacid'],
							);
			$where=' WHERE area_code = :area_code and uniacid =:uniacid ';	
			$orderby='';
			$limit =" limit 1";
			$exsit = pdo_fetch("SELECT count(*) as cnt FROM ".tablename($e_solution_area). $where.$orderby,$params);
			if ($exsit['cnt']) message('企业重复输入，请重试', referer(), 'error');
			
			//查询企业信息是，不允许重复
			

			$area_id = (int)($_GPC['area_id']);
			$location =$_GPC['location'];
         	$area_data=array(	
				'area_person'=>$_GPC['area_person'],
				'area_tel'=>$_GPC['area_tel'],
				'area_code'=>$_GPC['area_code'],
				'area_name'=>$_GPC['area_name'],
				'area_lat'=>$location['lat'],
				'area_lng'=>$location['lng'],
				'area_location'=>$_GPC['area_location'],
				'uniacid' => $_W['uniacid'],
			);

        	 if ($area_id)
        	 {
        	 		if (pdo_update($e_solution_area,$area_data,array('area_id' =>$area_id))) {
					message('修改成功', referer(), 'success');
					} else {
					message('修改失败，请重试');
					}
					exit();
        	 }
        	 else
        	 {
        	 	if (pdo_insert($e_solution_area,$area_data)) {
					message('新增成功',  $this->createWebUrl('access_setting',array('act' =>'area')), 'success');
					} else {
					message('新增失败，请重试');
					}
					exit();
        	 }

	
		}
		

	if($act=='area')
	{	
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$condition =" where uniacid =:uniacid";
		$params = array(
					'uniacid' => $_W['uniacid'],
								
			);
		
		$sql = "SELECT * from ".tablename($e_solution_area).$condition ." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql,$params);
		// print_r($list);exit;
		$sql = "SELECT COUNT(*) FROM " . tablename($e_solution_area);
		$total = pdo_fetchcolumn($sql,$params);
		$pager = pagination($total, $pindex, $psize);
	}




	if($act=='area_edit' || $act=='area_add')
	{	

		$area_id=$_GPC['id'];
		$params = array(
						'area_id' => $area_id,
						'uniacid' => $_W['uniacid']
						);
		$where=' WHERE area_id = :area_id and uniacid =:uniacid';	
		$orderby='';
		$limit =" limit 1";
		$item = pdo_fetch("SELECT * FROM ".tablename($e_solution_area). $where.$orderby,$params);
		$location['lat']=$item['area_lat'];
		$location['lng']=$item['area_lng'];


	}



	if($act=='edit' )
	{	

		$tcca=tablename($e_solution_area);
		$tccm=tablename($e_solution_member);
		$where=' WHERE uniacid = :uniacid ';	
		$orderby='';	
	    $params = array(
		'uniacid' => $_W['uniacid'],								
		);
	    $sql = "SELECT * from ".$tcca.$where;
		$area_list = pdo_fetchall($sql,$params);
		$m_id=$_GPC['id'];
		$params = array(
						'm_id' => $m_id,
						);
		$where=' WHERE m_id = :m_id ';	
		$orderby='';
		$limit =" limit 1";
		$item = pdo_fetch("SELECT * FROM ".tablename($e_solution_member). $where.$orderby,$params);


		$item['have']=(json_decode($item['m_op_module_page']));


		//模块信息加载
		$orderby =" order by $tsml.module_orderby ASC";
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition =" where $tsml.uniacid =:uniacid ";
		$params = array(
			'uniacid' => $_W['uniacid'],
		);
		$sql = "SELECT * from $tsml " .$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql,$params);
		// print_r($list);exit;
		
		foreach ($list as $key => $value) {
			$mo_id=$value['module_id'];
			$psql = "SELECT * from $tsmp where page_web=0 and page_module_id =".$value['module_id'];
			$plist = pdo_fetchall($psql);
			if (!empty($plist))
			$list[$key]['plist']=$plist;
			
		}
		$sql = "SELECT COUNT(*) FROM $tsml" ;
		$total = pdo_fetchcolumn($sql,$params);
		$pager = pagination($total, $pindex, $psize);



		
	}













	if($act=='area_del')
	{	
			
			$area_id=$_GPC['id'];
			
				
			
			if (pdo_delete($e_solution_area, array('area_id' => $area_id,'uniacid' =>$_W['uniacid']))) {
			message('区域信息删除成功', referer(), 'success');
			} else {
			message('区域信息删除失败，请重试');
			}

	}



	if($act=='cancel')
	{	
			//是否需要删除区域下的快递信息，待估
			$m_id=$_GPC['id'];
			

			if (pdo_update($e_solution_member,array('m_status' =>0),array('m_id' =>$m_id,'uniacid' => $_W['uniacid']))) {
				message('权限取消成功', referer(), 'success');
				} else {
				message('权限取消失败，请重试');
				}
				exit();
	}






	if($act=='display' || $act=='displaya')
	{	

	$tcca=tablename($e_solution_area);
	$tccm=tablename($e_solution_member);
 
	$pindex = max(1, intval($_GPC['page']));
	$psize = 30;
	$leftjoin=" LEFT JOIN $tcca on $tccm.m_area_code like $tcca.area_code";
	$condition =" where $tccm.uniacid =:uniacid and $tccm.m_status =:m_status";
	$orderby="  ";

	if ($act=='display')
	{
		$params = array(
		'uniacid' => $_W['uniacid'],
		'm_status' => 1,
		);
	}
	else
	{
		$params = array(
		'uniacid' => $_W['uniacid'],
		'm_status' => 0,
		);

	}

	$select ="*";

	$sql = "SELECT $select from ".$tccm.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;


	$mlist = pdo_fetchall($sql,$params);
	
	$sql = "SELECT COUNT(*) FROM " .$tccm.$condition;
	$total = pdo_fetchcolumn($sql,$params);
	$pager = pagination($total, $pindex, $psize);





	}






		

		
	 include $this->template('web/shop_manage');  
	

	?>