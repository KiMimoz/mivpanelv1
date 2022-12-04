<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php 
	if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { 
	return redirect::to(''); 
}?>
<div class="card">
	<div class="card-header bg-dark text-white">
	    <h4><i class="fa fa-file-text-o" aria-hidden="true"></i> Panel Logs</h4>
	    <?php echo this::create(connect::rows('panel_logs')); ?>
	</div>
	<div class="card-block">
		<div class="card-body table-responsive p-0">
			<table class="table table-hover text-nowrap">
				<thead>
					<tr>
						<th>#ID</th>
						<th>By</th>
						<th>Action</th>
						<th>IP</th>
						<th><i class="fa fa-clock-o"></i> Date</th>
					</tr>
				</thead>
				<?php
				$q = connect::$g_con->prepare('SELECT * FROM `panel_logs` ORDER BY `logID` DESC '.this::limit());
				$q->execute();
				while($log = $q->fetch(PDO::FETCH_OBJ)) { ?>
				<tbody>
					<tr>
						<td class="align-middle"><?php echo $log->logID ?></td>
	                  	<td class="align-middle"><?php if($log->logBy == 0) echo ' System';
	                  		else echo this::getData('admins', 'auth', $log->logBy) ?></td>
						<td class="align-middle"><?php echo $log->logText ?></td>
						<td class="align-middle"><?php echo $log->logIP ?></td>
						<td class="align-middle"><?php echo $log->logDate ?></td>
					</tr>
				</tbody>
				<?php } ?>
			</table>
		</div>
	</div>
</div>