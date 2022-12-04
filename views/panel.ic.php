<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(this::getData('admins', 'Boss', $_SESSION['user']) < 1) {	redirect::to(''); return 1; }?>

<script type="text/javascript" src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php
	if(isset($_POST['editserverinfo'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		    $q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 1");
		    $q->execute(array($purifier->purify(this::Protejez($_POST['serverinformations']))));

		    $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " modified server informations.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	      	$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai schimbat cu succes informatia serverului.
	        </div>'; redirect::to('panel'); return 1;
	    }
  	}

  	if(isset($_POST['turnon'])) {
  		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
	      	$q = connect::$g_con->prepare("UPDATE `panel_settings` SET `IPLoginVerify` = 1");
	        $q->execute();

	        $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " turned on ip login verify.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	      	$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai activat verificarea IP-ului la logare cu succes!
	        </div>'; redirect::to('panel'); return 1;
    	}
    }

    if(isset($_POST['turnoff'])) {
    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
	        $q = connect::$g_con->prepare("UPDATE `panel_settings` SET `IPLoginVerify` = 0");
	        $q->execute();

	        $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " turned off ip login verify.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	        $_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai dezactivat verificarea IP-ului la logare cu succes!
	        </div>'; redirect::to('panel'); return 1;
    	}
    }

    if(isset($_POST['maintenanceon'])) {
  		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
	      	$q = connect::$g_con->prepare("UPDATE `panel_settings` SET `Maintenance` = 1");
	        $q->execute();

	        $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " turned maintenance on.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	      	$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai activat mentenanta cu succes!
	        </div>'; redirect::to('panel'); return 1;
    	}
    }

    if(isset($_POST['maintenanceoff'])) {
    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
	        $q = connect::$g_con->prepare("UPDATE `panel_settings` SET `Maintenance` = 0");
	        $q->execute();

	        $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " turned maintenance off.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	        $_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai dezactivat mentenanta cu succes!
	        </div>'; redirect::to('panel'); return 1;
    	}
    }

    if(isset($_POST['deletefunction'])) {
    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$rmban = connect::$g_con->prepare('DELETE FROM `panel_groups` WHERE `groupID` = ?');
			$rmban->execute(array($_POST['deletefunction']));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " deleted function ID ".$_POST['deletefunction']." from panel.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai sters cu succes aceasta functie.
	        </div>'; redirect::to('panel'); return 1;
    	}
	}

	if(isset($_POST['creazagrup'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("INSERT INTO `panel_groups` (`groupAdmin`, `groupName`, `groupColor`, `groupFlags`) VALUES (?, ?, ?, ?);");

			$q->bindParam(1, $purifier->purify(this::Protejez($_POST['gpos'])));
			$q->bindParam(2, $purifier->purify(this::Protejez($_POST['gname'])));
			$q->bindParam(3, $purifier->purify(this::Protejez($_POST['gcolor'])));
			$q->bindParam(4, $purifier->purify(this::Protejez($_POST['gflags'])));
	
			$user = connect::$g_con->prepare("SELECT `groupName` FROM `panel_groups` WHERE `groupName` = '".$_POST['gname']."'");
			$user->execute();

			$find = $user->rowCount(); 

			if($find) {
				print '<div class="alert alert-danger">Eroare: Exista deja un grad cu acest nume.</div> <meta http-equiv="refresh" content="6; url=panel">';
			} else {
				$q->execute();
				$logwho = this::getData('admins','auth',$_SESSION['user']);
				$logresult = " created new group (".$_POST['gname'].", ".$_POST['gpos'].", ".$_POST['gcolor'].", ".$_POST['gflags'].").";

	        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
				$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
				<b><i class="fa fa-check-circle"></i> Success</b> Ai creeat cu succes acest Grad.
	        	</div>'; redirect::to('panel'); return 1;
	        }
		}
	}

	if(isset($_POST['setgroup'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `panel_groups` SET `groupAdmin` = ?, `groupName` = ?, `groupColor` = ?, `groupFlags` = ? WHERE `groupID` = ?');
			$q->execute(array($purifier->purify(this::Protejez($_POST['gepos'])), $purifier->purify(this::Protejez($_POST['gename'])), $purifier->purify(this::Protejez($_POST['gecolor'])), $purifier->purify(this::Protejez($_POST['geflags'])), $_POST['setgroup']));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
			$logresult = " edit group id (".$_POST['setgroup']."): (".$_POST['gename'].", ".$_POST['gepos'].", ".$_POST['gecolor'].", ".$_POST['geflags'].").";

			$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
			$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
			     <b><i class="fa fa-check-circle"></i> Success</b> Ai editat cu succes acest Grad.
			</div>'; redirect::to('panel'); return 1;
		}
	}
?>

<div id="creategroup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">Create Group</div>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><font color="black">x</font></button>
			</div>
			<div class="modal-body">
				<div class="tab-pane active" id="creategroup" role="tabpanel">
					<form action="" method="POST">
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<label class="floating-label">Group Name:</label>
							<input type="text" class="form-control" name="gname" required>
						</div>
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<label class="floating-label">Group Position:</label>
							<input type="number" class="form-control" name="gpos" required>
						</div>
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<label class="floating-label">Group Color:</label>
							<input type="text" class="form-control" name="gcolor" required>
						</div>
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<label class="floating-label">Group Flags:</label>
							<input type="text" class="form-control" name="gflags" required>
						</div>
						<div align="center">
							<button type="submit" name="creazagrup" class="btn btn-success waves-effect waves-light">
				                <span class="btn-label"><i class="fa fa-check"></i></span> confirm group
				            </button>
				        </div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
	<div class="card">
		<div class="card-header bg-dark text-white">
	        <h4><i class="fa fa-cogs"></i> Admin Panel</h4>
	        <button type="button" class="btn btn-outline-success float-right" data-toggle="modal" data-target="#creategroup" onclick="$('#creategroup-modal').modal();">create group</button>
	    </div>
		<div class="card-body">
			<ul class="nav nav-tabs customtab" role="tablist">
				<div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
					<thead>
						<tr>
							<th>SQL</th>
							<th>Position</th>
							<th>Name</th>
							<th>Color</th>
							<th>Flags</th>
							<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
							<th>Edit</th>
							<th><i class="fa fa-wrench"></i> Delete</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
							$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY groupAdmin DESC");
							$groups->execute();
							while($function = $groups->fetch(PDO::FETCH_OBJ)) {
	                     ?>
						<tr>
							<td><?php echo $function->groupID ?></td>
							<td><span class="badge" style="background-color:<?php echo $function->groupColor ?>"><strong><?php echo $function->groupAdmin ?></strong></span></td>
							<td><span class="badge" style="background-color:<?php echo $function->groupColor ?>"><strong><?php echo $function->groupName ?></strong></span></td>
							<td><span class="badge" style="background-color:<?php echo $function->groupColor ?>"><strong><?php echo $function->groupColor ?></strong></span></td>
							<td><span class="badge" style="background-color:<?php echo $function->groupColor ?>"><strong><?php echo $function->groupFlags ?></strong></span></td>
							<td><button type="button" class="btn btn-success" data-toggle="modal" value="<?php echo $function->groupID ?>" data-target="#editgroup<?php echo $function->groupID ?>"><i class="fa fa-edit"></i></button></td>
							<td><?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
							echo "<form method='post'><button name='deletefunction' value='{$function->groupID}' class='btn btn-danger btn-circle'><i class='fa fa-times'></i></button></form>";
							} ?></td>
							<div id="editgroup<?php echo $function->groupID ?>" class="modal fade show" tabindex="-1" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<div class="modal-title">Edit Group</div>
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><font color="black">x</font></button>
										</div>
										<div class="modal-body">
											<div class="tab-pane active" id="editgroup" role="tabpanel">
												<form action="" method="POST">
													<div class="form-group form-material floating" data-plugin="formMaterial">
														<label class="floating-label">Group Name:</label>
														<input type="text" name="gename" class="form-control" value="<?php echo $function->groupName ?>" required>
													</div>
													<div class="form-group form-material floating" data-plugin="formMaterial">
														<label class="floating-label">Group Position:</label>
														<input type="number" name="gepos" class="form-control" value="<?php echo $function->groupAdmin ?>" required>
													</div>
													<div class="form-group form-material floating" data-plugin="formMaterial">
														<label class="floating-label">Group Color:</label>
														<input type="text" name="gecolor" class="form-control" value="<?php echo $function->groupColor ?>" required>
													</div>
													<div class="form-group form-material floating" data-plugin="formMaterial">
														<label class="floating-label">Group Flags:</label>
														<input type="text" name="geflags" class="form-control" value="<?php echo $function->groupFlags ?>" required>
													</div>
													<div align="center">
														<button type="submit" name="setgroup" value="<?php echo $function->groupID ?>" class="btn btn-success waves-effect waves-light">
											                <span class="btn-label"><i class="fa fa-check"></i></span> SUBMIT
											            </button>
											        </div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</tr>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
	<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	<div class="col-md-12">
    	<div class="card">
			<div class="card-header bg-dark text-white">
		        <h4><i class="fa-solid fa-circle-info"></i> Server Information</h4>
		    </div>
			<div class="card-body">
				<?php
					$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 1");
					$q->execute();
					$topic = $q->fetch(PDO::FETCH_OBJ);
				?>
				<form method="post">
				<textarea name="serverinformations" id="serverinformations" class="form-control" rows="5"><?php echo $purifier->purify(this::Protejez($topic->Topic)) ?></textarea>
				<script>CKEDITOR.replace('serverinformations');</script><br/>
				<button name="editserverinfo" class="btn btn-primary waves-effect waves-light pull-right"><i class="fa fa-edit"></i> Edit Server Informations</button>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-4">
    	<div class="card">
			<div class="card-header bg-dark text-white">
		        <h4><i class="fa fa-lock"></i> Login IP Verify</h4>
		    </div>
			<div class="card-body">
				<form method="post">
				<?php
				if(this::getSpec("panel_settings","IPLoginVerify","ID", 1)) echo '<button type="submit" class="btn btn-danger btn-sm btn-block" name="turnoff"><i class="fa fa-lock"></i> TURN OFF IP SECURITY PANEL LOGIN</button>';
				else echo '<button type="submit" name="turnon" class="btn btn-success btn-sm btn-block"><i class="fa fa-unlock"></i> TURN ON IP SECURITY PANEL LOGIN</button>';
				?>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-4">
    	<div class="card">
			<div class="card-header bg-dark text-white">
		        <h4><i class="fa-brands fa-servicestack"></i> Panel Maintenance</h4>
		    </div>
			<div class="card-body">
				<form method="post">
				<?php
				if(this::getSpec("panel_settings","Maintenance","ID", 1)) echo '<button type="submit" class="btn btn-danger btn-sm btn-block" name="maintenanceoff"><i class="fa fa-lock"></i> TURN OFF PANEL MAINTENANCE</button>';
				else echo '<button type="submit" name="maintenanceon" class="btn btn-success btn-sm btn-block"><i class="fa fa-unlock"></i> TURN ON PANEL MAINTENANCE</button>';
				?>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-4">
    	<div class="card">
			<div class="card-header bg-dark text-white">
		        <h4><i class="fa-solid fa-newspaper"></i> Articles Panel</h4>
		    </div>
			<div class="card-body">
                <a href="<?php echo this::$_PAGE_URL ?>news">
                  <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-newspaper-o"></i> ARTICLES PANEL</button>
                </a>
			</div>
		</div>
	</div>
	<?php }?>
</div>