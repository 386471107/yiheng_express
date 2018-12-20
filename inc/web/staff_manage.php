<?php 

	global $_GPC, $_W;



	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();

	// $nav = $wopt->page_info();

		$express_area='yiheng_express_area';
		$express_member='yiheng_express_member';
		$express_sms_info='yiheng_express_sms_info';
		$express_member_bind='yiheng_express_member_bind';
		$express_web_user='yiheng_express_web_user';



		$Tea=tablename($express_area);
		$Tem=tablename($express_member);
		$Tesi=tablename($express_sms_info);
		$Temb=tablename($express_member_bind);


		$act=trim($_GPC['act']);
		$id=(int)($_GPC['id']);
		
		$def_shop_id = $wopt->Get_web_def_shop();


		$act = in_array($_GPC['act'], array('display','displaya','cancel','edit','del','area','area_add','area_edit','area_del'))?$_GPC['act']:'display';
		if ($act=='display')
		{
			$pindex = max(1, intval($_GPC['page']));
			$level=(int)($_GPC['level']);
			$list = $wopt->Get_web_member_list($def_shop_id,$level,$pindex,$psize=50);
			$mlist =$list['mlist'];
			$pager=$list['pager'];
		}

			if ($act=='cancel')
		{
			$b_uid=(int)($_GPC['b_uid']);
			$res =pdo_update($express_member_bind,array('bind_m_level' =>0),array('bind_m_uid' =>$b_uid));

			$resa =pdo_update($express_member,array('m_level' =>0),array('m_uid' =>$b_uid));


			if ($res && $resa)
			{
				message('权限取消成功', referer(), 'success');
			}
			else
			{
				message('权限取消失败，请检查');
			}

			
		}


		if ($act=='area_del')
		{
			global $_W;	
			$id=$_GPC['id'];
			$params = array(
							'area_id' => $id,
							'uniacid' => $_W['uniacid']
							);
			$where=' WHERE area_id = :area_id and uniacid =:uniacid';
			$item = pdo_fetch("SELECT area_code FROM ".tablename($express_area). $where,$params);

			$res = pdo_delete($express_area,array('area_id' =>$id,'uniacid' => $_W['uniacid']));

			$resa = pdo_update($express_member_bind,array('bind_m_level' =>0),array('bind_shop_id' =>$item['area_code'] ));
			
			if ($res) {
				$resa = pdo_delete($express_web_user,array('web_area_id' =>$id,'uniacid' => $_W['uniacid']));
					message('删除成功！', referer(), 'success');
					} else {
					message('删除失败！');
					}
					exit();
		}


		if ($act=='area')
		{
		$wopt = new yh_web_option();
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$list=$wopt-> Get_web_shop_list_by_weid($pindex,$psize);
		$shop_list =$list['lst'];
		$list=$wopt->Get_web_api_list_by_weid();
		}


		if(checksubmit('m_save'))
		{

		
			$id = (int)($_GPC['id']);

         	$m_data=array(
				'm_realname'=>trim($_GPC['m_realname']),
				'm_tel'=>trim($_GPC['m_tel']),
				'm_remark'=>trim($_GPC['m_remark']),
				'm_level'=>(int)($_GPC['m_level']),
				
			);

         	$bind_shop_id = intval($_GPC['bind_shop_id']);
 			$smsinfo = $wopt->Get_web_sms_info($id,$bind_shop_id);

 			if (!$bind_shop_id)
 				message("店铺代码不正确！");
 			$last_sms_surplus=$smsinfo['sms_surplus'];
 			$m_info =$wopt->Get_member_info_by_id($id);

        	 if ($id)
        	 {	
        	 	//权限分配 -更新member表中信息，同时需更新member_bind表。
        	 	$m_res =pdo_update($express_member,$m_data,array('m_uid' =>$id,'uniacid' => $_W['uniacid']));
        	 	$m_resa =pdo_update($express_member_bind,array('bind_m_level'=>(int)($_GPC['m_level']),'bind_m_ban'=>trim($_GPC['m_ban'])),array('bind_shop_id'=>$m_info['m_defaut_area'],'bind_m_uid' =>$m_info['m_uid'],'uniacid' => $_W['uniacid']));
        	 	//权限分配 -更新member表中信息，同时需更新member_bind表。
        	 	

        	 	//判断接收来的短信数量
        	 	// if ((int)($_GPC['sms_surplus'])<0)
        	 	// {
        	 	// 	//message('短信数据不正确，修改短信数据失败');
        	 	// }

        	 	// $smsinfoA=$wopt->Get_web_sms_info($m_info['m_uid'],$bind_shop_id);
        	 	
        	 	// if (empty($smsinfoA))
        	 	// {
        	 	// 	$wopt->Insert_web_sms_info($m_info['m_openid'],$m_info['m_uid'],$bind_shop_id);
        	 	// }
        	 		
        	 	//原有数据减传过来的数据,如果不为零，则进入修改+log记录
        	 	// $change_total = $smsinfo['sms_surplus']-(int)($_GPC['sms_surplus']) ;
     //    	 	$change_total = (int)($_GPC['sms_surplus']) ;

     //    	 	if ($change_total !=0)
     //    	 	{
        	 		
     //    	 		if ($change_total>0)
		   //      	 	{
		        	 		
		   //      	 		//会员数据大于当前，则为减少数据，店铺增加数据
		   //      	 		$res = $wopt->Change_shop_sms_total($smsinfo['sms_shop_id'],$change_total,1);
		   //      	 		$chg_type=0;
		   //      	 	}
		   //      	 	else
					// 	{
					// 		$res =$wopt->Change_shop_sms_total($smsinfo['sms_shop_id'],$change_total,0);
					// 		$chg_type=1;
					// 	}
						
					// if (!$res) message('短信修改数据不正确，处理失败');
					  // $wopt->Web_sms_insert_change($_W['username'],$_W['uid'],$smsinfo['sms_shop_id'],$smsinfo['sms_openid_cando'],$smsinfo['sms_uid'],(int)($_GPC['sms_surplus']),-$change_total,$last_sms_surplus,$chg_type);
        	 	// }
        	 	
				

        	 	if (empty($smsinfo))
        	 	{
        	 			$sms_data=array(	
						'sms_uid'=>$id,
						'sms_surplus'=>(int)($_GPC['sms_surplus']),
						'sms_shop_id'=>$bind_shop_id ,
						'sms_openid_cando'=>$m_info['m_openid'],
						'sms_type'=>trim($_GPC['m_level']),
						'sms_status'=>$_GPC['sms_status'] ,
						'sms_notice'=>$_GPC['sms_notice'] ,
						'sms_level'=>(int)($_GPC['m_level']),
						'uniacid' => $_W['uniacid'],
						'sms_m_uid'=>$id,
						);
					 $result=pdo_insert($express_sms_info, $sms_data);

        	 	}
        	 	else
        	 	{
    	 			$sms_data=array(
    	 				'sms_surplus'=>(int)($_GPC['sms_surplus']),
						'sms_type'=>trim($_GPC['m_level']),
						'sms_status'=>$_GPC['sms_status'] ,
						'sms_notice'=>$_GPC['sms_notice'] ,
						'sms_level'=>(int)($_GPC['m_level']),
					);
					$result = pdo_update($express_sms_info, $sms_data, array('sms_id' => $smsinfo['sms_id'],'uniacid' => $_W['uniacid']));

        	 	}

 
        	 		if ($m_res || $m_resa || $result) {
					message('修改店员信息成功', referer(), 'success');
					} else {
					message('修改店员信息失败，请重试');
					}
					exit();
        	 }
        	

	
		}



		

		


