
<?php 
	global $_GPC, $_W;
	$title = '管理中心-历史搜索';
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
	$tem=tablename($express_member);
	$ter=tablename($express_recode);
	

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
			
			$keyword= trim($_GPC['search_text']);
			$searchtype=$_GPC['search_type'];
			$searchstatus=intval($_GPC['search_status'])==1?1:0;


			if ($act == 'search')
			{
				if ($searchtype==0 || $searchtype==1 )
				$search_list=$opt->Get_list_by_tel($openid,$keyword,$searchstatus,$page_index=1,$psize=50);
				if ($searchtype==2)
				$search_list=$opt->Get_list_by_rand_code($openid,$keyword,$searchstatus,$page_index=1,$psize=50);
				if ($searchtype==3)
				$search_list=$opt->Get_list_by_barcode($openid,$keyword,$searchstatus,$page_index=1,$psize=50);


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
				            <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_user_recive.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>收件时间</p>
				            </div>
				             <div class="weui-cell__ft">'.date("Y-m-d H:i",$value['recoder_updatetime']).'</div>
				          </div>

				          <div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_tel.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>联系电话</p>
				            </div>
				            <div class="weui-cell__ft">'.$value['recoder_tel'].'</div>
				          </div>'.$a;
				
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
				$list= $opt->Get_list($openid,$status=0,$retention=0,$page_index,$psize);

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
		$list= $opt->Get_list($openid,$status=0,$retention=0,$page_index=1,$psize);
		if (sizeof($list['lst'])<$psize ||empty($list))
		{
			$load_done=1;
		}

		
		$pager=$list['pager'];
		$stocklist=$list['lst'];
		include $this->template('express_query');  
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






























