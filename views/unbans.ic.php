<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(!user::isLogged()) { 
	this::sweetalert("Error!", "Nu esti logat.", "error");
	redirect::to(''); return 1;
} ?>

<?php
	$q = connect::$g_con->prepare('SELECT * FROM `admins`');
    $q->execute();
    $data = $q->fetch(PDO::FETCH_OBJ);
?>

<script src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php if(!isset(this::$_url[1])) { ?>
<div class="card">
	<div class="card-header">
	    <i class="fa fa-ban"></i> Unban requests
	    <a href="<?php echo this::$_PAGE_URL ?>unbans/create"><button type="button" class="btn btn-danger btn-sm float-right">Create unban request</button></a>
	</div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
		                <th>#ID</th>
						<th>Requested by</th>
						<th>Date</th>
						<th>Status</th>
						<th>View</th>
		            </tr>
				</thead>
				<tbody>
	                <?php
				    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			                $unbanlist = connect::$g_con->prepare("SELECT * FROM `panel_unbans` WHERE `Hide` = 0 ORDER BY `id` DESC ".this::limit());
				    		$unbanlist->execute();
				    	}
				    	else {
			                $unbanlist = connect::$g_con->prepare("SELECT * FROM `panel_unbans` WHERE `UserID` = ? AND `Hide` = 0 ORDER BY `id` DESC ".this::limit());
				    		$unbanlist->execute(array(this::getData('admins', 'id', $_SESSION['user'])));
				    	}
			            while($unban = $unbanlist->fetch(PDO::FETCH_OBJ)) {
			        ?>
	                <tr>
	                   	<td class="align-middle"><b><?php echo $unban->id ?></b></td>
	                   	<td class="align-middle">Cerere Unban <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins','id',$unban->UserID) ?>"><?php echo this::getData('admins','auth',$unban->UserID) ?></a></td>
	                   	<td class="align-middle"><?php echo $unban->Date ?></td>
	                   	<td class="align-middle">
	                		<?php if($unban->Status == 0) { ?>
								<span class="fa fa-unlock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This unban request is opened"></span>
							<?php } else if($unban->Status != 0) { ?>
								<span class="fa fa-lock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This unban request is locked"></span>
							<?php } ?>
	                   	</td>
	                   	<td class="align-middle"><a href="<?php echo this::$_PAGE_URL ?>unbans/view/<?php echo $unban->id ?>" class="btn btn-primary btn--icon"><i class="fa fa-search"></i></a></td>
	                </tr>
	                <?php } ?>
	            </tbody>
			</table>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "create") { ?>

<?php
if(!user::isLogged()) {
	this::sweetalert("Error!", "Trebuie sa fi logat!", "error");
	redirect::to('unbans'); return 1;
}
?>

<?php
	$banns = connect::$g_con->prepare('SELECT * FROM `advanced_bans` WHERE `victim_name` = ?');
	$banns->execute(array(this::getData('admins','auth',$_SESSION['user'])));
	$bannshow = $banns->fetch(PDO::FETCH_OBJ);
	
	if(!$bannshow) {
		this::sweetalert("Error!", "Contul tau nu este banat.", "error");
		redirect::to('unbans'); return 1;
}
?>

<?php
  	$unbanhave = connect::$g_con->prepare('SELECT * FROM `panel_unbans` WHERE `PlayerID` = ? AND `Status` = 0');
    $unbanhave->execute(array(this::getData('admins', 'id', $_SESSION['user'])));

    if($unbanhave->rowCount()) {
		this::sweetalert("Error!", "Ai deja o cerere de unban activa.", "error");
		redirect::to('unbans'); return 1;
	}
?>

<?php
	if(isset($_POST['updatetopicinfo'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 3");
			$q->execute(array($purifier->purify(this::xss_clean($_POST['topicinfo']))));
			redirect::to('unbans/create');
		}
	}
?>
<?php
if(isset($_POST['createunban'])) {
	$checked = 0;
	$questions = "";
	for ($x = 1; $x <= $_SESSION['questions']; $x++) {
		if(strlen($_POST['question'.$x.'']) > 1)
		{
			$checked++;
			if($x == $_SESSION['questions']) $questions = $questions . $_POST['ques'.$x.''] . '@' . $_POST['question'.$x.''];
			else $questions = $questions . $_POST['ques'.$x.''] . '@' . $_POST['question'.$x.''] . '|';
		}
	}
	if($checked != $_SESSION['questions']) {
		this::sweetalert("Error!", "Ai lasat intrebari necompletate!", "error");
		redirect::to('unbans'); return 1;
	}
	else {
		$wcd = connect::$g_con->prepare('INSERT INTO `panel_unbans` (`UserID`,`UserName`,`Answers`,`Questions`) VALUES (?,?,?,?)');
		$wcd->execute(array($_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$purifier->purify(this::xss_clean($questions)),$purifier->purify(this::xss_clean($_SESSION['questions']))));

		this::sweetalert("Success!", "Cererea ta de unban a fost creata!", "success");
		redirect::to('unbans'); return 1;

		$_SESSION['questions'] = -1;
	}
}
?>
<div class="row">
    <div class="col-md-6">
    	<div class="card">
    		<div class="card-header">
			    Create unban request
			</div>
			<div class="card-body">
				<form class="form-horizontal" method="post">
				<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_unban_questions` ORDER BY `id` DESC");
					$w->execute();
					$count = 1;
					while($question = $w->fetch(PDO::FETCH_OBJ)) { ?>
					<b><?php echo $count ;?>. <?php echo $question->question ;?></b>
					<input type="hidden" name="ques<?php echo $count ;?>" value="<?php echo $question->question ;?>">
					<input type="text" class="form-control" placeholder="type your answer..." name="question<?php echo $count ;?>">

					<?php $_SESSION['questions'] = $count; $count++; ?>
					<br>
					<?php }?>
					<button name="createunban" type="submit" class="btn btn-info pull-right">submit unban request</button>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-6">
    	<div class="card">
			<div class="card-body">
			  	<?php
					$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 3");
					$q->execute();
					$update = $q->fetch(PDO::FETCH_OBJ);
			  	?>

			  	<?php if(isset($_GET['edit'])) { ?>
			  		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<form method="post">
						<textarea name="topicinfo" class="form-control" rows="16" required><?php echo $purifier->purify(this::xss_clean($update->Topic)); ?></textarea>
						<script>CKEDITOR.replace('topicinfo');</script>
						<br>
						<input type="submit" name="updatetopicinfo" value="Update informations" class="btn btn-info pull-right"/>
					</form>
					<?php } else { ?>
						<?php redirect::to('unbans'); ?>
					<?php } ?>
				<?php } else { ?>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<a href="<?php echo this::$_PAGE_URL ?>unbans/create?edit" class="btn btn-info btn-xs pull-right">edit informations</a>
					<?php } ?>

					<?php echo $purifier->purify(this::xss_clean($update->Topic)) ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "view") { ?>

<?php
	$qz = connect::$g_con->prepare('SELECT * FROM `panel_unbans` WHERE `ID` = ?');
	$qz->execute(array(this::$_url[2]));
	$view = $qz->fetch(PDO::FETCH_OBJ);
?>

<?php

	if(isset($_POST['submitcomment'])) {
		if(isset($_SESSION['user']) && $view->Status == 0) {
			$q = connect::$g_con->prepare('INSERT INTO `panel_reply_unbans` (`replyPlayerID`, `replyUnbanID`, `replyText`) VALUES (?, ?, ?)');
			$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($_POST['comentariu']))));

			this::sweetalert("Success!", "Your comment was posted with successfully!", "success");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		}
	}

	if(isset($_POST['submitclosecomment'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('INSERT INTO `panel_reply_unbans` (`replyPlayerID`, `replyUnbanID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($_POST['comentariu']))));

				$qs = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 1 WHERE `id` = ?');
	            $qs->execute(array($view->id));

				this::sweetalert("Success!", "Your comment was posted and unban request was closed with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['removecomment']))
	{
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('DELETE FROM  `panel_reply_unbans` WHERE `replyID` = ?');
			$q->execute(array($_POST['removecomment']));

			this::sweetalert("Success!", "Comment was removed!", "success");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		}
	}

	if(isset($_POST['editcomment']))
	{
		if(isset($_SESSION['user'])) {
			$q = connect::$g_con->prepare('UPDATE `panel_reply_unbans` SET replyText = ? WHERE `replyID` = ?');
			$q->execute(array($purifier->purify(this::xss_clean($_POST['newcomment'])),$_POST['editcomment']));

			this::sweetalert("Success!", "Comment was edited with successfully!", "success");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('unbans/view/'.$view->id.''); return 1;
		}
	}

	if(isset($_POST['acceptunban'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

				$rmban = connect::$g_con->prepare('DELETE FROM `advanced_bans` WHERE `victim_name` = ?');
				$rmban->execute(array(this::getData('admins','auth',$view->UserID)));

	            $unbanreply = "Cerere de unban acceptata. Ai primit unban!";
	            $q = connect::$g_con->prepare('INSERT INTO `panel_reply_unbans` (`replyPlayerID`, `replyUnbanID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($unbanreply))));


				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " accepted si unbaned request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a primit un raspuns!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was accepted with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['banulramane'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $unbanreply = "Cerere de unban respinsa. Banul ramane!";
	            $q = connect::$g_con->prepare('INSERT INTO `panel_reply_unbans` (`replyPlayerID`, `replyUnbanID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($unbanreply))));


				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " rejected unban request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a primit un raspuns!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was rejected with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['openunban'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status != 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 0, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " opened unban request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a fost redeschisa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was opened with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['closeunban'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " closed unban request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a fost inchisa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was closed with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['deleteunban'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Hide == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 1, `Hide` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " deleted unban request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a fost stearsa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was deleted with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['recoverunban'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Hide != 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_unbans` SET `Status` = 0, `Hide` = 0, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " recovered unban request id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Cererea ta de unban a fost reconditionata!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Unban request was recovered with successfully!", "success");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('unbans/view/'.$view->id.''); return 1;
			}
		}
	}

?>

<div class="row">
    <div class="col-md-4">
    	<div class="card">
    		<div class="card-header">
			    Unban creator
			</div>
			<div class="card-body" align="center">
				<div class="col">
					<img style="width: 60px;" src="">
				</div>
				<div class="col">
					<h5><a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins','id',$view->UserID) ?>"><?php echo this::getData('admins','auth',$view->UserID) ?></a></h5>
					<hr>
					<p class="m-0">Warnings: <?php echo this::getData('admins','warn',$view->UserID) ?>/3</p>
					<p class="m-0">Email: <?php echo this::getData('admins','email',$view->UserID) ?></p>
					<p class="m-0">IP: <?php echo this::getData('admins','IP',$view->UserID) ?></p>
					<p class="m-0">Last online: <?php echo this::getData('admins','last_time',$view->UserID) ?></p>
					<p class="m-0">Status:
						<?php if($view->Status == 0) { ?>
	               			<span class="badge bg-success">Opened</span>
	               		<?php } if($view->Status != 0) { ?>
	               			<span class="badge bg-danger">Closed</span>
	               		<?php }?>
					</p>
				</div>
			</div>
			<div class="card-footer" align="center">
				Unban request created date: <?php echo $view->Date ?>
			</div>
		</div>

		<div class="card">
    		<div class="card-header">
			    Last bans
			</div>
			<div class="card-body">
				in lucru
			</div>
		</div>

		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
		<div class="card">
    		<div class="card-header">
			    Admin tools
			</div>
			<div class="card-body">
				<form method="post">
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<?php if($view->Status == 0) { ?>
						<button type="submit" class="btn btn-success btn-block" name="acceptunban"><i class="fa fa-legal"></i> Accept unban request</button>

						<button type="submit" class="btn btn-success btn-block" name="banulramane"><i class="fa fa-legal"></i> Reject unban request</button>
						<?php } ?>
					<?php } ?>

					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<?php if($view->Status == 0) { ?>
						<button type="submit" class="btn btn-info btn-block" name="closeunban"><i class="fa fa-lock"></i> Close unban request</button>
						<?php } else if($view->Status != 0) { ?>
						<button type="submit" class="btn btn-info btn-block" name="openunban"><i class="fa fa-unlock"></i> Open unban request</button>
						<?php } ?>
					<?php } ?>

					<?php if($view->Hide == 0) { ?>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<button type="submit" class="btn btn-danger btn-block" name="deleteunban"><i class="fa fa-trash"></i> Delete unban request</button>
						<?php } ?>
					<?php } else if($view->Hide != 0) { ?>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<button type="submit" class="btn btn-danger btn-block" name="recoverunban"><i class="fa fa-undo"></i> Recover unban request</button>
						<?php } ?>
					<?php } ?>
				</form>
			</div>
		</div>
		<?php }?>
	</div>

	<div class="col-md-8">
    	<div class="card">
    		<div class="card-header">
			    Unban details
			</div>
			<div class="card-body">
				<?php
					$count = 1;
					$row = explode("|", $view->Answers);
					for ($x = 0; $x < $view->Questions; $x++) {
						$show = explode("@", $row[$x]);
						echo'

							<font size="3px">'.$count.'. '.$show[0].'</font>
							<hr>
							<p>'.$show[1].'</p>
						<br>';
						$_SESSION['Questions'] = $count;
						$count++;
					}
				?>
			</div>
		</div>

		<div class="card">
    		<div class="card-header">
			    Unban comments
			</div>
			<div class="card-body">
		        <ul class="list-unstyled">
				<?php
				$q = connect::$g_con->prepare("SELECT * FROM `panel_reply_unbans` WHERE `replyUnbanID` = ? ORDER BY replyID ASC");
		        $q->execute(array(this::$_url[2]));
		        while($row = $q->fetch(PDO::FETCH_OBJ)) { ?>
					<li class="media">
						<div class="media-body">
						<h5 class="mt-0 mb-1 mt-2">
						<?php if(this::getData('admins', 'Boss', $row->replyPlayerID)) echo '<i class="fa fa-shield text-success" data-toggle="tooltip" data-original-title="admin"></i>'; ?>
						<a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins', 'id', $row->replyPlayerID) ?>"><b><?php echo this::getData('admins', 'auth', $row->replyPlayerID) ?></b></a> :
						<?php echo $purifier->purify(this::xss_clean($row->replyText)) ?>
						</h5>

						<?php if($_SESSION['user'] == $row->replyPlayerID || this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<form method='post'>
						<button name="removecomment" type="submit" value="<?php echo $row->replyID ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> remove</button>
						<button type="button" class="btn btn-info btn-sm" data-toggle="modal" value="<?php echo $row->replyID ?>" data-target="#editeazacomentariul<?php echo $row->replyID ?>"><i class="fa fa-edit"></i> edit</button>
						</form>

						<div id="editeazacomentariul<?php echo $row->replyID ?>" class="modal fade show" tabindex="-1" role="dialog">
						    <div class="modal-dialog modal-lg">
						        <div class="modal-content">
						            <div class="modal-header">
						                <h4 class="modal-title">Edit this comment</h4>
						                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						            </div>
						            <div class="modal-body" align="center">
						                <div class="tab-pane active" id="editeazacomentariul<?php echo $row->replyID ?>" role="tabpanel">
							            <br>
						                <form action="" method="POST">
											<textarea name="newcomment" id="newcomment" class="form-control" rows="1"><?php echo $purifier->purify(this::xss_clean($row->replyText)) ?></textarea>
											<p></p>
											<button name="editcomment" value="<?php echo $row->replyID ?>" class="btn btn-primary waves-effect waves-light">
									    		Edit comment
									    	</button>
											</form>
										</div>
						            </div>
						        </div>
						    </div>
						</div>
						<?php } ?>

						<br>
						<small class="text-muted">
						<?php echo $row->replyDate ?>
						</small>
						</div>
					</li>
					<hr>
					<?php }?>
				</ul>

		    	<?php
		    	if($view->Status == 0 && ($_SESSION['user'] == $view->PlayerID || this::getData('admins', 'Boss', $_SESSION['user']) >= 1)) { ?>
                <form method="post">
					<textarea name="comentariu" rows="1" placeholder="Comment..." class="form-control" required></textarea>
					<br>
					<input type="submit" name="submitcomment" value="Submit comment" class="btn btn-info pull-right"/>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<input type="submit" name="submitclosecomment" value="Submit comment & Close unban request" style="margin-right: 5px;" class="btn btn-danger pull-right"/>
					<?php }?>
				</form>
				<?php } else if($view->Status != 0) { ?>
					<div class="card-footer" align="center">
						<b>You can't post. This application was <font color="red">closed</font>.</b>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php } ?>