if(checksubmit('area_add'))
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
			$exsit = pdo_fetch("SELECT count(*) as cnt FROM ".tablename($express_area). $where.$orderby,$params);

			if ($exsit['cnt']) message('企业重复输入，请重试', referer(), 'error');
			
			//查询企业信息是，不允许重复
			$area_code_sence ='YH_'.$_GPC['area_code'];
		

			$area_id = (int)($_GPC['area_code']);
			$location =$_GPC['location'];
         	$area_data=array(	
				'area_person'=>$_GPC['area_person'],
				'area_tel'=>$_GPC['area_tel'],
				'area_code'=>$_GPC['area_code'],
				'area_name'=>$_GPC['area_name'],
				'area_desc'=>$_GPC['area_desc'],
				'area_code_sence'=>$area_code_sence,
				'area_lat'=>$location['lat'],
				'area_lng'=>$location['lng'],
				'area_location'=>$_GPC['area_location'],
				'area_logo'=>$_GPC['area_logo'],
				'area_wx_notice_used'=>$_GPC['area_wx_notice_used'],
				'area_wx_notice_total'=>$_GPC['area_wx_notice_total'],


				'uniacid' => $_W['uniacid'],


			);

			

			$web_data=array(	
				'web_uid'=>$_W['uid'],
				'web_uname'=>$_W['username'],
				'web_shop_id'=>$_GPC['area_code'],
				'web_shop_name'=>$_GPC['area_name'],
				'web_group'=>$_W['user']['groupid'],
				'uniacid' => $_W['uniacid'],
			);
        	
        	
		 	if (pdo_insert($express_area,$area_data)) {
		 		
		 		$web_data['web_area_id']=pdo_insertid();
		 		pdo_insert($express_web_user,$web_data);
				message('新增成功',   $this->createWebUrl('staff_manage',array('act'=>'area')), 'success');
				} else {
				message('新增失败，请重试');
				}
				exit();
		 

	
		}




