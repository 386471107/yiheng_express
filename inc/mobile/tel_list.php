<?php 

	global $_GPC, $_W;
	
	$title = '手机号码编辑';

 	include_once MODULE_ROOT.'/inc/mobile/common.php';
	if ($myinfo['m_level']==4)
	{
		$url=$this->createMobileUrl('employee');
		header("Location: $url");exit();
	}
	
	//走权限管理
	$page="home";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
// if (empty($openid)) die();


	if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			$keyword= trim($_GPC['search_text']);
			$sid =intval($_GPC['sid']);
			$m_tel=trim($_GPC['m_tel']);
			if ($act == 'save' && $sid )
			{
				$udata = array(
						'tl_uname' => trim($_GPC['sname']),
						'tl_area' =>  trim($_GPC['sarea']),
						'tl_addr' =>  trim($_GPC['saddr']),
						'm_flag' =>  intval($_GPC['member_f']),
						'tl_modify_ex' =>  1,
						); 
				$result = pdo_update($express_tel_list, $udata,array('id' => $sid));
				 if ($result)
				 {
				 	//同步更新绑定号码，暂不做更新检测 2018.12.24
				 	$resultA = pdo_update($express_member, array('m_ban' =>intval($_GPC['member_f'])),array('m_tel' => $m_tel));
				 }
				if ($result)
				{
					result_back(1);
				}
			}
			
			if ($act == 'dis' && $sid )
			{
				$dis_info = pdo_fetch("select * from ".tablename($express_tel_list)." where id = ".$sid);
				
				if(!empty($dis_info))
				{
					result_back(1,$dis_info);
				}
				else{
					result_back(0);
				}
				
			}
			
			
			
			if ($act == 'query'  )
			{
				$phonenum=$_GPC['phonenum'];
				$user_info = pdo_fetch("select m_tel,tl_addr,tl_area,tl_uname from ".tablename($express_tel_list)." where m_tel = ".$phonenum." and uniacid=".$_W['uniacid']);
				
				if(!empty($user_info)) 
				{
					result_back(1,$user_info);
				}
				else{
					result_back(0);
				}
				
			}
			
			
			
			
			if ($act == 'search')
				{

					$tel_where= ' where m_tel =:m_tel and uniacid=:uniacid';
					$tel_params = array(
					'uniacid' => $_W['uniacid'],
					'm_tel' =>$keyword,
					);  
					$tel_info = pdo_fetch("SELECT *  FROM ".tablename($express_tel_list). $tel_where,$tel_params);

					if (!empty($tel_info))
					{
						$res['tel']=$keyword;
						$res['str']='<div class="weui-cell" onclick="udata('.$tel_info["id"].')">'.
						            '<div class="weui-cell__bd">'.
						             '<p>'.$keyword.'</p></div>'.
						            '<div class="weui-cell__ft">'.$tel_info['tl_uname'].'</div>'.
						         '</div>';
						result_back(1,$res);
					}

				
					
				}
			result_back(0);
		}


	// $tel_where= ' where  uniacid=:uniacid';
	// $orderby = " order by tl_modify_ex asc,id  DESC ";
	// $limit = " limit 0 ,800";
	// $tel_params = array(
	// 'uniacid' => $_W['uniacid'],
	
	// );  
	// $tel_info = pdo_fetchall("SELECT *  FROM ".tablename($express_tel_list).$orderby.$limit,$tel_params);
	
	

include $this->template('tel_list');  
exit();
 
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