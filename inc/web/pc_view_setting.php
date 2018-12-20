<?php 

	global $_GPC, $_W;


	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();
		
	// checklogin();

	
	$express_pages_group="yiheng_express_pages_group";
	$express_pages="yiheng_express_pages";




	if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			$ver=(int)($_GPC['ver']);
			$page_id =(int)($_GPC['page_id']);

			if ($act=='change' && $ver && $page_id )
			{

				
				$params = array(
								'group_id' => $ver,
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE group_id = :group_id and uniacid =:uniacid ';	
				$orderby='';
				$limit =" limit 1";
				$view_info = pdo_fetch("SELECT *  FROM ".tablename($express_pages_group). $where.$orderby,$params);
				
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








	$version= $wopt->Get_user_all_version();
	$v_id=(int)($_GPC['ver']);

	$ver_id =$v_id==0?1:$v_id;
	
	$select_version=$wopt->Get_user_version($ver_id);
	$arr_group_page=json_decode($select_version['group_pages']);

	$version_list = array('member'=>1,'shops' =>2,'collection' =>3,'deliver' =>4,'send_log' =>5,'send_log' =>5,'pc_api' =>6,'pc_statistics' =>7,'base_setting' =>8,'safe_manage' =>9);
	$version_name_list = array('1'=>'会员管理','2' =>'店铺管理','3' =>'快递信息','4' =>'收寄列表','5' =>'发送记录','6' =>'PC接入','7' =>'统计汇总','8' =>'基本设置','9' =>'安全管理');
	foreach ($version_list as $key => $value) {

		$pages= $wopt->Get_pages_list($value);
		$i = 0;
		foreach ($pages as $keya => $value) {
			$list[$key][$i]=$value;
			if(!empty($arr_group_page))
			{
				if(in_array($value['pages_id'],$arr_group_page))
		 		$list[$key][$i]['checked']=true;
			}
			$i++;
			
		}
		
	}
	




	include $this->template('pc/pc_view_setting');  
	
	
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