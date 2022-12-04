<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php
	$qa = connect::$g_con->prepare('SELECT id FROM admins WHERE Admin > 0'); 
	$qa->execute();
	$count = $qa->rowCount() . '';
?>
<div class="card">
	<div class="card-header bg-dark text-white">
	    <h4><i class="fa fa-user-secret" aria-hidden="true"></i> Staff [<?php echo $count; ?>]</h4>
	</div>
	<div class="card-body table-responsive p-0">
		<div class="p-20">
			<table class="table table-hover text-nowrap">
				<thead>
					<tr>
						<th>ID</th>
						<th>Status</th>
						<th>Name</th>
						<th>Grad</th>
						<th>Warnings</th>
						<th>Last Online</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$adm = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `Admin` > 0 ORDER BY `Admin` DESC");
					$adm->execute();
					while($row = $adm->fetch(PDO::FETCH_OBJ)) { ?>
					<tr>
						<td><?php echo $row->id; ?></td>
						<td>
							<?php if($row->online == 0) { ?>
								<span class="badge" style="background-color:red"><strong>offline</strong></span>
							<?php } else if($row->online == 1) { ?>
								<span class="badge" style="background-color:green"><strong>online</strong></span>
							<?php } ?>
						</td>
						<td>
							<?php
							$country = connect::$g_con->prepare("SELECT * FROM `countries` ORDER BY `ID` ASC");
							$country->execute();
							while($flag = $country->fetch(PDO::FETCH_OBJ)) {
								if($flag->Country == $row->Country) { echo ' <i class="flag-icon flag-icon-'.$flag->Flag.' mr-2"></i>';
								}
							}
							?>
							<a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $row->id; ?>"><?php echo $row->auth; ?></a>
						</td>
						<td>
							<?php
							$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY `groupAdmin` ASC");
							$groups->execute();
							while($function = $groups->fetch(PDO::FETCH_OBJ)) {
								if($function->groupAdmin == $row->Admin) { echo ' <span class="badge" style="background-color:'.$function->groupColor.'"><strong>'.$function->groupName.'</strong></span>';
								}
							}
							?>
						</td>
						<td>
							<?php
							if($row->warn == 0) echo ' <span class="badge" style="background-color:green"><strong>0</strong></span>';
							if($row->warn == 1) echo ' <span class="badge" style="background-color:orange"><strong>1</strong></span>';
							if($row->warn == 2) echo ' <span class="badge" style="background-color:red"><strong>2</strong></span>';
							if($row->warn > 2) echo ' <span class="badge" style="background-color:red"><strong>'.$row->warn.'</strong></span>';
							?>
						</td>
						<td><?php echo $row->last_time; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>