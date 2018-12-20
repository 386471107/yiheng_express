<?php 
defined('IN_IA') or die('Access Denied');
	Class yh_opt extends WeModuleSite

	{


		protected $express_member="yiheng_express_member";
		protected $express_recode="yiheng_express_recode";
		protected $express_statistics="yiheng_express_statistics";
		protected $express_log_login='yiheng_express_log_login';
		protected $express_tpl='yiheng_express_tpl';
		protected $express_apply='yiheng_express_apply';
		protected $express_notice_log='yiheng_express_notice_log';
		protected $express_sms_setting='yiheng_express_sms_setting';
		protected $express_sms_log='yiheng_express_sms_log';
		protected $express_reg='yiheng_express_reg';
		protected $express_reg_log='yiheng_express_reg_log';
		protected $express_sms_info='yiheng_express_sms_info';
		
		protected $express_member_bind='yiheng_express_member_bind';
		protected $express_area='yiheng_express_area';
		
		


		//pdo_update('users_failed_login', array('count +=' => 1), array('username' => 'mizhou'));
		//
		//
		//
		/**
		 * @Author   YIHENG..NET
		 * @DateTime 2018-08-27
		 * @param    [type]      $m_openid   [openid]
		 * @param    integer     $status     [接收状态]
		 * @param    integer     $retention  [是否滞留]
		 * @param    integer     $page_index [当前页面]
		 * @param    integer     $psize      [页面条数]
		 */
		public function Get_list($openid,$status=0,$retention=0,$page_index=1,$psize=50)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_shop_id =:recoder_shop_id and recoder_retention =:recoder_retention";
				$params = array(
				'recoder_retention' => $retention,	
				'recoder_get_status' => $status,
				'recoder_shop_id' => $u_shop_id,
				'uniacid' => $_W['uniacid'],
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_add_openid like :recoder_add_openid and recoder_retention =:recoder_retention";
				$params = array(
				'recoder_retention' => $retention,	
				'recoder_get_status' => $status,
				'recoder_add_openid' => $openid,
				'uniacid' => $_W['uniacid'],
				);
			}
			$orderby =" order by id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$lst = pdo_fetchall($sql,$params);
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;

			return $list;

		}


		/**
		 * @Author   YIHENG.NET
		 * @DateTime 2018-08-28
		 * @param    [type]     $openid [description]
		 * @param    integer    $hours  [滞留时间]
		 * 一般加到定时任务中使用
		 */
		public function Update_to_retention($openid,$hours=48)
		{
			global $_W;

			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			
			$member = new yh_member();

			$time_node= time()-$hours*3600;
			
			$u_shop_id = $member->Get_shop_id($openid);

			if ($u_shop_id)
			{
			$sql="UPDATE ".tablename($this->express_recode)." SET recoder_retention = 1 WHERE recoder_get_status =0 and recoder_shop_id = $u_shop_id AND uniacid = ".$_W['uniacid']." AND recoder_create < $time_node";

			$res = pdo_query($sql);
			}
			else{$res =0;}
			return $res;
		}


		/**
		 * @Author      YIHENG.NET
		 * @DateTime    2018-09-14
		 * @description
		 * @param       [type]     $m_openid [用户openid]
		 * @return      [type]               [48小时后删除申请需求]
		 */
		public function del_apply_timeout($m_openid)
		{

			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			$u_shop_id = $member->Get_shop_id($m_openid);
			$timediff=time()-48*3600;
			$res = pdo_delete($this->express_apply,array('apply_createtime <' => $timediff,'apply_shop_id' =>$u_shop_id));
			return $res;

		}

		public function Get_apply_list($openid,$page_index=1,$psize=500)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			$tem=tablename($this->express_member);
			$tea=tablename($this->express_apply);

			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where $tea.uniacid =:uniacid  and $tea.apply_shop_id =:apply_shop_id";
				$params = array(
				'apply_shop_id' => $u_shop_id,
				'uniacid' => $_W['uniacid'],
				);
			}
			else
			{
				unset($list);
				return $list;
			}

					
			$leftjoin=" LEFT JOIN $tem on $tea.apply_openid = $tem.m_openid";

			$orderby =" order by $tea.apply_id ASC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_apply).$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql,$params);
			return $list;
		}



		/**
		 * @Author      YIHENG.NET
		 * @DateTime    2018-10-19
		 * @description
		 * @return [array] [店铺下属员工]
		 * @version
		 * @param       [type]     $openid     [description]
		 * @param       integer    $page_index [description]
		 * @param       integer    $psize      [description]
		 */
		public function Get_my_shop_staff_list($openid,$page_index=1,$psize=500)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			$Tem=tablename($this->express_member);
			$Temb=tablename($this->express_member_bind);

			$u_shop_id = $member->Get_shop_id($openid);
			if ((int)($u_shop_id)==0) die;
			if($member->Get_user_level($u_shop_id,$openid)==2 || $member->Get_user_level($u_shop_id,$openid)==1)
			{
				$leftjoin=" LEFT JOIN $Tem on $Temb.bind_m_openid like $Tem.m_openid ";
				$condition =" where $Temb.uniacid =:uniacid  and $Temb.bind_m_level =:bind_m_level and $Temb.bind_shop_id=:bind_shop_id";
				$params = array(
					'bind_m_level' => 3,
					'uniacid' => $_W['uniacid'],
					'bind_shop_id' => $u_shop_id,
				);
				
				$pindex = max(1, intval($page_index));
				$psize = $psize;
				$sql = "SELECT * from ".tablename($this->express_member_bind).$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql,$params);
				return $list;
			}
			
			unset($list);
			return $list;
			
		}





		public function Get_my_shop_staff_total($openid)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			$tem=tablename($this->express_member);
			$temb=tablename($this->express_member_bind);
			$u_shop_id = $member->Get_shop_id($openid);
			if ((int)($u_shop_id)==0) die;
			

			if($member->Get_user_level($u_shop_id,$openid)==2 || $member->Get_user_level($u_shop_id,$openid)==1)
			{
				$params = array(
					'bind_m_level' => 3,
					'bind_shop_id' => $u_shop_id,
					'uniacid' => $_W['uniacid'],
				);
				$condition=" where bind_m_level =:bind_m_level and bind_shop_id =:bind_shop_id and uniacid =:uniacid";

				$sql = "SELECT count(*) as cnt from ".tablename($this->express_member_bind).$condition ;
				$item = pdo_fetch($sql,$params);
				return ($item['cnt']!=0)?$item['cnt']:0;
			}
		
			return 0;
		}

