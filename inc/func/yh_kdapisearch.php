<?php
 goto YEBs0; Fpevx: function sendPost($url, $datas) { goto J4XU_; ZYsUC: return $gets; goto l4rXl; e4sFh: goto F4E6U; goto wZV2u; fTWTY: if (feof($fd)) { goto uLva9; } goto zlljV; JsM2L: QUKd0: goto V9nHv; wZV2u: uQ2FI: goto EUbYx; qnH79: $gets .= fread($fd, 128); goto e4sFh; pkfkU: uLva9: goto kQZIq; bzRWB: yDf4r: goto PAtBp; EUbYx: fclose($fd); goto ZYsUC; uSWBu: YT_wa: goto qvn8I; aPVM5: foreach ($datas as $key => $value) { $temps[] = sprintf("\45\163\x3d\x25\163", $key, $value); DK9pd: } goto uSWBu; MufiK: if (feof($fd)) { goto uQ2FI; } goto qnH79; W4hGl: uWERi: goto fTWTY; PAtBp: $httpheader = "\120\117\x53\124\40" . $url_info["\160\141\x74\150"] . "\40\110\124\x54\120\x2f\x31\x2e\x30\15\xa"; goto rzQzc; qvn8I: $post_data = implode("\46", $temps); goto w3hBA; VYkWo: $httpheader .= "\103\x6f\x6e\156\145\x63\x74\151\157\x6e\72\143\154\157\163\x65\15\12\xd\12"; goto s17bM; Mmggm: fwrite($fd, $httpheader); goto ctCrw; kQZIq: F4E6U: goto MufiK; cDCxy: goto uLva9; goto JsM2L; pbZWT: $httpheader .= "\103\157\156\x74\x65\x6e\x74\55\114\145\x6e\x67\164\x68\72" . strlen($post_data) . "\xd\xa"; goto VYkWo; s9Rdy: $headerFlag = true; goto W4hGl; V9nHv: goto uWERi; goto pkfkU; w3hBA: $url_info = parse_url($url); goto oSnRp; ctCrw: $gets = ''; goto s9Rdy; rzQzc: $httpheader .= "\x48\157\163\x74\72" . $url_info["\x68\157\x73\x74"] . "\xd\12"; goto mxmr1; J4XU_: $temps = array(); goto aPVM5; NW5nY: $fd = fsockopen($url_info["\150\x6f\x73\164"], $url_info["\160\x6f\162\x74"]); goto Mmggm; mxmr1: $httpheader .= "\103\157\x6e\x74\145\156\164\x2d\124\171\160\145\x3a\x61\x70\160\x6c\151\143\141\x74\x69\x6f\156\57\170\x2d\x77\167\x77\x2d\x66\157\162\155\x2d\x75\162\154\x65\x6e\x63\157\x64\x65\x64\xd\12"; goto pbZWT; oSnRp: if (!empty($url_info["\160\157\162\164"])) { goto yDf4r; } goto ijk8t; zlljV: if (!(($header = @fgets($fd)) && ($header == "\xd\xa" || $header == "\xa"))) { goto QUKd0; } goto cDCxy; ijk8t: $url_info["\160\x6f\x72\x74"] = 80; goto bzRWB; s17bM: $httpheader .= $post_data; goto NW5nY; l4rXl: } goto ylMCk; JgAOS: function getOrderTracesByJson() { goto tF2MC; GzxqU: return $result; goto o1lOt; s0TJ9: $datas = array("\x45\x42\165\163\x69\156\x65\x73\x73\111\x44" => EBusinessID, "\122\x65\x71\165\x65\163\x74\124\171\160\145" => "\61\60\60\x32", "\x52\145\161\x75\145\x73\164\x44\141\164\141" => urlencode($requestData), "\x44\x61\x74\141\124\x79\x70\x65" => "\x32"); goto JZwi_; qjrQi: $result = sendPost(ReqURL, $datas); goto GzxqU; JZwi_: $datas["\x44\141\164\x61\123\x69\147\x6e"] = encrypt($requestData, AppKey); goto qjrQi; tF2MC: $requestData = "\x7b\47\117\x72\x64\x65\x72\x43\x6f\x64\x65\x27\72\47\47\x2c\x27\123\x68\x69\160\x70\145\162\103\157\144\145\47\x3a\x27\131\124\117\47\x2c\47\114\x6f\x67\151\x73\164\x69\x63\103\x6f\x64\x65\x27\x3a\47\x31\62\63\x34\x35\x36\x37\x38\x27\x7d"; goto s0TJ9; o1lOt: } goto Fpevx; JOYk0: defined("\122\x65\161\x55\x52\114") or define("\x52\145\x71\125\x52\114", "\x68\x74\164\160\72\x2f\x2f\141\x70\x69\x2e\x6b\144\156\151\x61\157\56\143\x63\x2f\x45\142\165\x73\151\156\x65\x73\163\x2f\x45\142\x75\163\151\x6e\x65\x73\x73\x4f\x72\x64\145\162\x48\141\156\x64\154\145\x2e\141\163\160\170"); goto sEmqZ; PIx0D: defined("\x41\160\160\113\x65\x79") or define("\x41\160\x70\x4b\145\171", "\350\257\xb7\xe5\210\260\xe5\xbf\253\xe9\x80\222\351\270\237\xe5\xae\230\347\xbd\x91\xe7\x94\xb3\xe8\xaf\267\150\164\x74\x70\72\57\x2f\153\144\156\x69\141\x6f\x2e\x63\157\x6d\57\x72\x65\147"); goto JOYk0; sEmqZ: $logisticResult = getOrderTracesByJson(); goto s1g8W; s1g8W: echo logisticResult; goto JgAOS; YEBs0: defined("\105\102\165\x73\151\x6e\145\x73\163\111\x44") or define("\105\x42\165\x73\151\x6e\145\163\x73\x49\104", "\xe8\257\xb7\xe5\210\260\xe5\xbf\253\xe9\x80\222\351\270\237\345\256\x98\xe7\275\221\xe7\x94\xb3\350\xaf\267\150\164\164\x70\x3a\x2f\57\153\x64\x6e\x69\141\157\x2e\x63\x6f\x6d\57\162\145\x67"); goto PIx0D; ylMCk: function encrypt($data, $appkey) { return urlencode(base64_encode(md5($data . $appkey))); }