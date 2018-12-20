<?php 
defined('IN_IA') or die('Access Denied');
	Class yh_member extends WeModuleSite

	{


		protected $express_member="yiheng_express_member";
		protected $express_member_bind="yiheng_express_member_bind";
		protected $express_area="yiheng_express_area";
		protected $express_log_login='yiheng_express_log_login';
		protected $qrcode_stat='qrcode_stat';
		protected $express_m_pages='yiheng_express_m_pages';
		
		
		
		public function Get_Userinfo()
		{
			global  $_W;
			 $appid	 =$_W['oauth_account']['key'];
			 $secret	 =$_W['oauth_account']['secret'];
			 $snsapi  = 'snsapi_userinfo'; 
			 $expired = '1200';
			 $wxuser = $_COOKIE[$_W['config']['cookie']['pre'] . $appid];
			if ($wxuser === NULL) {
				$code = ((isset($_GET['code']) ? $_GET['code'] : ''));

				if (!($code)) {
					$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
					header('Location: ' . $oauth_url);
					exit();
				}
				load()->func('communication');
				$getOauthAccessToken = ihttp_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code');
				$json = json_decode($getOauthAccessToken['content'], true);

				if (!(empty($json['errcode'])) && (($json['errcode'] == '40029') || ($json['errcode'] == '40163'))) {
					$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ((strpos($_SERVER['REQUEST_URI'], '?') ? '' : '?'));
					$parse = parse_url($url);

					if (isset($parse['query'])) {
						parse_str($parse['query'], $params);
						unset($params['code'], $params['state']);
						$url = 'http://' . $_SERVER['HTTP_HOST'] . $parse['path'] . '?' . http_build_query($params);
					}


					$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
					header('Location: ' . $oauth_url);
					exit();
				}


				if ($snsapi == 'snsapi_userinfo') {
					$userinfo = ihttp_get('https://api.weixin.qq.com/sns/userinfo?access_token=' . $json['access_token'] . '&openid=' . $json['openid'] . '&lang=zh_CN');
					$userinfo = $userinfo['content'];
				}
				 else if ($snsapi == 'snsapi_base') {
					$userinfo = array();
					$userinfo['openid'] = $json['openid'];
				}
				$userinfostr = json_encode($userinfo);
				isetcookie($appid, $userinfostr, $expired);
			}
			else
			{
				$userinfo= json_decode($wxuser, true);
			}
			return $userinfo;
		}



		public function judge_db_member($openid)
		{
			if (empty($openid))
			{

				$userinfo= json_decode($this->Get_Userinfo());
				$openid = $userinfo->openid;
				//判断用户是否存在于
				$exsit = $this->Check_Member($userinfo->openid);
				if (!$exsit)
				{
					$this->Insert_memberinfo();
					// header("Location: $url"); 
				} 
			}
			else
			{
				$mcinfo = mc_oauth_userinfo();
				$openid=$mcinfo['openid'];
				$exsit = $this->Check_Member($openid);
				if (!$exsit)
				{
					
					$this->Insert_memberinfo();
					//header("Location: $url");
				}
			}
		}


		public function getMember($openid)
		{
			global $_W;		

			if (empty($openid))  die();
			$express_member="yiheng_express_member";
			$where =' and m_ban = 0';
			$limit =" limit 1";
			$Pramas=array(

					':uniacid' => $_W['uniacid'], 
					':m_openid' => $openid,
			);
			$memberitem = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);

			empty($memberitem)?$this->Insert_memberinfo():$this->Update_visittime($memberitem['id'],$memberitem['m_visit_count']+1);	
			
 
			return $memberitem;
		}


 		
		/**
		 * @Author   YIHENG.NET
		 * @DateTime 2018-10-18
		 * @param    [type]     $shop_id  [description]
		 * @param    [type]     $m_openid [description]
		 */
		 public function Get_user_level($shop_id,$m_openid)
				{
					global $_W;	
					$Pramas=array(
							'uniacid' => $_W['uniacid'], 
							'bind_m_openid' => $m_openid,
							'bind_shop_id' => $shop_id,
					); 
					$item = pdo_fetch('select * from ' . tablename($this->express_member_bind) . ' where bind_m_openid =:bind_m_openid and bind_shop_id =:bind_shop_id and uniacid=:uniacid '.$where.$limit, $Pramas);

					
					return ($item['bind_m_level']!=0)?$item['bind_m_level']:0;

				 }

		public function Get_user_cur_level($openid)
		{
			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':m_openid' => $openid,
			); 
			$item = pdo_fetch('select m_level from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
		
			return empty($item)?0:$item['m_level'];
		}
		
		
		public function is_allow_to_view($page,$level)
		{
			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':mpage_page' => $page,
			); 
			$item = pdo_fetch('select mpage_view_groups from ' . tablename($this->express_m_pages) . ' where mpage_page =:mpage_page and uniacid=:uniacid '.$where.$limit, $Pramas);
			if (!empty($item))
			{
				$arr_item=json_decode($item['mpage_view_groups']);
				if (in_array($level, $arr_item))
				return 1;
				return 0;
			}
			else
			{
				return 0;
			}
			
		}

