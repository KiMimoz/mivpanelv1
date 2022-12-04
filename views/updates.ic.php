<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php 
$updates = connect::$g_con->prepare('SELECT * FROM `panel_updates` ORDER BY `id` DESC');
$updates->execute();

if(isset($_POST['adaugaupdate'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Admin', $_SESSION['user']) >= 6) {
		$q = connect::$g_con->prepare('INSERT INTO `panel_updates` (`title`, `text`, `textshort`, `admin`) VALUES (?, ?, ?, ?)');
		$q->execute(array(this::xss_clean(this::clean($_POST['updatetitle'])), this::xss_clean(this::clean($_POST['updatetext'])), this::xss_clean(this::clean($_POST['updatetext'])), $_SESSION['user']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " added a new update";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('updates');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
            <b><i class="fa fa-check-circle"></i> Success</b> New update was added successfully!
        </div>';
	}
}

if(isset($_POST['editupdate'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Admin', $_SESSION['user']) >= 6) {
		$q1 = connect::$g_con->prepare('UPDATE `panel_updates` SET `title` = ? , `text` = ? , `textshort` = ? WHERE `id` = ?');
		$q1->execute(array(this::xss_clean(this::clean($_POST['edittitleupdate'])), this::xss_clean(this::clean($_POST['edittextupdate'])), this::xss_clean(this::clean($_POST['edittextupdate'])), $_POST['editupdate']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " edited update";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('updates');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
            <b><i class="fa fa-check-circle"></i> Success</b> This update was editet successfully!
        </div>';
	}
}

if(isset($_POST['deleteupdate'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Admin', $_SESSION['user']) >= 6) {
		$q2 = connect::$g_con->prepare('DELETE FROM `panel_updates` WHERE `id` = ?');
		$q2->execute(array($_POST['deleteupdate']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " removed update";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('updates');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
            <b><i class="fa fa-check-circle"></i> Success</b> This update was removed successfully!
        </div>';
	}
}
?>

<script src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
<button type="submit" data-toggle="modal" data-target="#add-update" class="btn btn-success waves-effect waves-light">
	<i class="fa fa-plus"></i> add update
</button>
<br><br>
<?php } ?>
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<!-- Timelime example  -->
		<div class="row">
			<div class="col-md-12">
				<!-- The time line -->
				<div class="timeline">
					<!-- timeline time label -->
					<div class="time-label">
						<span class="bg-red">Server Updates</span>
					</div>
					<?php while($showupdate = $updates->fetch(PDO::FETCH_OBJ)) { ?>
					<div>
						<i class="fa fa-gamepad bg-purple"></i>
						<div class="timeline-item">
							<h3 class="timeline-header"><b><?php echo $showupdate->title ?></b>
								<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
								<button type="submit" data-toggle="modal" data-target="#edit-update<?php echo $showupdate->id ?>" value="<?php echo $showupdate->id ?>" class="btn btn-success btn-sm pull-right"><i class="fa fa-edit" aria-hidden="true"></i></button>
								<button type="submit" data-toggle="modal" data-target="#remove-update<?php echo $showupdate->id ?>" value="<?php echo $showupdate->id ?>" class="btn btn-danger btn-sm pull-right"><i class="fa fa-trash" aria-hidden="true"></i></button>
								<?php } ?>
							</h3> 
							<div class="timeline-body">
								<?php echo $showupdate->text ?>
							</div>
							<div class="timeline-footer">
								<a class="btn btn-primary btn-sm"><i class="fas fa fa-clock-o"></i> Posted by <?php echo this::getData('admins', 'auth', $showupdate->admin) ?> - <?php echo this::timeAgo($showupdate->date); ?></a>
							</div>
						</div>
					</div>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<div id="edit-update<?php echo $showupdate->id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
					                <h4 class="modal-title">Edit update <?php echo $showupdate->title ?></h4>
					            </div>
					            <div class="modal-body" align="center">
					                <div class="tab-pane active" id="edit-update<?php echo $showupdate->id ?>" role="tabpanel">
					                    <form action="" method="POST">
										<textarea name="edittitleupdate" id="edittitleupdate" class="form-control" rows="1"><?php echo this::xss_clean(this::clean($showupdate->title)); ?></textarea><br>
										<textarea name="edittextupdate" id="edittextupdate<?php echo $showupdate->id ?>" tabindex='-1' class="form-control" rows="10"><?php echo this::xss_clean(this::clean($showupdate->text)); ?></textarea>
										<script>CKEDITOR.replace('edittextupdate<?php echo $showupdate->id ?>');</script>
										<p></p>
										<button name="editupdate" value="<?php echo $showupdate->id ?>" class="btn btn-primary waves-effect waves-light">
			    							<i class="fa fa-edit"></i> Edit update
			    						</button>
										</form>
					                </div>
					            </div>
							</div>
						</div>
					</div>
					<div id="remove-update<?php echo $showupdate->id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
    					            <h4 class="modal-title">Remove update <?php echo $showupdate->title ?> ?</h4>
    					        </div>
    					        <div class="modal-body" align="center">
    					            <div class="tab-pane active" id="remove-update<?php echo $showupdate->id ?>" role="tabpanel">
    					                <form method='post'>
								    	<button name="deleteupdate" value="<?php echo $showupdate->id ?>" class="btn btn-danger waves-effect waves-light">
								    		Yes, i'm sure!
								    	</button>
									    </form>
    					            </div>
    					        </div>
							</div>
						</div>
					</div>
					<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>




<?php if(isset($_SESSION['user']) && this::getData('admins', 'Admin', $_SESSION['user']) >= 6) { ?>
<div id="add-update" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title">Add new update</h4>
            </div>
            <div class="modal-body" align="center">
                <div class="tab-pane active" id="add-update" role="tabpanel">
                    <form action="" method="POST">
                    <textarea name="updatetitle" id="updatetitle" class="form-control" placeholder="update title" rows="1"></textarea><br>
                    <textarea name="updatetext" id="updatetext" class="form-control" placeholder="update content" rows="10"></textarea>
                    <script>CKEDITOR.replace('updatetext');</script>
                    <p></p>
                    <button name="adaugaupdate" value="Add update" class="btn btn-success waves-effect waves-light">
                        add update
                    </button>
                    </form>
                </div>
            </div>
		</div>
	</div>
</div>
<?php } ?>