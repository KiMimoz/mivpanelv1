<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(user::isLogged()) {
  redirect::to(''); return 1;
}
?>
<?php 
	$q = connect::$g_con->prepare('SELECT * FROM `panel_recovery` WHERE `RecoverKey` = ?'); 
	$q->execute(array(this::$_url[1]));
	if($row = $q->fetch(PDO::FETCH_OBJ)) {
	if(isset($_POST['submit'])) {
		if(!$_POST['password']) {
			echo '<div class="alert alert-block alert-danger">Complete the field.</div>';
		} else {
		$q = connect::$g_con->prepare('UPDATE `admins` SET `password` = ? WHERE `auth` = ?');
		$q->execute(array(htmlspecialchars($_POST['password']),$row->name));
		
		$dele = connect::$g_con->prepare("DELETE FROM panel_recovery WHERE RecoverKey='".this::$_url[1]."'");
		$dele->execute();

		$logresult = " ".$row->name." confirmed password change.";

		$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logresult.'", "0", ?)');
		$insertlog->execute(array($_SERVER['REMOTE_ADDR']));
		
		$_SESSION['msg'] = '<div class="alert alert-block alert-success" role="alert">
	        <b>Your password was changed with successfully!</b><br/>
	        Your new password is '. htmlspecialchars($_POST['password']) .'</div>';
	        redirect::to('login'); return 1;
	}
}
?>

<div class="alert alert-success">
<h4>Your recovery key was confirmed with successfully!</h4>
You can now chooose a new password!
</div>
<div align="center">
    <div class="login__block active" id="l-login">
        <div class="login__block__header">
            <i class="zmdi zmdi-account-circle"></i>
            Hello, please write your new password.
        </div>

        <div class="login__block__body">
            <form method="POST" action="" accept-charset="UTF-8" class="form-group">
                <div class="form-group">
                    <input placeholder="Type your new password here.." name="password" class="form-control" type="text" value="" id="password">
                </div>
                <button type="submit" name="submit" class="btn btn-dark">Change password</button>
            </form>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="alert alert-danger">
<h4>Invalid recovery key.</h4>
The recovery link is invalid. Please try again.<br/>
The links expire after 2 days. You can request a new password recovery <a href="<?php echo this::$_PAGE_URL; ?>recover">here</a>.
</div>
<?php } ?>
