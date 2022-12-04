<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(!user::isLogged()) { 
	this::sweetalert("Error!", "Nu esti logat.", "error");
	redirect::to(''); return 1;
} ?>

<script src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php if(!isset(this::$_url[1])) { ?>
<div class="card">
	<div class="card-header">
    	<h5><i class="fa fa-fw fa-list "></i> Request Admin
    		<a href="<?php echo this::$_PAGE_URL ?>requestadmin/apply"><button type="button" class="btn btn-primary btn-sm float-right">Apply</button></a>
    	</h5>
    </div>
	<div class="card-block">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
		                <th>Title</th>
						<th>Posted by</th>
						<th>Date</th>
						<th>Status</th>
						<th>View</th>
		            </tr>
		        </thead>
		        <tbody>
		        <?php
	                $q = connect::$g_con->prepare("SELECT * FROM `panel_applications` ORDER BY `id` DESC");
					$q->execute(); ?>
	                <?php while($topic = $q->fetch(PDO::FETCH_OBJ)) { ?>
	                <tr>
	                	<td class="align-middle"><b><?php echo $topic->id ?></b></td>
	                	<td class="align-middle">Cerere Admin <a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('admins', 'id', $topic->UserID) ;?>"><?php echo this::getData('admins', 'auth', $topic->UserID) ;?></a></td>
	                	<td class="align-middle"><a href="<?php echo this::$_PAGE_URL ;?>profile/<?php echo this::getData('admins', 'id', $topic->UserID) ;?>"><?php echo this::getData('admins', 'auth', $topic->UserID) ;?></a></td>
	                	<td class="align-middle"><span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $topic->Date ;?>">postat <?php echo this::timeAgo($topic->Date) ;?></span></td>
	                	<td class="align-middle">
	                		<?php if($topic->Status == 0) { ?>
								<span class="fa fa-unlock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This topic is opened"></span>
							<?php } else if($topic->Status != 0) { ?>
								<span class="fa fa-lock fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="This topic is locked"></span>
							<?php } ?>
	                	</td>
						<td class="align-middle"><a href="<?php echo this::$_PAGE_URL ;?>requestadmin/viewapp/<?php echo $topic->id ;?>" class="btn btn-primary btn--icon"><i class="fa fa-search"></i></a></td>
	                </tr>
	            	<?php } ?>
	            </tbody>
			</table>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "apply") { ?>

<?php
  	$apphave = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `UserID` = ? AND `Status` = 0');
    $apphave->execute(array($_SESSION['user']));

    if($apphave->rowCount()) {
		this::sweetalert("Error!", "Ai deja o aplicatie activa!", "error");
		redirect::to('requestadmin'); return 1;
	}
?>

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
if(isset($_POST['app_send'])) {
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
		redirect::to('requestadmin/apply'); return 1;
	}
	else {
		$wcd = connect::$g_con->prepare('INSERT INTO `panel_applications` (`UserID`,`UserName`,`Answers`,`Questions`) VALUES (?,?,?,?)');
		$wcd->execute(array($_SESSION['user'], this::getData("admins","auth",$_SESSION['user']), $purifier->purify(this::xss_clean($questions)), $purifier->purify(this::xss_clean($_SESSION['questions']))));

		this::sweetalert("Success!", "Aplicatia ta a fost trimisa cu succes!", "success");
		redirect::to('requestadmin'); return 1;
		$_SESSION['questions'] = -1;
	}
}
?>
<?php
	if(isset($_POST['updateadmininfo'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 9");
			$q->execute(array($purifier->purify(this::xss_clean($_POST['topicinfo']))));
			redirect::to('requestadmin/apply');
		}
	}
?>
<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Your application</h5>
			</div>
			<div class="card-body">
				<form method="post">

				<?php
					$w = connect::$g_con->prepare("SELECT * FROM `panel_questions` ORDER BY `id` DESC");
					$w->execute();
					$count = 1;
					while($question = $w->fetch(PDO::FETCH_OBJ)) { ?>

					<b><?php echo $count ;?>. <?php echo $question->question ;?></b>
					<input type="hidden" name="ques<?php echo $count ;?>" value="<?php echo $question->question ;?>">
					<input type="text" class="form-control" placeholder="type your answer..." name="question<?php echo $count ;?>">

					<?php $_SESSION['questions'] = $count; $count++; ?>
					<br>
					<?php } ?>

					<center>
						<button type="submit" class="btn btn-info" name="app_send">
							<span>Trimite aplicatia</span>
						</button>
					</center>

				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Salut, <?php echo this::getData('admins', 'auth', $_SESSION['user']) ?>!</h5>
			</div>

			<div class="card-body">
			  	<?php
					$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 9");
					$q->execute();
					$update = $q->fetch(PDO::FETCH_OBJ);
			  	?>

			  	<?php if(isset($_GET['edit'])) { ?>
			  		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<form method="post">
						<textarea name="topicinfo" class="form-control" rows="16" required><?php echo $purifier->purify(this::xss_clean($update->Topic)); ?></textarea>
						<script>CKEDITOR.replace('topicinfo');</script>
						<br>
						<input type="submit" name="updateadmininfo" value="Update informations" class="btn btn-info pull-right"/>
					</form>
					<?php } else { ?>
						<?php redirect::to('suggestions'); ?>
					<?php } ?>
				<?php } else { ?>
					<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<a href="<?php echo this::$_PAGE_URL ?>requestadmin/apply?edit" class="btn btn-info btn-xs pull-right">edit</a>
					<?php } ?>

					<?php echo $purifier->purify(this::xss_clean($update->Topic)) ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php } else if(this::$_url[1] == "viewapp") { ?>

<?php
if(!user::isLogged()) {
	this::sweetalert("Error!", "Trebuie sa fi logat!", "error");
	redirect::to('requestadmin'); return 1;
} ?>

<?php
	$qz = connect::$g_con->prepare('SELECT * FROM `panel_applications` WHERE `id` = ?');
	$qz->execute(array(this::$_url[2]));
	$view = $qz->fetch(PDO::FETCH_OBJ);
?>

<?php
if(isset($_POST['retrageaplicatia'])) {
	$q = connect::$g_con->prepare("SELECT * FROM `panel_applications` WHERE `UserID` = ? AND `Status` = 0");
	$q->execute(array($_SESSION['user']));
	$ro = $q->fetch(PDO::FETCH_OBJ);
	if(isset($_SESSION['user']) && $_SESSION['user'] == $ro->UserID)
	{
	    $q = connect::$g_con->prepare('DELETE FROM `panel_applications` WHERE `id` = ?');
	    $q->execute(array(this::$_url[2]));

		this::sweetalert("Success!", "Ai retras aplicatia ID: #".this::$_url[2]." cu succes!", "success");
		redirect::to('requestadmin'); return 1;
	} else { 
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to(''); return 1;
	}
}

if(isset($_POST['acceptaaplicatia'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 1,`ActionBy` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), this::$_url[2]));

		this::sweetalert("Success!", "Ai acceptat aplicatia ID: #".this::$_url[2]." cu succes!", "success");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	} else { 
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to(''); return 1;
	}
}

