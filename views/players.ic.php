<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php
	$qa = connect::$g_con->prepare('SELECT * FROM `admins`'); 
	$qa->execute();
	$count = $qa->rowCount() . '';
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header bg-dark text-white">
				<h4><i class="fa fa-user-o" aria-hidden="true"></i> List of Players [<font color="gold"><?php echo $count; ?></font>]</h4>
				<?php echo this::create(connect::rows('admins')); ?>
			</div>
			<div class="card-body">
				<form method="POST">
					<div class="form-group row">
						<div class="col-lg-4">
							<div class="input-group">
								<input type="text" class="form-control input-lg" placeholder="Type a name..." aria-label="Search a player" aria-describedby="basic-addon2" name="searchuser">
								<div class="input-group-append">
									<button class="btn btn-succes" type="submit" name="search"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</form>
				<div class="table-responsive">
                	<table class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Online</th>
								<th>Name</th>
								<th>Grad</th>
								<th>Warn</th>
								<th>Last Online</th>
							</tr>
						</thead>
						<?php if(isset($_POST["searchuser"])) { ?>
						<?php
						$q = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `auth` LIKE ? OR `LastIP` LIKE ?');
						$q->execute(array('%'.$_POST['searchuser'].'%', '%'.$_POST['searchuser'].'%')); ?>
						<?php } else { ?>
						<?php
						$q = connect::$g_con->prepare('SELECT * FROM `admins` ORDER BY `id` ASC '.this::limit());
						$q->execute(); ?>
						<?php } ?>
						<tbody>
							<?php while($row = $q->fetch(PDO::FETCH_OBJ)) { ?>
							<tr>
								<td class="align-middle">
									<?php echo $row->id ?>
								</td>
								<td class="align-middle">
									<?php if($row->online == 0) { ?>
										<span class="badge" style="background-color:red"><strong>offline</strong></span>
									<?php } else if($row->online == 1) { ?>
										<span class="badge" style="background-color:green"><strong>online</strong></span>
									<?php } ?>
								</td>
								<td class="align-middle">
									<a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $row->id; ?>"><?php echo $row->auth; ?></a>
								</td>
								<td class="align-middle">
									<?php
									$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY `groupAdmin` ASC");
									$groups->execute();
									while($function = $groups->fetch(PDO::FETCH_OBJ)) {
										if($function->groupAdmin == $row->Admin) { echo ' <span class="badge" style="background-color:'.$function->groupColor.'"><strong>'.$function->groupName.'</strong></span>';
										}
									}
									?>
								</td>
								<td class="align-middle">
									<?php
									if($row->warn == 0) echo ' <span class="badge" style="background-color:green"><strong>0</strong></span>';
									if($row->warn == 1) echo ' <span class="badge" style="background-color:red"><strong>1</strong></span>';
									if($row->warn == 2) echo ' <span class="badge" style="background-color:red"><strong>2</strong></span>';
									if($row->warn > 2) echo ' <span class="badge" style="background-color:black"><strong>'.$row->warn.'</strong></span>';
									?>
								</td>
								<td class="align-middle"><?php echo $row->last_time; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>