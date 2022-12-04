<?php

$navigateur = $_SERVER["HTTP_USER_AGENT"];
$bannav = Array('HTTrack','httrack','WebCopier','HTTPClient','websitecopier','webcopier');

foreach ($bannav as $banni) {
	$comparaison = strstr($navigateur, $banni);
	if($comparaison!==false) {
		echo '<center>Re valet!<br><br>Am salvat asta ca-mi place.';
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		echo '<br>';
		echo $hostname;
		echo '</center>';
		exit;
	}
}


class this {
	private static $instance;
	public static $pdo;
	public static $htmlpurifier;
	public static $_url = array();
	private static $_perPage = 25;
	public static $_PAGE_URL;
	public static $_FORUM_URL;
	public static $_FACEBOOK_URL;
	public static $_DISCORD_URL;
	public static $_SERVER_IP;

	public function __construct() {
		connect::init();
		self::_getUrl();
	}

	private static function _getUrl() {
		$url = isset($_GET['page']) ? $_GET['page'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        self::$_url = explode('/', $url);
	}

	public static function init() {
		$url = isset($_GET['page']) ? $_GET['page'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        self::$_url = explode('/', $url);

		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function getContent() {
		require_once 'system/HTMLPurifier.auto.php';
		include_once 'views/general/header.ic.php';
		if(self::$_url[0] === 'action') { include 'actions/' . self::$_url[1] . '.a.php'; return; }
		if(isset(self::$_url[0]) && !strlen(self::$_url[0]))
			include_once 'views/index.ic.php';
		else if(file_exists('views/' . self::$_url[0] . '.ic.php'))
			include 'views/' . self::$_url[0] . '.ic.php';
		else
			include_once 'views/index.ic.php';
		include_once 'views/general/footer.ic.php';
	}

	public static function alert($type,$message) {
		return '<div class="alert alert-'.$type.'">'.$message.'</div>';
	}

	public static function sweetalert($text,$text2,$type) {
    	$_SESSION['msg'] = '<script>swal("'.$text.'", "'.$text2.'", "'.$type.'")</script>';
        return 1;
    }

    //new
	public static function get_item_shop_price_with_id($itemID) {
		$st = connect::$g_con->prepare("SELECT `itemPrice` FROM `panel_shop` WHERE `itemID` = :itemID LIMIT 1");
		$st->bindParam(':itemID', $itemID);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['itemPrice'];
			}
		} else {
			return 'Mysql error.';
		}
	}
	public static function get_online_status($name) {
		$st = connect::$g_con->prepare("SELECT `online` FROM `admins` WHERE `auth` = ? LIMIT 1");
		$st->bindParam(1, $name);
		$st->execute();

		foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
			return $r['online'];
		}
	}

	public static function get_username_from_id($id) {
		$st = connect::$g_con->prepare("SELECT `auth` FROM `admins` WHERE `id` = ?");
		$st->bindParam(1, $id);
		$st->execute();

		foreach ($st->fetchAll() as $r) {
			return $r['auth'];
		}
	}

	public static function get_item_shop_value_with_id($itemID) {
		$st = connect::$g_con->prepare("SELECT `itemValue` FROM `panel_shop` WHERE `itemID` = :itemID LIMIT 1");
		$st->bindParam(':itemID', $itemID);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['itemValue'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public static function get_user_access_from_name($name) {
		$st = connect::$g_con->prepare("SELECT `access` FROM `admins` WHERE `auth` = ? LIMIT 1");
		$st->bindParam(1, $name);
		$st->execute();

		foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
			return $r['access'];
		}
	}

	public static function get_shop_log_paymentid($id) {
		$st = connect::$g_con->prepare("SELECT `paymentid` FROM `shop_buy_log` WHERE `paymentid` = :paymentid LIMIT 1");
		$st->bindParam(':paymentid', $id);
		if ($st->execute()) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return 'Mysql error.';
		}
	}

