<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<div class="container container-md pv-lg">
    <div class="text-center mb-lg pb-lg">
        <div class="h1 text-bold">Cauta un jucator</div>
        <p>Introdu numele cautat mai jos. Sunt afisate primele 50 de rezultate.</p>
    </div>
</div>
<div class="container container-md">
    <div id="step1">
    	<form method="POST">
	        <div class="input-group input-group-lg">
	        	<input name="searchPlayer" type="text" class="form-control flat" placeholder="Search" required>
	            <span class="input-group-btn">
		            <button class="btn btn-primary btn-square waves-effect waves-light" name="searchButton" id="searchButton" type="submit" style="height:48px;">
		            	<strong>Search</strong>
		            </button>
	            </span>
	        </div>
        </form>
    </div>

    <?php if(isset($_POST['searchButton'])) {
		if(isset($_POST['searchButton'])) $_SESSION['searchPlayer'] = $_POST['searchPlayer'];
		$search = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `auth` LIKE ? LIMIT 50");
		$search->execute(array('%'.$_SESSION['searchPlayer'].'%')); ?>
		
		<?php if($search->RowCount()) { ?>
		<div class="card">
		  	<div class="card-body" id="searchTable">
			  	<table class="table">
					<thead>
						<tr>
							<th>#SQLID</th>
							<th>Name</th>
							<th>Functie</th>
						</tr>
					</thead>
			        <tbody>
				    <?php while($player = $search->fetch(PDO::FETCH_OBJ)) { ?>
				    <tr>
				      	<td><?php echo $player->id ;?></td>
				      	<td><a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $player->id; ?>"><?php echo $player->auth ;?></a></td>
						<td>
						<?php
							if($player->Admin == 7) echo ' <span class="badge" style="background-color:#3600FF"><strong>Founder</strong></span>';
							if($player->Admin == 6) echo ' <span class="badge" style="background-color:red"><strong>Owner</strong></span>';
							if($player->Admin == 5) echo ' <span class="badge" style="background-color:purple"><strong>God</strong></span>';
							if($player->Admin == 4) echo ' <span class="badge" style="background-color:#ff3300"><strong>Semi-God</strong></span>';
							if($player->Admin == 3) echo ' <span class="badge" style="background-color:#00FF00"><strong>Moderator</strong></span>';
							if($player->Admin == 2) echo ' <span class="badge" style="background-color:orange"><strong>Administrator</strong></span>';
							if($player->Admin == 1) echo ' <span class="badge" style="background-color:aqua"><strong>Helper</strong></span>';
							if($player->access == "br") echo ' <span class="badge" style="background-color:darkorange"><strong>VIP</strong></span>';
							if($player->Admin == 0) echo ' <span class="badge" style="background-color:grey"><strong>Player</strong></span>';
						?>
						</td>
					</tr>
			    	<?php } ?>
			    	</tbody>
		    	</table>
		    </div>
		</div>
		<?php }?>
	<?php }?>
</div>

