<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<script type="text/javascript" src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php
	if(isset($_POST['editserver'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		    $q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 6");
		    $q->execute(array($purifier->purify(this::xss_clean($_POST['editservertext']))));

		    $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " modified server rules.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	      	$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai editat cu succes regulamentul in limbar romana.
	        </div>'; redirect::to('rules'); return 1;
	    }
  	}

	if(isset($_POST['editpanel'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		    $q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 7");
		    $q->execute(array($purifier->purify(this::xss_clean($_POST['editpaneltext']))));

		    $logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " modified panel rules.";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

	      	$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	            <b><i class="fa fa-check-circle"></i> Success</b> Ai editat cu succes regulamentul in limba engleza.
	        </div>'; redirect::to('rules'); return 1;
	    }
  	}
?>
<?php
$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 6");
$q->execute();
$server = $q->fetch(PDO::FETCH_OBJ);

$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 7");
$q->execute();
$panel = $q->fetch(PDO::FETCH_OBJ);
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header bg-dark text-white">
	    	    <h4><i class="fa-solid fa-user-shield"></i> Server Rules</h4>
	    	    	<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<div class="pull-right">
						<button type="submit" data-toggle="modal" data-target="#editserverrules" class="btn btn-primary btn-sm" style="width:125px; margin-right: 10px;"><i class="fa fa-edit"></i> Edit RO</button>
						<button type="submit" data-toggle="modal" data-target="#editpanelrules" class="btn btn-primary btn-sm" style="width:125px; margin-right: 10px;"><i class="fa fa-edit"></i> Edit EN</button>
					</div>
					<?php }?>
	    	    </h4>
	    	</div>
			<div class="card-body">
				<ul class="nav nav-tabs customtab" role="tablist">
					<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#serverrules" role="tab"><span><img src="https://i.imgur.com/wr0fQa6.png"> Romana</span></a></li>
					<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#panelrules" role="tab"><span><img src="https://i.imgur.com/ng0E8Y3.png"> English</span></a></li></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="serverrules" role="tabpanel">
						<?php echo $purifier->purify(this::xss_clean($server->Topic)) ?>
						<div style="float: right; font-weight: bold; display: block;">
							Last edit on <?php echo $server->Date ?>
						</div>
					</div>

					<div class="tab-pane fade" id="panelrules" role="tabpanel">
						<?php echo $purifier->purify(this::xss_clean($panel->Topic)) ?>
						<div style="float: right; font-weight: bold; display: block;">
							Last edit on <?php echo $panel->Date ?>
						</div>
					</div>
				</div>
			</div>
			<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
			<div id="editserverrules" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
			                <h4 class="modal-title">Edit RO</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			            </div>
			            <div class="modal-body" align="center">
			                <div class="tab-pane active" id="editserverrules" role="tabpanel">
			                    <form action="" method="POST">
								<textarea name="editservertext" id="editservertext" tabindex='-1' class="form-control" rows="10"><?php echo $purifier->purify(this::xss_clean($server->Topic)); ?></textarea>
								<script>CKEDITOR.replace('editservertext');</script>
								<p></p>
								<button name="editserver" class="btn btn-primary waves-effect waves-light">
									<i class="fa fa-edit"></i> SUBMIT
								</button>
								</form>
			                </div>
			            </div>
					</div>
				</div>
			</div>

			<div id="editpanelrules" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
			                <h4 class="modal-title">Edit EN</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			            </div>
			            <div class="modal-body" align="center">
			                <div class="tab-pane active" id="editpanelrules" role="tabpanel">
			                    <form action="" method="POST">
								<textarea name="editpaneltext" id="editpaneltext" tabindex='-1' class="form-control" rows="10"><?php echo $purifier->purify(this::xss_clean($panel->Topic)); ?></textarea>
								<script>CKEDITOR.replace('editpaneltext');</script>
								<p></p>
								<button name="editpanel" class="btn btn-primary waves-effect waves-light">
						    		<i class="fa fa-edit"></i> SUBMIT
						    	</button>
								</form>
			                </div>
			            </div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>