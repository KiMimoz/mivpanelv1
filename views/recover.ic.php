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
    $ipaddress = getenv("REMOTE_ADDR") ; 
    $now = date("Y-m-d H:i:s");

if(isset($_POST['submit'])) {
  if(!$_POST['recover']) {
    echo '<div class="alert alert-block alert-danger">Complete the field.</div>';
  } else {
    $q = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `auth` = ?');
    $q->execute(array($_POST['recover']));
    if(!$q->rowCount()) {
      echo '<div class="alert alert-block alert-danger">This account does not exist in the database!</div>';
    }
    else {
      while($row = $q->fetch(PDO::FETCH_OBJ)) {
        $d = connect::$g_con->prepare('SELECT * FROM `panel_recovery` WHERE `name` = ?');
        $d->execute(array($_POST['recover']));
        if($d->rowCount()) {
          echo '<div class="alert alert-block alert-danger">You have already an active "account recovery" request!</div>';
        }
        else if($row->email == "") {
          echo '<div class="alert alert-block alert-danger">This account does not have and valid email address!</div>';
        }
        else {
          $email = $row->email;
          $user = $row->auth;

          $first = md5(uniqid());
          $final_key = $first . md5($first);
          
          $s = connect::$g_con->prepare("INSERT INTO panel_recovery (RecoverKey, name, email) VALUES (?, ?, ?)");
          $s->execute(array($final_key, $user, $email));

          $mail = new PHPMailer;
          $mail->setFrom('no-replay@ploiesti.csstats.eu', 'Ploiesti.LaLeagane.Ro');
          $mail->addAddress($email);
          $mail->Subject = 'ploiesti.laleagane.ro - account recovery / recuperare cont';
          $mail->Body    = "$user,
		  You received this email because you requested password reset on Ploiesti.Laleagane.Ro
		  If you don't want to change your password, you can ignore/delete this email.
		  If you didn't request to change your admin password, please ignore this email and don't click on the link below.
		  To change your password, please click on this link:
		  ".this::$_PAGE_URL."checkpwkey/".$final_key."
		  
		  
      Ai primit acest email pentru ca ai solicitat resetarea parolei pe serverul Ploiesti.Laleagane.Ro
      Daca nu doresti sa iti schimbi parola, poti ignora/sterge acest email.
		  Daca nu tu ai solicitat schimbarea parolei, te rog sa ignori acest mail si sa nu dai click pe acest link. 
      Pentru a-ti schimba parola, da click pe link-ul de mai jos: 
      ".this::$_PAGE_URL."checkpwkey/".$final_key."


          Cu stima,
          Echipa Ploiesti
          Website: https://laleagane.ro/ploiesti
          Forum: https://laleagane.ro/forum/forums/ploiesti.43416/
          Discord: https://discord.gg/5JnS9P9Yfb";
          if(!$mail->send()) {
              echo 'Email could not be sent.';
          } else {
            $email1 = explode('@', $email);       
            $first_part = $email1[0];         
            $domain = $email1[1];
            $newemail = substr($first_part, 0, 4) . "****@" . substr($domain, 0, 10);
            echo "<div class='alert alert-block alert-success'><b>Success!</b> The verification code was sent to ". $newemail ."!<br />Please check your email. Also check the Spam directory!</div> <meta http-equiv='refresh' content='6; url=recover'>";

            $logwho = $_SERVER['REMOTE_ADDR'];
            $logresult = " requested password change";

            $insertlog = get::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
            $insertlog->execute(array($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR']));
          }
        }     
      }
    }
  }
}
?>
<div align="center">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <center><h3><b>RECOVER PASSWORD</b></h3></center>
            </div>
            <div class="card-body">
                <form method="post">
                  <div class="form-group form-material floating" data-plugin="formMaterial">
                    <input type="text" class="form-control" name="recover" required>
                    <label class="floating-label">Write your username</label>
                  </div>
                  <button type="submit" name="submit" class="btn btn-primary">Recover Password</button>
                </form>
            </div>
        </div>
    </div>
</div>