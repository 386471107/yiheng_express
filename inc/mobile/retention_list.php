
<?php 
	global $_GPC, $_W;
	$title = '管理中心-滞留列表';
	$express_member='yiheng_express_member';
	$express_reg='yiheng_express_reg';
	$express_log_login='yiheng_express_log_login';
	$express_recode='yiheng_express_recode';
	include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
	include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';
	$member = new yh_member();
	$opt  = new yh_opt();
	$mc_info=mc_oauth_userinfo();
	$url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'none_per'));
	$openid=$_W['openid'];
	$member->judge_db_member($openid);
	$member->Update_member_status($openid,$follow=$mc_info['subscribe'],$page="home");
	if (empty($openid)) die();
	
	$myinfo = $member->Get_member($openid);
	$has_shop= $myinfo['m_defaut_area'];

	if ($has_shop==0)
	{
		include $this->template('my'); 
		exit();
	}

	
	$Permission= $member  -> Get_Permission($openid);
	if (!$Permission)
	{
		header("Location: $url");exit();
	}

	
	if ($_W['isajax'])
		{

			$act=trim($_GPC['act']);


			if ($act == 'wxsent')
			{
				$id=intval($_GPC['id']);
				$sended = $opt ->wx_notice($id) ;
				result_back($sended);

			}

				if ($act == 'msnsent')
			{
				$id=intval($_GPC['id']);
				$shop_id=$member->Get_shop_id($openid);
				$item = $opt->Check_Sms($shop_id,3);
				if (empty($item))
				{
					$params['tips']='短信功能未开启';
					$params['sign']='forbidden';
					result_back(4,$params);
				}

				//配置检查
				if ($item['sms_key']=='' || $item['sms_message_id']==0 || $item['sms_message']=='')
				{
					$params['tips']='短信未正确配置';
					$params['sign']='forbidden';
					result_back(5,$params);
				}
				//
				//检查当日是否超次
				$sms_cur_total =  $opt->Get_today_total_send($shop_id,1);

				if ($sms_cur_total>$item['sms_day_max'])
				{
					$params['tips']='当日超次';
					$params['sign']='forbidden';
					result_back(6,$params);
				}

				$wait_item_byid=$opt->Get_wait_sms_byid($id);
				if(!empty($wait_item_byid))
				{
					$params['id']=$wait_item_byid['id'];
					$opt ->Update_record_status($wait_item_byid['id']);
					$opt->send_sms($openid,$wait_item_byid['recoder_tel'],$shop_id,3,$wait_item_byid['recoder_code']); 
					result_back(1,$params);
					
				}
				else
				{
					result_back(0);
				}

			}

			
			
			if ($act == 'del')
			{
				//权限判断，如果是加入者或店铺负责人，那就可以删除
				$id= $_GPC['id'];
				if ($member->is_shop_manage($openid))
					{
						$u_shop_id = $member->Get_shop_id($openid);
						if ((int)($u_shop_id)==0) die;
						$condition =" where id =:id and recoder_shop_id =:recoder_shop_id";
						$params = array(
						'id' => $id,
						'recoder_shop_id' => $u_shop_id,
						);

					}
					else
					{
						$condition =" where recoder_add_openid like:recoder_add_openid and id =:id ";
						$params = array(
						'id' => $id,
						'recoder_add_openid' => $$openid,
						);

					}

					$leftjoin = " ";
					
					$sql = "SELECT count(*) as cnt from $ter".$leftjoin.$condition  ;
					$item = pdo_fetch($sql,$params);
					if ($item['cnt']>0)
					{
						$del_res=$opt->Del_record($id);	
						if ($del_res)
						{
							$res['rid']=$id;
							result_back(1,$res);	
						}
						result_back(0);
					}


			}


			if ($act == 'done')
			{
				$id= $_GPC['id'];
				$done_res=$opt->Done_record($id,$openid);	

						if ($done_res)
						{	
							$opt ->Stats_out_add($member->Get_shop_id($openid));
							$res['rid']=$id;
							result_back(1,$res);	
						}

			}


			if ($act == 'search')
			{
				$tel= trim($_GPC['tel']);

				$search_list=$opt->Get_list_by_tel($openid,$tel,$status=0,$page_index=1,$psize=50);

				if (!empty($search_list))
				{
					foreach ($search_list['lst'] as $key => $value) {
					# code...
					if (!empty($value['recoder_barcode']))
					{ 
					$a='<div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_express.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>快递单号</p>
				            </div>
				            <div class="weui-cell__ft">'.$value['recoder_barcode'].'</div>
				          </div>';	
				     }
				     else
				     {$a='';}
					 $str .=' <div class="weui-cells weui-cells_checkbox" id="code_'.$value['id'].'">
				          <label class="weui-cell weui-check__label" onclick="del_recode('.$value['id'].')">
				            <div class="weui-cell__bd" >
				              <p>取件码:'.$value['recoder_code'].'</p>
				            </div>
				        <div class="weui-cell__ft">'.$value['recoder_shelves'].'-'.$value['recoder_goods_num'].'</div>
				          </label>
				          <div class="weui-cell">
				            <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_time.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>发送时间</p>
				            </div>
				             <div class="weui-cell__ft">'.date("Y-m-d H:i",$value['recoder_create']).'</div>
				          </div>

				          <div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_tel.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>联系电话</p>
				            </div>
				            <div class="weui-cell__ft">'.$value['recoder_tel'].'</div>
				          </div>'.$a.'
				        <div class="weui-cell weui-flex">
				          <div class="weui-flex__item" ></div>
				          <div class="weui-flex__item"></div>
				         <div class="weui-flex__item "><a class="button button-small button-royal  " onclick="done('.$value['id'].')"><i class="fa fa-sign-in " aria-hidden="true"></i>&nbsp;收货</a></div>
				        </div>
				      </div>';
				
				}

					$params = array(
					'res_str' => $str,
					'cnt' => sizeof($search_list['lst']),
					);
					result_back(1,$params);

				}
				else
				{
					result_back(0,$res);	
				}

				

				
			}


			if ($act=="load_more")
			{	
				$page_index=$_GPC['page'];
				$psize = 50;
				$list= $opt->Get_list($openid,$status=0,$retention=1,$page_index,$psize);

				foreach ($list['lst'] as $key => $value) {
					# code...
					if (!empty($value['recoder_barcode']))
					{ 
					$a='<div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_express.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>快递单号</p>
				            </div>
				            <div class="weui-cell__ft">'.$value['recoder_barcode'].'</div>
				          </div>';	
				     }
				     else
				     {$a='';}
					 $str .=' <div class="weui-cells weui-cells_checkbox" id="code_'.$value['id'].'">
				          <label class="weui-cell weui-check__label" onclick="del_recode('.$value['id'].')">
				            <div class="weui-cell__bd" >
				              <p>取件码:'.$value['recoder_code'].'</p>
				            </div>
				        <div class="weui-cell__ft">'.$value['recoder_shelves'].'-'.$value['recoder_goods_num'].'</div>
				          </label>
				          <div class="weui-cell">
				            <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_time.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>发送时间</p>
				            </div>
				             <div class="weui-cell__ft">'.date("Y-m-d H:i",$value['recoder_create']).'</div>
				          </div>

				          <div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_tel.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>联系电话</p>
				            </div>
				            <div class="weui-cell__ft">'.$value['recoder_tel'].'</div>
				          </div>'.$a.'
				        <div class="weui-cell weui-flex">
				          <div class="weui-flex__item" ></div>
				          <div class="weui-flex__item"></div>
				         <div class="weui-flex__item "><a class="button button-small button-highlight  " onclick="done('.$value['id'].')"><i class="fa fa-sign-in " aria-hidden="true"></i>&nbsp;收货</a></div>
				        </div>
				      </div>';
				
				}
					$done=(sizeof($list['lst'])<$psize ||empty($list))?1:0;
					$params = array(
					'res_str' => $str,
					'done' =>$done,
					);
					result_back($flag,$params);

				
					exit($re);
			}

		}
	

		//自己只能看自己所录之单，如果店铺管理员可以查看所有

		$psize=50;
		$list= $opt->Get_list($openid,$status=0,$retention=1,$page_index=1,$psize);
		if (sizeof($list['lst'])<$psize ||empty($list))
		{
			$load_done=1;
		}

		
		$pager=$list['pager'];
		$retention=$list['lst'];
		include $this->template('retention_list');  
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






























