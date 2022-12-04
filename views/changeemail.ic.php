<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(!user::isLogged()) { redirect::to(''); return 1; } ?>

<?php
if(isset($_POST['submit'])) {
	if(!$_POST['email'] || !$_POST['password']) {
		echo '<div class="alert alert-block alert-danger">Email invalid.</div>';
	} else {
		$q = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `auth` = ? AND `password` = ?');
		$q->execute(array(this::getData('admins','auth',$_SESSION['user']),$_POST['password']));
		while($row = $q->fetch(PDO::FETCH_OBJ))
		if($q->rowCount()) {
			$d = connect::$g_con->prepare('SELECT * FROM `panel_checkemail` WHERE `name` = ?');
			$d->execute(array(this::getData('admins','auth',$_SESSION['user'])));
			if($d->rowCount()) {
				echo '<div class="alert alert-block alert-danger">Ai deja o cerere pentru schimbarea emailului activa!</div>';
			}
			else {
				if($row->email == "email@yahoo.com") {					
					$email = htmlspecialchars($_POST['email']);
				}
				else {
					$email = $row->email;
				}
				$user = $row->auth;

				$first = md5(uniqid());
				$final_key = $first . md5($first);
				
				$s = connect::$g_con->prepare("INSERT INTO panel_checkemail (ChangeMailKey, name, email) VALUES (?, ?, ?)");
				$s->execute(array($final_key, $user, $_POST['email']));
				
$mail = new PHPMailer;
$mail->setFrom('no-reply@ploiesti.csstats.eu', 'Ploiesti.LaLeagane.Ro');
$mail->addAddress($email);
$mail->Subject = 'ploiesti.laleagane.ro - cerere de schimbare email';
$mail->Body    = "Salutare $user,
You received this email because you requested email change on Ploiesti.LaLeagane.Ro
To connect ". $_POST['email'] ." with your account, please click on the link below:
".this::$_PAGE_URL."checkemailkey/".$final_key."

Ai primit acest email pentru ca ai solicitat schimbarea emailului pe serverul Ploiesti.LaLeagane.Ro
Pentru a avea emailul ". $_POST['email'] ." asociat cu contul tau, da click pe link-ul de mai jos:
".this::$_PAGE_URL."checkemailkey/".$final_key."


Cu stima,
Echipa Ploiesti
Website: https://laleagane.ro/ploiesti
Forum: https://laleagane.ro/forum/forums/ploiesti.43416/
Discord: https://discord.gg/5JnS9P9Yfb";
if(!$mail->send()) {
echo 'Email could not go through!';
}
else {
echo "<div class='alert alert-block alert-success'>An email was sent to ". $email .".<br />
In that email you will find a link to click on, in order to confirm you email change associated with your account.
Email will arrive in few minutes. Please check also SPAM folder.


In acel email vei gasi un link pe care va trebui sa dai click pentru a confirma schimbarea adresei de email asociata cu contul tau.<br /><br />
Emailul va ajunge la tine in cateva minute in casuta ta de email.<br />
Daca nu ai primit emailul, verifica si directorul spam.</div>";

$logwho = this::getData('admins','auth',$_SESSION['user']);
$logresult = " requested email change";

$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));
}
}
}
		else echo '<div class="alert alert-block alert-danger">Email invalid!</div>';
	}
} ?>
 
<div class="card">
	<div class="card-header">
		<h5><i class="fa fa-envelope"></i> Change your email address</h5>
	</div>
	<div class="card-body">
		<p><b>ATTENTION !!!</b><br>
		It is forbidden to sell your account or to give it to other persons.<br>
		Once you submit this form, you will not have anymore access to your account, if you don't have access to the new email.<br>
		If your account is compromised please inform <b>maNIa</b> as soon as possible.</p>
		<hr>

		<form method="POST" action="" accept-charset="UTF-8">
		New email:<br />
		<input placeholder="type your new email address" name="email" class="form-control" style="width:300px;" type="email"><br />
		Password:<br />
		<input placeholder="type your password" name="password" type="password" class="form-control" style="width:300px;"><br/><br />
		<button type="submit" name="submit" class="btn btn-primary"><font size="4">Change Email</font></button>
		</form>
	</div>
</div>