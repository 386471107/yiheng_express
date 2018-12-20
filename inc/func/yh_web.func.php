<?php 
defined('IN_IA') or die('Access Denied');
	Class yh_web_option extends WeModuleSite

	{
 

		protected $express_area="yiheng_express_area";
		protected $express_member='yiheng_express_member';
		protected $express_sms_info='yiheng_express_sms_info';
		protected $express_level_recode='yiheng_express_level_recode';
		protected $express_pages='yiheng_express_pages';
		protected $express_pages_group='yiheng_express_pages_group';
		protected $express_m_pages='yiheng_express_m_pages';
		protected $express_m_pages_group='yiheng_express_m_pages_group';
		protected $express_web_user='yiheng_express_web_user';
		protected $express_recode='yiheng_express_recode';
		protected $express_notice_log='yiheng_express_notice_log';
		protected $express_sms_log='yiheng_express_sms_log';
				
		protected $express_sms_change_log='yiheng_express_sms_change_log';
		protected $express_member_bind='yiheng_express_member_bind';
		protected $express_api='yiheng_express_api';
		protected $express_statistics='yiheng_express_statistics';
		protected $express_recode_att='yiheng_express_recode_att';
		protected $express_tpl_example='yiheng_express_tpl_example';
		protected $express_tpl='yiheng_express_tpl';
		protected $express_sms_setting='yiheng_express_sms_setting';
				
	
			public function Get_staff_list($lv_m_level)
			{

					global $_W;
					$TEA=tablename($this->express_area);
					$TEM=tablename($this->express_member);
					$TESI=tablename($this->express_sms_info);
					$TELR=tablename($this->express_level_recode);

					$pindex = max(1, intval($_GPC['page']));
					$psize = 90;

					$leftjoin=" LEFT JOIN $TEM on $TELR.lv_openid like $TEM.m_openid  "; 
					 
					$orderby="  ";
					$params = array(
						'uniacid' => $_W['uniacid'],
						'lv_m_level' => $lv_m_level,
					);
					$condition =" where $TELR.uniacid =:uniacid and $TELR.lv_m_level =:lv_m_level";
					$select ="*";
					$sql = "SELECT $select from ".$TELR.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$mlist = pdo_fetchall($sql,$params);

					$sql = "SELECT COUNT(*) FROM " .$TELR.$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);
					$list['mlist']=$mlist;
					$list['pager']=$pager;
					return $list;
			}
			
			public function Get_openid_by_uid($uid)
			{
				
				$sql = "SELECT m_openid from ".tablename($this->express_member)." where m_uid =$uid";
				$item = pdo_fetch($sql);
				return $item['m_openid'];
			}

			public function Get_shop_name($shop_code)
			{

				global $_W;	
				$params = array(
								'area_code' => $shop_code,
								'uniacid' => $_W['uniacid']
								);
				$where=' WHERE area_code = :area_code and uniacid =:uniacid';	
				
				$item = pdo_fetch("SELECT area_name FROM ".tablename($this->express_area). $where.$orderby,$params);


				
				return $item['area_name'];

				

			}

			
		

 			
 			public function Get_realname_by_openid($openid)
			{
				global $_W;	
				$params = array(
								'm_openid' => $openid,
								'uniacid' => $_W['uniacid']
								);
				$where=' WHERE m_openid like :m_openid and  uniacid =:uniacid';	
				$item = pdo_fetch("SELECT m_realname FROM ".tablename($this->express_member). $where,$params);
				return  $item['m_realname'];
			}


			public function Get_nickname_by_openid($openid)
			{
				global $_W;	
				$params = array(
								'm_openid' => $openid,
								'uniacid' => $_W['uniacid']
								);
				$where=' WHERE m_openid like :m_openid and  uniacid =:uniacid';	
				$item = pdo_fetch("SELECT m_nickname FROM ".tablename($this->express_member). $where,$params);
				return  $item['m_nickname'];
			}


			public function Get_pages_list($page_group)
			{
				global $_W;	
				$params = array(
								'page_group' => $page_group,
								'uniacid' => $_W['uniacid']
								);
				$where=' WHERE page_group =:page_group and uniacid =:uniacid';
				$orderby= ' order by page_order_by asc';	
				$list = pdo_fetchall("SELECT * FROM ".tablename($this->express_pages). $where.$orderby,$params);
				return  $list;
			}


			public function Get_user_all_version()
			{
				global $_W;	
				$params = array(
								
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE  uniacid =:uniacid';
				$orderby= ' order by g_id asc';	
				$list = pdo_fetchall("SELECT * FROM ".tablename($this->express_pages_group). $where.$orderby,$params);
				return  $list;
			}
			
			
			public function Get_user_all_level()
			{
				global $_W;	
				$params = array(
								
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE  uniacid =:uniacid';
				$orderby= ' order by mg_id asc';	
				$list = pdo_fetchall("SELECT * FROM ".tablename($this->express_m_pages_group). $where.$orderby,$params);
				return  $list;
			}
			
			
		
			
			

			public function Get_user_version($v_id)
			{
				global $_W;	
				$params = array(
								'group_id' => $v_id,
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE group_id =:group_id and uniacid =:uniacid';
				$orderby= ' order by g_id asc';	
				$list = pdo_fetch("SELECT * FROM ".tablename($this->express_pages_group). $where.$orderby,$params);
				return  $list;
			}
			
			
				public function Get_user_level($v_id)
			{
				global $_W;	
				$params = array(
								'mgroup_id' => $v_id,
								'uniacid' => $_W['uniacid'],
								);
				$where=' WHERE mgroup_id =:mgroup_id and uniacid =:uniacid';
				$orderby= ' order by mg_id asc';	
				$list = pdo_fetch("SELECT * FROM ".tablename($this->express_m_pages_group). $where.$orderby,$params);
				return  $list;
			}




			public function page_info()
			{
				global $_W;

				$str=$_W['siteurl'];
				$pattern = '/&do=(.*?)&/';
				preg_match($pattern,$str,$match);
				if (!empty($match) && sizeof($match)==2)
				{
					$page=$match[1];	
				}
				else
				{
					die();
				}
				$params = array(
					'uniacid' => $_W['uniacid'],
					'page_page' => $page,
					'page_status' => 1,
				);
				$condition=" where page_page =:page_page and page_status =:page_status and  uniacid=:uniacid";
				$sql = "SELECT * from ".tablename($this->express_pages).$condition ;
				$item = pdo_fetchall($sql,$params);
				if (!empty($item))
				{
					if (sizeof($item)==1)
					{
						return $item[0];
					}	
					else
					{
						foreach ($item as $key => $value) {
							if (strrpos($str,$value['page_param'])>0)
							{
								return $item[$key];
								break;
							}
						}
						
					}
				}
			}



 

			public function Get_web_shop_list()
				{
					global $_W;	
					$uid=$_W['uid'];
					$username=$_W['username'];
					$pindex = max(1, intval($_GPC['page']));
					$psize = 100;
					$condition =" where web_uname =:web_uname and web_uid =:web_uid and uniacid =:uniacid";
					$params = array(
					'web_uid' => $uid,
					'web_uname' => $username,	
					'uniacid' => $_W['uniacid'],
					);
					$sql = "SELECT * from ".tablename($this->express_web_user).$condition ." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$lst = pdo_fetchall($sql,$params);
					// print_r($list);exit;
					$sql = "SELECT COUNT(*) FROM " . tablename($this->express_area);
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['lst']=$lst;
					$list['pager']=$pager;
					return $list;

				}


				public function Get_web_def_shop()
				{
					global $_W;	
					$uid=$_W['uid'];
					$username=$_W['username'];
					$condition =" where web_uname =:web_uname and web_uid =:web_uid and web_default_shop =:web_default_shop and uniacid =:uniacid";
					$params = array(
						'web_uid' => $uid,
						'web_uname' => $username,
						'web_default_shop' => 1,
						'uniacid' => $_W['uniacid'],
					);

					$sql = "SELECT * from ".tablename($this->express_web_user).$condition ;
					$item = pdo_fetch($sql,$params);
				
					return $item['web_shop_id'];

				}

				public function Get_web_collection_list($shop_id,$status=0,$retention=0,$page=1,$psize=50)
				{
					
					global $_W;	
					$condition =" where recoder_shop_id =:recoder_shop_id and recoder_get_status =:recoder_get_status and recoder_retention =:recoder_retention and uniacid =:uniacid";
						$params = array(
							'recoder_shop_id' => $shop_id,	
							'recoder_retention' => $retention,	
							'recoder_get_status' => $status,
							'uniacid' => $_W['uniacid'],
						);
					$orderby =" order by recoder_updatetime DESC";
					$pindex = max(1, intval($page));

					$psize = $psize;
					$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$lst = pdo_fetchall($sql,$params);
					$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
					$total = pdo_fetchcolumn($sqlt,$params);
					$pager = pagination($total, $pindex, $psize);


					if ($status==0 || $retention==1)
					{

						$today_start = strtotime(date("Y-m-d"),time());
						$attr=' and recoder_create > '.$today_start;
						$sqltd = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition.$attr ;
						$totaltd = pdo_fetchcolumn($sqltd,$params);
						$att_tel=' and recoder_tel_exsit = 0';
						$sql_tel = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition.$att_tel ;
						$total_tel = pdo_fetchcolumn($sql_tel,$params);
						$list['total']=$total;
						$list['totaltd']=$totaltd;
						$list['total_tel']=$total_tel;
					}
					$list['lst']=$lst;
					$list['pager']=$pager;

					 $list['total']=$total;
					 $list['pindex']=$pindex;
					 $list['psize']=$psize;


					$pager = pagination($total, $pindex, $psize);
					

					return $list;

				}		




				public function Get_web_weixing_sendlog($def_shop,$pindex,$psize=100)
				{
					
					global $_W;	
					
					$condition =" where send_shop_id =:send_shop_id and uniacid =:uniacid";
					$orderby=" order by id DESC";

					$params = array(
						'send_shop_id' => $def_shop,	
						'uniacid' => $_W['uniacid'],								
					);

					$sql = "SELECT * from ".tablename($this-> express_notice_log).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

					$list['lst'] = pdo_fetchall($sql,$params);

					$sql = "SELECT COUNT(*) FROM " . tablename($this-> express_notice_log).$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$list['pager'] = pagination($total, $pindex, $psize);

					return $list;

				}

			public function Get_web_msn_sendlog($def_shop,$pindex,$psize=100)
				{
					global $_W;	
					
					$condition =" where send_shop_id =:send_shop_id and uniacid =:uniacid";
					$orderby=" order by send_id DESC";

					$params = array(
						'send_shop_id' => $def_shop,	
						'uniacid' => $_W['uniacid'],
					);


					$sql = "SELECT * from ".tablename($this-> express_sms_log).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

					$list['lst'] = pdo_fetchall($sql,$params);


					$sql = "SELECT COUNT(*) FROM " . tablename($this-> express_sms_log).$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$list['pager'] = pagination($total, $pindex, $psize);

					return $list;

				}


// $shop_id,$status=0,$retention=0,$page_index=1,$psize=50

			
			public function Get_web_member_list1($def_shop,$level=0,$pindex=1,$psize=50)
			{
 
					global $_W;

					$tea=tablename($this->express_area);
					$tem=tablename($this->express_member);
					
					$leftjoin=" LEFT JOIN $tea on $tea.area_code = $tem.m_belong_shop";

					$orderby="  ";


					$params = array(
					'uniacid' => $_W['uniacid'],
					'm_defaut_area' => $def_shop,
					'm_level' => $level,
					);

					$condition =" where $tem.m_defaut_area =:m_defaut_area and $tem.uniacid =:uniacid and $tem.m_level =:m_level";

					$select ="*";

					$sql = "SELECT $select from ".$tem.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;


					$mlist = pdo_fetchall($sql,$params);

					$sql = "SELECT COUNT(*) FROM " .$tem.$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['mlist']=$mlist;
					$list['pager']=$pager;

					return $list;

			}


			public function Get_web_member_list($def_shop,$level=0,$pindex=1,$psize=50)
			{

					global $_W;

					$tem=tablename($this->express_member);
					$temb=tablename($this->express_member_bind);
					
					$leftjoin=" LEFT JOIN $tem on $temb.bind_m_uid = $tem.m_uid";
					$orderby=" order by id ASC";
 
					$params = array(
						'uniacid' => $_W['uniacid'],
						'bind_shop_id' => $def_shop,
						'bind_m_level' => $level,
						'bind_m_ban' => 0,
					);
					$condition =" where $temb.bind_shop_id =:bind_shop_id and $temb.uniacid =:uniacid and $temb.bind_m_level =:bind_m_level and bind_m_ban = :bind_m_ban";
					$select ="*";
					$sql = "SELECT $select from ".$temb.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$mlist = pdo_fetchall($sql,$params);
					$sql = "SELECT COUNT(*) FROM " .$temb.$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

							
					$list['mlist']=$mlist;
					$list['pager']=$pager;
				
					return $list;

			}


			public function Get_web_ban_member_list($def_shop,$ban=1,$pindex=1,$psize=50)
			{

					global $_W;

					$tem=tablename($this->express_member);
					$temb=tablename($this->express_member_bind);
					
					$leftjoin=" LEFT JOIN $tem on $temb.bind_m_uid = $tem.m_uid";
					$orderby=" ";
 
					$params = array(
						'uniacid' => $_W['uniacid'],
						'bind_shop_id' => $def_shop,
						'bind_m_ban' => $ban,
					);

					$condition =" where $temb.bind_shop_id =:bind_shop_id and $temb.uniacid =:uniacid and $temb.bind_m_ban =:bind_m_ban ";
					$select ="*";
					$sql = "SELECT $select from ".$temb.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$mlist = pdo_fetchall($sql,$params);


					$sql = "SELECT COUNT(*) FROM " .$temb.$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['mlist']=$mlist;
					$list['pager']=$pager;

					return $list;

			}


			public function Get_web_shop_list_by_weid($pindex,$psize=100)
				{
					global $_W;	
					$Tewu =tablename($this->express_web_user);
					$Tea =tablename($this->express_area);
					$leftjoin =" left join $Tewu on  $Tewu.web_area_id =$Tea.area_id";
					$condition =" where $Tea.uniacid =:uniacid";
					$params = array(
					'uniacid' => $_W['uniacid'],
					);

					$sql = "SELECT * from ".tablename($this->express_area).$leftjoin.$condition ." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$lst = pdo_fetchall($sql,$params);
					// print_r($list);exit;
					$sql = "SELECT COUNT(*) FROM " . tablename($this->express_area);
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['lst']=$lst;
					$list['pager']=$pager;
					return $list;

				}


				public function Web_sms_insert_change($chg_web_user,$chg_web_uid,$chg_shop_id,$chg_user_openid,$chg_uid,$chg_cur_total,$chg_total,$chg_before_total,$chg_type)
				{
					global $_W;	
					
					$data = array(		 
						'uniacid' => $_W['uniacid'],
						'chg_web_user' => $chg_web_user,
						'chg_web_uid' => $chg_web_uid,
						'chg_shop_id' => $chg_shop_id,
						'chg_user_openid' => $chg_user_openid,
						'chg_uid' => $chg_uid,
						'chg_cur_total' => $chg_cur_total,
						'chg_total' => $chg_total,
						'chg_before_total' => $chg_before_total,
						'chg_type' => $chg_type,
						'chg_ip' =>  $_W['clientip'],			
						);
						$result=pdo_insert($this->express_sms_change_log, $data);

				}



	public function Get_sms_surplus($shop_code,$openid)
			{

				global $_W;	
				$params = array(
								'sms_shop_id' => $shop_code,
								'sms_openid_cando' => $openid,
								'uniacid' => $_W['uniacid']
								);
				$where=' WHERE sms_shop_id = :sms_shop_id and sms_openid_cando  like :sms_openid_cando and  uniacid =:uniacid';	
				
				$item = pdo_fetch("SELECT * FROM ".tablename($this->express_sms_info). $where.$orderby,$params);
				
				return  empty($item)?0:$item['sms_surplus'];
			}



			public function Get_web_sms_info($uid,$shop_id)
			{

				$params = array(
					'sms_m_uid' => $uid,
					'sms_shop_id' => $shop_id,
					);
					$condition =" where sms_m_uid =:sms_m_uid and sms_shop_id =:sms_shop_id";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_sms_info).$condition;
					$smsitem = pdo_fetch($sql,$params);
					return $smsitem;

			}


			public function Get_web_sms_info_by_openid($openid,$shop_id)
			{
				global $_W;	
				$params = array(
					'sms_openid_cando' => $openid,
					'sms_shop_id' => $shop_id,
					);
					$condition =" where sms_openid_cando like :sms_openid_cando and sms_shop_id =:sms_shop_id";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_sms_info).$condition;
					$smsitem = pdo_fetch($sql,$params);
					return $smsitem;
			}

			public function Insert_web_sms_info($openid,$uid,$shop_id)
			{
				global $_W;	
				$data = array(		 
				'uniacid' => $_W['uniacid'],
				'sms_uid' => $uid,
				'sms_shop_id' => $shop_id,
				'sms_openid_cando' => $openid,
				'sms_m_uid' => $uid,
				);
				
				$result=pdo_insert($this->express_sms_info, $data);
				return $result;

			}


			public function Get_web_shop_info($shop_id)
			{
				global $_W;	
				$params = array(
						'uniacid' => $_W['uniacid'],
						'area_code' => $shop_id,
					);

					$condition =" where area_code =:area_code and uniacid =:uniacid";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_area).$condition;
					$smsitem = pdo_fetch($sql,$params);
					return $smsitem;

			}



			public function Get_web_member_level_by_bind($uid,$shop_id)
			{
				$params = array(
					'bind_m_uid' => $uid,
					'bind_shop_id' => $shop_id,
					);
					$condition =" where bind_m_uid =:bind_m_uid and bind_shop_id =:bind_shop_id";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_member_bind).$condition;
					$item = pdo_fetch($sql,$params);
					return $item['bind_m_level'];
			}

			public function Get_web_api_list_by_weid()
			{
				global $_W;
				$params = array(
					'uniacid' => $_W['uniacid'],
					);
					$condition =" where uniacid =:uniacid";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_api).$condition;
					$list = pdo_fetchall($sql,$params);
					return $list;
			}


			public function Get_statistics_by_month_total($year=0,$month=0,$field)
			{
				
				global $_W;

				if ($year == 0 || $month == 0)
				{
					$time=strtotime(date("Y-m-d",time()));
					$year=date("Y",time());
					$month=date("m",time());
				}
				
				else
				{
					$time=strtotime(date("$year-$month-1"));
				}
					
				
				$countDay = date("t",$time);
				$day_data_arr=array();
				for ($i = 1;$i <=$countDay;$i ++)
				{
					$day_data=$this->Get_statistics_by_day_total($year,$month,$i,$field,$shop_id=0);
					if(!$day_data)
						$day_data=0;
					array_push($day_data_arr,$day_data);
					

				}
				return $day_data_arr;
				
			}

			public function Get_statistics_by_day_total($year,$month,$day,$field,$shop_id=0)
			{
				global $_W;
				if ($shop_id)
				{
					$params = array(
						'uniacid' => $_W['uniacid'],
						's_shop_id' => $shop_id,
						's_date_y' => $year,
						's_date_m' => $month,
						's_date_d' => $day,

					);
					$condition =" where s_shop_id =:s_shop_id and s_date_y =:s_date_y and s_date_m =:s_date_m and s_date_d=:s_date_d and uniacid =:uniacid";
				}
				else
				{
					$params = array(
						'uniacid' => $_W['uniacid'],
						's_date_y' => $year,
						's_date_m' => $month,
						's_date_d' => $day,
					);
					$condition =" where s_date_y =:s_date_y and s_date_m =:s_date_m and s_date_d=:s_date_d and uniacid =:uniacid";
				}

				$select ="sum($field) as cnt";
				$sql = "SELECT $select from ".tablename($this->express_statistics).$condition;
				$item = pdo_fetch($sql,$params);

				
				return (empty($item))?0:$item['cnt'];
				 
			}


			public function Get_statistics_by_shop_total($year,$month,$field)
			{
				global $_W;

					$params = array(
						'uniacid' => $_W['uniacid'],
						's_date_y' => $year,
						's_date_m' => $month,
					);
					$condition =" where s_date_y =:s_date_y and s_date_m =:s_date_m and uniacid =:uniacid GROUP BY s_shop_id ORDER BY cnt DESC limit 0,20";
				$select ="s_shop_id,SUM($field) AS cnt";
				$sql = "SELECT $select from ".tablename($this->express_statistics).$condition;
				$list = pdo_fetchall($sql,$params);

				return $list;
				
				 
			}


			public function Get_recode_in_by_hour_day($shop_id,$year,$month,$day,$hour)
			{
				global $_W;

					$params = array(
						'recoder_shop_id' => $shop_id,
						'recoder_create_year' => $year,
						'recoder_create_month' => $month,
						'recoder_create_day' => $day,
						'recoder_create_hour' => $hour,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where recoder_shop_id =:recoder_shop_id and recoder_create_year =:recoder_create_year and recoder_create_month =:recoder_create_month and recoder_create_day =:recoder_create_day and recoder_create_hour =:recoder_create_hour and uniacid =:uniacid";
				$select ="count(*) AS cnt";
				$sql = "SELECT $select from ".tablename($this->express_recode).$condition;
				$item = pdo_fetch($sql,$params);

				return $item['cnt'];
				 
			}

			public function Get_recode_out_by_hour_day($shop_id,$year,$month,$day,$hour)
			{
				global $_W;

					$params = array(
						'recoder_shop_id' => $shop_id,
						'recoder_updatetime_year' => $year,
						'recoder_updatetime_month' => $month,
						'recoder_updatetime_day' => $day,
						'recoder_updatetime_hour' => $hour,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where recoder_shop_id =:recoder_shop_id and recoder_updatetime_year =:recoder_updatetime_year and recoder_updatetime_month =:recoder_updatetime_month and recoder_updatetime_day =:recoder_updatetime_day and recoder_updatetime_hour =:recoder_updatetime_hour and uniacid =:uniacid";
				$select ="count(*) AS cnt";
				$sql = "SELECT $select from ".tablename($this->express_recode).$condition;
				$item = pdo_fetch($sql,$params);

				return $item['cnt'];
				 
			}

			

			public function Change_shop_sms_total($shop_id,$new_total,$direction=0)
			{
				global $_W;
				$new_total = abs($new_total);
					$params = array(
						'area_code' => $shop_id,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where area_code =:area_code and uniacid =:uniacid";
				$select =" * ";
				$sql = "SELECT $select from ".tablename($this->express_area).$condition;
				$item = pdo_fetch($sql,$params);
				if (empty($item))
				{
					return false;
				}
				else
				{
					if ($direction==0)
					{
						if(intval($new_total)>intval($item['area_last_message'])) 
						{
							return false;
						}
						else
						{
							//shop中新当前数据
							$shop_cur_total = intval($item['area_last_message'])-intval($new_total);
						
							$udata = array(
								'area_last_message' => $shop_cur_total,
							); 
							$result = pdo_update($this->express_area, $udata, array('area_id' => $item['area_id']));
							if ($result)
								return true;
							return false;
						}
					}
					if($direction==1)
					{	
						$shop_cur_total = intval($item['area_last_message'])+intval($new_total);
							$udata = array(
								'area_last_message' => $shop_cur_total,
							); 
							$result = pdo_update($this->express_area, $udata, array('area_id' => $item['area_id']));
							if ($result)
								return true;
							return false;
					}

				}
				
				return false;
				 
			}


		public function Get_express_company_by_day($shop_id,$day)
			{
				global $_W;

				
					$params = array(
						'recoder_shop_id' => $shop_id,
						'recoder_create_year' => date("Y",time()),
						'recoder_create_month' => date("m",time()),
						'recoder_create_day' => $day,
						'uniacid' => $_W['uniacid'],
					);

					
					$condition =" where recoder_shop_id =:recoder_shop_id and recoder_create_year =:recoder_create_year and  recoder_create_month =:recoder_create_month and uniacid =:uniacid and recoder_create_day =:recoder_create_day group by recoder_express_name";
				$select =" recoder_express_name as name,count(*) as value ";
				$sql = "SELECT $select from ".tablename($this->express_recode).$condition;
				$list = pdo_fetchall($sql,$params);

				return $list;


				

				 
			}


			public function Get_tpl_example_all()
				 {
				 	global $_W;
			        $params = array(
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where  uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_tpl_example).$condition;
					$list = pdo_fetchall($sql,$params);
					return $list;
				 }

			public function Get_tpl_example_by_id($example_id)
				 {
				 	global $_W;
			        $params = array(
			        	'id' =>$example_id,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where id= :id and  uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_tpl_example).$condition;
					$item = pdo_fetch($sql,$params);
					return $item;
				 }	

			public function Get_tpl_example_by_tplid($tpl_id)
				 {
				 	global $_W;
			        $params = array(
			        	'tpl_id' =>$tpl_id,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where tpl_id like :tpl_id and  uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_tpl_example).$condition;
					$item = pdo_fetch($sql,$params);
					return $item;
				 }		  

				public function Get_msn_tpl_by_id($shop_id,$msn_tpl_id)
				 {
				 	global $_W;
			        $params = array(
			        	'id' =>$msn_tpl_id,
			        	'sms_area_id' =>$shop_id,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where sms_area_id =:sms_area_id and id= :id and  uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_sms_setting).$condition;
					$item = pdo_fetch($sql,$params);
					return $item;
				 }		 


				 public function Get_my_weixin_tpl($shop_id)
				 {
				 	global $_W;
			        $params = array(
			        	'tpl_shop_id' => $shop_id,
			        	'tpl_status' => 1,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where tpl_status =:tpl_status and  tpl_shop_id =:tpl_shop_id and uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_tpl).$condition;
					$item = pdo_fetch($sql,$params);
					return $item;
				 }



				  public function Get_my_msn_tpl($shop_id)
				 {
				 	global $_W;
			        $params = array(
			        	'sms_area_id' => $shop_id,
						'uniacid' => $_W['uniacid'],
					);
					$condition =" where sms_area_id =:sms_area_id and uniacid =:uniacid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_sms_setting).$condition;
					$list = pdo_fetchall($sql,$params);
					return $list;
				 }


				 /**
				  * @Author      YIHENG.NET
				  * @DateTime    2018-10-23
				  * @description 返回管理员短信量，供注册等使用
				  * @return
				  * @version
				  * @param       [type]     $shop_id [description]
				  */
				  public function Get_manage_msn_total($shop_id)
				 {
				 	global $_W;
			        $params = array(
			        	'sms_shop_id' => $shop_id,
			        	'sms_status' => 1,
						'uniacid' => $_W['uniacid'],
					);

					$condition =" where sms_shop_id =:sms_shop_id and sms_status =:sms_status and uniacid =:uniacid ";
					$sql = "SELECT sum(sms_surplus) as surplus  from ".tablename($this->express_sms_info).$condition;
					$total = pdo_fetch($sql,$params);
					if ($total['surplus']>0)
						return $total['surplus'];
					return 0;
				 }



// =====================================================================================================
























			











			public function Get_member_info_by_id($uid)
			{
				$params = array(
					'm_uid' => $uid,
					);
					
					$condition =" where m_uid =:m_uid ";
					$select ="*";
					$sql = "SELECT $select from ".tablename($this->express_member).$condition;

					$mitem = pdo_fetch($sql,$params);
					return $mitem;

			}


		

			public function Get_member_list($level=0)
			{

					global $_W;

					$tea=tablename($this->express_area);
					$tem=tablename($this->express_member);
					$pindex = max(1, intval($_GPC['page']));
					$psize = 50;
					$leftjoin=" LEFT JOIN $tea on $tea.area_code = $tem.m_belong_shop";
					
					$orderby="  ";
					

					$params = array(
					'uniacid' => $_W['uniacid'],
					'm_level' => $level,
					);
					
					$condition =" where $tem.uniacid =:uniacid and $tem.m_level =:m_level";

					$select ="*";

					$sql = "SELECT $select from ".$tem.$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;


					$mlist = pdo_fetchall($sql,$params);

					

					$sql = "SELECT COUNT(*) FROM " .$tem.$condition;
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['mlist']=$mlist;
					$list['pager']=$pager;

					return $list;

			}


				public function Get_shop_list()
				{
					global $_W;	
					$pindex = max(1, intval($_GPC['page']));
					$psize = 50;
					$condition =" where uniacid =:uniacid";
					$params = array(
					'uniacid' => $_W['uniacid'],
					);

					$sql = "SELECT * from ".tablename($this->express_area).$condition ." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
					$lst = pdo_fetchall($sql,$params);
					// print_r($list);exit;
					$sql = "SELECT COUNT(*) FROM " . tablename($this->express_area);
					$total = pdo_fetchcolumn($sql,$params);
					$pager = pagination($total, $pindex, $psize);

					$list['lst']=$lst;
					$list['pager']=$pager;
					return $list;

				}
 

				public function area_info($area_id)
				{
					global $_W;	
					$params = array(
									'area_id' => $area_id,
									'uniacid' => $_W['uniacid']
									);
					$where=' WHERE area_id = :area_id and uniacid =:uniacid';	
					$orderby='';
					$limit =" limit 1";
					$item = pdo_fetch("SELECT * FROM ".tablename($this->express_area). $where.$orderby,$params);

					return $item;
				}

				public function Get_tpl_all()
				 {

				 	global $_W;
			        $account_api = WeAccount::create();
			        $token = $account_api->getAccessToken();
			        if (is_error($token)) {
			            message('获取access token 失败');
			        }
			        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$token}";
			        $response = ihttp_request($url, urldecode(json_encode($data)));
			        if (is_error($response)) {
			            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
			        }
			        $list = json_decode($response['content'], true);

			        return $list['template_list'];

				 }


				public function Get_tpl_info($template_id)
				 {
				 	 global $_W;
			        $account_api = WeAccount::create();
			        $token = $account_api->getAccessToken();
			        if (is_error($token)) {
			            message('获取access token 失败');
			        }
			        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$token}";
			        $response = ihttp_request($url, urldecode(json_encode($data)));
			        if (is_error($response)) {
			            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
			        }


			        $list = json_decode($response['content'], true);
			       	if (!empty($list))
			       	{
				       	foreach ($list['template_list'] as $key => $value) {
				       		 if ($value['template_id']==$template_id)
				       		 {
				       		 	$temp_key= $key;
				       		 	break;
				       		 }
				       	}
				       	 return ( $list['template_list'][$temp_key]);
			       	}
			       	return '';
				 }


				public function Get_tpl_id($tpl_short_id)
				 {
				 	 global $_W;
			        $account_api = WeAccount::create();
			        $token = $account_api->getAccessToken();
			        if (is_error($token)) {
			            message('获取access token 失败');
			        }
			       	 $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$token}";
			            $postdata = array('template_id_short' => $tpl_short_id);
			            $response = ihttp_request($url, urldecode(json_encode($postdata)));
			            $result = json_decode($response['content'], true);

			         

			        	return $result['template_id'];
					}


				/**
				 * @param $url
				 * @return mixed
				 */
				public  function curlGet($url)
				{
					// 1. 初始化
					$ch = curl_init();
					// 2. 设置选项，包括URL
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					// 3. 执行并获取HTML文档内容
					$output = curl_exec($ch);
					if ($output === FALSE) {
						echo "CURL Error:" . curl_error($ch);
					}
					// 4. 释放curl句柄
					curl_close($ch);
					return $output;
				}

				/**
				 * @param $url
				 * @param $postData
				 * @return mixed
				 */
				public  function curlPost($url, $postData = null)
				{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
					$ch_arr = array(CURLOPT_TIMEOUT => 3, CURLOPT_RETURNTRANSFER => 1);
					curl_setopt_array($ch, $ch_arr);
					$output = curl_exec($ch);
					curl_close($ch);
					return $output;
				}


				/**
				 * @param $URL
				 * @param $type
				 * @param $params
				 * @param null $headers
				 * @return mixed
				 */
				public  function curlRequest($URL,$type,$params=null,$headers=null){
					$ch = curl_init($URL);
					$timeout = 5;
					if(isset($headers)){
						curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
					}else {
						curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
					}
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					switch ($type){
						case "GET" : curl_setopt($ch, CURLOPT_HTTPGET, true);break;
						case "POST": curl_setopt($ch, CURLOPT_POST,true);
							curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
						case "PUT" : curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
							curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
						case "PATCH": curl_setopt($ch, CULROPT_CUSTOMREQUEST, 'PATCH');
							curl_setopt($ch, CURLOPT_POSTFIELDS, $params);break;
						case "DELETE":curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
							curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
					}
					$file_contents = curl_exec($ch);//获得返回值
					return $file_contents;
					curl_close($ch);
				}






	}

?>	