//********************************************************************************************
		public function Check_Member($m_openid)
		{
			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':m_openid' => $m_openid,
			);
			$item = pdo_fetch("select * from ". tablename($this->express_member) . " where m_openid =:m_openid and uniacid=:uniacid ".$where.$limit, $Pramas);
			return empty($item)?0:1; 

		 }

		 
	  	
		  public function Get_shop_list_by_sence($m_openid)
			{
				global $_W;	
				$uniacid= $_W['uniacid'];
				$list = pdo_fetchall("select scene_str from ims_qrcode_stat where openid  like '$m_openid' and uniacid = $uniacid  group by scene_str ");
				if (!empty($list))
				{

					foreach ($list as $key => $value) 
					{
						$sence_str[] =$value['scene_str'];
						$sence_item = $this-> Get_shop_id_by_sence($value['scene_str']);
						$area_code[]= $sence_item['area_code'];
						$area_id[]= $sence_item['area_id'];
					}
					$info['json_sence']=json_encode($sence_str);
					$info['json_area_code']=json_encode($area_code);
					$info['deaufult_area']=$area_code[0];
					return $info;
				}
				else
				{
					return '';
				}

			 }
			 

			 /**
			  * @Author   YIHENG.NET
			  * @DateTime 2018-08-30
			  * @description 通过场景取得shop_id 
			  * @param    [type]     $sence_str [description]
			  */
			 public function Get_shop_id_by_sence($sence_str)
			{
				global $_W;	
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':area_code_sence' => $sence_str,
				);
				$where=" where area_code_sence =:area_code_sence and uniacid=:uniacid ";
				$item = pdo_fetch("select * from ". tablename($this->express_area) .$where, $Pramas);
				return $item;
			 }


			  public function Get_shop_sence_by_id($area_code)
			{
				global $_W;	
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':area_code' => $area_code,
				);
				$where=" where area_code =:area_code and uniacid=:uniacid ";
				$item = pdo_fetch("select * from ". tablename($this->express_area) .$where, $Pramas);
				return $item;
			 } 


			 	  public function Get_shop_sence_all($openid)
			{
				global $_W;	
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':openid' => $openid,
				);
				$where=" where openid =:openid and uniacid=:uniacid ";
				$groupby =" group by scene_str";
				$list = pdo_fetchall("select * from ". tablename($this->qrcode_stat) .$where. $groupby , $Pramas);
				return $list;
			 }






		public function Insert_memberinfo()
		{
			global $_W;	

			$area_info = $this ->Get_shop_list_by_sence($_W['fans']['tag']['openid']);
			if(empty($area_info))
			{
				$area_info['json_area_code']='';
				$area_info['json_sence']='';
				$area_info['deaufult_area']='';
			}
			 $data = array(		 
				'uniacid' => $_W['uniacid'],
				'm_uid' => $_W['fans']['uid'],
				'm_fansid' => $_W['fans']['fanid'],
				'm_openid' => $_W['fans']['tag']['openid'],
				'm_avatar' =>  $_W['fans']['tag']['avatar'],
				'm_nickname' =>  $_W['fans']['tag']['nickname'],
				'm_addtime' =>  $_W['fans']['tag']['subscribe_time'],
				'm_sex' =>  $_W['fans']['tag']['sex'],
				'm_city' =>  $_W['fans']['tag']['city'],
				'm_province' =>  $_W['fans']['tag']['province'],
				'm_country' =>  $_W['fans']['tag']['country'],
				'm_subscribe_time' =>  $_W['fans']['followtime'],
				'm_follow' =>  $_W['fans']['follow'],
				'm_visit_count' =>  1,
				'm_area_list' =>  $area_info['json_area_code'],
				'm_sence_list' =>  $area_info['json_sence'],
				'm_visit_lasttime' =>  time(),
				'm_defaut_area' =>  $area_info['deaufult_area'],
				'm_visit_ip' =>  $_W['clientip'],			
				);
			 if (!empty($data['m_openid']))
			 {
			 	 $result=pdo_insert($this->express_member, $data);
			 	 //多个二维码,bind里面多列添加
			 	if ($area_info['json_area_code']!='')
			 	{
			 		$shop_list = json_decode($data['m_area_list']);
			 		$json_sence =json_decode($area_info['json_sence']);
			 		foreach ($shop_list as $key => $value) {
			 			$data['m_defaut_area']=$value;
			 			$data['m_sence_list']=$json_sence[$key];
			 			$this->Insert_bind_member($data);
			 		}
			 	}
			 	else
			 	{
			 		$this->Insert_bind_member($data);
			 	}	

			 }
			
			
		}

		







		public function Insert_bind_member($data)
		{
			

			$bind_data = array(		 
				'uniacid' => $data['uniacid'],
				'bind_m_uid' => $data['m_uid'],
				'bind_m_openid' => $data['m_openid'],
				'bind_m_level' =>  '0',
				'bind_shop_id' =>   $data['m_defaut_area'],
				'bind_from_sence' =>   $data['m_sence_list'],
				'bind_time' =>   time(),
				);
			
			 $result=pdo_insert($this->express_member_bind, $bind_data);


		}


		


