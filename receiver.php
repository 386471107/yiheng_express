<?php
/**
 * 一恒快递模块订阅器
 *
 * @author 宁波一恒网络
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yiheng_expressModuleReceiver extends WeModuleReceiver {
	public function receive()
    {
        global $_W, $_GPC;
        $openid = $this->message['from'];
        $type = $this->message['type'];
		
        load()->func('logging');
        logging_run($this->message, 'trace', 'wxsella');
        load()->model('mc');
        $uid = mc_openid2uid($openid);
        $member = mc_fetch($uid);
        if ($type == "subscribe" or $type == "unsubscribe") {
            $set = pdo_fetch("SELECT * FROM " . tablename('moyansj_gzqgtz') . " where uniacid =:uniacid", array(':uniacid' => $_W['uniacid']));
            $datetime = date('Y-m-d H:i:s');
            $nickname = $member['nickname'] ? $member['nickname'] : "";
            $notify_openid = str_replace('，', ',', $set['notify_openid']);
            $openidArray = explode(',', $notify_openid);
            $account_api = WeAccount::create();
            for ($index = 0; $index < count($openidArray); $index++) {
                if ($set['customnotice_flag'] == 1) { 
                    if ($type == "subscribe") { 
                        $info = str_replace("[昵称]", $nickname, $set['subscribe_notify_text']);
                    } else {
                        $info = str_replace("[昵称]", $nickname, $set['unsubscribe_notify_text']);
                    }
                    $message = array('msgtype' => 'text', 'text' => array('content' => urlencode($info)), 'touser' => $openidArray[$index]);
                    logging_run($message, 'trace', 'wxsella');
                    $status = $account_api->sendCustomNotice($message);
                    if (is_error($status)) {
                    }
                }
                
            }
        }
    }
}