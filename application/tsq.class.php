<?php
class player
{
	var $id;
	var $name;
	var $kills;
	var $time;
}

class details
{
	var $address;
	var $hostname;
	var $map;
	var $dir;
	var $mode;
	var $pplayers;
	var $tplayers;
	var $version;
	var $type;
	var $os;
	var $password;
	var $mod;
	var $modurlinfo;
	var $modurldown;
	var $modversion;
	var $modsize;
	var $modsvonly;
	var $modcldll;
	var $secure;
	var $bots;
	var $ping;
}

class time
{
	var $hours;
	var $minutes;
	var $seconds;
}

class tsq
{
	var $ip;
	var $port;
	var $index=0;
	var $sock;
	var $error=1;
	var $challenge='';
	var $details;
	var $players=Array();
	function tsq($ip,$port='27015')
	{
		$this->ip=gethostbyname($ip);
		$this->port=$port;
		if(!($this->sock=fsockopen('udp://'.$ip,$port,$errno,$errstr,5)))
			$this->error=1;
		else
			$this->error=0;
	}
	function getchallenge()
	{
		if($this->error)
			return $this;
		socket_set_timeout($this->sock,2);
		fwrite($this->sock,"\xFF\xFF\xFF\xFF\x55\x76\x50\xF0\x22");
		$data=fread($this->sock,1024);
		$status=socket_get_status($this->sock);
		if($status['timed_out'])
		{
			$this->error=1;
			return $this;
		}
		$this->challenge=substr($data,5,4);
		return $this;
	}
	function getdetails()
	{
		if($this->error)
			return $this;
		socket_set_timeout($this->sock,3);
		$ping['start']=$this->gettime();
		fwrite($this->sock,"\xFF\xFF\xFF\xFFTSource Engine Query\x00");
		$data=fread($this->sock,4096);
		$status=socket_get_status($this->sock);
		if($status['timed_out'])
		{
			$this->error=1;
			return $this;
		}
		$ping['end']=$this->gettime();
		$this->details=new details;
		$this->reset();
		$this->skip(4);
		$type=$this->getchar($data);
		if($type=='m')
		{
			$this->details->address=$this->getstring($data);
			$this->details->hostname=$this->getstring($data);
			$this->details->map=$this->getstring($data);
			$this->details->dir=$this->getstring($data);
			$this->details->mode=$this->getstring($data);
			$this->details->pplayers=$this->getbyte($data);
			$this->details->tplayers=$this->getbyte($data);
			$this->details->version=$this->getbyte($data);
			$this->details->type=$this->getchar($data);
			$this->details->os=$this->getchar($data);
			$this->details->password=$this->getbyte($data);
			$this->details->mod=$this->getbyte($data);
			if($this->details->mod)
			{
				$this->details->modurlinfo=$this->getstring($data);
				$this->details->modurldown=$this->getstring($data);
				$this->getstring($data);
				$this->details->modversion=$this->getlong($data);
				$this->details->modsize=$this->getlong($data);
				$this->details->modsvonly=$this->getbyte($data);
				$this->details->modcldll=$this->getbyte($data);
			}
			$this->details->secure=$this->getbyte($data);
			$this->details->bots=$this->getbyte($data);
		}
		else
		{
			$this->details->address=$this->ip.':'.$this->port;
			$this->details->version=$this->getbyte($data);
			$this->details->hostname=$this->getstring($data);
			$this->details->map=$this->getstring($data);
			$this->details->dir=$this->getstring($data);
			$this->details->mode=$this->getstring($data);
			$this->getshort($data);
			$this->details->pplayers=$this->getbyte($data);
			$this->details->tplayers=$this->getbyte($data);
			$this->details->bots=$this->getbyte($data);
			$this->details->type=$this->getchar($data);
			$this->details->os=$this->getchar($data);
			$this->details->password=$this->getbyte($data);
			$this->details->secure=$this->getbyte($data);
			$this->details->mod=0;
		}
		$this->details->ping=(int)(($ping['end']-$ping['start'])*1000);
		return $this;
	}
	function getplayers()
	{
		if($this->error)
			return $this;
		if(!$this->challenge)
			return $this;
		socket_set_timeout($this->sock,3);
		fwrite($this->sock,"\xFF\xFF\xFF\xFF\x55".$this->challenge);
		$data=fread($this->sock,4096);
		$status=socket_get_status($this->sock);
		if($status['timed_out'])
		{
			$this->error=1;
			return $this;
		}
		$this->reset();
		$this->skip(5);
		$aplayers=$this->getbyte($data);
		for($i=0;$i<$aplayers;$i++)
		{
			$this->players[$i]=new player;
			$this->players[$i]->id=$this->getbyte($data);
			$this->players[$i]->name=$this->getstring($data);
			$this->players[$i]->kills=$this->getlong($data);
			$time=$this->getfloat($data);
			$min=floor($time/60);
			$this->players[$i]->time=new time;
			$this->players[$i]->time->hours=floor($min/60);
			$this->players[$i]->time->minutes=$min-(floor($min/60)*60);
			$this->players[$i]->time->seconds=floor($time-($min*60));
		}
		return $this;
	}
	function reset()
	{
		$this->index=0;
	}
	function skip($c)
	{
		$this->index+=$c;
	}
	function getbyte($data)
	{
		return ord(substr($data,$this->index++,1));
	}
	function getlong($data)
	{
		$long=unpack('llong',substr($data,$this->index,4));
		$this->index+=4;
		return $long['long'];
	}
	function getfloat($data)
	{
		$float=unpack('ffloat',substr($data,$this->index,4));
		$this->index+=4;
		return $float['float'];
	}
	function getshort($data)
	{
		$short=unpack('sshort',substr($data,$this->index,2));
		$this->index+=2;
		return $short['short'];
	}
	function getstring($data)
	{
		$string='';
		while(($c=substr($data,$this->index++,1))!="\x00")
		{
			if(strlen($data)<$this->index)
				return false;
			$string.=$c;
		}
		return $string;
	}
	function getchar($data)
	{
		return substr($data,$this->index++,1);
	}
	function gettime()
	{
		list($u,$s)=explode(' ',microtime());
		return ((float)$u+(float)$s);
	}
}

