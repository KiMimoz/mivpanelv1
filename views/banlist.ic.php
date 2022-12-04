<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>
<?php
    if(isset($_POST['deleteallbans'])) {
    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
    		//$q = connect::$g_con->prepare('DELETE FROM `advanced_bans`');
    		//$q->execute();

			$logwho = this::getData('admins','auth',$_SESSION['user']);
			$logresult = " unbanned";
			$loglast = " all players";

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			this::sweetalert("Success!", "Ai debanat toti jucatorii de pe server.<br>(ps: deoarece e site beta, functia este dezactivata, insa functioneaza)", "success");
			redirect::to('banlist'); return 1;
		}
	}
?>
<div class="card">
	<div class="card-header bg-dark text-white">
	    <i class="fa fa-ban"></i> Banned players
	    <?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
	    <button class="btn btn-danger btn-sm float-right" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#unbanall" onclick="$('#unbanall-modal').modal();"><i class="fa fa-trash" aria-hidden="true"></i> Unban all</button>
	    <?php } ?>
	</div>
	<div class="card-body">
		<form method="POST">
			<div class="form-group row">
				<div class="col-lg-4">
					<div class="input-group">
						<input type="text" class="form-control input-lg" placeholder="Type a Name/IP/SteamID" aria-label="Search a player" aria-describedby="basic-addon2" name="searchuser">
						<div class="input-group-append">
           					<button class="btn btn-succes" type="submit" name="search"><i class="fa fa-search"></i></button>
           				</div>
					</div>
				</div>
			</div>
		</form>
		<table class="table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Player</th>
					<th>Player IP</th>
					<th>Admin Name</th>
					<th>Admin SteamID</th>
					<th>Reason</th>
					<th><i class="fa fa-clock-o"></i> Expiration date</th>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<th><i class="fa fa-wrench"></i> Unban</th>
					<?php } ?>
				</tr>
			</thead>
			<?php if(isset($_POST["searchuser"])) { ?>
			<?php
			$q = connect::$g_con->prepare('SELECT * FROM `advanced_bans` WHERE `victim_name` LIKE ? OR `victim_steamid` LIKE ?');
			$q->execute(array('%'.$_POST['searchuser'].'%', '%'.$_POST['searchuser'].'%')); ?>
			<?php } else { ?>
			<?php
			$q = connect::$g_con->prepare('SELECT * FROM `advanced_bans` ORDER BY `ID` DESC '.this::limit());
			$q->execute(); ?>
			<?php } ?>
			<?php while($aluatmuie = $q->fetch(PDO::FETCH_OBJ)) { ?>
			<tbody>
				<tr>
					<td class="align-middle"><?php echo $aluatmuie->id ?></td>
					<td class="align-middle"><a href="<?php echo this::$_PAGE_URL; ?>banned/<?php echo $aluatmuie->id; ?>"><font color="red"><?php echo $aluatmuie->victim_name ?></font></a></td>
					<td class="align-middle"><?php echo $aluatmuie->victim_steamid ?></td>
					<td class="align-middle"><?php echo $aluatmuie->admin_name ?></td>
					<td class="align-middle"><?php echo $aluatmuie->admin_steamid ?></td>
					<td class="align-middle"><?php echo $aluatmuie->reason ?></td>
		          	<td class="align-middle"><center><?php if($aluatmuie->banlength == 0) echo ' <font color="red">Permanent</font>';
		          	else echo ''.$aluatmuie->unbantime.'';?></td>
					<?php
					if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
					echo "<td><form method='post'><button name='unbanlabanat' value='{$aluatmuie->id}' class='btn btn-primary btn--icon'><i class='fa fa-times'></i></button></form></td>";
					}
					?>
				</tr>
			</tbody>
			<?php 
			if(isset($_POST['unbanlabanat'])) {
				if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
					$rmban = connect::$g_con->prepare('DELETE FROM `advanced_bans` WHERE `id` = ?');
					$rmban->execute(array($_POST['unbanlabanat']));

					$logtext = $aluatmuie->victim_name;
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
			<?php } ?>
		</table>
		<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
		<div id="unbanall" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		    <div class="modal-dialog modal-sm">
		        <div class="modal-content">
		            <div class="modal-body">
		                <div class="tab-pane active" id="unbanall" role="tabpanel">
		                    <form role="form" method="post" action="" id = "form">
		                        <div class="form-group">
		                            <h4 align="center">Are you sure?</h4>
		                        </div>
		                        <hr>
		                        <div align="center">
		                            <button type="submit" name="deleteallbans" action="Logout" class="btn btn-danger btn-block">Yes, unban all!</button>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<?php } ?>
	</div>
</div>