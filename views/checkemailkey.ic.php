<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(!user::isLogged()) { redirect::to(''); return 1; } ?>

<?php
	$q = connect::$g_con->prepare('SELECT * FROM `panel_checkemail` WHERE `ChangeMailKey` = ?');
	$q->execute(array(this::$_url[1]));
	if($row = $q->fetch(PDO::FETCH_OBJ)) {

	$q = connect::$g_con->prepare('UPDATE `admins` SET `email` = ? WHERE `auth` = ?');
	$q->execute(array($row->email, $row->name));

	$dele = connect::$g_con->prepare("DELETE FROM panel_checkemail WHERE ChangeMailKey='".this::$_url[1]."'");
	$dele->execute();
?>
<?php
$logwho = this::getData('admins','auth',$_SESSION['user']);
$logresult = " confirmed email change";

$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

$_SESSION['msg'] = '<div class="alert alert-success" role="alert">
	Adresa de email asociata acestui cont, a fost schimbata cu succes!
</div>'; redirect::to(''); return 1; ?>

<?php } else { ?>
<?php
$_SESSION['msg'] = '<div class="alert alert-danger" role="alert"><h4>Invalid Key!</h4>
	Link-urile expira dupa 2 zile sau cand emailul este schimbat.<br>
	Daca ai schimbat emailul recent sau ai primit emailul acum mai mult de 2 zile, incearca sa faci o solicitare noua.</div>';
	redirect::to(''); return 1; ?>
<?php } ?>