if(isset($_POST['redeschideaplicatia'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 0,`ActionBy` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), this::$_url[2]));

		this::sweetalert("Success!", "Ai redeschis aplicatia ID: # ".this::$_url[2]." cu succes!", "success");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	} else { 
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to(''); return 1;
	}
}

if(isset($_POST['respingeaplicatia'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('UPDATE `panel_applications` SET `Status` = 2,`ActionBy` = ?, `Motiv` = ? WHERE `id` = ?');
		$q->execute(array(this::getData('admins', 'auth', $_SESSION['user']), $purifier->purify(this::xss_clean($_POST['motivrespingere'])), this::$_url[2]));

		this::sweetalert("Success!", "Ai respins aplicatia ID: # ".this::$_url[2]." cu succes!", "success");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	} else { 
  		this::sweetalert("Error!", "Nu poti face asta.", "error");
  		redirect::to(''); return 1; 
  	}
}

if(isset($_POST['submitcomment'])) {
	if(isset($_SESSION['user']) && $view->Status == 0) {
		$q = connect::$g_con->prepare('INSERT INTO `panel_reply_suggestions` (`replyPlayerID`, `replyAdminID`, `replyText`) VALUES (?, ?, ?)');
		$q->execute(array($_SESSION['user'], $view->id, $purifier->purify(this::xss_clean($_POST['comentariu']))));

		this::sweetalert("Success!", "Your reply was posted with successfully!", "success");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	} else {
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
		return 1;
	}
}

if(isset($_POST['removecomment'])) {
	if($_SESSION['user'] == $view->UserID || this::getData('admins', 'Admin', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('DELETE FROM  `panel_reply_admin_topics` WHERE `replyID` = ?');
		$q->execute(array($_POST['removecomment']));

		this::sweetalert("Success!", "Comment was removed!", "success");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;

	} else {
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	}
}

if(isset($_POST['editcomment']))
{
	if($_SESSION['user'] == $view->UserID || this::getData('admins', 'Admin', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('UPDATE `panel_reply_admin_topics` SET `replyText` = ? WHERE `replyID` = ?');
		$q->execute(array($purifier->purify(this::xss_clean($_POST['newcomment'])), $_POST['editcomment']));

		this::sweetalert("Success!", "Comment was edited with successfully!", "success");
        redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;

	} else {
		this::sweetalert("Error!", "Nu poti face asta.", "error");
		redirect::to('requestadmin/viewapp/'.$view->id.''); return 1;
	}
}
?>

<div class="row">
    <div class="col-md-4">
    	<div class="card">
    		<div class="card-header">
			    Application Creator
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
					<p class="m-0">
						<?php if($view->Status == 0) { ?>
						<label class="badge bg-primary" style="font-size:12px;">aplicatie noua</label>
						<?php } else if($view->Status == 1) { ?>
						<label class="badge bg-success" style="font-size:12px;">aplicatie acceptata</label>
						<?php } else if($view->Status == 2) { ?>
						<label class="badge bg-danger" style="font-size:12px;">aplicatie respinsa</label>
						<?php } ?>
						<label class="badge bg-info" style="font-size:12px;"><?php echo $view->Date ?></label>
						<br>
						<?php if($view->Status == 1) { ?>
						<b><?php echo this::getData('admins', 'auth', $view->UserID) ?> a fost acceptat catre <?php echo $view->ActionBy ;?></b>
						<?php } else if($view->Status == 2) { ?>
						<b><?php echo this::getData('admins', 'auth', $view->UserID) ?> a fost respins de catre <?php echo $view->ActionBy ;?> pe motiv: <?php echo $view->Motiv ;?></b>
						<?php } ?>
					</p>
				</div>
			</div>
			<div class="card-footer" align="center">
				Application created date: <?php echo $view->Date ?>
			</div>
		</div>

		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
		<div class="card">
    		<div class="card-header">
			    Admin Tools
			</div>
			<div class="card-body">
				<form method="post">
				<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
					<?php if($view->Status == 0) { ?>
					<button type="submit" class="btn btn-success btn-block" name="acceptaaplicatia" style="margin-right: 5px; font-size:12px;">Accepta aplicatia</button>
					<button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#respingeapp" style="margin-right: 5px; font-size:12px;">Respinge aplicatia</button>
					<?php } if($view->Status == 1) { ?>
					<button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#respingeapp" style="margin-right: 5px; font-size:12px;">Respinge aplicatia</button>
					<?php } if($view->Status == 2) { ?>
					<button type="submit" class="btn btn-success btn-block" name="redeschideaplicatia" style="margin-right: 5px; font-size:12px;">Redeschide aplicatia</button>
					<button type="submit" class="btn btn-success btn-block" name="acceptaaplicatia" style="margin-right: 5px; font-size:12px;">Accepta aplicatia</button>
					<?php } ?>
				<?php } ?>
				</form>
				<div id="respingeapp" class="modal fade show" tabindex="-1" role="dialog">
				    <div class="modal-dialog modal-small">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title">De ce respingi aplicatia?</h4>
				                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="respingeapp" role="tabpanel">
				                    <form method="post">
										<h5>Motivul respingerii aplicatiei:</h5>
										<input class="form-control" type="text" name="motivrespingere" placeholder="motivul respingerii" required>
										<br><br>
										<button type="submit" class="btn btn-danger btn-flat" name="respingeaplicatia" style="margin-right: 5px; font-size:12px;">Respinge aplicatia</button>
						            </form>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<?php }?>
	</div>

	<div class="col-md-8">
    	<div class="card">
    		<div class="card-header">
			    Application Questions
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
			    Application Comments
			</div>
			<div class="card-body">
		        <ul class="list-unstyled">
				<?php
				$q = connect::$g_con->prepare("SELECT * FROM `panel_reply_admin_topics` WHERE `replyAdminID` = ? ORDER BY replyID ASC");
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