//****************************************************************************************
//
//





		public function Update_memberinfo($page)
		{
			global $_W;	
			$area_info = $this ->Get_shop_list_by_sence($_W['fans']['tag']['openid']);
			if (!empty($area_info))
			{
				 $data = array(		 
					'uniacid' => $_W['uniacid'],
					'm_follow' =>  $_W['fans']['tag']['subscribe'],
					'm_area_list' =>  $area_info['json_area_code'],
					'm_sence_list' =>  $area_info['json_sence'],
					);
				// $result = pdo_update($this->express_member, $data, array('m_openid like' => $_W['fans']['tag']['openid'],'uniacid' => $_W['uniacid']));
				$result =$this->Update_member_status($_W['fans']['tag']['openid'],$_W['fans']['tag']['subscribe'],$page);
			}

		}
  

		public function Update_member_status($openid,$follow,$page='my')
		{
			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':m_openid' => $openid,
			);
			$item = pdo_fetch("select * from ". tablename($this->express_member) . " where m_openid =:m_openid and uniacid=:uniacid ".$where.$limit, $Pramas);

			if (!empty($item))
			{
				$data = array(
					'm_visit_count' => $item['m_visit_count']+1,
					'm_visit_lasttime' => time(),
					'm_visit_ip' => $_W['clientip'],
					'm_follow' =>  $follow,
					); 
				$result = pdo_update($this->express_member, $data, array('id' => $item['id']));
			}
	
			$login_data = array(
					'login_uniacid' => $_W['uniacid'],
					'login_time' => time(),
					'login_mid' =>  $item['id'],
					'login_ip' => $_W['clientip'],
					'login_os' => $_W['os'],
					'login_container' =>$_W['container'],
					'login_page' =>$page,
					); 
					
			$result=pdo_insert($this->express_log_login, $login_data); 
		}



		  public function is_shop_manage($m_openid)
			{
				global $_W;	
				$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':m_openid' => $m_openid,
				); 
				$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
				if (empty($item))
				{
					return 0;
				}
				elseif($item['m_level']==2)
				{
					return 1;
				}
				
				

			 }

			 public function Get_shop_id($m_openid)
			{
				global $_W;	
				$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':m_openid' => $m_openid,
				); 
				$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
				return empty($item)?0:$item['m_defaut_area'];
			 }

			

			  public function Get_member($m_openid)
				{
					global $_W;	
					$Pramas=array(
							':uniacid' => $_W['uniacid'], 
							':m_openid' => $m_openid,
					); 
					$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid like :m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
					return $item ;

				 }


				 





		  public function Get_Permission($m_openid)
			{
				global $_W;	
				$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':m_openid' => $m_openid,
				); 
				$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);

				if ($item['m_level']==1 || $item['m_level']==2 || $item['m_level']==3 )
				{
					$allow=1;
				}
				return empty($item)?0:$allow;

			 }

			 // public function Get_shop_name($shop_id)
			 // {
			 // 	$Pramas=array(
				// 		':area_code' => $shop_id,
				// ); 
				// $where =" where area_code =:area_code";
				// $item = pdo_fetch('select area_name from ' . tablename($this->express_area) .$where, $Pramas);
				// return $item['area_name'];

			 // }

			  public function Get_shop_name($shop_id)
			 {
			 	$Pramas=array(
						':area_code' => $shop_id,
				); 
				$where =" where area_code =:area_code";
				$item = pdo_fetch('select * from ' . tablename($this->express_area) .$where, $Pramas);
				return $item;

			 }


			  public function Get_my_shop_list($m_openid)
				{

					global $_W;	
					$Temb= tablename($this->express_member_bind);
					$Tea= tablename($this->express_area);

					$Pramas=array(
							':uniacid' => $_W['uniacid'], 
							':bind_m_openid' => $m_openid,
					); 
					$leftjoin=" LEFT JOIN $Tea on $Temb.bind_shop_id =$Tea.area_code ";
					$condition = " where $Temb.bind_m_openid like :bind_m_openid and $Temb.uniacid =:uniacid";
					$my_shop_list = pdo_fetchall('select * from '.$Temb.$leftjoin.$condition, $Pramas);
					
					return $my_shop_list;
					
					

				}

			 public function Get_shop_list($m_openid)
			{
				global $_W;	
				$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':m_openid' => $m_openid,
				); 
				$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
				if (!empty($item))
				{
					$arr_area=json_decode($item['m_area_list']);
					if (!empty($arr_area))
					{
						$k=0;
						foreach ($arr_area as $key => $value) {
						$shop_info =$this->Get_shop_name($value);
						$shop_list[$k]['area_name']=$shop_info['area_name'];
						$shop_list[$k]['area_location']=$shop_info['area_location'];
						$shop_list[$k]['area_tel']=$shop_info['area_tel'];
						$shop_list[$k]['area_person']=$shop_info['area_person'];
						$shop_list[$k]['area_last_message']=$shop_info['area_last_message'];
						$shop_list[$k]['area_member_total']=$shop_info['area_member_total'];
						$shop_list[$k]['area_member_wx']=$shop_info['area_member_wx'];
						$shop_list[$k]['id']=$value;
						$k++;
						}
					}
					return $shop_list;
				}

			

				 return '';

			}


			 public function Get_shop_list_all()
			{
				global $_W;	
				$Pramas=array(
						':uniacid' => $_W['uniacid'], 
				); 
				$list = pdo_fetchall('select * from ' . tablename($this->express_area) . ' where uniacid=:uniacid '.$where.$limit, $Pramas);

				 return $list;

			}




				

			


			  public function Get_Level($m_openid)
				{
					global $_W;	
					$Pramas=array(
							':uniacid' => $_W['uniacid'], 
							':m_openid' => $m_openid,
					); 
					$item = pdo_fetch('select * from ' . tablename($this->express_member) . ' where m_openid =:m_openid and uniacid=:uniacid '.$where.$limit, $Pramas);
					return $item['m_level'];

				 }


		 public function Get_tel_exist($tel) 
		 { 
		 	
			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':m_tel' => $tel,
			); 
			$where =" where m_tel like :m_tel and uniacid=:uniacid ";
			$item = pdo_fetch('select * from ' . tablename($this->express_member) .$where, $Pramas);
			
			if (empty($item))
				return 0;
				return 1;
				 
		 }	


		 public function User_in_shop($openid)
		 	{
		 		global $_W;	
		 		$params = array(
					'uniacid' => $_W['uniacid'],
					'bind_m_openid' => $openid,
					'bind_shop_id' => 0,
					);	
		 			$where  =" where bind_m_openid like :bind_m_openid and bind_shop_id >:bind_shop_id and uniacid =:uniacid";
		 			$item = pdo_fetch('select count(*) as cnt from ' . tablename($this->express_member_bind) . $where, $params);
		 			if ($item['cnt']>0)
		 				return true;
		 			return false;
		 	}




	
	}

?>