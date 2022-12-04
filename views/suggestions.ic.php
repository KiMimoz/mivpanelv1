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
	<div class="card-header bg-dark text-white">
	    <i class="fa fa-ban"></i> Suggestions
		<a href="<?php echo this::$_PAGE_URL ?>suggestions/create"><button type="button" class="btn btn-primary btn-sm float-right">Create suggestion</button></a>
	</div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
		                <th>ID</th>
						<th>Suggestion by</th>
						<th>Date</th>
						<th>Status</th>
						<th>View</th>
		            </tr>
				</thead>
				<tbody>
					<?php
						if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
							$suggestionlist = connect::$g_con->prepare("SELECT * FROM `panel_suggestions` WHERE `Hide` = 0 ORDER BY `id` DESC ".this::limit());
							$suggestionlist->execute();
						} else {
							$suggestionlist = connect::$g_con->prepare("SELECT * FROM `panel_suggestions` WHERE `UserID` = ? AND `Hide` = 0 ORDER BY `id` DESC ".this::limit());
							$suggestionlist->execute(array(this::getData('admins', 'id', $_SESSION['user'])));
						}
						while($suggestion = $suggestionlist->fetch(PDO::FETCH_OBJ)) {
					?>
	                <tr>
	                   	<td class="align-middle"><?php echo $suggestion->id ?></td>
	                   	<td class="align-middle">Sugestie creata de <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins','id',$suggestion->UserID) ?>"><?php echo this::getData('admins','auth',$suggestion->UserID) ?></a></td>
	                   	<td class="align-middle"><?php echo $suggestion->Date ?></td>
	                   	<td class="align-middle">
	                		<?php if($suggestion->Status == 0) { ?>
								<span class="fa fa-unlock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This topic is opened"></span>
							<?php } else if($suggestion->Status != 0) { ?>
								<span class="fa fa-lock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This topic is locked"></span>
							<?php } ?>
	                   	</td>
	                   	<td class="align-middle"><a href="<?php echo this::$_PAGE_URL ?>suggestions/view/<?php echo $suggestion->id ?>" class="btn btn-primary btn--icon"><i class="fa fa-search"></i></a></td>
	                </tr>
	                <?php } ?>
				</tbody>
			</table>
		</div>
		<?php echo this::create(connect::rows('panel_suggestions')); ?>
	</div>
</div>


