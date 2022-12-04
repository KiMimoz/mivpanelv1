<?php
ob_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if(!file_exists('views/' . self::$_url[0] . '.ic.php') && strlen(self::$_url[0])) redirect::to("");

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

if(isset($_POST['amviatasociala']) && user::isLogged()) {
    unset($_SESSION['user']);
    session_destroy();
    $_SESSION['msg'] = '<div class="alert alert-success" role="alert">
	<a type="button" class="close" data-dismiss="alert" aria-label="Close"> <font color="black">x</font> </a>
                <b><i class="fa fa-check-circle"></i> Success!</b> You logged out successfully!
            </div>'; redirect::to(''); return 1;
}

if(isset($_GET['check']) && isset($_GET['notify']) && user::isLogged()) {
  if($_GET['check'] == "on" && is_numeric($_GET['notify'])) {
    $check = connect::$g_con->prepare('SELECT `ID` FROM `panel_notifications` WHERE `ID` = ?');
    $check->execute(array($_GET['notify']));
		if($check->rowCount()) {
			$nread = connect::$g_con->prepare('UPDATE `panel_notifications` SET `Seen` = 1 WHERE `ID` = ?');
			$nread->execute(array($_GET['notify']));
		}
	}
}
?>
<?php
$bannedplayers = connect::$g_con->prepare("SELECT * FROM `advanced_bans`");
$bannedplayers->execute();

$adminsplayers = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `Admin` > 0");
$adminsplayers->execute();

$playerslist = connect::$g_con->prepare("SELECT * FROM `admins`");
$playerslist->execute();

$q = connect::$g_con->prepare('SELECT * FROM `panel_settings` WHERE `ID` = 1');
$q->execute();
$row = $q->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
	<title>(maintenance) SYKOEGAY - User Control Panel</title>
	<?php } else { ?>
	<title>SYKOEGAY - User Control Panel</title>
	<?php }?>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css">
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/jqvmap/jqvmap.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/dist/css/adminlte.min.css?v=3.2.0">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/summernote/summernote-bs4.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/flag-icon-css/css/flag-icon.min.css">
	<link rel="stylesheet" href="<?php echo this::$_PAGE_URL ;?>resources/plugins/ekko-lightbox/ekko-lightbox.css">

	<!-- Sweet alerts -->
	<link href="<?php echo this::$_PAGE_URL ?>resources/plugins/sweetalert2/sweetalert2.css" rel="stylesheet">
	<script src="https://unpkg.com/sweetalert2@7.20.1/dist/sweetalert2.all.js"></script>

	<script src="<?php echo this::$_PAGE_URL ;?>resources/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
