<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 7 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 7) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php $q = connect::$g_con->prepare('SELECT * FROM `panel_settings` WHERE `ID` = 1');
    $q->execute();
    $row = $q->fetch(PDO::FETCH_OBJ); ?>

<?php
if(!user::isLogged()) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Trebuie sa fi logat!</div>';
	redirect::to(''); return 1;
}

if(this::getData("admins","Boss",$_SESSION['user']) < 1) {
	$_SESSION['msg'] = '<div class="alert alert-danger alert-white">Nu esti owner!</div>';
	redirect::to(''); return 1;
}
?>

<?php
	if(isset($_POST['openApp'])) {
	    $q = connect::$g_con->prepare('UPDATE `panel_settings` SET `AdminApp` = 1 WHERE `ID` = 1');
	    $q->execute();
	    $_SESSION['msg'] = '<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		Ai deschis cu succes aplicatiile pentru admin.
		</div>'; redirect::to('manageapp'); return 1;
    }

	if(isset($_POST['closeApp'])) {
	    $q = connect::$g_con->prepare('UPDATE `panel_settings` SET `AdminApp` = 0 WHERE `ID` = 1');
	    $q->execute();
	    $_SESSION['msg'] = '<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		Ai inchis cu succes aplicatiile pentru admin.
		</div>'; redirect::to('manageapp'); return 1;
    }

	if(isset($_POST['openAppS'])) {
	    $q = connect::$g_con->prepare('UPDATE `panel_settings` SET `SuggestionApp` = 1 WHERE `ID` = 1');
	    $q->execute();
	    $_SESSION['msg'] = '<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		Ai deschis cu succes sugestiile.
		</div>'; redirect::to('manageapp'); return 1;
    }

	if(isset($_POST['closeAppS'])) {
	    $q = connect::$g_con->prepare('UPDATE `panel_settings` SET `SuggestionApp` = 0 WHERE `ID` = 1');
	    $q->execute();
	    $_SESSION['msg'] = '<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
		Ai inchis cu succes sugestiile.
		</div>'; redirect::to('manageapp'); return 1;
    }

	if(isset($_POST['add'])) {
		$w = connect::$g_con->prepare("INSERT INTO `panel_questions` (`question`) VALUES (?)");
		$w->execute(array($purifier->purify(this::Protejez($_POST['text']))));
	}

	if(isset($_POST['delete'])) {
		$w = connect::$g_con->prepare("DELETE FROM `panel_questions` WHERE `id` = ?");
		$w->execute(array($_POST['delete']));
	}

	if(isset($_POST['edit'])) {
		$w = connect::$g_con->prepare("UPDATE `panel_questions` SET `question` = ? WHERE `id` = ?");
		$w->execute(array($purifier->purify(this::Protejez($_POST['question'.$_POST['edit'].''])),$_POST['edit']));
	}

	if(isset($_POST['adds'])) {
		$w = connect::$g_con->prepare("INSERT INTO `panel_suggestion_questions` (`question`) VALUES (?)");
		$w->execute(array($purifier->purify(this::Protejez($_POST['texts']))));
	}

	if(isset($_POST['deletes'])) {
		$w = connect::$g_con->prepare("DELETE FROM `panel_suggestion_questions` WHERE `id` = ?");
		$w->execute(array($_POST['deletes']));
	}

	if(isset($_POST['edits'])) {
		$w = connect::$g_con->prepare("UPDATE `panel_suggestion_questions` SET `question` = ? WHERE `id` = ?");
		$w->execute(array($purifier->purify(this::Protejez($_POST['questions'.$_POST['edits'].''])),$_POST['edits']));
	}

	if(isset($_POST['addu'])) {
		$w = connect::$g_con->prepare("INSERT INTO `panel_unban_questions` (`question`) VALUES (?)");
		$w->execute(array($purifier->purify(this::Protejez($_POST['textu']))));
	}

	if(isset($_POST['deleteu'])) {
		$w = connect::$g_con->prepare("DELETE FROM `panel_unban_questions` WHERE `id` = ?");
		$w->execute(array($_POST['deleteu']));
	}

	if(isset($_POST['editu'])) {
		$w = connect::$g_con->prepare("UPDATE `panel_unban_questions` SET `question` = ? WHERE `id` = ?");
		$w->execute(array($purifier->purify(this::Protejez($_POST['questionu'.$_POST['editu'].''])),$_POST['editu']));
	}
