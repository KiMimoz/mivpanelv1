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
if(isset($_POST['autentificama']) && !user::isLogged()) {
    $que = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `auth` = ? AND `password` = ?');
    $que->execute(array($_POST['your_name'],$_POST['your_password']));
    if($que->rowCount()) {
        $inter = $que->fetch(PDO::FETCH_OBJ);
        if(this::getSpec("panel_settings","IPLoginVerify","ID",1)) {
            if($inter->IP == $_SERVER['REMOTE_ADDR']) {
                $_SESSION['user'] = $inter->id;
            } 
            else {
            $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
                    <b><i class="fa fa-exclamation-triangle"></i> Your IP is blocked!</b><br>
                    Your IP is not the same as last IP registered on the server.<br>
                    First login into the game, before to login on here.
                </div>'; redirect::to('login'); return 1;
            }
        }
        else {
          //$updateip = connect::$g_con->prepare('UPDATE `admins` SET IP = ? WHERE `id` = ?');
          //$updateip->execute(array($_SERVER['REMOTE_ADDR'], $inter->id)); <button type="button" class="btn btn-success toastrSuccess">

          $_SESSION['user'] = $inter->id;
          $_SESSION['msg'] = '<div class="alert alert-success" role="alert">
            <a type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&#9447;</span> </a>
            <b><i class="fa fa-check-circle"></i> Success</b> You logged in successfully!
            </div>'; redirect::to(''); return 1;
        }
    }
    else {
        $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
            <b><i class="fa fa-exclamation-triangle"></i> Invalid login credentials.</b>
          </div>'; redirect::to('login'); return 1;
      }
  }
?>
<div align="center">
    <div class="login-box">
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <center><h3><b>LOGIN</b></h3></center>
            </div>
            <div class="card-body">
                <form method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="your_name" placeholder="Nick" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-user-shield"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="your_password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa-solid fa-key"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <button type="submit" name="autentificama" class="btn btn-primary btn-block toastrSuccess">Log In</button>
                    </div>
                </div>
                </form><br/>
                <p class="mb-1" align="left">
                <a href="<?php echo this::$_PAGE_URL ?>recover">I forgot my password (RECOVER)</a>
                </p>
                <p class="mb-0" align="left">
                <a href="<?php echo this::$_PAGE_URL ?>register" class="text-center">Register a new account</a>
                </p>
            </div>
        </div>
    </div>
</div>