	public static function get_user_query_from_shop($buy_id, $buy_price) {
		if(substr($buy_price, 0, -3) == this::get_item_shop_price_with_id($buy_id)) {
			$st = connect::$g_con->prepare(this::get_item_shop_query_with_id($buy_id));
			$st->bindParam(':itemValue', this::get_item_shop_value_with_id($buy_id));
			$st->bindParam(':auth', this::get_username_from_id($_SESSION['user']));
			if ($st->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function get_item_shop_query_with_id($itemID) {
		$st = connect::$g_con->prepare("SELECT `itemQuery` FROM `panel_shop` WHERE `itemID` = :itemID LIMIT 1");
		$st->bindParam(':itemID', $itemID);
		if ($st->execute()) {
			foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
				return $r['itemQuery'];
			}
		} else {
			return 'Mysql error.';
		}
	}

	public static function set_shop_log($price, $status, $paymentID) {
		$st = connect::$g_con->prepare("INSERT INTO `shop_buy_log` (`username`, `pret`, `status`, `paymentid`) VALUES (:user, :price, :status, :paymentid)");
		$st->bindParam(':user', this::get_username_from_id($_SESSION['user']));
		$st->bindParam(':price', $price);
		$st->bindParam(':status', $status);
		$st->bindParam(':paymentid', $paymentID);
		if ($st->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public static function check_avatar($id) {
		$st = connect::$g_con->prepare("SELECT `Avatar` FROM `admins` WHERE `id` = ?");
		$st->bindParam(1, $id);
		$st->execute();

		foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
			return $r['Avatar'];
		}
	}

	public static function change_avatar($file, $user) {
		$st = connect::$g_con->prepare("UPDATE `admins` SET `Avatar` = ? WHERE `id` = ?");
		$st->bindParam(1, $file);
		$st->bindParam(2, $user);
		$st->execute();
	}

	public static function get_user_avatar_from_name($name) {
		$st = connect::$g_con->prepare("SELECT `Avatar` FROM `admins` WHERE `auth` = ? LIMIT 1");
		$st->bindParam(1, $name);
		$st->execute();

		foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
			return $r['Avatar'];
		}
	}

	public static function format($number) {
		return number_format($number,0,'.','.');
	}

	public static function isAjax() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			return true;
		}
		return false;
	}

	public static function date($data,$reverse = false) {
		return (!$reverse ? date('H:i:s d-m-Y',$data) : date('d-m-Y H:i:s',$data));
	}

	public static function getDate($timestamp,$time = true){
		if(!$timestamp) return 1;
		$difference = time() - $timestamp;
		if($difference == 0)
			return 'just now';
		$periods = array("second", "minute", "hour", "day", "week",
		"month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		if ($difference > 0) {
			$ending = "ago";
		} else {
			$difference = -$difference;
			$ending = "to go";
		}
		if(!$difference) return 'just now';
		for($j = 0; $difference >= $lengths[$j]; $j++)
		$difference /= $lengths[$j];
		$difference = round($difference);
		if($difference != 1) $periods[$j].= "s";
		if($time) $text = "$difference $periods[$j]";
		else $text = "$difference $periods[$j] $ending";
		return $text;
	}

	public static function getSpec($table, $data, $name, $value) {
		$q = connect::$g_con->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `'.$name.'` = ?');
		$q->execute(array($value));
		$r_data = $q->fetch();
		return $r_data[$data];
	}

	public static function getName($user, $name = true) {
		if($name == true) {
			$wd = connect::$g_con->prepare('SELECT `auth` FROM `admins` WHERE `id` = ?');
			$wd->execute(array($id));
			if($wd->rowCount()) {
				$r_data = $wd->fetch();
				$text = $r_data['name'];
			} else $text = $user;
		}
		return $text;
	}
	public static function getID($user) {
		$wd = connect::$g_con->prepare('SELECT `id` FROM `admins` WHERE `auth` = ?');
		$wd->execute(array($user));
		if($wd->rowCount()) {
			$r_data = $wd->fetch();
			$text = $r_data['id'];
		} else $text = $user;
		return $text;
	}

	public static function getUser() {
		return (isset($_SESSION['user']) ? $_SESSION['user'] : false);
	}

	public static function getNameFromID($id) {
		$wc = connect::$g_con->prepare('SELECT `auth` FROM `admins` WHERE `id` = ?');
		$wc->execute(array($id));
		$r_data = $wc->fetch();
		return $r_data['name'];
	}

	public static function timeFuture($time_ago)
	{
		$cur_time   = time();
		$time_elapsed   = $time_ago - $cur_time;
		$days       = round($time_elapsed / 86400 );

		if($days > -1){
			return "in $days days";
		 }else {
			return "$days days ago";
		}
	}
	public static function timeAgo($time_ago, $icon = true)
	{
		$time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );

