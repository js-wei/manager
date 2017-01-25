<?php
namespace Service;
class AllowIp{
	function getIpAdr(&$ip){
  $ip1=getenv("HTTP_X_FORWARDED_FOR");
  $ip2=getenv("HTTP_CLIENT_IP");
  $ip3=getenv("REMOTE_ADDR");
if($ip1&&$ip1!='unknow')
   $ip=$ip1;
else if($ip2&&$ip2!='unknow')
   $ip=$ip2;
else if($ip3&&$ip3!='unknow')
   $ip=$ip3;
else
   $ip='127.0.0.1';
}
}
function get_netip($myip){   //只留客户IP地址的前三位
  $temp=explode(".",$myip);
  $netip.=$temp[0];
  $netip.=".";
  $netip.=$temp[1];
  $netip.=".";
  $netip.=$temp[2];
  return $netip;
}

}