if(checksubmit('area_add'))
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
			$exsit = pdo_fetch("SELECT count(*) as cnt FROM ".tablename($express_area). $where.$orderby,$params);

			if ($exsit['cnt']) message('企业重复输入，请重试', referer(), 'error');
			
			//查询企业信息是，不允许重复
			$area_code_sence ='YH_'.$_GPC['area_code'];
		

			$area_id = (int)($_GPC['area_code']);
			$location =$_GPC['location'];
         	$area_data=array(	
				'area_person'=>$_GPC['area_person'],
				'area_tel'=>$_GPC['area_tel'],
				'area_code'=>$_GPC['area_code'],
				'area_name'=>$_GPC['area_name'],
				'area_desc'=>$_GPC['area_desc'],
				'area_code_sence'=>$area_code_sence,
				'area_lat'=>$location['lat'],
				'area_lng'=>$location['lng'],
				'area_location'=>$_GPC['area_location'],
				'area_logo'=>$_GPC['area_logo'],
				'area_wx_notice_used'=>$_GPC['area_wx_notice_used'],
				'area_wx_notice_total'=>$_GPC['area_wx_notice_total'],

				'uniacid' => $_W['uniacid'],
			);
        	
		 	if (pdo_insert($express_area,$area_data)) {
				message('新增成功',  $this->createWebUrl('staff_manage',array('act'=>'area')), 'success');
				} else {
				message('新增失败，请重试');
				}
				exit();
		 

	
		}


		
if(checksubmit('area_save'))
		{

			$id=$_GPC['id'];
			$area_code =trim($_GPC['area_code']);
			empty($area_code)?message("企业信息不允许为空"):'';

			$area_id = (int)($_GPC['area_id']);
			$location =$_GPC['location'];
         	$area_data=array(	
				'area_person'=>$_GPC['area_person'],
				'area_tel'=>$_GPC['area_tel'],
				'area_name'=>$_GPC['area_name'],
				'area_lat'=>$location['lat'],
				'area_lng'=>$location['lng'],
				'area_desc'=>$_GPC['area_desc'],
				'area_location'=>$_GPC['area_location'],
				'area_logo'=>$_GPC['area_logo'],
				'area_wx_notice_used'=>$_GPC['area_wx_notice_used'],
				'area_wx_notice_total'=>$_GPC['area_wx_notice_total'],

				'uniacid' => $_W['uniacid'],
			);


			if ($_GPC['shop_status']==1)
			{
				pdo_update($express_web_user,array('web_default_shop' =>0),array('uniacid' =>$_W['uniacid']));
				pdo_update($express_web_user,array('web_default_shop' =>1),array('web_shop_id'=>$area_code,'uniacid' =>$_W['uniacid']));
				$flag = 1;

			}

	 		if ($flag || pdo_update($express_area,$area_data,array('area_id' =>$area_id))) {


			message('修改成功', referer(), 'success');
			} else {
			message('修改失败，请重试');
			}
			exit();
        	

	
		}

	
	
		if ($act=='edit' && $id)
		{
			$def_shop_id = $wopt->Get_web_def_shop();
			$params = array(
							'bind_m_uid' => $id,
							'uniacid' => $_W['uniacid'],
							'bind_shop_id' => $def_shop_id,
							);
			$leftjoin=" LEFT JOIN $Tem on $Temb.bind_m_uid = $Tem.m_uid ";
			$where=" WHERE $Temb.bind_m_uid = :bind_m_uid and bind_shop_id =:bind_shop_id and $Temb.uniacid =:uniacid";	
			$orderby='';
			$limit =" limit 1";
			$item = pdo_fetch("SELECT * FROM $Temb".$leftjoin .$where.$orderby,$params);


			if (!empty($item))
			{
				$item['m_level'] =  $wopt->Get_web_member_level_by_bind($item['bind_m_uid'],$def_shop_id);
				$s_info = $wopt->Get_web_sms_info($item['bind_m_uid'],$def_shop_id);
				$item['sms_surplus']=$s_info['sms_surplus'];
				$item['sms_status']=$s_info['sms_status'];

			}
			else
			{
				message('无权限修改此用户信息！',"error");
			}
		
		}



		if ($act=="area_edit" )
		{
			
			$area_id=$_GPC['id'];
			$params = array(
							'area_id' => $area_id,
							'uniacid' => $_W['uniacid'],

							);
			$where=" WHERE area_id = :area_id and  uniacid =:uniacid";	
			$orderby='';
			$limit =" limit 1";
			$item = pdo_fetch("SELECT * FROM ".tablename($express_area) .$where.$orderby,$params);

			if (!empty($item))
			{
				$params = array(
							'web_shop_id' => $item['area_code'],
							'uniacid' => $_W['uniacid'],

							);
				$where=" WHERE web_shop_id = :web_shop_id and  uniacid =:uniacid";	
				$orderby='';
				$limit =" limit 1";
				$web_item = pdo_fetch("SELECT * FROM ".tablename($express_web_user) .$where.$orderby,$params);
				if (!empty($web_item))
				{
					$item['status'] =$web_item['web_default_shop'];
				}

			}



		
		}
		

	include $this->template('web/staff_manage');  

?>