<?php } else if(this::$_url[1] == "create") { ?>

<?php
	$q = connect::$g_con->prepare('SELECT * FROM `panel_settings` WHERE `ID` = 1');
	$q->execute();
	$checkapp = $q->fetch(PDO::FETCH_OBJ);
?>

<?php
if(!user::isLogged()) {
	this::sweetalert("Error!", "Trebuie sa fi logat!", "error");
	redirect::to('requestadmin'); return 1;
}
else if($checkapp->AdminApp == 0) {
	this::sweetalert("Error!", "Aplicatiile sunt inchise!", "error");
	redirect::to('requestadmin'); return 1;
}
?>
<?php
  	$suggestionhave = connect::$g_con->prepare('SELECT * FROM `panel_suggestions` WHERE `UserID` = ? AND `Status` = 0');
    $suggestionhave->execute(array(this::getData('admins', 'id', $_SESSION['user'])));

    if($suggestionhave->rowCount()) {
		this::sweetalert("Error!", "Ai deja o sugestie activa.", "error");
		redirect::to('suggestions'); return 1;
	}
?>

<?php
	if(isset($_POST['updatetopicinfo'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 8");
			$q->execute(array($purifier->purify(this::xss_clean($_POST['topicinfo']))));
			redirect::to('suggestions/create');
		}
	}
?>
<?php
if(isset($_POST['createsuggestion'])) {
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
		redirect::to('suggestions'); return 1;
	}
	else {
		$wcd = connect::$g_con->prepare('INSERT INTO `panel_suggestions` (`UserID`,`UserName`,`Answers`,`Questions`) VALUES (?,?,?,?)');
		$wcd->execute(array($_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$purifier->purify(this::xss_clean($questions)),$purifier->purify(this::xss_clean($_SESSION['questions']))));

		this::sweetalert("Success!", "Sugestia ta a fost creata!", "success");
		redirect::to('suggestions'); return 1;

		$_SESSION['questions'] = -1;
	}
}
?>
<div class="row">
    <div class="col-md-6">
    	<div class="card">
    		<div class="card-header">
			    Create Suggestion
			</div>
			<div class="card-body">
				<form class="form-horizontal" method="post">
				<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_suggestion_questions` ORDER BY `id` DESC");
					$w->execute();
					$count = 1;
					while($question = $w->fetch(PDO::FETCH_OBJ)) { ?>
					<b><?php echo $count ;?>. <?php echo $question->question ;?></b>
					<input type="hidden" name="ques<?php echo $count ;?>" value="<?php echo $question->question ;?>">
					<input type="text" class="form-control" placeholder="type your answer..." name="question<?php echo $count ;?>">

					<?php $_SESSION['questions'] = $count; $count++; ?>
					<br>
					<?php }?>
					<button name="createsuggestion" type="submit" class="btn btn-info pull-right">submit suggestion</button>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-6">
    	<div class="card">
			<div class="card-body">
			  	<?php
					$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 8");
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
						<?php redirect::to('suggestions'); ?>
					<?php } ?>
				<?php } else { ?>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<a href="<?php echo this::$_PAGE_URL ?>suggestions/create?edit" class="btn btn-info btn-xs pull-right">edit information</a>
					<?php } ?>

					<?php echo $purifier->purify(this::xss_clean($update->Topic)) ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "view") { ?>

<?php
	$qz = connect::$g_con->prepare('SELECT * FROM `panel_suggestions` WHERE `ID` = ?');
	$qz->execute(array(this::$_url[2]));
	$view = $qz->fetch(PDO::FETCH_OBJ);
?>

<?php

	if(isset($_POST['submitcomment'])) {
		if(isset($_SESSION['user']) && $view->Status == 0) {
			$q = connect::$g_con->prepare('INSERT INTO `panel_reply_suggestions` (`replyPlayerID`, `replySuggestionID`, `replyText`) VALUES (?, ?, ?)');
			$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($_POST['comentariu']))));

			this::sweetalert("Success!", "Your reply was posted with successfully!", "success");
			redirect::to('suggestions/view/'.$view->id.''); return 1;

		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('suggestions/view/'.$view->id.'');
			return 1;
		}
	}

	if(isset($_POST['submitclosecomment'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('INSERT INTO `panel_reply_suggestions` (`replyPlayerID`, `replySuggestionID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($_POST['comentariu']))));

				$qs = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 1 WHERE `id` = ?');
	            $qs->execute(array($view->id));

	        	this::sweetalert("Success!", "Your comment was posted and suggestion was closed with successfully!", "success");
	        	redirect::to('suggestions/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['removecomment']))
	{
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('DELETE FROM  `panel_reply_suggestions` WHERE `replyID` = ?');
			$q->execute(array($_POST['removecomment']));

	        this::sweetalert("Success!", "Comment was removed!", "success");
	        redirect::to('suggestions/view/'.$view->id.''); return 1;
		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('suggestions/view/'.$view->id.''); return 1;
		}
	}

	if(isset($_POST['editcomment']))
	{
		if(isset($_SESSION['user'])) {
			$q = connect::$g_con->prepare('UPDATE `panel_reply_suggestions` SET replyText = ? WHERE `replyID` = ?');
			$q->execute(array($purifier->purify(this::xss_clean($_POST['newcomment'])),$_POST['editcomment']));

	        this::sweetalert("Success!", "Comment was edited with successfully!", "success");
	        redirect::to('suggestions/view/'.$view->id.''); return 1;

		} else {
			this::sweetalert("Error!", "Nu poti face asta.", "error");
			redirect::to('suggestions/view/'.$view->id.''); return 1;
		}
	}

	if(isset($_POST['acceptsuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $suggestionreply = "Propunere acceptata!";
	            $q = connect::$g_con->prepare('INSERT INTO `panel_reply_suggestions` (`replyPlayerID`, `replySuggestionID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($suggestionreply))));

				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " accepted suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost acceptata!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was accepted with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.'');
				return 1;
			}
		}
	}

	if(isset($_POST['rejectsuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $suggestionreply = "Propunere respinsa!";
	            $q = connect::$g_con->prepare('INSERT INTO `panel_reply_suggestions` (`replyPlayerID`, `replySuggestionID`, `replyText`) VALUES (?, ?, ?)');
				$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($suggestionreply))));


				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " rejected suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost respinsa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was rejected with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;

			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['opensuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status != 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 0, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " opened suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost redeschisa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was opened with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['closesuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Status == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

				$logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " closed suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost inchisa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was closed with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;		        
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['deletesuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Hide == 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 1, `Hide` = 1, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " deleted suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost stearsa!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was deleted with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;	
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

	if(isset($_POST['recoversuggestion'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			if(isset($_SESSION['user']) && $view->Hide != 0) {
				$q = connect::$g_con->prepare('UPDATE `panel_suggestions` SET `Status` = 0, `Hide` = 0, `ActionBy` = ? WHERE `id` = ?');
	            $q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $view->id));

	            $logwho = this::getData('admins','id',$_SESSION['user']);
		        $logresult = " recovered suggestion id: ";
		        $loglast = $view->id;

		        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

				$notif = 'Propunerea ta a fost returnata!';
				$link = $_SERVER['REQUEST_URI'];
				this::makeNotification($view->id,this::getData("admins","auth",$view->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

				this::sweetalert("Success!", "Suggestion was recovered with successfully!", "success");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			} else {
				this::sweetalert("Error!", "Nu poti face asta.", "error");
				redirect::to('suggestions/view/'.$view->id.''); return 1;
			}
		}
	}

?>

<div class="row">
    <div class="col-md-4">
    	<div class="card">
    		<div class="card-header">
			    Suggestion Creator
			</div>
			<div class="card-body" align="center">
				<div class="col">
					<img style="width: 60px;" src="">
				</div>
				<div class="col">
					<h5><a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins','id',$view->UserID) ?>"><?php echo this::getData('admins','auth',$view->UserID) ?></a></h5>
					<hr>
					<p class="m-0">Warnings: <?php echo this::getData('admins','warn',$view->UserID) ?>/3</p>
					<p class="m-0">IP: <?php echo this::getData('admins','IP',$view->UserID) ?></p>
					<p class="m-0">Email: <?php echo this::getData('admins','email',$view->UserID) ?></p>
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
				Suggestion created date: <?php echo $view->Date ?>
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
						<button type="submit" class="btn btn-success btn-block" name="acceptsuggestion"><i class="fa fa-legal"></i> Accept suggestion</button>

						<button type="submit" class="btn btn-success btn-block" name="rejectsuggestion"><i class="fa fa-legal"></i> Reject suggestion</button>
						<?php } ?>
					<?php } ?>

					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<?php if($view->Status == 0) { ?>
						<button type="submit" class="btn btn-info btn-block" name="closesuggestion"><i class="fa fa-lock"></i> Close suggestion</button>
						<?php } else if($view->Status != 0) { ?>
						<button type="submit" class="btn btn-info btn-block" name="opensuggestion"><i class="fa fa-unlock"></i> Open suggestion</button>
						<?php } ?>
					<?php } ?>

					<?php if($view->Hide == 0) { ?>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<button type="submit" class="btn btn-danger btn-block" name="deletesuggestion"><i class="fa fa-trash"></i> Delete suggestion</button>
						<?php } ?>
					<?php } else if($view->Hide != 0) { ?>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<button type="submit" class="btn btn-danger btn-block" name="recoversuggetion"><i class="fa fa-undo"></i> Recover suggestion</button>
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
			    Suggestion details
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
			    Suggestion comments
			</div>
			<div class="card-body">
		        <ul class="list-unstyled">
				<?php
				$q = connect::$g_con->prepare("SELECT * FROM `panel_reply_suggestions` WHERE `replySuggestionID` = ? ORDER BY replyID ASC");
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
		    	if($view->Status == 0 && ($_SESSION['user'] == $view->UserID || this::getData('admins', 'Boss', $_SESSION['user']) >= 1)) { ?>
                <form method="post">
					<textarea name="comentariu" rows="1" placeholder="Comment..." class="form-control" required></textarea>
					<br>
					<input type="submit" name="submitcomment" value="Submit comment" class="btn btn-info pull-right"/>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<input type="submit" name="submitclosecomment" value="Submit comment & Close suggestion" style="margin-right: 5px;" class="btn btn-danger pull-right"/>
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
