<?php 

	global $_GPC, $_W;


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();
		
	// checklogin();

	
	$express_m_pages_group="yiheng_express_m_pages_group";
	$express_m_pages="yiheng_express_m_pages";




	if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			$level=(int)($_GPC['level']);
			$page_id =(int)($_GPC['page_id']);
			$status =$_GPC['status'];
			if ($act=='change' && $level && $page_id )
			{
				//更新yiheng_express_m_pages
				$params_a = array(
								'mpages_id' => $page_id,
								'uniacid' => $_W['uniacid'],
								'mpage_type' => 'page',
								
								);
				$where=' WHERE mpages_id = :mpages_id and mpage_type =:mpage_type and uniacid =:uniacid ';	
				$orderby='';
				$limit =" limit 1";
				$view_info = pdo_fetch("SELECT mpage_view_groups  FROM ".tablename($express_m_pages). $where.$orderby,$params_a);
				$m_groups = $view_info['mpage_view_groups'];
				$arr_m_groups=json_decode($m_groups); 
				
				if ($status=='true')
				{
					array_push($arr_m_groups,$level);
					$new_groups=json_encode($arr_m_groups);
				}
				else
				{
					$arr_new_groups =array();
					foreach($arr_m_groups as $key =>$kv)
					{
						if($level != $kv)
						array_push($arr_new_groups,$kv);
					}
				$new_groups=json_encode($arr_new_groups);
				}
				pdo_update($express_m_pages,array('mpage_view_groups' =>$new_groups),$params_a);
				
				
				//更新yiheng_express_m_pages
				//====================================================================================
				exit();
				
				//更新express_m_pages_group表中权限
				$params = array(
								'mgroup_id' => $level,
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE mgroup_id = :mgroup_id and uniacid =:uniacid ';	
				$orderby='';
				$limit =" limit 1";
				$view_info = pdo_fetch("SELECT mgroup_pages  FROM ".tablename($express_m_pages_group). $where.$orderby,$params);
				$m_pages = $view_info['mgroup_pages'];
				
				$arr_m_pages=json_decode($m_pages); 
				
				if ($status=='true')
				{
					array_push($arr_m_pages,$page_id);
					$new_pages=json_encode($arr_m_pages);
				}
				else
				{
					$arr_new_pages =array();
					foreach($arr_m_pages as $key =>$kv)
					{
						if($page_id != $kv)
						array_push($arr_new_pages,$kv);
					}
				$new_pages=json_encode($arr_new_pages);
					
				}
				pdo_update($express_m_pages_group,array('mgroup_pages' =>$new_pages),$params);
				//更新express_m_pages_group表中权限
				
				
				
				
				
				 
				exit();
				if (!empty($view_info))
				{
					//更新pages_group
				 	if (!empty($view_info['group_pages']))
				 	{
						$arr_pages=json_decode($view_info['group_pages']);
						if (in_array($page_id,$arr_pages))
						{
							foreach( $arr_pages as $k=>$v) {
								 if($page_id == $v) unset($arr_pages[$k]);
								}
								$arr_pages = array_merge($arr_pages);
								$json_arr_pages=json_encode($arr_pages);
						}
						else
						{
						   array_push($arr_pages,$page_id);
						   $json_arr_pages= json_encode($arr_pages);
						}
					}
					else
					{
					
						$arr_pages[0]=$page_id;
						$json_arr_pages= json_encode($arr_pages);
					}
					$res = pdo_update($express_pages_group, array('group_pages' => $json_arr_pages), array('g_id' => $view_info['g_id']));
					//更新pages_group
					//
					//
					//
					unset($arr_pages);
					unset($json_arr_pages);

					
					$p_params = array(
									'pages_id' => $page_id,
									'uniacid' => $_W['uniacid'],
									);
					$where=' WHERE pages_id = :pages_id and uniacid =:uniacid ';	
					$orderby='';
					$limit =" limit 1";
					$page_info = pdo_fetch("SELECT *  FROM ".tablename($express_pages). $where.$orderby,$p_params);
					if (!empty($page_info))
					{
						if (!empty($page_info['page_view_groups']))
					 	{
							$arr_pages=json_decode($page_info['page_view_groups']);
							if (in_array($ver,$arr_pages))
							{
								foreach( $arr_pages as $k=>$v) {
									 if($ver == $v) unset($arr_pages[$k]);
									}
									$arr_pages = array_merge($arr_pages);
									$json_arr_pages=json_encode($arr_pages);
							}
							else
							{
							   array_push($arr_pages,$ver);
							   $json_arr_pages= json_encode($arr_pages);
							}
						}
						else
						{
						
							$arr_pages[0]=$ver;
							$json_arr_pages= json_encode($arr_pages);
						}
						$res = pdo_update($express_pages, array('page_view_groups' => $json_arr_pages), array('pages_id' => $page_info['pages_id']));

					}

					result_back(1);
				}
				else
				{
					result_back(0);
				}

			}

		}	



	//下拉菜单显示所有级别用户
	$levels= $wopt->Get_user_all_level();

	
	
	$level_id=(int)($_GPC['level']);
	
	// $select_level=$wopt->Get_user_level($level_id);
	// $arr_mgroup_page=json_decode($select_level['mgroup_pages']);
	
	

	
		$p_params = array(
						'uniacid' => $_W['uniacid'],
						);
		$where=" WHERE  uniacid =:uniacid ";
		$m_page_list= pdo_fetchall("SELECT *  FROM ".tablename($express_m_pages). $where.$orderby,$p_params);
		
		if (!empty($m_page_list))
		 {
			foreach($m_page_list as $key =>$v)
			{
				$view_groups=$v['mpage_view_groups'];
				if (in_array($level_id,json_decode($view_groups)))
				$m_page_list[$key]['checked']=true; 
			}
		 }

		// print_r($m_page_list);exit();
 
	 include $this->template('pc/pc_m_view_setting');  
	
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