<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(this::getData('admins', 'Boss', $_SESSION['user']) < 1) {	redirect::to(''); return 1; }?>

<?php
	if(isset($_POST['down_adm'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(user::getData($_POST['down_adm'],'warn') > 0)
			{
				$prep = connect::prepare('UPDATE `admins` SET `warn` = `warn`-1 WHERE `id`=?');
				$prep->execute(array($_POST['down_adm']));

				this::sweetalert("Success!", "Ai acordat warn down cu succes.", "success");
			} else {
	        	this::sweetalert("Error!", "Acest player are deja 0 warnuri.", "error");
			}
			redirect::to('owner'); return 1;
		}
	}

	if(isset($_POST['up_adm'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(user::getData($_POST['up_adm'],'warn') < 3)
			{
				$prep = connect::prepare('UPDATE `admins` SET `warn` = `warn`+1 WHERE `id`=?');
				$prep->execute(array($_POST['up_adm']));

				this::sweetalert("Success!", "Ai acordat warn up cu succes.", "success");
			} else {
				this::sweetalert("Error!", "Acest player are deja 3 warnuri.", "error");
			}
			redirect::to('owner'); return 1;
		}
	}

	if(isset($_POST['resetwarns'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$prep = connect::prepare('UPDATE `admins` SET `warn` = 0 WHERE `id`= ?');
			$prep->execute(array($_POST['resetwarns']));

			this::sweetalert("Success!", "Ai sters warnurile adminului cu succes.", "success");
			redirect::to('owner'); return 1;
		}
	}

	if(isset($_POST['removeadmin'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$prep = connect::prepare('UPDATE `admins` SET `Admin` = 0 AND `access` = "z" WHERE `id`= ?');
			$prep->execute(array($_POST['removeadmin']));

	        this::sweetalert("Success!", "Ai sters functia de admin cu succes.", "success");
			redirect::to('owner'); return 1;
		}
	}

?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header bg-dark text-white">
				<h5><i class="fa fa-wrench"></i> Owner Panel
					<?php if(isset($_POST["searchuser"])) { ?>
        			<button type="button" class="btn btn-success btn-sm float-right" onclick="window.location.href='<?php echo this::$_PAGE_URL ;?>owner'">
        			    back
        			</button>
        			<?php } ?>
				</h5>
			</div>
			<div class="card-body">
            	<form method="POST">
            		<div class="form-group row">
            			<div class="col-lg-4">
            				<div class="input-group">
            					<input type="text" class="form-control input-lg" placeholder="Type a Name" aria-label="Search a player" aria-describedby="basic-addon2" name="searchuser">
            					<div class="input-group-append">
            						<button class="btn btn-succes" type="submit" name="search"><i class="fa fa-search"></i></button>
            					</div>
            				</div>
            			</div>
            		</div>
            	</form>
				<?php echo this::create(connect::rows('admins')); ?>
				<table class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Status</th>
							<th>Name</th>
							<th>Function</th>
							<th>Warns</th>
							<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
							<th>Actions</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php if(isset($_POST["searchuser"])) { ?>
					<?php
					$adm = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `auth` LIKE ?");
					$adm->execute(array('%'.$_POST['searchuser'].'%'));
					?>
					<?php } else { ?>
					<?php
					$adm = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `Admin` >= 0 ORDER BY `Admin` DESC ".this::limit());
					$adm->execute();
					?>
					<?php } ?>
					<?php while($row = $adm->fetch(PDO::FETCH_OBJ)) { ?>
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

								$functii = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY `funcID` ASC");
								$functii->execute(array($row->id));
								while($badge = $functii->fetch(PDO::FETCH_OBJ)) {
								if($badge->funcPlayerID == $row->id) { echo ' <span class="badge" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'" data-toggle="tooltip" data-original-title="'.$badge->funcName.'"></i> '.$badge->funcName.'</font></span>';
									}
								} ?>
						</td>
						<td>
							<?php
							if($row->warn == 0) echo ' <span class="badge" style="background-color:green"><strong>0</strong></span>';
							if($row->warn == 1) echo ' <span class="badge" style="background-color:yellow"><strong>1</strong></span>';
							if($row->warn == 2) echo ' <span class="badge" style="background-color:orange"><strong>2</strong></span>';
							if($row->warn > 2) echo ' <span class="badge" style="background-color:red"><strong>'.$row->warn.'</strong></span>';
							?>
						</td>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<td>
							<form method="post">
								<button type="submit" name="up_adm" class="btn btn-primary btn-sm" value="<?php echo $row->id ?>">
								<i class="fa fa-arrow-up"></i></button>
								<button type="submit" name="down_adm" class="btn btn-danger btn-sm" value="<?php echo $row->id ?>">
								<i class="fa fa-arrow-down"></i></button>
								<button type="submit" name="resetwarns" class="btn btn-success btn-sm" value="<?php echo $row->id ?>">
								<i class="fa fa-history"></i></button>
								<button type="submit" name="removeadmin" class="btn btn-danger btn-sm" value="<?php echo $row->id ?>">
								<i class="fa fa-trash"></i></button>
							</form>
						</td>
						<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>