?>

<div class="row">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-group"></i> Manage Applications
		    </div>
			<div class="card-body">
		  		<form method="post">
				<?php if($row->AdminApp == 0) { ?>
				<button type="submit" class="btn btn-block btn-success" name="openApp">
				<i class="fa fa-toggle-on"></i> open applications
				</button>
				<?php } else { ?>
				<button type="submit" class="btn btn-block btn-danger" name="closeApp">
				<i class="fa fa-toggle-off"></i> close applications
				</button>
				<?php }?>
		      	</form>
			</div>
		</div>
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-users"></i> New Admin Applications
		    </div>
			<div class="card-body">
				<table class="table">
					<tbody>
						<tr>
			                <th>ID</th>
							<th>Name</th>
							<th>Date</th>
							<th>View</th>
			            </tr>
		                <?php
		                $q = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `Status` = 0 ORDER BY `id` DESC');
						$q->execute();
						while($showapp = $q->fetch(PDO::FETCH_OBJ)) {
						?>
		                <tr>
		                	<td><?php echo $showapp->id ?></td>
		                   	<td>
		                   		<a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('admins', 'id', $showapp->UserID) ;?>"><?php echo this::getData('admins', 'auth', $showapp->UserID) ;?></a></a>
                        	</td>
	                   		<td><?php echo $showapp->Date ?></td>
	                   		<td><a href="<?php echo this::$_PAGE_URL ;?>requestadmin/viewapp/<?php echo $showapp->id ;?>">click</a></td>
		                </tr>
		            	<?php } ?>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-list"></i> Manage admin questions
		    </div>
			<div class="card-body">
				<form method="post">
					<div class="input-group">
						<input class="form-control" type="text" name="text" placeholder="type your question here...">
						<span class="input-group-btn">
							<button class="btn btn-success" type="submit" name="add"><i class="fa fa-plus-square"></i></button>
						</span>
					</div><br>
					<hr>
					<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_questions` ORDER BY `id` DESC");
					$w->execute();
					while($question = $w->fetch(PDO::FETCH_OBJ)) {
						echo '
						<div class="input-group" style="margin-bottom: 5px">
							<input class="form-control" type="test" name="question'.$question->id.'" value="'.$question->question.'">
							<span class="input-group-btn">
								<button class="btn btn-primary" type="submit" name="edit" value="'.$question->id.'"><i class="fa fa-edit"></i></button>
								<button class="btn btn-danger" type="submit" name="delete" value="'.$question->id.'"><i class="fa fa-trash"></i></button>
							</span>
						</div>
						';
					}
					?>
				</form>
			</div>
		</div>
	</div>
	<hr style="width:100%;text-align:left;margin-left:0">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-group"></i> Manage Suggestions
		    </div>
			<div class="card-body">
		  		<form method="post">
				<?php if($row->SuggestionApp == 0) { ?>
				<button type="submit" class="btn btn-block btn-success" name="openAppS">
				<i class="fa fa-toggle-on"></i> open suggestions
				</button>
				<?php } else { ?>
				<button type="submit" class="btn btn-block btn-danger" name="closeAppS">
				<i class="fa fa-toggle-off"></i> close suggestions
				</button>
				<?php }?>
		      	</form>
			</div>
		</div>
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-users"></i> New Suggestions
		    </div>
			<div class="card-body">
				<table class="table">
					<tbody>
						<tr>
			                <th>ID</th>
							<th>Name</th>
							<th>Date</th>
							<th>View</th>
			            </tr>
		                <?php
		                $q = connect::$g_con->prepare('SELECT * FROM `panel_suggestions` WHERE `Status` = 0 ORDER BY `id` DESC');
						$q->execute();
						while($showapp = $q->fetch(PDO::FETCH_OBJ)) {
						?>
		                <tr>
		                	<td><?php echo $showapp->id ?></td>
		                   	<td>
		                   		<a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('admins', 'id', $showapp->UserID) ;?>"><?php echo this::getData('admins', 'auth', $showapp->UserID) ;?></a></a>
                        	</td>
	                   		<td><?php echo $showapp->Date ?></td>
	                   		<td><a href="<?php echo this::$_PAGE_URL ;?>requestadmin/viewapp/<?php echo $showapp->id ;?>">click</a></td>
		                </tr>
		            	<?php } ?>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-list"></i> Manage Suggestion Questions
		    </div>
			<div class="card-body">
				<form method="post">
					<div class="input-group">
						<input class="form-control" type="text" name="texts" placeholder="type your question here...">
						<span class="input-group-btn">
							<button class="btn btn-success" type="submit" name="adds"><i class="fa fa-plus-square"></i></button>
						</span>
					</div><br>
					<hr>
					<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_suggestion_questions` ORDER BY `id` DESC");
					$w->execute();
					while($question = $w->fetch(PDO::FETCH_OBJ)) {
						echo '
						<div class="input-group" style="margin-bottom: 5px">
							<input class="form-control" type="test" name="questions'.$question->id.'" value="'.$question->question.'">
							<span class="input-group-btn">
								<button class="btn btn-primary" type="submit" name="edits" value="'.$question->id.'"><i class="fa fa-edit"></i></button>
								<button class="btn btn-danger" type="submit" name="deletes" value="'.$question->id.'"><i class="fa fa-trash"></i></button>
							</span>
						</div>
						';
					}
					?>
				</form>
			</div>
		</div>
	</div>
	<hr style="width:100%;text-align:left;margin-left:0">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-users"></i> New Unban Applications
		    </div>
			<div class="card-body">
				<table class="table">
					<tbody>
						<tr>
			                <th>ID</th>
							<th>Name</th>
							<th>Date</th>
							<th>View</th>
			            </tr>
		                <?php
		                $q = connect::$g_con->prepare('SELECT * FROM `panel_unbans` WHERE `Status` = 0 ORDER BY `id` DESC');
						$q->execute();
						while($showapp = $q->fetch(PDO::FETCH_OBJ)) {
						?>
		                <tr>
		                	<td><?php echo $showapp->id ?></td>
		                   	<td>
		                   		<a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('admins', 'id', $showapp->UserID) ;?>"><?php echo this::getData('admins', 'auth', $showapp->UserID) ;?></a></a>
                        	</td>
	                   		<td><?php echo $showapp->Date ?></td>
	                   		<td><a href="<?php echo this::$_PAGE_URL ?>unbans/view/<?php echo $showapp->id ?>">click</a></td>
		                </tr>
		            	<?php } ?>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-dark text-white">
		        <i class="fa fa-list"></i> Manage unban questions
		    </div>
			<div class="card-body">
				<form method="post">
					<div class="input-group">
						<input class="form-control" type="text" name="textu" placeholder="type your question here...">
						<span class="input-group-btn">
							<button class="btn btn-success" type="submit" name="addu"><i class="fa fa-plus-square"></i></button>
						</span>
					</div><br>
					<hr>
					<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_unban_questions` ORDER BY `id` DESC");
					$w->execute();
					while($question = $w->fetch(PDO::FETCH_OBJ)) {
						echo '
						<div class="input-group" style="margin-bottom: 5px">
							<input class="form-control" type="test" name="questionu'.$question->id.'" value="'.$question->question.'">
							<span class="input-group-btn">
								<button class="btn btn-primary" type="submit" name="editu" value="'.$question->id.'"><i class="fa fa-edit"></i></button>
								<button class="btn btn-danger" type="submit" name="deleteu" value="'.$question->id.'"><i class="fa fa-trash"></i></button>
							</span>
						</div>
						';
					}
					?>
				</form>
			</div>
		</div>
	</div>
</div>
