<?php 

	global $_GPC, $_W;
	
	$title = '我的收件记录';
  


include_once MODULE_ROOT.'/inc/func/yh_member.func.php';
include_once MODULE_ROOT.'/inc/func/yh_operation.func.php';

$member = new yh_member();
$opt  = new yh_opt();

$url= $_W['siteroot'].$this->createMobileUrl('error',array('dis' =>'none_per'));

$openid=$_W['openid']; 

	if (empty($openid))
	{
		$userinfo= json_decode($member->Get_Userinfo());
		$openid = $userinfo->openid;
		//判断用户是否存在于
		$exsit = $member->Check_Member($userinfo->openid);
		if (!$exsit) $member->Insert_memberinfo($userinfo->openid,$userinfo->nickname,$userinfo->headimgurl);
		
	} 
	else
	{
		$exsit = $member->Check_Member($openid);
		if (!$exsit)
		{
			mc_oauth_userinfo();
			$member->Insert_memberinfo($openid,$_W['fans']['nickname'],$_W['fans']['avatar']);
		}
	}

if (empty($openid)) die();

$act=$_GPC['act'];
if ($act=="load_more")
			{	

		

    		
				$page_index=$_GPC['page'];
				$psize = 4;
				
				$list= $opt->Get_my_done_list($openid,$page_index,$psize);
				
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
				              <p>通知时间</p>
				            </div>
				             <div class="weui-cell__ft">'.date("Y-m-d H:i",$value['recoder_create']).'</div>
				          </div>

				          <div class="weui-cell">
				              <div class="weui-cell__hd"><img src="'.MODULE_URL.'/template/mobile/images/icon_tel.png" alt="" style="width:25px;margin-right:5px;display:block"></div>
				            <div class="weui-cell__bd">
				              <p>收件时间</p>
				            </div>
				               <div class="weui-cell__ft">'.date("Y-m-d H:i",$value['recoder_updatetime']).'</div>
				           
				          </div>'.$a;
				        
				
				}
					$done=(sizeof($list['lst'])<$psize ||empty($list))?1:0;
					$params = array(
					'res_str' => $str,
					'done' =>$done,
					);
					result_back($flag,$params);

				
					exit($re);
			}


		$psize=4;
		$list= $opt->Get_my_done_list($openid,1,$psize);
		if (sizeof($list['lst'])<$psize ||empty($list))
		{
			$load_done=1;
		}

		$mitem= $member->Get_member($openid);

		$done_list=$list['lst'];
		 
include $this->template('my_done');  
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