<?php

// Put this file in /usr/local/www

require("functions.inc");
require_once("filter.inc");
require("shaper.inc");
require("captiveportal.inc");

if (isset($_GET['user_portal']))
{
	$cpzone = 'captif';
	$cpdb = captiveportal_read_db();
	foreach ($cpdb as $user)
	{
		echo '<tr class="portal-user-table-row"><td width="5px"></td><td>'.$user[2].'</td><td>'.$user[4].'</td><td>'.$user[3].'</td><td>'.htmlspecialchars(date("m/d/Y H:i:s", $user[0])).'</td></tr>';
	}
}

if (isset($_GET['count_portal']))
{
	$cpzone = 'captif';
	$cpdb = captiveportal_read_db();
	echo count($cpdb);
}

if (isset($_GET['history']))
{
	$t1 = $_GET['t1'];
	$t2 = $_GET['t2'];
	
	$res = '';
	$result = '';
	while ($t1 <= $t2)
	{
		(strlen(date('j',$t1)) == 1) ? $d = date('M',$t1)."  ".date('j',$t1) : $d = date('Y-m-d',$t1); 
		$res .= shell_exec('grep "name = " /var/log/portal_public_auth.log| grep "'.$d.'"');
		$t1 = strtotime('+1 day', $t1);
	}
	
	$ret .= '<div class="portal-history-table-container">                                                                                                                     
		<table class="portal-history-table">
		<tr class="portal-history-table-header">
		<td style="width:250px;">Nom  complet</td>
		<td style="width:200px;">Mail</td>
		<td style="width:140px;">Date </td>
		</tr>';

	$xres = explode("\n", $res);
	foreach ($xres as $line)
	{
		$linearray = explode(",",$line);
		$login_array = sscanf(stristr($linearray[0],"name = "), "name = %[^&]s");
		$r_array = sscanf(stristr($linearray[0],"email = "), "email = %[^&]s");
		$date_array = sscanf(stristr($linearray[0],"20"), "%[^&]s");
		$date = implode($date_array);
		$login = utf8_encode( implode($login_array));
		$r = implode ($r_array);
		$result .= "<tr><td>".$login."</td><td>".$r."</td><td>".$date."</td></tr>";
		
	}
	$ret.=$result;
	$ret .= '</table></div>';
	echo $ret;
}

if (isset($_GET['next']))
{
	$login = base64_decode($_GET['login']);
	
	$r = shell_exec('grep "'.$login.'" /var/log/portal_public_auth.log | tail -n 1');
	//echo $r;
}

?>