		if($seconds <= 60){
			return "chiar acum";
		}
		else if($minutes <=60){
			if($minutes==1){
				return "acum 1 minut";
			}
			else{
				return "acum $minutes minute";
			}
		}
		else if($hours <=24){
			if($hours==1){
				return "acum o ora";
			}else{
				return "acum $hours ore";
			}
		}
		else if($days <= 7){
			if($days==1){
				return "ieri";
			}else{
				return "acum $days zile";
			}
		}
		else if($weeks <= 4.3){
			if($weeks==1){
				return "acum o saptamana";
			}else{
				return "acum $weeks saptamani";
			}
		}
		else if($months <=12){
			if($months==1){
				return "acum o luna";
			}else{
				return "acum $months luni";
			}
		}
		else{
			if($years==1){
				return "acum 1 an";
			}else{
				return "acum $years ani";
			}
		}
	}

	public static function getData($table, $data, $id) {
		$q = connect::$g_con->prepare('SELECT `'.$data.'` FROM `'.$table.'` WHERE `id` = ?');
		$q->execute(array($id));
		$r_data = $q->fetch();
		return $r_data[$data];
	}

	public static function getData2($table,$cescoti,$cebagi,$data) {
		$q = connect::$g_con->prepare('SELECT `'.$cescoti.'` FROM `'.$table.'` WHERE `'.$cebagi.'` = ?');
		$q->execute(array($data));
		$r_data = $q->fetch();
		return $r_data[$cescoti];
	}

	public static function limit() {
		if(!isset($_GET['pg']))
			$_GET['pg'] = 1;
		return "LIMIT ".(($_GET['pg'] * self::$_perPage) - self::$_perPage).",".self::$_perPage;
	}

	public static function create($rows) {
		if(!isset($_GET['pg']))
			$_GET['pg'] = 1;
		$adjacents = "2";
		$prev = $_GET['pg'] - 1;
		$next = $_GET['pg'] + 1;
		$lastpage = ceil($rows/self::$_perPage);
		$lpm1 = $lastpage - 1;

		$pagination = "<ul class='pagination pagination justify-content-center' style='margin:0px;display:0;'>";
		if($lastpage > 1)
		{
			if($prev != 0)
				$pagination.= "<li class='previous_page btn btn-dark flat'><a href='?pg=1'>« First</a></li>";  
			else 
				$pagination.= "<li class='previous_page disabled btn btn-dark flat'><a>« First</a></li>";  
			if ($lastpage < 7 + ($adjacents * 2))
			{   
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $_GET['pg'])
						$pagination.= "<li class='active btn btn-dark flat'><a href='#'>$counter</a></li>";
					else
						$pagination.= "<li class='btn btn-dark flat'><a href='?pg=$counter'>$counter</a></li>";                   
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))
			{
				if($_GET['pg'] < 1 + ($adjacents * 2))       
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active btn btn-dark flat'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li class='btn btn-dark flat'><a href='?pg=$counter'>$counter</a></li>";                   
					}
					$pagination.= "<li class='dots btn btn-dark flat'><a href='#'>...</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$lpm1'>$lpm1</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$lastpage'>$lastpage</a></li>";       
				}
				elseif($lastpage - ($adjacents * 2) > $_GET['pg'] && $_GET['pg'] > ($adjacents * 2))
				{
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=1'>1</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=2'>2</a></li>";
					$pagination.= "<li class='dots btn btn-dark flat'><a href='#'>...</a></li>";
					for ($counter = $_GET['pg'] - $adjacents; $counter <= $_GET['pg'] + $adjacents; $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active btn btn-dark flat'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$counter'>$counter</a></li>";                   
					}
					$pagination.= "<li class='dots btn btn-dark flat'><a href='#'>...</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$lpm1'>$lpm1</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$lastpage'>$lastpage</a></li>";      
				}
				else
				{
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=1'>1</a></li>";
					$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=2'>2</a></li>";
					$pagination.= "<li class='dots btn btn-dark flat'><a href='#'>...</a></li>";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $_GET['pg'])
							$pagination.= "<li class='active btn btn-dark flat'><a href='#'>$counter</a></li>";
						else
							$pagination.= "<li class='active btn btn-dark flat'><a href='?pg=$counter'>$counter</a></li>";                   
					}
				}
			}
			if($lastpage == (isset($_GET['pg']) ? $_GET['pg'] : 1))
				$pagination.= "<li class='next_page disabled btn btn-dark flat'><a>Last »</a></li>";  
			else 
				$pagination.= "<li class='next_page btn btn-dark flat'><a href='?pg=$lastpage'>Last »</a></li>";  
		}
		$pagination .= "</ul><div class='clearfix'></div>";
		return $pagination;
	}

	public static function getPage() {
		return isset(self::$_url[2]) ? self::$_url[2] : 1;
	}

	public static function isActive($active) {
		if(is_array($active)) {
			foreach($active as $ac) {
				if($ac === self::$_url[0]) return ' class="navigation__active"';
			}
			return;
		} else return self::$_url[0] === $active ? ' class="navigation__active"' : false;
	}

	public static function makeNotification($userid,$username,$notif,$vid,$vname,$link) {
		$notify = connect::$g_con->prepare('INSERT INTO `panel_notifications` (`UserID`,`UserName`,`Notification`,`FromID`,`FromName`,`Link`) VALUES (?, ?, ?, ?, ?, ?)');
		$notify->execute(array($userid,$username,$notif,$vid,$vname,$link));
		return 1;
	}

	public static function Protejez($data)
	{
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
		    $old_data = $data;
		    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		return $data;
	}

	public static function xss_clean($data)
	{
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
		    $old_data = $data;
		    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		return $data;
	}
	
	public static function clean($text = null) {
		if(strpos($text, '<h1') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<h2') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<h3') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<h4') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<h5') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<h6') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<script') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, '<img') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, 'meta') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, 'document.location') !== false) return '<i><small>Unknown</small></i>';
		if(strpos($text, 'olteanu') !== false) return '<i><small>Unknown</small></i>';
		strtr ($text, array ('olteanuadv' => '<replacement>'));

		$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';

		return preg_replace_callback($regex, function ($matches) {

			return '<a target="_blank" href="'.$matches[0].'">'.$matches[0].'</a>';

		}, $text);
	}
}
