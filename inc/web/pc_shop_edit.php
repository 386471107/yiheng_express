<?php 

	global $_GPC, $_W;
	
	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';
	$wopt = new yh_web_option();
	$nav = $wopt->page_info();

	$express_area='yiheng_express_area';
	$express_web_user='yiheng_express_web_user';



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
				'area_time_status'=>$_GPC['area_time_status'],
				'area_start_time'=>$_GPC['area_start_time'],
				'area_end_time'=>$_GPC['area_end_time'],
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
		 		//同时新增入web表中，供后台用户管理
		 		$web_data['web_area_id']=pdo_insertid();
		 		pdo_insert($express_web_user,$web_data);
				message('新增成功',  $this->createWebUrl('pc_shop_list'), 'success');
				} else {
				message('新增失败，请重试');
				}
				exit();
		 

	
		}
		
if(checksubmit('area_edit'))
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
				'area_last_message'=>intval($_GPC['area_last_message']),
				'area_wx_notice_total'=>intval($_GPC['area_wx_notice_total']),
				'uniacid' => $_W['uniacid'],
				'area_default'=>$_GPC['shop_default'],
				'area_time_status'=>$_GPC['area_time_status'],
				'area_start_time'=>$_GPC['area_start_time'],
				'area_end_time'=>$_GPC['area_end_time'],
			);

         	pdo_update($express_web_user,array('web_default_shop' =>0),array('uniacid' =>$_W['uniacid']));
			$res=pdo_update($express_web_user,array('web_default_shop' =>1,'web_shop_name'=>$_GPC['area_name']),array('web_shop_id'=>$area_code,'uniacid' =>$_W['uniacid']));
			$resb=pdo_update($express_area,$area_data,array('area_id' =>$area_id));

	 		if ($res || $resb) {
			message('修改成功', referer(), 'success');
			} else {
			message('修改失败，请重试');
			}
			exit();
	
		}

	
	$area_id=$_GPC['id'];
	$params = array(
					'area_id' => $area_id,
					'uniacid' => $_W['uniacid'],

					);
	$where=" WHERE area_id = :area_id and  uniacid =:uniacid";	
	$orderby='';
	$limit =" limit 1";
	$item = pdo_fetch("SELECT * FROM ".tablename($express_area) .$where.$orderby,$params);
	
	$location['lat']=$item['area_lat'];
	$location['lng']=$item['area_lng'];
	include $this->template('pc/pc_shop_edit');  
	
	 
	
?>