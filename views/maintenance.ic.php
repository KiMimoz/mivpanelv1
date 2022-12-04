<script src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>

<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
<div class="card">
	<div class="card-body">
		<?php
			if(isset($_POST['updatetopicinfo'])) {
				if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
					$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 6");
					$q->execute(array($purifier->purify(this::Protejez($_POST['topicinfo']))));
					redirect::to('maintenance');
				}
			}

			$q = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 6");
			$q->execute();
			$update = $q->fetch(PDO::FETCH_OBJ);
	  	?>

		<?php if(isset($_GET['edit'])) { ?>
	  		<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
			<form method="post">
				<textarea name="topicinfo" id="topicinfo" class="form-control" rows="20" required><?php echo $purifier->purify(this::Protejez($update->Topic)); ?></textarea>
				<script>CKEDITOR.replace('topicinfo');</script>
				<br>
				<input type="submit" name="updatetopicinfo" value="Update informations" class="btn btn-info pull-right"/>
			</form>
			<?php } else { ?>
				<?php redirect::to('tickets'); ?>
			<?php } ?>
		<?php } else { ?>
			<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
			<a href="<?php echo this::$_PAGE_URL ?>maintenance?edit" class="btn btn-info btn-xs pull-right">edit informations</a>
			<?php } ?>

			<?php echo $purifier->purify(this::Protejez($update->Topic)); ?>
		<?php } ?>
	</div>
</div>

<?php } else { ?>
	<?php redirect::to(''); ?>
<?php } ?>