</head>

	
<?php if(user::isLogged()) { ?>
<div id="logout-up-modal" class="modal fade show" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-small">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Logout</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<div class="tab-pane active" id="logout-up" role="tabpanel">
					<form role="form" method="post" action="" id = "form">
						<div class="form-group">
							<h4 align="center">Are you sure you want to logout?</h4>
						</div>
						<hr>
						<div align="center">
							<button type="amviatasociala" name="amviatasociala" action="Logout" class="btn btn-info btn-block">Yes, i'm sure!</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<body class="hold-transition sidebar-mini layout-fixed dark-mode">
	<div class="wrapper">
		<nav class="main-header navbar navbar-expand navbar-dark">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa-solid fa-bars-staggered fa-rotate-180"></i></a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<?php if(user::isLogged()) { ?>
				<li class="nav-item dropdown">
				<?php
				$notif_unread = connect::$g_con->prepare('SELECT * FROM `panel_notifications` WHERE `UserID` = ? AND `Seen` = 0');
				$notif_unread->execute(array(auth::user()->id)); ?>
					<a class="nav-link" data-toggle="dropdown" href="#">
						<i class="fa-regular fa-bell"></i>
						<?php if($notif_unread->rowCount() == 0) { ?>
						<span class="badge badge-success navbar-badge"><?php echo $notif_unread->rowCount() ?></span>
						<?php } ?>
						<?php if($notif_unread->rowCount() != 0) { ?>
						<span class="badge badge-danger navbar-badge"><?php echo $notif_unread->rowCount() ?></span>
						<?php } ?>
					</a>
					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<span class="dropdown-item dropdown-header"><?php echo $notif_unread->rowCount() ?> Notifications</span>
						<?php
						$notif_unread = connect::$g_con->prepare('SELECT * FROM `panel_notifications` WHERE `UserID` = ? AND `Seen` = 0');
						$notif_unread->execute(array(auth::user()->id)); ?>
						<?php
						$notif = connect::$g_con->prepare('SELECT * FROM `panel_notifications` WHERE `UserID` = ? ORDER BY `ID` DESC LIMIT 5');
						$notif->execute(array(auth::user()->id));
						$count = 0;
						while($no = $notif->fetch(PDO::FETCH_OBJ)) { ?>
						<a href="<?php echo $no->Link ;?>?check=on&notify=<?php echo $no->ID ;?>" class="dropdown-item">
							<?php if($notif_unread->rowCount() == 0) { ?>
								<h3 class="dropdown-item-title">From <?php echo $no->FromName ;?><span class="float-right text-sm text-success"><i class="fa-solid fa-star-half-stroke"></i></span></h3>
							<?php } ?>
							<?php if($notif_unread->rowCount() != 0) { ?>
								<h3 class="dropdown-item-title">From <?php echo $no->FromName ;?><span class="float-right text-sm text-danger"><i class="fa-solid fa-star-half-stroke"></i></span></h3>
							<?php } ?>
							<p class="text-sm"><?php echo $no->Notification ;?></p>
							<p class="text-sm text-muted"><i class="fa fa-clock mr-1"></i> <?php echo $no->Date ;?></p>
						</a>
						<?php $count++; ?>
						<?php } ?>
						<div class="dropdown-divider"></div>
						<a href="<?php echo this::$_PAGE_URL ;?>notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#"><i class="fa-solid fa-bars-staggered"></i> User Account</a>
					<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
						<a href="<?php echo this::$_PAGE_URL ;?>profile" class="dropdown-item"><i class="fa-solid fa-id-card-clip"></i> My Profile</a>
						<a href="<?php echo this::$_PAGE_URL ;?>changeemail" class="dropdown-item"><i class="fa-solid fa-envelope-circle-check"></i> Change Email</a>
						<div class="dropdown-divider"></div>
						<a onclick="$('#logout-up-modal').modal();" href="javascript:void(0)" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
					</div>
				</li>
				<?php } else { ?>
				<li class="nav-item">
					<a href="<?php echo this::$_PAGE_URL ;?>register" class="nav-link"><i class="fa-regular fa-registered"></i> Register</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo this::$_PAGE_URL ;?>login" class="nav-link"><i class="fa fa-user-shield"></i> Login</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo this::$_PAGE_URL ;?>recover" class="nav-link"><i class="fa-solid fa-unlock-keyhole"></i> Forgot Password?</a>
				</li>
				<?php } ?>
			</ul>
		</nav>
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<a class="brand-link">
				<center>DEVELUPĂRI PULI MELE</center>
			</a>
			<div class="sidebar">
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>owner" class="nav-link">
								<i class="nav-icon fa fa-shield"></i>
								<p>Manage Admins</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>panel" class="nav-link">
								<i class="nav-icon fa-solid fa-screwdriver-wrench"></i>
								<p>Manage Panel</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>manageapp" class="nav-link">
								<i class="nav-icon fa-solid fa-screwdriver-wrench"></i>
								<p>Manage Applications</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>news" class="nav-link">
								<i class="nav-icon fa-regular fa-newspaper"></i>
								<p>News Panel</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>panellogs" class="nav-link">
								<i class="nav-icon fa-regular fa-folder-closed"></i>
								<p>Panel Logs</p>
							</a>
						</li>
						<hr style="width:100%;text-align:left;margin-left:0">
						<?php } ?>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>requestadmin" class="nav-link">
								<i class="nav-icon fa-solid fa-users"></i>
								<p>Request Admin <?php if($row->AdminApp == 0) { ?><span class="right badge badge-danger">OFF</span><?php } else if($row->AdminApp == 1) { ?> <span class="right badge badge-success">ON</span><?php } ?></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>unbans" class="nav-link">
								<i class="nav-icon fa-solid fa-ban"></i>
								<p>Request Unban <span class="right badge badge-success">ON</span></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>suggestions" class="nav-link">
								<i class="nav-icon fa-solid fa-lightbulb"></i>
								<p>Make a Suggestion <?php if($row->SuggestionApp == 0) { ?><span class="right badge badge-danger">OFF</span><?php } else if($row->SuggestionApp == 1) { ?> <span class="right badge badge-success">ON</span><?php } ?></p>
							</a>
						</li>
						<hr style="width:100%;text-align:left;margin-left:0">
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>" class="nav-link">
								<i class="nav-icon fa-solid fa-house-flag"></i>
								<p>Dashboard<span class="right badge badge-danger">Home Page</span></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>rules" class="nav-link">
								<i class="nav-icon fa-solid fa-user-shield"></i>
								<p>Server Rules</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>shop" class="nav-link">
								<i class="nav-icon fa-solid fa-cart-arrow-down"></i>
								<p>Shop<span class="right badge badge-danger">50% OFF</span></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>search" class="nav-link">
								<i class="nav-icon fa-solid fa-magnifying-glass-arrow-right"></i>
								<p>Search</p>
							</a>
						</li>
						<hr style="width:100%;text-align:left;margin-left:0">
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>staff" class="nav-link">
								<i class="nav-icon fa fa-shield"></i>
								<p>Staff<span class="right badge badge-primary"><?php echo $adminsplayers->RowCount(); ?></span></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>stats" class="nav-link">
								<i class="nav-icon fa fa-users"></i>
								<p>Stats</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>updates" class="nav-link">
								<i class="nav-icon fa fa-wrench"></i>
								<p>Updates<span class="right badge badge-success">NEW</span></p>
							</a>
						</li>
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>banlist" class="nav-link">
								<i class="nav-icon fa-solid fa-ban"></i>
								<p>Ban List<span class="right badge badge-danger"><?php echo $bannedplayers->RowCount(); ?></span></p>
							</a>
						</li>
						<hr style="width:100%;text-align:left;margin-left:0">
						<li class="nav-item">
							<a href="<?php echo this::$_PAGE_URL ;?>players" class="nav-link">
								<i class="nav-icon fa fa-users"></i>
								<p>Registered Players</p>
							</a>
						</li>
						<hr style="width:100%;text-align:left;margin-left:0">
						<li class="nav-item">
							<a href="<?php echo this::$_FORUM_URL; ?>" class="nav-link">
								<i class="nav-icon fa fa-tasks"></i>
								<p>Forum</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="https://www.gametracker.com/server_info/<?php echo this::$_SERVER_IP; ?>:27015/" class="nav-link">
								<i class="nav-icon fa fa-cube"></i>
								<p>GameTracker</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="steam://connect/<?php echo this::$_SERVER_IP; ?>:27015" class="nav-link">
								<i class="nav-icon fa fa-steam"></i>
								<p>Connect (with steam)</p>
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</aside>
		<div class="content-wrapper">
			<section class="content"><br>
<?php if(isset($_SESSION['msg'])) { echo $_SESSION['msg']; $_SESSION['msg'] = ''; }  ?>