<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php 
$news = connect::$g_con->prepare('SELECT * FROM `panel_news` ORDER BY id DESC');
$news->execute();

if(isset($_POST['adauganews'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q = connect::$g_con->prepare('INSERT INTO `panel_news` (`title`, `text`, `admin`) VALUES (?, ?, ?)');
		$q->execute(array($purifier->purify(this::Protejez($_POST['updatetitle'])), $purifier->purify(this::Protejez($_POST['updatetext'])), $_SESSION['user']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " added a new news";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('news');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
            <b><i class="fa fa-check-circle"></i> Success</b> new news was added successfully!
        </div>';
	}
}

if(isset($_POST['editnews'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q1 = connect::$g_con->prepare('UPDATE `panel_news` SET `title` = ? , `text` = ? WHERE `id` = ?');
		$q1->execute(array($purifier->purify(this::Protejez($_POST['edittitleupdate'])), $purifier->purify(this::Protejez($_POST['edittextupdate'])), $_POST['editnews']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " edited news";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('news');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
            <b><i class="fa fa-check-circle"></i> Success</b> This news was edited successfully!
        </div>';
	}
}

if(isset($_POST['deletenews'])) {
	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
		$q2 = connect::$g_con->prepare('DELETE FROM `panel_news` WHERE `id` = ?');
		$q2->execute(array($_POST['deletenews']));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
        $logresult = " removed news";

        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
		
		redirect::to('news');
		$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
            <b><i class="fa fa-check-circle"></i> Success</b> This news was removed successfully!
        </div>';
	}
}

?>

<script src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>


<div class="card">
	<div class="card-header">
        <h4><i class="fa fa-cogs" aria-hidden="true"></i> News</h4>
		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
			<button type="submit" data-toggle="modal" data-target="#add-news" class="btn btn-outline-success float-right">
			<i class="fa fa-plus"></i> Add News
			</button>
		<?php } ?>
	</div>
    <div class="card-body">
	    <?php while($shownews = $news->fetch(PDO::FETCH_OBJ)) { ?>
	    <div class="card">
		<a href="#" data-toggle="modal" data-target="#shownews<?php echo $shownews->id ?>" class="list-group-item">
		<span style="font-size:20px;"><?php echo $shownews->title ?></span>
		<br>
		Posted by <?php echo this::getData('admins', 'auth', $shownews->admin) ?> - <?php echo this::timeAgo($shownews->date); ?>
		</a>
		</div>
		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
		<div class="col-md-6">
		<button type="submit" data-toggle="modal" data-target="#edit-news<?php echo $shownews->id ?>" value="<?php echo $shownews->id ?>" class="btn btn-primary btn-sm" style="width:125px; margin-right: 10px;">
    		<i class="fa fa-edit"></i> edit
    	</button>
		<button type="submit" data-toggle="modal" data-target="#remove-news<?php echo $shownews->id ?>" value="<?php echo $shownews->id ?>" class="btn btn-danger btn-sm" style="width:125px;">
    		<i class="fa fa-trash"></i> remove
    	</button>
    	</div>
		<?php } ?>
		<hr>

<div id="shownews<?php echo $shownews->id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title">Articles <?php echo $shownews->title ?></h4>
            </div>
            <div class="modal-body">
                <div class="tab-pane active" id="shownews<?php echo $shownews->id ?>" role="tabpanel">
                    <?php echo $shownews->text ?>
                </div>
            </div>
		</div>
	</div>
</div>

<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
<div id="edit-news<?php echo $shownews->id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title">edit article<br/> <font color="gold"><?php echo $shownews->title ?></font></h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" align="center">
                <div class="tab-pane active" id="edit-news<?php echo $shownews->id ?>" role="tabpanel">
                    <form action="" method="POST">
					<textarea name="edittitleupdate" id="edittitleupdate" class="form-control" rows="1"><?php echo $purifier->purify(this::Protejez($shownews->title)) ; ?></textarea><br>
					<textarea name="edittextupdate" id="edittextupdate<?php echo $shownews->id ?>" tabindex='-1' class="form-control" rows="10"><?php echo $purifier->purify(this::Protejez($shownews->text)); ?></textarea>
					<script>CKEDITOR.replace('edittextupdate<?php echo $shownews->id ?>');</script>
					<p></p>
					<button name="editnews" value="<?php echo $shownews->id ?>" class="btn btn-primary waves-effect waves-light">
			    		<i class="fa fa-edit"></i> SUBMIT
			    	</button>
					</form>
                </div>
            </div>
		</div>
	</div>
</div>
<?php } ?>

<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
<div id="remove-news<?php echo $shownews->id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title">Are you sure you want to remove article <br/><font color="gold"><?php echo $shownews->title ?></font> ?</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" align="center">
                <div class="tab-pane active" id="remove-news<?php echo $shownews->id ?>" role="tabpanel">
                    <form method='post'>
			    	<button name="deletenews" value="<?php echo $shownews->id ?>" class="btn btn-danger waves-effect waves-light">
			    		Yes, i'm sure!
			    	</button>
				    </form>
                </div>
            </div>
		</div>
	</div>
</div>
<?php } ?>
<?php } ?>

<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
<div id="add-news" class="modal fade" tabindex="-1" role="dialog" aria-labellethisy="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title">Add new article</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" align="center">
                <div class="tab-pane active" id="add-news" role="tabpanel">
                    <form action="" method="POST">
                    <textarea name="updatetitle" id="updatetitle" class="form-control" placeholder="article title" rows="1"></textarea><br>
                    <textarea name="updatetext" id="updatetext" class="form-control" placeholder="update content" rows="10"></textarea>
                    <script>CKEDITOR.replace('updatetext');</script>
                    <p></p>
                    <button name="adauganews" value="add news" class="btn btn-success waves-effect waves-light">
                        SUBMIT
                    </button>
                    </form>
                </div>
            </div>
		</div>
	</div>
</div>
<?php } ?>

	</div>
</div>