<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(!user::isLogged()) { redirect::to(''); return 1; } ?>

<?php
if(isset($_POST['markallasread'])) {
	if(user::isLogged()) {
		$q = connect::$g_con->prepare('UPDATE `panel_notifications` SET `Seen` = 1 WHERE `UserID` = ?');
		$q->execute(array($_SESSION['user']));
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
                <b><i class="fa fa-check-circle"></i> Success</b> All notifications was successfully marked AS READ!
            </div>';
    	redirect::to('notifications'); return 1;
	}
}
?>
<div class="card">
	<div class="card-header bg-dark text-white">
	    <form method="post">
	    	<h4>
	    		<i class="icon-bell"></i> Notifications
				<div class="pull-right">
					<button type="submit" class="btn btn-info btn-block" name="markallasread"><i class="fa fa-mark"></i> Mark all notifications AS READ</button>
				</div>
	    	</h4>
	    </form>
	</div>
	<div class="card-body">
	<?php
	$notif_unread = connect::$g_con->prepare('SELECT * FROM `panel_notifications` WHERE `UserID` = ?');
	$notif_unread->execute(array(auth::user()->id)); ?>
		<div class="p-20">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th>Text</th>
						<th>Admin</th>
						<th>Date</th>
						<th>Link</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$notif = connect::$g_con->prepare('SELECT * FROM `panel_notifications` WHERE `UserID` = ? ORDER BY `ID` DESC LIMIT 10');
					$notif->execute(array(auth::user()->id));
					$count = 0;
					while($no = $notif->fetch(PDO::FETCH_OBJ)) { ?>
					<tr>
						<td>
							<span class="mail-desc"><?php echo $no->Notification ;?></span>
						</td>
						<td>
							<?php echo $no->UserName; ?>
						</td>
						<td>
							<?php echo $no->Date; ?>
						</td>
						<td>
							<a href="<?php echo $no->Link ;?>?check=on&notify=<?php echo $no->ID ;?>"> click</a>
						</td>
					</tr>
					<?php $count++; ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>