//******************************************************************************************************
		
		public function Get_myshop_staff($openid,$page_index=1,$psize=500)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			

			$tem=tablename($this->express_member);
			$tea=tablename($this->express_apply);

			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid =:uniacid  and m_level =:m_level and m_defaut_area=:m_defaut_area";
				$params = array(
					'm_level' => 3,
					'uniacid' => $_W['uniacid'],
					'm_defaut_area' => $u_shop_id,
				);

				$pindex = max(1, intval($page_index));
				$psize = $psize;
				$sql = "SELECT * from ".tablename($this->express_member).$leftjoin.$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql,$params);
				return $list;


			}
			else
			{
				unset($list);
				return $list;
			}

					
			
		}
		
		


		/**
		 * @Author   YIHENG..NET
		 * @DateTime 2018-10-18
		 * @param    [type]      $openid [description]
		 * @retrun :管理员店铺数据
		 */
		public function Get_my_shop_total($openid)
		{
			global $_W;
			$params = array(
				'bind_m_openid' => $openid,
				'bind_m_level' => 2,
				'uniacid' => $_W['uniacid'],
			);
			$condition=" where bind_m_level =:bind_m_level and bind_m_openid like :bind_m_openid and uniacid =:uniacid";
			$sql = "SELECT count(*) as cnt  from ".tablename($this->express_member_bind).$condition;
			$item = pdo_fetch($sql,$params);
			
			return ($item['cnt']!=0)?$item['cnt']:0;
		}

		




 
		public function Get_list_by_tel($openid,$tel,$status=0,$page_index=1,$psize=50)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_shop_id =:recoder_shop_id and recoder_tel like :recoder_tel";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_shop_id' => $u_shop_id,
				'recoder_tel' => $tel,	
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_add_openid like :recoder_add_openid and recoder_tel = :recoder_tel";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_add_openid' => $openid,
				'recoder_tel' => $tel,
				);
			}

			$orderby =" order by id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			
			$lst = pdo_fetchall($sql,$params);
			
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;
			return $list;

		}


		public function Get_list_by_rand_code($openid,$randcode,$status=0,$page_index=1,$psize=50)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_shop_id =:recoder_shop_id and recoder_code like :recoder_code";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_shop_id' => $u_shop_id,
				'recoder_code' => $randcode,	
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_add_openid like :recoder_add_openid and recoder_code = :recoder_code";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_add_openid' => $openid,
				'recoder_code' => $randcode,
				);
			}

			$orderby =" order by id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			
			$lst = pdo_fetchall($sql,$params);
			
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;
			return $list;

		}


		public function Get_list_by_barcode($openid,$barcode,$status=0,$page_index=1,$psize=50)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_shop_id =:recoder_shop_id and recoder_barcode like :recoder_barcode";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_shop_id' => $u_shop_id,
				'recoder_barcode' => $barcode,	
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid and recoder_get_status =:recoder_get_status and recoder_add_openid like :recoder_add_openid and recoder_barcode like :recoder_barcode";
				$params = array(
				'uniacid' => $_W['uniacid'],
				'recoder_get_status' => $status,
				'recoder_add_openid' => $openid,
				'recoder_barcode' => $barcode,
				);
			}

			$orderby =" order by id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			
			$lst = pdo_fetchall($sql,$params);
			
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;
			return $list;

		}


		
		public function Get_send_list_all($openid,$u_shop_id,$send=1,$page_index=1,$psize=50)
		{
			
			
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			// $u_shop_id='10002';
			 // $openid='oe_aJ1SI-BeOQGiMwdCelRipl-ug'; 
			  
			if ($member->is_shop_manage($openid))
			{
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid = :uniacid  and send_shop_id = :send_shop_id ";
				$params = array(
				'send_shop_id' => $u_shop_id,
				'uniacid' => $_W['uniacid'],
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid  and send_shop_id =:send_shop_id and send_openid like :send_openid ";
				$params = array(
				'send_shop_id' => $u_shop_id,
				'send_openid' => $openid,
				'uniacid' => $_W['uniacid'],
				);
			}
			
			
			$orderby =" order by send_id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT send_to_tel,send_err,send_respose,send_time from ".tablename($this->express_sms_log).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$lst = pdo_fetchall($sql,$params);
			
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_sms_log).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);
		
			$list['lst']=$lst;
			$list['pager']=$pager;
	
			return $list;

		}
	

		public function Get_send_list($openid,$send=1,$page_index=1,$psize=50)
		{
			global $_W;
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();
			
			if ($member->is_shop_manage($openid))
			{
				$u_shop_id = $member->Get_shop_id($openid);
				if ((int)($u_shop_id)==0) die;
				$condition =" where uniacid = :uniacid and recoder_send_status = :recoder_send_status and recoder_shop_id = :recoder_shop_id ";
				$params = array(
				'recoder_send_status' => $send,
				'recoder_shop_id' => $u_shop_id,
				'uniacid' => $_W['uniacid'],
				);

			}
			else
			{
				$condition =" where uniacid =:uniacid and recoder_send_status =:recoder_send_status and recoder_add_openid like :recoder_add_openid ";
				$params = array(
				'recoder_send_status' => $send,
				'recoder_add_openid' => $openid,
				'uniacid' => $_W['uniacid'],
				);
			}
			$orderby =" order by id DESC";
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$condition .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$lst = pdo_fetchall($sql,$params);
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$condition  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;

			return $list;

		}



		public function Get_wait_num($m_openid)
		{
			global $_W;	
			if (empty($m_openid))  die();

			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':recoder_openid' => $m_openid,
					':recoder_get_status' => 0,
					
			); 
			$where  =" where recoder_get_status =:recoder_get_status and recoder_openid like :recoder_openid and uniacid=:uniacid";
			$item = pdo_fetch('select count(*) as cnt from ' . tablename($this->express_recode) . $where.$limit, $Pramas);
			return $item['cnt'];
		}


		public function Get_wait_list($m_openid)
		{
			global $_W;	
			if (empty($m_openid))  die();

			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':recoder_openid' => $m_openid,
					':recoder_get_status' => 0,
					
			); 
			$where  =" where recoder_get_status =:recoder_get_status and recoder_openid like :recoder_openid and uniacid=:uniacid";
			$orderby =' order by id asc';
			$list = pdo_fetchall('select * from ' . tablename($this->express_recode) . $where.$orderby.$limit, $Pramas);
			return $list;
		}


			public function Get_my_done_list($m_openid,$page_index=1,$psize=50)
		{
			global $_W;	
			if (empty($m_openid))  die();

			$params=array(
					':uniacid' => $_W['uniacid'], 
					':recoder_openid' => $m_openid,
					':recoder_get_status' => 1,
			); 
			$where  =" where uniacid = :uniacid and recoder_openid like :recoder_openid and recoder_get_status= :recoder_get_status";
			$orderby =' order by id DESC';
			$pindex = max(1, intval($page_index));
			$psize = $psize;
			$sql = "SELECT * from ".tablename($this->express_recode).$where .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$lst = pdo_fetchall($sql,$params);
			$sqlt = "SELECT COUNT(*) FROM ".tablename($this->express_recode).$where  ;
			$total = pdo_fetchcolumn($sqlt,$params);
			$pager = pagination($total, $pindex, $psize);

			$list['lst']=$lst;
			$list['pager']=$pager;
			return $list;
		}






		public function Del_record($del_id)
		{
			global $_W;	
			$res = pdo_delete($this->express_recode,array('id' => $del_id));
			return $res;
		}


		public function Done_record($done_id,$openid,$done_by="admin")
		{
			global $_W;	
			$udata = array(
				'recoder_updatetime' =>time(),
				'recoder_get_status' =>1,
				'recoder_done_by' =>$done_by,
				'recoder_done_openid' =>$openid,
				'recoder_updatetime_year' =>date("Y",time()),
				'recoder_updatetime_month' =>date("m",time()),
				'recoder_updatetime_day' =>date("d",time()),
				'recoder_updatetime_hour' =>date("H",time()),
				); 
			$res = pdo_update($this->express_recode,$udata,array('id' => $done_id));

			return $res;
		}


		public function Update_record_status($id)
		{
			global $_W;	
			
				$udata = array(
				'recoder_send_status' => 1,
				'recoder_senttime' => time(),
				); 
			$res = pdo_update($this->express_recode, $udata, array('id' => $id));
			return $res;
		}


		public function set_default_shop($id,$openid)
		{

			global $_W;	
			include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
			$member = new yh_member();

			$item=$member->Get_member($openid);
			$res = pdo_update($this->express_member,array('m_defaut_area' =>$id),array('id' => $item['id']));

			return $res;
		}

		public function Get_staff_total($shop_id)
		{

			global $_W;	
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':m_area_list' => '%"'.$shop_id.'"%',
					':m_level' => 3,


			); 
			$where  =" where m_area_list like :m_area_list and m_level =:m_level and uniacid=:uniacid";
			$res = pdo_fetch('select count(*) as total  from ' . tablename($this->express_member) . $where.$limit, $Pramas);

			return empty($res['total'])?0:$res['total'];
		}



		/**
		 * @Author      YIHENG.NET
		 * @DateTime    2018-09-05
		 * @description
		 * @param       [type]     $shop_id [shopid ]
		 * @param       integer    $s_type  [数据增加类型，1，进。0 出]
		 * @param       integer    $s_flag  [类型 0微信，1短信]
		 */
		public function Stats_data_change($shop_id,$s_type=1,$s_flag=0)
		{

			global $_W;	
			$year=date("Y",time());
			$month=date("m",time());
			$day=date("d",time());
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => $year,
					':s_date_m' => $month,
					':s_date_d' => $day,
			); 
			$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m and s_date_d = :s_date_d and uniacid=:uniacid";
			$item = pdo_fetch('select * from ' . tablename($this->express_statistics) . $where.$limit, $Pramas);
			if (empty($item))
			{
				if ($s_type==1)
				{
					$res=$this->Insert_stats_recode($shop_id,1,0,0,0,1,0,0,0);
				}
				else
				{
					$res=$this->Insert_stats_recode($shop_id,1,0,0,0,0,1,0,0);
				}
				
			}
			else
			{
				if ($s_type==1)
				{	
					if ($s_flag==0)
					{
						$res=$this->update_stats_recode($item['s_id'],1,$item['s_in']+1,$item['s_wx_in']+1,$item['s_sms_in']);
					}
					else
					{
						$res=$this->update_stats_recode($item['s_id'],1,$item['s_in']+1,$item['s_wx_in'],$item['s_sms_in']+1);
					}
					
				}
				else
				{
					$res=$this->update_stats_recode($item['s_id'],0,$item['s_out']+1,0,0);
				}
				
			}

				

			return $res;
		}
 
		public function Stats_out_add($shop_id)
		{
			global $_W;	
			
			$year=date("Y",time());
			$month=date("m",time());
			$day=date("d",time());
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => $year,
					':s_date_m' => $month,
					':s_date_d' => $day,
			); 
			$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m and s_date_d = :s_date_d and uniacid=:uniacid";
			$item = pdo_fetch('select * from ' . tablename($this->express_statistics) . $where.$limit, $Pramas);
			

			// print_r($item);exit();
			if (empty($item)) 
				$this->Insert_stats_recode($shop_id,0,1);
				//如果没数据，则插入，数据或许是之前的入库
			else
				$res=$this->Stats_data_change($shop_id,$s_type=0,$s_flag=0);
				// $res=$this->update_stats_recode($item['s_id'],$item['s_out']+1,0);
			return $res;
		}




		public function Update_stats_send_notice($shop_id,$notice_type)
		{
			global $_W;	
			
			$year=date("Y",time());
			$month=date("m",time());
			$day=date("d",time());
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => $year,
					':s_date_m' => $month,
					':s_date_d' => $day,
			); 
			$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m and s_date_d = :s_date_d and uniacid=:uniacid";
			$item = pdo_fetch('select * from ' . tablename($this->express_statistics) . $where.$limit, $Pramas);
			if (empty($item)) 
				if($notice_type==0)
				 $this->Insert_stats_recode($shop_id,$s_in=0,$s_out=0,$s_wx_notice=1,$s_message=0,$s_wx_in=0,$s_sms_in=0,$s_wx_out=1,$s_sms_out=0);
				else
				 $this->Insert_stats_recode($shop_id,$s_in=0,$s_out=0,$s_wx_notice=0,$s_message=1,$s_wx_in=0,$s_sms_in=0,$s_wx_out=0,$s_sms_out=1);	
			else
			if ($notice_type==0)
			$result = pdo_update($this->express_statistics, array('s_wx_notice' => $item['s_wx_notice']+1), array('s_id' => $item['s_id']));
			else
			$result = pdo_update($this->express_statistics, array('s_message' => $item['s_message']+1), array('s_id' => $item['s_id']));
			return $res;
		}




		protected function Insert_stats_recode_log($shop_id,$openid)
		{
			//
		}



		protected function Insert_stats_recode($shop_id,$s_in=1,$s_out=0,$s_wx_notice=0,$s_message=0,$s_wx_in=0,$s_sms_in=0,$s_wx_out=0,$s_sms_out=0)
		{
			global $_W;	
			
			 $data = array(		 
				'uniacid' => $_W['uniacid'],
				's_shop_id' => $shop_id,
				's_date' => time(),
				's_date_y' =>date("Y",time()),
				's_date_m' => date("m",time()),
				's_date_d' => date("d",time()),
				's_in' => $s_in,
				's_out' => $s_out,
				's_wx_notice' => $s_wx_notice,
				's_message' => $s_message,
				's_wx_in' => $s_wx_in,
				's_sms_in' => $s_sms_in,
				's_wx_out' => $s_wx_out,
				's_sms_out' => $s_sms_out,
				);
			 $result=pdo_insert($this->express_statistics, $data);

		}

		protected function update_stats_recode($stats_id,$type,$s_cnt,$s_wxcnt,$s_smscnt)
		{
			global $_W;	
			if( $type==1)
			{
				$result = pdo_update($this->express_statistics, array('s_in' => $s_cnt,'s_wx_in' => $s_wxcnt,'s_sms_in' => $s_smscnt), array('s_id' => $stats_id));
			}
			else
			{
				$result = pdo_update($this->express_statistics, array('s_out' => $s_cnt,'s_wx_out' => $s_wxcnt,'s_sms_out' => $s_smscnt), array('s_id' => $stats_id));
			}


		}

		

		 public function send_wx($send_to_openid,$tplid,$datainfo) 
		 {
			 

 			global $_W,$_GPC;
 			$tpl_size = $datainfo['size'];
 			switch ($tpl_size) {
 				case 5:
 					$data = array(
					'first' => array(
						'value' => $datainfo['first'],
						'color' => '#743A3A'
					),
					'keyword1' => array(
						'value' => $datainfo['keyword1'],
						'color' => '#ff510'
					),
					'keyword2' => array(
						'value' => $datainfo['keyword2'],
						'color' => '#ff510'
					),
					'keyword3' => array(
						'value' => $datainfo['keyword3'],
						'color' => '#ff510'
					),
					'keyword4' => array(
						'value' => $datainfo['keyword4'],
						'color' => '#ff510'
					),
					'keyword5' => array(
						'value' => $datainfo['keyword5'],
						'color' => '#ff510'
					),
					'remark' => array(
						'value' => $datainfo['remark'] ,
						'color' => '#272822'
					), 
				);
 					break;
 				case 4:
 					$data = array(
					'first' => array(
						'value' => $datainfo['first'],
						'color' => '#743A3A'
					),
					'keyword1' => array(
						'value' => $datainfo['keyword1'],
						'color' => '#ff510'
					),
					'keyword2' => array(
						'value' => $datainfo['keyword2'],
						'color' => '#ff510'
					),
					'keyword3' => array(
						'value' => $datainfo['keyword3'],
						'color' => '#ff510'
					),
					'keyword4' => array(
						'value' => $datainfo['keyword4'],
						'color' => '#ff510'
					),
					
					'remark' => array(
						'value' => $datainfo['remark'] ,
						'color' => '#272822'
					), 
				);
 					break;
 				case 3:
 					$data = array(
					'first' => array(
						'value' => $datainfo['first'],
						'color' => '#743A3A'
					),
					'keyword1' => array(
						'value' => $datainfo['keyword1'],
						'color' => '#ff510'
					),
					'keyword2' => array(
						'value' => $datainfo['keyword2'],
						'color' => '#ff510'
					),
					'keyword3' => array(
						'value' => $datainfo['keyword3'],
						'color' => '#ff510'
					),
					
					'remark' => array(
						'value' => $datainfo['remark'] ,
						'color' => '#272822'
					), 
				);
 				break;
				case 2:
				$data = array(
				'first' => array(
					'value' => $datainfo['first'],
					'color' => '#743A3A'
				),
				'keyword1' => array(
					'value' => $datainfo['keyword1'],
					'color' => '#ff510'
				),
				'keyword2' => array(
					'value' => $datainfo['keyword2'],
					'color' => '#ff510'
				),
				'remark' => array(
					'value' => $datainfo['remark'] ,
					'color' => '#272822'
				), 
				);
				break;
					case 1:
				$data = array(
				'first' => array(
					'value' => $datainfo['first'],
					'color' => '#743A3A'
				),
				'keyword1' => array(
					'value' => $datainfo['keyword1'],
					'color' => '#ff510'
				),
				'remark' => array(
					'value' => $datainfo['remark'] ,
					'color' => '#272822'
				), 
				);
				break;
 				
 				default:
 					# code...
 					break;
 			}

			
 

				$tplid=$datainfo['tpl_id'];
				$account_api = WeAccount::create();
				$url=$_W['siteroot'].'app/'.$this->createMobileUrl('my').'yiheng_express';
				$url=str_replace('./','',$url);
				$status = $account_api->sendTplNotice($send_to_openid,  $tplid, $data, $url);
				return $status;
// } 
			 }
		


		
		/**
		 * @Author   YIHENG.NET
		 * @DateTime 2018-11-13
		 * @param    [type]     $openid   [description]
		 * @param    [type]     $tel      [description]
		 * @param    [type]     $shop_id  [description]
		 * @param    [type]     $data     [description]
		 * @param    integer    $sms_type [description]
		 * @return   [type]               [发送状态信息]
		 */
		public function send_sms_by_Ali($openid,$tel,$shop_id,$data,$sms_type=3) 
		 {
		 	global $_W;
		 	include_once MODULE_ROOT.'/inc/func/ali_message_api.php';
		 	$Ali=new SignatureHelper();
			$where =" where  sms_type =:sms_type and sms_area_id=:sms_area_id and uniacid =:uniacid";
			$params= array(
				'sms_type' => $sms_type,
				'sms_area_id' => $shop_id,
				'uniacid' =>$_W['uniacid'],
			);	
			$sms_info = pdo_fetch("SELECT * FROM ".tablename($this->express_sms_setting). $where,$params);
			$accessKeyId=$sms_info['sms_accesskey'];
			$accessKeySecret=$sms_info['sms_accesssecret'];
			$PhoneNumbers=$data['recoder_tel'];
			$SignName=$sms_info['sms_signname'];

			$shop_params = array(
			'area_code' => $shop_id,
			'uniacid' => $_W['uniacid']
			);
			$where=' WHERE area_code = :area_code and uniacid =:uniacid';	

			$shop_item = pdo_fetch("SELECT * FROM ".tablename($this->express_area). $where,$shop_params);

			// echo $sms_info['sms_message'];
			// $a = preg_match_all("/{(.*?)}/",$sms_info['sms_message'],$matches);

			// print_r($matches[1]);

			$Template_date= Array (
			            "expressname" => $data['recoder_express_name'],
			            "barcode" => substr($data['recoder_barcode'],-4),
			            "shop_name" => $shop_item['area_name'],
			            "shop_addr" => $shop_item['area_location'],
			            "slv" => $data['recoder_shelves'].'-'.$data['recoder_goods_num'],
			            "phone" => $shop_item['area_tel'],
			);
			$Template_date["TemplateCode"] = $sms_info['sms_message_tpl_id'];
		 $res= $Ali->sendSms($security,$accessKeyId,$accessKeySecret,$PhoneNumbers,$SignName,$Template_date);
		
		 // load()->func('logging');
		 // logging_run(json_encode($res,JSON_UNESCAPED_UNICODE), 'SEND_TO '.$PhoneNumbers, $filename = 'SMSA_LOG');
	
	
	
		
		 $this->send_sms_log_ali($openid,$PhoneNumbers,$shop_id,3,$res,$data['id']) ;
		 return $res;
		 }

		 /**
		  * @Author   YIHENG.NET
		  * @DateTime 2018-11-13
		  * @param    [type]     $send_openid [description]
		  * @param    [type]     $send_to_tel [description]
		  * @param    [type]     $shop_id     [description]
		  * @param    integer    $send_type   [description]
		  * @param    string     $rsp         [description]
		  * @param    string     $error_desc  [description]
		  * @return   [type]                  [description]
		  */
		  public function send_sms_log_ali($send_openid,$send_to_tel,$shop_id,$send_type=0,$rsp='',$send_recode_id) 
		 {
		 	global $_W;
		 	$resp=json_encode($rsp,JSON_UNESCAPED_UNICODE);

		 	$logdata= array(
					'send_openid' => $send_openid,
					'send_to_tel' => $send_to_tel,
					'send_shop_id' => $shop_id,
					'send_time' => time(),
					'send_type' => $send_type,
					'send_ip' => $_W['clientip'],
					'send_respose' => $resp,
					'send_recode_id' => $send_recode_id,
					'send_status' =>($rsp->Message=='OK')?1:0,
					'send_err' =>($rsp->code=='OK')?'':$rsp->code,
					'send_bizid' => $rsp->BizId,
					'uniacid' => $_W['uniacid'],
				);	
				$resultlog = pdo_insert($this->express_sms_log, $logdata);
				return $resultlog;
		 }	

		/**
		 * @Author   YIHENG..NET
		 * @DateTime 2018-09-09
		 * @param    [type]      $openid   [description]
		 * @param    [type]      $tel      [description]
		 * @param    [type]      $shop_id  [description]
		 * @param    [type]      $code     [description]
		 * @param    [type]      $sms_type [发送类型，1注册，2重新注册，3通知消息]
		 * @return   [type]                [description]
		 */
		 public function send_sms($openid,$tel,$shop_id,$code,$sms_type=3) 
		 { 
		 	global $_W;
		 	$where =" where  sms_type =:sms_type and sms_area_id=:sms_area_id and uniacid =:uniacid";
	 				$params= array(
					'sms_type' => $sms_type,
					'sms_area_id' => $shop_id,
					'uniacid' =>$_W['uniacid'],
					);	
			$sms_info = pdo_fetch("SELECT * FROM ".tablename($this->express_sms_setting). $where,$params);
				if (!empty($sms_info))
				{
		 			$sms_key=$sms_info['sms_key']; 					
					$sms_do_id = $sms_info['sms_message_id'];
					$content=str_replace('#code#',$code,$sms_info['sms_message']);
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => "http://apis.haoservice.com/sms/sendv2?mobile=$tel&tpl_id=$sms_do_id&content=$content&key=$sms_key",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10, 
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					));
 
					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);

					$this->send_sms_log($openid,$tel,$shop_id,$sms_type,$status=1,$rsp=$response,$error_desc=$err);
					return true;

				}

				return false;

				
		
		 }


	  	public function Get_today_data($shop_id) 
		 {
		 	global $_W;
			$year=date("Y",time());
			$month=date("m",time());
			$day=date("d",time());
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => $year,
					':s_date_m' => $month,
					':s_date_d' => $day,
			); 
			$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m and s_date_d = :s_date_d and uniacid=:uniacid";
			$item = pdo_fetch('select * from ' . tablename($this->express_statistics) . $where.$limit, $Pramas);
			
			return $item;

		 }

		 public function Get_month_data($shop_id) 
		 {
		 	global $_W;
			$year=date("Y",time());
			$month=date("m",time());
			$day=date("d",time());
			$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => $year,
					':s_date_m' => $month,
					
			); 
			$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m  and uniacid=:uniacid";
			$list = pdo_fetchall('select * from ' . tablename($this->express_statistics) . $where.$limit, $Pramas);
			
			return $list;

		 }

 public function wx_notice($id) 
		 {
		 		

			 	global $_W,$_GPC;
				$params = array(
					'id' => $id,
					'recoder_tel_exsit' => 1,
					'uniacid' => $_W['uniacid'],
				);  
				$where=' WHERE  recoder_tel_exsit =:recoder_tel_exsit and id =:id and uniacid = :uniacid';		
				$item = pdo_fetch("SELECT *  FROM ".tablename($this->express_recode). $where,$params);


				if(!empty($item))
				{
					$timediff=time()-$item['recoder_senttime'];
					if($timediff<86400) 
					{
						return 2;
					}

					$tplwhere=" where uniacid =:uniacid and tpl_status =:tpl_status";	
					$tplparams = array(
					'uniacid' => $_W['uniacid'],
					'tpl_status' =>1,
					);  
					$tplinfo = pdo_fetch("SELECT *  FROM ".tablename($this->express_tpl). $tplwhere,$tplparams);

					if (!empty($tplinfo))
					{
						$datainfo= array(
						'first' => $tplinfo['tpl_title']."\n",
						'keyword1' => $tplinfo['tpl_kw1'],
						'keyword2' => $item['recoder_code'],
						'remark' => "\n".$tplinfo['tpl_remark'],
						); 
						$wx_openid=$item['recoder_openid'];
						$this->send_wx($wx_openid,$tplid,$datainfo);
						$this->Stats_send_notice($item['recoder_shop_id'],0);

						$res['id']=$item['id'];	 
						$udata = array(
							'recoder_wxsentcount' => $item['recoder_wxsentcount']+1,
							'recoder_senttime' => time(),
						); 
						$result = pdo_update($this->express_recode, $udata, array('id' => $item['id']));
						if ($result)
							return 1;
							return 0;  
					}
					else
					{
						return 0;
					} 
				} 
				else
				{
					return 0;
				}
			}



			public function Get_weixin_wait_item($openid)
			{

				global $_W;
				include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
				$member = new yh_member();
				if($member->is_shop_manage($openid))
				{
					$params = array(
					'recoder_get_status' => 0,	
					'recoder_tel_exsit' => 1,
					'recoder_send_status' => 0,
					'uniacid' => $_W['uniacid'],
					'recoder_shop_id' => $member->Get_shop_id($openid),
					);  
					$where=' WHERE   recoder_get_status =:recoder_get_status and  recoder_tel_exsit =:recoder_tel_exsit and recoder_shop_id =:recoder_shop_id and recoder_send_status =:recoder_send_status and  uniacid = :uniacid';
				}
				else
				{
					$params = array(
					'recoder_get_status' => 0,
					'recoder_tel_exsit' => 1,
					'recoder_send_status' => 0,
					'uniacid' => $_W['uniacid'],
					'recoder_add_openid' => $openid,
					);  
					$where=' WHERE  recoder_get_status =:recoder_get_status and recoder_tel_exsit =:recoder_tel_exsit and recoder_add_openid =:recoder_add_openid and recoder_send_status =:recoder_send_status and uniacid = :uniacid';
				}


				$item = pdo_fetch("SELECT *  FROM ".tablename($this->express_recode). $where,$params);
				return $item;
			}


			public function Get_sms_wait_item($openid)
			{
				global $_W;
				include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
				$member = new yh_member();
				
				if($member->is_shop_manage($openid))
				{
					
					$params = array(
					'recoder_get_status' => 0,	
					'recoder_tel_exsit' => 0,
					'recoder_send_status' => 0,
					'uniacid' => $_W['uniacid'],
					'recoder_shop_id' => $member->Get_shop_id($openid),
					);  
					$where=' WHERE recoder_get_status =:recoder_get_status and recoder_tel_exsit =:recoder_tel_exsit and recoder_shop_id =:recoder_shop_id and recoder_send_status =:recoder_send_status and  uniacid = :uniacid';
				}
				else
				{
					
					$params = array(
					'recoder_tel_exsit' => 0,
					'recoder_get_status' => 0,
					'recoder_send_status' => 0,
					'recoder_add_openid' => $openid,
					'uniacid' => $_W['uniacid'],
					);  
					$where=' WHERE recoder_tel_exsit =:recoder_tel_exsit and recoder_get_status =:recoder_get_status and recoder_send_status =:recoder_send_status and  recoder_add_openid like :recoder_add_openid  and uniacid = :uniacid';
				}

				$item = pdo_fetch("SELECT *  FROM ".tablename($this->express_recode). $where,$params);
				
				return $item;
			}

 
 
			public function Get_tplinfo($shop_id)
			{
				global $_W;
				$tplwhere=" where uniacid =:uniacid and tpl_status =:tpl_status and tpl_shop_id =:tpl_shop_id";	
				$tplparams = array(
				'uniacid' => $_W['uniacid'],
				'tpl_status' =>1,
				'tpl_shop_id' =>$u_shop_id,
				);  
				$tplinfo = pdo_fetch("SELECT *  FROM ".tablename($express_tpl). $tplwhere,$tplparams);
				return $tplinfo;
			}

			/**
			 * @Author      YIHENG.NET
			 * @DateTime    2018-08-31
			 * @description 检查店铺短信是否开启
			 * @param       [type]     $shop_id [description]
			 */
			public function Check_Sms($openid,$shop_id,$sms_type=0)
			{
				global $_W; 

				if (!$sms_type)
					return $item;

				$where=' WHERE sms_type =:sms_type and  sms_shop_id =:sms_shop_id  and  sms_type =:sms_type and sms_openid_cando like :sms_openid_cando and uniacid = :uniacid';
				$params = array(
				'sms_shop_id' => $shop_id,
				'sms_type' => $sms_type,
				'sms_openid_cando' => $openid,
				'uniacid' => $_W['uniacid'],
				); 

				$item = pdo_fetch("SELECT *  FROM ".tablename($this->express_sms_info). $where,$params);
				return $item;
			}



			public function Check_sms_cfg($shop_id)
			{
				global $_W; 

				$where=' WHERE   sms_area_id =:sms_area_id and uniacid = :uniacid';
				$params = array(
				'sms_area_id' => $shop_id,
				'uniacid' => $_W['uniacid'],
				); 
				$item = pdo_fetch("SELECT *  FROM ".tablename($this->express_sms_setting). $where,$params);

				return $item;
			}

			
			public function Get_wait_sms_byid($id)
			{
				global $_W;
				$params = array(
					'id' => $id,
					);  
					$where=' WHERE  id =:id';
					$orderby =" ";		
					$wait_item_byid = pdo_fetch("SELECT *  FROM ".tablename($this->express_recode). $where.$orderby,$params);
					return $wait_item_byid;
			}


			public function Get_today_total_send($shop_id,$send_type)
			{
				global $_W;
				//判断是否有数据，如果没有，则要新新条数
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':s_shop_id' => $shop_id,
					':s_date_y' => date("Y",time()),
					':s_date_m' => date("m",time()),
					':s_date_d' => date("d",time()),
				); 
				
				$where  =" where s_shop_id =:s_shop_id and s_date_y = :s_date_y  and s_date_m  = :s_date_m and s_date_d = :s_date_d and uniacid=:uniacid";
				$item = pdo_fetch('select *  from ' . tablename($this->express_statistics) . $where, $Pramas);
				
				if (empty($item))
				return 0;	
				if ($send_type==1)
				return $item['s_message'];	
				return $item['s_wx_notice'];
			}


			public function Get_today_weixin_surplus($shop_id)
			{
				global $_W;
				//判断是否有数据，如果没有，则要新新条数
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':area_code' => $shop_id,
				); 
				$where  =" where area_code =:area_code and uniacid=:uniacid";
				$item = pdo_fetch('select *  from ' . tablename($this->express_area) . $where, $Pramas);
				if (empty($item))
				return 0;	
				return ($item['area_wx_notice_total']-$item['area_wx_notice_used']);	
			}



			public function Uptdate_today_weixin_used($shop_id)
			{
				global $_W;
				//判断是否有数据，如果没有，则要新新条数
				$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':area_code' => $shop_id,
				); 
				$where  =" where area_code =:area_code and uniacid=:uniacid";
				$item = pdo_fetch('select *  from ' . tablename($this->express_area) . $where, $Pramas);

				
				$udata=array(
					'area_wx_notice_used' => $item['area_wx_notice_used']+1,
				); 	
				
				$res = pdo_update($this->express_area, $udata, array('area_id' => $item['area_id']));

				return $res;
			}

			//用于注册，下回修改从店铺中扣除注册短信2018.12.07
			public function Get_sms_surplus($shop_id,$openid="")
			{
				global $_W;
				//判断是否有数据，如果没有，则要新新条数
				if (!empty($openid))
				{
					$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':sms_shop_id' => $shop_id,
						':sms_openid_cando' =>$openid,
					); 

					$where  =" where sms_shop_id =:sms_shop_id and sms_openid_cando = :sms_openid_cando   and uniacid=:uniacid";
					$item = pdo_fetch('select *  from ' . tablename($this->express_sms_info) . $where, $Pramas);
					return $item;
				}
				else
				{
					$Pramas=array(
						':uniacid' => $_W['uniacid'], 
						':sms_shop_id' => $shop_id,
						'sms_level' => 2,
						'sms_surplus' => 0,
					); 
					$where  =" where sms_level =:sms_level and sms_shop_id =:sms_shop_id and sms_surplus >:sms_surplus and uniacid=:uniacid";
					$item = pdo_fetch('select *  from ' . tablename($this->express_sms_info) . $where, $Pramas);
					return $item;

				}	
			}

			public function Uptdate_sms_surplus($surplus_id,$user_total,$surplus_total)
			{
				global $_W;
				//判断是否有数据，如果没有，则要新新条数
				$data=array(
					'sms_used' => $user_total,
					'sms_surplus' =>$surplus_total,
				); 
				$res = pdo_update($this->express_sms_info, $data, array('sms_id' => $surplus_id));

				return $res;	
			}



		
		 public function send_wx_log($send_openid,$send_to_openid='',$shop_id,$send_type=0,$status=1,$error_desc='') 
		 {
		 	global $_W;

		 	$logdata= array(
					'send_openid' => $send_openid,
				    'send_status' =>json_encode($status),
					'send_to_openid' => $send_to_openid,
					'send_time' => time(),
					'send_ip' => $_W['clientip'],
					'send_type' => $send_type,
					'send_shop_id' => $shop_id,
					'uniacid' => $_W['uniacid'],
				);			
			$resultlog = pdo_insert($this->express_notice_log, $logdata);
			return $resultlog;
		 }	




		 public function send_sms_log($send_openid,$send_to_tel,$shop_id,$send_type=0,$rsp='',$error_desc='') 
		 {
		 	global $_W;
		 	$logdata= array(
					'send_openid' => $send_openid,
					'send_to_tel' => $send_to_openid,
					'send_shop_id' => $shop_id,
					'send_time' => time(),
					'send_type' => $send_type,
					'send_ip' => $_W['clientip'],
					'send_respose' => $rsp,
					'send_err' => $error_desc,
					'uniacid' => $_W['uniacid'],
				);			
			$resultlog = pdo_insert($this->express_sms_log, $logdata);
			return $resultlog;
		 }	


		  public function Get_reg_info($openid) 
		 {
		 	global $_W;
		 	$Pramas=array(
					':uniacid' => $_W['uniacid'], 
					':reg_openid' => $openid,
				); 
				
				$where  =" where reg_openid like :reg_openid and uniacid=:uniacid";
				$item = pdo_fetch('select *  from ' . tablename($this->express_reg) . $where, $Pramas);

				return $item;

		 }	


		 public function Insert_reg_info($openid,$rand_code,$reg_tel) 
		 {
		 	global $_W;
		 	$data = array(
			'uniacid' => $_W['uniacid'],
			'reg_tel'=> $reg_tel,
			'reg_openid'=> $openid,
			'reg_cnt'=> 1,
			'reg_lasttime'=> time(),
			'reg_code'=> $rand_code,
			'reg_ip'=>  $_W['clientip'],
			);	
			$result=pdo_insert($this->express_reg, $data);
			return $result;
		 }	

		  public function Insert_reg_info_log($openid,$rand_code,$reg_tel,$reg_cnt) 
		 {
		 	global $_W;
		 	$data = array(
			'uniacid' => $_W['uniacid'],
			'reg_tel'=> $reg_tel,
			'reg_openid'=> $openid,
			'reg_cnt'=> $reg_cnt,
			'reg_lasttime'=> time(),
			'reg_code'=> $rand_code,
			'reg_ip'=>  $_W['clientip'],
			);	
			$result=pdo_insert($this->express_reg_log, $data);
			return $result;
		 }	

		   public function Get_shop_msm_list($shop_id,$sms_type,$sms_openid,$page_index=1,$psize=100) 
		 	{
		 	global $_W;

		 	$tem=tablename($this->express_member);
			$tesi=tablename($this->express_sms_info);

		 	switch ($sms_type) {
		 		case 1:
		 			$params = array(
					'uniacid' => $_W['uniacid'],					
					);	
		 			$where  =" where $tesi.uniacid =:uniacid";
		 			break;
		 		case 2:
		 			$params = array(
					'uniacid' => $_W['uniacid'],
					'sms_shop_id' => $shop_id,
					);	
		 			$where  =" where $tesi.sms_shop_id =:sms_shop_id and $tesi.uniacid=:uniacid";
		 			break;
		 		case 3:
		 			$params = array(
					'uniacid' => $_W['uniacid'],
					'sms_shop_id' => $shop_id,
					'sms_openid_cando' => $sms_openid,
					);	
		 			$where  =" where $tesi.sms_openid_cando like :sms_openid_cando and $tesi.sms_shop_id =:sms_shop_id and $tesi.uniacid=:uniacid";
		 			break;	
		 		
		 		default:
		 			# code...
		 			break;
		 	}

		 	

		 	$leftjoin=" LEFT JOIN $tem on $tesi.sms_openid_cando like $tem.m_openid";
			$orderby =" order by $tesi.sms_id ASC";
			$pindex = max(1, intval($page_index));
			
			$sql = "SELECT * from $tesi ".$leftjoin.$where .$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql,$params);


			foreach ($list as $key => $value) {
				if ($value['sms_type']==2)
				{
					$sms_list['manage']['total'] = $value['sms_surplus'];
				}
				if ($value['sms_type']==3)
				{
					$i++;
					$sms_list['staff'][$i]['m_nickname']= $value['m_nickname'];
					$sms_list['staff'][$i]['sms_surplus']= $value['sms_surplus'];
					$sms_list['staff'][$i]['sms_id']= $value['sms_id'];
				}
			}
			return $sms_list;
		 }	
			
			
			public function Get_shop_open_status($shop_id) 
		 	{
				$shop_id=intval($shop_id);
				if ($shop_id)
				{
					$item = pdo_fetch('select area_time_status,area_start_time,area_end_time  from ' . tablename($this->express_area) . " where area_code = $shop_id");
					if(!empty($item))
					{
						
						if ($item['area_time_status']==0)
						return true;
						$time_start=$item['area_time_status'];
						$time_end=$item['area_end_time'];
						$etime_s =explode(":",$time_start);
						$etime_e =explode(":",$time_end);
						@$start_time = mktime($etime_s[0],$etime_s[1]);
						@$end_time = mktime($etime_e[0],$etime_e[1]);
						if ($start_time < time() && $end_time > time())
						{
							return true;
						}
					}
				}
				return false;
				
			}

		 public function Get_shop_msm($shop_id,$m_openid,$sms_type) 
		 	{

		 		global $_W;
				
		 		switch ($sms_type) {
		 		case 1:
		 			$params = array(
					'uniacid' => $_W['uniacid'],
					'sms_shop_id' => $shop_id,
					);	
		 			$where  =" where sms_shop_id =:sms_shop_id and uniacid=:uniacid";
		 			break;
					
				case 2:
		 			$params = array(
					'uniacid' => $_W['uniacid'],
					'sms_shop_id' => $shop_id,
					'sms_openid_cando' => $m_openid,
					'sms_type' => $sms_type,
					);	
		 			$where  =" where sms_openid_cando like :sms_openid_cando and sms_shop_id =:sms_shop_id and sms_type =:sms_type and uniacid=:uniacid";
		 			break;	
		 		case 3:
				case 4:
		 			$params = array(
					'uniacid' => $_W['uniacid'],
					'sms_shop_id' => $shop_id,
					'sms_openid_cando' => $m_openid,
					'sms_type' => $sms_type,
					);	
		 			$where  =" where sms_openid_cando like :sms_openid_cando and sms_shop_id =:sms_shop_id and sms_type =:sms_type and uniacid=:uniacid";
		 			break;	
		 		
		 		default: 
		 			return $item;  
		 			break;
		 	}
				
				$item = pdo_fetch('select *  from ' . tablename($this->express_sms_info) . $where, $params);
				
				return $item;


		 	}



		 	public function Word_replace($str,$r_str)
		 	{
			 	// $replce_str = array('#area_name#','#rnd_code#','#in_time#','#barcode#');
			 	$replce_str = array('#area_name#','#rnd_code#','#in_time#','#barcode#','#slv#');
				foreach ($replce_str as $key => $value) {
			 		$str = str_replace($value,$r_str[$key],trim($str));
			 	}
			 	return $str;
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

				
				
				
			public  function getDevice($agent){
				if(true == preg_match("/.+Windows.+/", $agent)){
					return "web";
				}elseif(true == preg_match("/.+Macintosh.+/", $agent)){
					return "mac";
				}elseif(true == preg_match("/.+iPad.+/", $agent)){
					return "iPad";
				}elseif(true == preg_match("/.+iPhone.+/", $agent)){
					return "iPhone";
				}elseif(true == preg_match("/.+Android.+/", $agent)){
					return "Android";
				}else{
					return "未知设备";
				}
			} 

			public  function getbrand($agent){
				if(preg_match('/iPhone\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = 'IPhone';
			    }elseif(preg_match('/SAMSUNG\s([^\s|;]+)/i', $agent)) {
			         $mobile_brand = '三星';
			    }elseif(preg_match('/Huawei\s([^\s|;]+)/i', $agent)) {
			         $mobile_brand = '华为';
			      }elseif(preg_match('\/HUAWEI/i', $agent)) {
			         $mobile_brand = '华为';
			           
			    }elseif(preg_match('/Mi note\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '小米';
			        
			    }elseif(preg_match('/HM NOTE\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '红米';
			        
			    }elseif(preg_match('/Coolpad\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '酷派';
			        
			    }elseif(preg_match('/ZTE\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '中兴';
			        
			    }elseif(preg_match('/OPPO\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = 'OPPO';
			        
			    }elseif(preg_match('/HTC\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = 'HTC';
			        
			    }elseif(preg_match('/Nubia\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '努比亚';
			        
			    }elseif(preg_match('/M045\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '魅族';
			        
			    }elseif(preg_match('/Gionee\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '金立';
			        
			    }elseif(preg_match('/HS-U\s([^\s|;]+)/i', $agent)) {        
			        $mobile_brand = '海信';
			        
			    }elseif(preg_match('/Lenove\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '联想';
			        
			    }elseif(preg_match('/ONEPLUS\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '一加';
			        
			    }elseif(preg_match('/vivo\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = 'vivo';
			        
			    }elseif(preg_match('/K-Touch\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '天语';
			        
			    }elseif(preg_match('/DOOV\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '朵唯';
			        
			    }elseif(preg_match('/GFIVE\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '基伍';
			        
			    }elseif(preg_match('/GFIVE\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '基伍';
			        
			    }elseif(preg_match('/K3DX\s([^\s|;]+)/i', $agent)) {
			        $mobile_brand = '中兴';
			        
			    }else{
			        $mobile_brand = '其他';
			      }
			      return  $mobile_brand;
			}
			
			
			
			/**
			 * 求两个已知经纬度之间的距离,单位为米
			 * 
			 * @param lng1 $ ,lng2 经度
			 * @param lat1 $ ,lat2 纬度
			 * @return float 距离，单位米
			 */
			function getdistance($lng1, $lat1, $lng2, $lat2) {
				// 将角度转为狐度
				$radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
				$radLat2 = deg2rad($lat2);
				$radLng1 = deg2rad($lng1);
				$radLng2 = deg2rad($lng2);
				$a = $radLat1 - $radLat2;
				$b = $radLng1 - $radLng2;
				$distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
				return $distance;
			} 
					

		 	



		 
	
	}



?>