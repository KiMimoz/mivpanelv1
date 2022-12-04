<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
	<?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
		<?php redirect::to('maintenance'); ?>
	<?php } ?>
<?php } ?>

<?php
	if(!isset(this::$_url[1])) redirect::to('');
	if(!isset(this::$_url[1]) && user::isLogged()) redirect::to('banned/'.auth::user()->id.'');
	else $user = User::where('auth', this::$_url[1])->orWhere('id', (int) this::$_url[1])->first();
	$q = connect::$g_con->prepare('SELECT * FROM `advanced_bans` WHERE `id` = ?');
	$q->execute(array(this::$_url[1]));


	if(!$q->rowCount()) {
	    echo '<div class="alert alert-danger">
			<h3><i class="fa fa-exclamation-triangle"></i> This player does not exist!</h3>
			</div>'
		;
	    return;
	}
	$data = $q->fetch(PDO::FETCH_OBJ);
?>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
				<div class="card card-danger card-outline">
					<div class="card-body box-profile">
						<div class="text-center">
							<h3 class="profile-username text-center"><font size="6" color="red"><?php echo $data->victim_name ?></font></h3>
						</div>
						<br>
						<p class="text-muted text-center"><font color="red">BANNED</font></p>
						<ul class="list-group list-group-unbordered mb-3">
							<li class="list-group-item">
								<b>Ban Reason:</b>
								<a class="float-right">
									<?php echo $data->reason; ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="card card-danger card-outline">
					<div class="card-header bg-dark text-white">
						<i class="fa fa-ban" aria-hidden="true"></i> Ban Information
						<div class="card-tools pull-right">
							<?php
							if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
								echo "<form method='post'><button name='unbanlaidiot' value='{$data->id}' class='btn btn-danger btn-icon'><i class='fa fa-times'></i> UNBAN</button></form>";
							}
							?>  
                		</div>
						<?php 
							if(isset($_POST['unbanlaidiot'])) {
								if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
									$rmban = connect::$g_con->prepare('DELETE from `advanced_bans` WHERE `id` = ?');
									$rmban->execute(array($_POST['unbanlaidiot']));

									$logtext = $data->victim_name;
									$logresult = " was unbanned by ";
									$logwho = this::getData('admins','auth',$_SESSION['user']);
									$loglast = " (from banlist)";

									$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logtext.''.$logresult.''.$logwho.''.$loglast.'", ?, ?)');
									$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

		    						this::sweetalert("Success!", "Player has been successfully unbanned!", "success");
									redirect::to('banlist'); return 1;
								}
							}
						?>
					<div>
					<div class="card-body">
						<div class="tab-content">
							<div class="card-body table-responsive p-0">
								<table class="table table-hover text-nowrap">
									<tbody>
										<tr>
											<td>Nickname:</td>
											<td><font color="red"><?php echo $data->victim_name ?></font></td>
											<td></td>
										</tr>
										<tr>
											<td>IP/SteamID:</td>
											<td><?php echo $data->victim_steamid ?></td>
											<td></td>
										</tr>
										<tr>
											<td>Reason:</td>
											<td><?php echo $data->reason; ?></td>
											<td></td>
										</tr>
											<td>Admin:</td>
											<td><font color="gold"><?php echo $data->admin_name ?></font>
											<?php if(user::isLogged() && ((auth::user()->Admin >= 22))) { ?>
											(<?php echo $data->admin_steamid ;?>)</td><?php }?>
											<td></td>
										</tr>
										<tr>
											<td>Ban date/time:</td>
											<td><?php echo $data->date; ?></td>
											<td></td>
										</tr>
										<tr>
											<td>Expiration date/time:</td>
											<td><?php if($data->banlength == 0) echo ' <font color="red">Permanent</font>';
											else echo ''.$data->unbantime.'';?></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>