function _FillInStats($section)
{
	$server_host= "93.114.82.249";  //modifici;
	$server_port= "27015";  //modifici;

	$_Query=new tsq($server_host,$server_port);
	$_Query->getchallenge()->getdetails()->getplayers();
	$_BuildStats = Array(
		"HOST_VALUE"		=>	$server_host,
		"PORT_VALUE"		=>	$server_port,
		"HOST_NAME"			=>	'ip',
		"PORT_NAME"			=>	'port',
		#"FORM_METHOD"		=>	'get',
		#"FORM_ACTION"		=>	$_SERVER['PHP_SELF'],
		"SERVER_HOSTNAME"	=>	$_Query->error>0?' (Server offline)':$_Query->details->hostname,
		#"PAGE_TITLE"		=>	$_Query->error>0?' (Server offline)':$_Query->details->hostname,
		#"SERVER_IP"			=>	$_Query->error>0?' (Server offline)':$_Query->details->address,
		"SERVER_MOD"		=>	$_Query->error>0? @_gooffline():$_Query->details->dir.' ('.$_Query->details->mode.')',
		#"SERVER_PROTOCOL"	=>	$_Query->error>0?' (Server offline)':$_Query->details->version,
		#"SERVER_TIP"		=>	$_Query->error>0?' (Server offline)':($_Query->details->type =='m' ? "Dedicat":"HLTV"),
		#"SERVER_PAROLA"		=>	$_Query->error>0?' (Server offline)':($_Query->details->password?"Da":"Nu"),
		#"SERVER_OS"			=>	$_Query->error>0?' (Server offline)':($_Query->details->os=='w'?'Windows':'Linux'),
		"SERVER_LATENTA"	=> 	$_Query->error>0? @_gooffline():$_Query->details->ping,
		"SERVER_PLAYERS"	=>	$_Query->error>0? @_gooffline():$_Query->details->pplayers.' / '.$_Query->details->tplayers,
		"SERVER_MAP"		=>	$_Query->error>0? @_gooffline():$_Query->details->map
		);
	$_Players=$_Query->players;
	$_FIScontent = "";

	switch($section)
	{
		default:
		case 'header':
		$_FIScontent .= "Jucatori: ".$_BuildStats[SERVER_PLAYERS]." | Latenta: ".$_BuildStats[SERVER_LATENTA]." | Harta: ".$_BuildStats[SERVER_MAP]." <br> MOD: ".$_BuildStats[SERVER_MOD]."";
		break;

		case 'users':
		do
		{
			$g=0;
			for($i=0;$i<count($_Players)-1;$i++)
				if($_Players[$i]->kills<$_Players[$i+1]->kills)
				{
					$g=$_Players[$i];
					$_Players[$i]=$_Players[$i+1];
					$_Players[$i+1]=$g;
					$g=1;
				}
			} while($g);

			if($_Query->error==0)
			{
				for($i=0;$i<count($_Players);$i++)
					$_FIScontent .= "
				<tr>
				<td>
				".htmlentities($_Players[$i]->name)."
				</td>
				<td>
				{$_Players[$i]->kills}
				</td>
				<td>
				".date('H:i:s',mktime($_Players[$i]->time->hours,$_Players[$i]->time->minutes,$_Players[$i]->time->seconds))."
				</td>
				</tr>
				";
				for($i=0;$i<($_Query->details->pplayers-count($_Players));$i++)
				{
					$_FIScontent .=  '<tr class="warning">
					<td colspan="3">
					<blink>+</blink> conectare in curs...
					</td>
					</tr>';
				}
			}
			break;
		}
		return $_FIScontent;
	}
?>