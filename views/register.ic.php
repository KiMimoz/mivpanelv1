<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php if(user::isLogged()) { redirect::to(''); return 1; } ?>

<?php
	if(isset($_POST['createaccount']))
	{
		$q = connect::$g_con->prepare("INSERT INTO `admins` (`auth`, `password`, `access`, `flags`, `email`, `IP`, `LastIP`) VALUES (?, ?, 'z', 'a', ?, ?, ?);");

		$q->bindParam(1, $purifier->purify(this::xss_clean(this::clean($_POST['nume']))));
		$q->bindParam(2, $purifier->purify(this::xss_clean(this::clean($_POST['parola']))));
		$q->bindParam(3, $purifier->purify(this::xss_clean(this::clean($_POST['email']))));
		$q->bindParam(4, $_SERVER['REMOTE_ADDR']);
		$q->bindParam(5, $_SERVER['REMOTE_ADDR']);

		$ip = connect::$g_con->prepare("SELECT `IP` FROM `admins` WHERE `IP` = '".$_SERVER['REMOTE_ADDR']."'");
		$ip->execute();

		$find = $ip->rowCount();

		if($find) {
			print '<div class="alert alert-danger">Error: Your IP is already registered on the server.</div>';
		} 
		else 
		{
			$user = connect::$g_con->prepare("SELECT `auth` FROM `admins` WHERE `auth` = '".$_POST['nume']."'");
			$user->execute();

			$find = $user->rowCount();

			if($find) {
				print '<div class="alert alert-danger">Error: This nickname is already reserved on the server.</div> <meta http-equiv="refresh" content="6; url=register">';
			} else {
				$q->execute();
				$_SESSION['msg'] = '<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
					You successfully reserved your nickname. Before connecting on the server, type in console <b>setinfo _pw "your password"</b>.
					</div>';
				redirect::to('register'); return 1;
			}
		}
	}
?>

<div id="termsandconditions" class="modal fade show" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Terms and Conditions</h4>
				<a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
			</div>
			<div class="modal-body">
        		<p>
        Terms "csstats.eu" and "service/services" represent services offered through website <b><font color="gold">ploiesti.csstats.eu</font></b> 
		and CS.16 server <b><font color="gold">Ploiesti.Laleagane.Ro</font></b>.
        </p>
        <ul>
        <li>All donations are final and refund will be possible only if we cannot deliver the service which you donated for.</li>
		<li>Please read carefully details about the service you want to donate for and understand what you'll receive.</li>
        <li>If you donate for one service, this doesn't means that you can break our rules. Rules must be respected by all players/admins. 
		You can check our <b><a href="https://laleagane.ro/forum/threads/eng-server-rules-admins-players.843126/#post-5518464" target="_blank">rules HERE</a></b></li>
        <li>If we delivered the service according to the donation and after it is used the option of "<b>refund</b>" on paypal, this will be clearly lead
		to a remove of the services and ban on the server/web</li>
        <li>If a player made a donation and is caught <b><font color="red">cheating</font></b> on server, he/she will receive 
		<b><font color="red">permanent ban</font></b>. <b><font color="red">No refund</font></b> will be possible in this case.</li>
        </ul>
		<hr>
        <h3>Privacy policy</h3>
        Information collected from users are limited to email address and IP. This is ensure the safety of their account.<br/>
        <br><br>
        <b>Cookies</b><br><br>
        Our website use cookies to improve users experience. 
        <br><br>
        <b>Protection of collected informations</b><br><br>
        We ensure you that we do our best to protect your data and we will never give these information to third parties, without your prior consent
        We do not sell, exchange or rent your informations to third parties.
        <br><br>
        <b>Third party websites</b><br><br>
        Users can find advertisements on our website, or content which includes links to other websites. 
		We try to control as much as possible what contect we promote on our website or server, but we are not responsible about what you'll find there.<br><br>
        <b>Changes and Updates</b><br><br>
        We can modify anytime privacy policy. Anyway, when this will happen, all users will receive a notification email with this change and requested to agree 
		with. Otherwise, access on our website can be restricted.<br><br>
		Using this site and registering on our database, means your consent and approval for our terms.
			</div>
		</div>
	</div>
</div>
<div align="center">
    <div class="login-box">
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <center><h3><b>REGISTER</b></h3></center>
            </div>
            <div class="card-body">
                <form method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="nume" placeholder="Nick" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-user-shield"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="parola" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa-solid fa-key"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa-solid fa-envelope-circle-check"></span>
                        </div>
                    </div>
                </div>
                <br>
				<div align="left" class="icheck-primary">
					<input type="checkbox" id="agreeTerms" required>
					<label for="agreeTerms">
					Please read the <a href="#" data-toggle="modal" data-target="#termsandconditions"> rules here</a> then tick the box.
					</label>
				</div><br>
                <div class="row">
                    <div class="col-4">
                        <button type="submit" name="createaccount" class="btn btn-primary btn-block toastrSuccess">Register</button>
                    </div>
                </div>
                </form><br>
                <p class="mb-1" align="left">
                <a href="<?php echo this::$_PAGE_URL ?>login">You already have an account? Log In</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script> 
    document.addEventListener('contextmenu', event=> event.preventDefault()); 
    document.onkeydown = function(e) { 
        if(event.keyCode == 123) { 
            return false; 
        } 
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ 
            return false; 
        } 
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ 
            return false; 
        } 
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ 
            return false; 
        } 
    } 
</script>