<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
<div class="container container-md">
    <div id="step1">
    	<form method="POST">
	        <div class="input-group input-group-lg">
	        	<input name="searchbyIP" type="text" class="form-control flat" placeholder="Search by IP" required>
	            <span class="input-group-btn">
		            <button class="btn btn-primary btn-square waves-effect waves-light" name="serachIP" id="serachIP" type="submit" style="height:48px;">
		            	<strong>Search</strong>
		            </button>
	            </span>
	        </div>
        </form>
    </div>

    <?php if(isset($_POST['serachIP'])) {
		if(isset($_POST['serachIP'])) $_SESSION['searchbyIP'] = $_POST['searchbyIP'];
		$search = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `IP` LIKE ? LIMIT 50");
		$search->execute(array('%'.$_SESSION['searchbyIP'].'%')); ?>
		
		<?php if($search->RowCount()) { ?>
		<div class="card">
		  	<div class="card-body" id="searchTable">
			  	<table class="table">
					<thead>
						<tr>
							<th>#SQLID</th>
							<th>Name</th>
							<th>Functie</th>
							<th>Email</th>
							<th>IP</th>
						</tr>
					</thead>
			        <tbody>
				    <?php while($player = $search->fetch(PDO::FETCH_OBJ)) { ?>
				    <tr>
				      	<td><?php echo $player->id ;?></td>
				      	<td><a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $player->id; ?>"><?php echo $player->auth ;?></a></td>
						<td>
						<?php
							if($player->Admin == 7) echo ' <span class="badge" style="background-color:#3600FF"><strong>Founder</strong></span>';
							if($player->Admin == 6) echo ' <span class="badge" style="background-color:red"><strong>Owner</strong></span>';
							if($player->Admin == 5) echo ' <span class="badge" style="background-color:purple"><strong>God</strong></span>';
							if($player->Admin == 4) echo ' <span class="badge" style="background-color:#ff3300"><strong>Semi-God</strong></span>';
							if($player->Admin == 3) echo ' <span class="badge" style="background-color:#00FF00"><strong>Moderator</strong></span>';
							if($player->Admin == 2) echo ' <span class="badge" style="background-color:orange"><strong>Administrator</strong></span>';
							if($player->Admin == 1) echo ' <span class="badge" style="background-color:aqua"><strong>Helper</strong></span>';
							if($player->access == "br") echo ' <span class="badge" style="background-color:darkorange"><strong>VIP</strong></span>';
							if($player->Admin == 0) echo ' <span class="badge" style="background-color:grey"><strong>Player</strong></span>';
						?>
						</td>
						<td><?php echo $player->email;?></td>
						<td><?php echo $player->IP; ?></td>
					</tr>
			    	<?php } ?>
			    	</tbody>
		    	</table>
		    </div>
		</div>
		<?php }?>
	<?php }?>
</div>

<div class="container container-md">
    <div id="step1">
    	<form method="POST">
	        <div class="input-group input-group-lg">
	        	<input name="searchbyemail" type="text" class="form-control flat" placeholder="Search by Email" required>
	            <span class="input-group-btn">
		            <button class="btn btn-primary btn-square waves-effect waves-light" name="searchemail" id="serachlastIP" type="submit" style="height:48px;">
		            	<strong>Search</strong>
		            </button>
	            </span>
	        </div>
        </form>
    </div>

    <?php if(isset($_POST['searchemail'])) {
		if(isset($_POST['searchemail'])) $_SESSION['searchbyemail'] = $_POST['searchbyemail'];
		$search = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `email` LIKE ? LIMIT 50");
		$search->execute(array('%'.$_SESSION['searchbyemail'].'%')); ?>
		
		<?php if($search->RowCount()) { ?>
		<div class="card">
		  	<div class="card-body" id="searchTable">
			  	<table class="table">
					<thead>
						<tr>
							<th>#SQLID</th>
							<th>Name</th>
							<th>Functie</th>
							<th>Email</th>
							<th>IP</th>
						</tr>
					</thead>
			        <tbody>
				    <?php while($player = $search->fetch(PDO::FETCH_OBJ)) { ?>
				    <tr>
				      	<td><?php echo $player->id ;?></td>
				      	<td><a href="<?php echo this::$_PAGE_URL; ?>profile/<?php echo $player->id; ?>"><?php echo $player->auth ;?></a></td>
						<td>
						<?php
							if($player->Admin == 7) echo ' <span class="badge" style="background-color:#3600FF"><strong>Founder</strong></span>';
							if($player->Admin == 6) echo ' <span class="badge" style="background-color:red"><strong>Owner</strong></span>';
							if($player->Admin == 5) echo ' <span class="badge" style="background-color:purple"><strong>God</strong></span>';
							if($player->Admin == 4) echo ' <span class="badge" style="background-color:#ff3300"><strong>Semi-God</strong></span>';
							if($player->Admin == 3) echo ' <span class="badge" style="background-color:#00FF00"><strong>Moderator</strong></span>';
							if($player->Admin == 2) echo ' <span class="badge" style="background-color:orange"><strong>Administrator</strong></span>';
							if($player->Admin == 1) echo ' <span class="badge" style="background-color:aqua"><strong>Helper</strong></span>';
							if($player->access == "br") echo ' <span class="badge" style="background-color:darkorange"><strong>VIP</strong></span>';
							if($player->Admin == 0) echo ' <span class="badge" style="background-color:grey"><strong>Player</strong></span>';
						?>
						</td>
						<td><?php echo $player->email;?></td>
						<td><?php echo $player->IP; ?></td>
					</tr>
			    	<?php } ?>
			    	</tbody>
		    	</table>
		    </div>
		</div>
		<?php }?>
	<?php }?>
</div>
<?php }?>