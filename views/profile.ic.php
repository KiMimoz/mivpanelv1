<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php
	if(!isset(this::$_url[1])) redirect::to('');
	if(!isset(this::$_url[1]) && user::isLogged()) redirect::to('profile/'.auth::user()->id.'');
	else $user = User::where('auth', this::$_url[1])->orWhere('id', (int) this::$_url[1])->first();
	$q = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `id` = ?');
	$q->execute(array(this::$_url[1]));


	if(!$q->rowCount()) {
	    echo '<div class="alert alert-danger">
			<a type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&#9447;</span> </a>
			<h3><i class="fa fa-exclamation-triangle"></i> This player does not exist!</h3>
			</div>'
		;
	    return;
	}
	$data = $q->fetch(PDO::FETCH_OBJ);
?>

<?php
    if(isset($_POST['setname'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `admins` SET `auth` = ? WHERE `auth` = ?');
			$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['nametext']))), $data->auth));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " changed name for player id: ";
	        $loglast = $data->id;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Numele tau a fost schimbat.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Numele jucatorului a fost schimbat cu succes!", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}
	
    if(isset($_POST['removeadmin'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `admins` SET `access` = "z", `Admin` = 0 WHERE `id` = ?');
			$q->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " remove ";
	        $loglast = $data->auth;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Ai fost inlaturat din functie.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Gradul jucatorului a fost schimbat cu succes!", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}

	if(isset($_POST['email_submit'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `admins` SET `email` = ? WHERE `auth` = ?');
			$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['email']))), $data->auth));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " changed email for player id: ";
	        $loglast = $data->id;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Email-ul tau a fost schimbat.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Email-ul jucatorului a fost schimbat cu succes!", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}

	if(isset($_POST['password_submit'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `admins` SET `password` = ? WHERE `auth` = ?');
			$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['password']))), $data->auth));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " changed password for player id: ";
	        $loglast = $data->id;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Parola ta a fost schimbata.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Parola jucatorului a fost schimbata cu succes!", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}

	if(isset($_POST['staffwarnup'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$w = connect::$g_con->prepare('UPDATE `admins` SET `warn` = `warn`+1 WHERE `id` = ?');
			$w->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " gived staff warn for player name: ";
	        $loglast = $data->auth;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Ai primit 1 Warn.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "I-ai dat Warn jucatorului.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}

	if(isset($_POST['staffwarndown'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$w = connect::$g_con->prepare('UPDATE `admins` SET `warn` = `warn`-1 WHERE `id` = ?');
			$w->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " removed staff warn for player name: ";
	        $loglast = $data->auth;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Ti-a fost scos 1 Warn.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "I-ai scos un Warn jucatorului.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}

	if(isset($_POST['resetawarn'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `admins` SET `warn` = 0 WHERE `id` = ?');
			$q->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
        	$logresult = " reseted admin warns for player name: ";
        	$loglast = $data->auth;

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Warnurile tale au fost resetate.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai resetat cu succes admin warn-urile acestui jucator.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
	    }
	}

    if(isset($_POST['setplayerfunction'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('INSERT INTO `panel_functions` (`funcPlayerID`, `funcColor`, `funcIcon`, `funcName`) VALUES (?, ?, ?, ?)');
			$q->execute(array($data->id, $purifier->purify(this::xss_clean(this::clean($_POST['functioncolor']))), $purifier->purify(this::xss_clean(this::clean($_POST['functionicon']))), $purifier->purify(this::xss_clean(this::clean($_POST['functionname'])))));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
        	$logresult = " setted function ";
        	$loglast = $purifier->purify(this::Protejez($_POST['functionname']));
        	$logfinal = " for player id ";
        	$finallog = $data->id;

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.''.$logfinal.''.$finallog.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'O functie ti-a fost setata.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai adaugat cu succes functia <b>".$_POST['functionname']."</b> acestui player.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
	    }
	}

    if(isset($_POST['removefunction'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('DELETE FROM `panel_functions` WHERE `funcID` = ?');
			$q->execute(array($_POST['removefunction']));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
        	$logresult = " removed function for player id: ";
        	$loglast = $data->id;

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'O functie ti-a fost scoasa.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai sters cu succes functia acestui player.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
	    }
	}

    if(isset($_POST['removeallfunctions'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('DELETE FROM `panel_functions` WHERE `funcPlayerID` = ?');
			$q->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
        	$logresult = " removed all functions for player id: ";
        	$loglast = $data->id;

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Toate functiile ti-au fost scoase.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai sters cu succes toate functiile acestui player..", "success");
			redirect::to('profile/'.$data->id.''); return 1;
	    }
	}
	if(isset($_POST['setfacebook'])) {
		$q = connect::$g_con->prepare('UPDATE `admins` SET `facebook` = ? WHERE `auth` = ?');
		$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['facebooktext']))), $data->auth));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
		$logresult = " changed facebook link for player id: ";
		$loglast = $data->id;

		$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

		$notif = 'Your Facebook link has been changed.';
		$link = $_SERVER['REQUEST_URI'];
		this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

		this::sweetalert("Success!", "Player's Facebook has been successfully changed!", "success");
		redirect::to('profile/'.$data->id.''); return 1;
	}
	
	if(isset($_POST['setinstagram'])) {
		$q = connect::$g_con->prepare('UPDATE `admins` SET `instagram` = ? WHERE `auth` = ?');
		$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['instagramtext']))), $data->auth));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
		$logresult = " changed instagram link for player id: ";
		$loglast = $data->id;

		$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
		$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

		$notif = 'Your Instagram link has been changed.';
		$link = $_SERVER['REQUEST_URI'];
		this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

		this::sweetalert("Success!", "Player's Instagram has been successfully changed!", "success");
		redirect::to('profile/'.$data->id.''); return 1;
	}

	if(isset($_POST['setcountry'])) {
		$q = connect::$g_con->prepare('UPDATE `admins` SET `Country` = ? WHERE `auth` = ?');
		$q->execute(array($purifier->purify(this::xss_clean(this::clean($_POST['edittara']))), $data->auth));

		$logwho = this::getData('admins','auth',$_SESSION['user']);
	    $logresult = " changed country for player id: ";
	    $loglast = $data->id;

	    $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	    $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

		$notif = 'Your Country has been changed.';
		$link = $_SERVER['REQUEST_URI'];
		this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

		this::sweetalert("Success!", "Country has been successfully changed!", "success");
		redirect::to('profile/'.$data->id.''); return 1;
	}
    if(isset($_POST['deleteaccount'])) {
    	if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
    		$q = connect::$g_con->prepare('DELETE FROM `admins` WHERE `id` = ?');
    		$q->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
			$logresult = " deleted account name: ";
			$loglast = $data->auth;

        	$insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
        	$insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			this::sweetalert("Success!", "Ai sters cu succes contul acestui jucator.", "success");
			redirect::to(''); return 1;
		}
	}
	if(isset($_POST['giveboss'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$w = connect::$g_con->prepare('UPDATE `admins` SET `Boss` = `Boss`+1 WHERE `id` = ?');
			$w->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " gived panel access for player name: ";
	        $loglast = $data->auth;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Ai primit Manager (full access) pe panel.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai acordat Manager Panel acestui jucator.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}
	if(isset($_POST['removeboss'])) {
		if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$w = connect::$g_con->prepare('UPDATE `admins` SET `Boss` = `Boss`-1 WHERE `id` = ?');
			$w->execute(array($data->id));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " removed panel access for player name: ";
	        $loglast = $data->auth;

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$notif = 'Ai fost scos din functia de Manager (full access) pe panel.';
			$link = $_SERVER['REQUEST_URI'];
			this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

			this::sweetalert("Success!", "Ai scos Manager Panel acestui jucator.", "success");
			redirect::to('profile/'.$data->id.''); return 1;
		}
	}
?>
<style type="text/css">
#photo_rotate img{
      -webkit-transition: all 0.3s ease;
      -moz-transition: all 0.3s ease;
      transition: all 0.3s ease;
      padding:3px;
}
#photo_rotate img:hover {
       border-radius: 0 0 0 0;
       -moz-transform: scale(1.2) rotate(720deg) ;
       -webkit-transform: scale(1.2) rotate(720deg) ;
       -o-transform: scale(1.2) rotate(720deg) ;
       -ms-transform: scale(1.2) rotate(720deg) ;
       transform: scale(1.2) rotate(720deg) ;
}
</style>
<?php
if (isset($_POST['change_avatar_btn'])) {
	$avatar = $_FILES['change_avatar'];

	$name = $data->id;

	if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss >= 1))) {
	    if(empty($avatar)) {
			this::sweetalert("Error!", "Nu fi stupid. Alege un fisier.", "error");
			redirect::to('profile/'.$data->id.''); return 1;
	    } else {
	        $fileName = rand(1000,100000)."-".rand(1000,100000).".png";
	        $check = getimagesize($avatar["tmp_name"]);
	        $maxsize = 2097152;
	        $image_width = $check[0];
	        $image_height = $check[1];

	        if ($avatar["error"] > 0) {
				this::sweetalert("Error!", "Eroare, fisierul nu este acceptat.", "error");
				redirect::to('profile/'.$data->id.''); return 1;
	        } else {
	            if ($image_width > '150' && $image_height > '250') {
					this::sweetalert("Error!", "Dimensiunea maxima este de 150x250.", "error");
					redirect::to('profile/'.$data->id.''); return 1;
	            } else {
	                if (($avatar['size'] >= $maxsize) || ($avatar["size"] == 0)) {
						this::sweetalert("Error!", "Dimensinuea maxima a fisierului nu trebuie sa depaseasca 2MB.", "error");
						redirect::to('profile/'.$data->id.''); return 1;
	                } else {
	                    $extension = pathinfo($avatar["name"], PATHINFO_EXTENSION);
	                    if ($extension == 'png') {
	                        if (move_uploaded_file($avatar["tmp_name"], "resources/avatars/" . $fileName)) {
	                        	if (this::check_avatar($name) != 'default.png') {
	                            	unlink("icons/avatars/".this::check_avatar($name)."");
		                            this::change_avatar($fileName, $name);
									this::sweetalert("Success!", "Avatar schimbat cu succes!", "success");
									redirect::to('profile/'.$data->id.''); return 1;
	                            } else {
									this::change_avatar($fileName, $name);
									this::sweetalert("Success!", "Avatar schimbat cu succes!", "success");
									redirect::to('profile/'.$data->id.''); return 1;
	                            }
	                        } else {
								this::sweetalert("Error!", "Fisierul nu a putut fi mutat, mai incearca odata!", "error");
								redirect::to('profile/'.$data->id.''); return 1;
	                        }
	                    } else {
							this::sweetalert("Error!", "Sunt permise doar imagini de tip PNG!", "error");
							redirect::to('profile/'.$data->id.''); return 1;
	                    }
	                }
	            }
	        }
	    }
	} else {
		this::sweetalert("Error!", "Nu poti face acest lucru unui alt utilizator.", "error");
		redirect::to('profile/'.$data->id.''); return 1;
	}
}
?>
<!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
					<h3 class="profile-username text-center"><font size="6"><?php echo $data->auth ?></font></h3>
					<center>
						<img src="../resources/avatars/<?php echo this::get_user_avatar_from_name($data->auth); ?>">
					</center>
                </div>
				<br>
                <ul class="list-group list-group-unbordered mb-3">
					<li class="list-group-item">
						<form method="post">
							<button type="submit" class="btn btn-primary btn-xs btn-block" name="refreshprofile">refresh profile</button>
						</form>
                  	<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
					<li class="list-group-item">
						<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#editavatar" onclick="$('#editavatar-modal').modal();">edit avatar</button>
                  	</li>
					<li class="list-group-item">
						<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#editcountry" onclick="$('#editcountry-modal').modal();">edit country</button>
                  	</li>
                  	<?php } ?>
                  	</li>
                  	<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
              		<li class="list-group-item">
						<?php if($data->Boss == 0) { ?>
						<button type="button" class="btn btn-primary btn-xs btn-block" data-toggle="modal" data-target="#manageboss" onclick="$('#manageboss-modal').modal();">set panel access</button>
						<?php } else { ?>
						<button type="button" class="btn btn-danger btn-xs btn-block" data-toggle="modal" data-target="#manageboss" onclick="$('#manageboss-modal').modal();">remove panel access</button>
						<?php } ?>
					</li>
					<?php }?>

                  	<li class="list-group-item">
                    	<b>Country</b>
                    	<a class="float-right">
                    		<?php
							$country = connect::$g_con->prepare("SELECT * FROM `countries` ORDER BY `ID` ASC");
							$country->execute();
							while($flag = $country->fetch(PDO::FETCH_OBJ)) {
								if($flag->Country == $data->Country) { echo ' <i class="flag-icon flag-icon-'.$flag->Flag.' mr-2"></i>'; }
							}
							?>
						</a>
                  	</li>
				  	<li class="list-group-item">
						<b>Social</b>
						<div class="float-right" id="photo_rotate">
							<a href="https://www.facebook.com/<?php echo $data->Facebook; ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/8h47n4P.png" width="25" height="25"></a>&nbsp;&nbsp;
							<a href="https://www.instagram.com/<?php echo $data->Instagram; ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/3R4TENj.png" width="25" height="25"></a>&nbsp;&nbsp;
							<a href="https://steamcommunity.com/profiles/<?php echo user::SteamStr2SteamId($data->SteamID); ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/nwmttsc.png" width="25" height="25"></a>&nbsp;&nbsp;
						</div>
					</li>
				</ul>
			</div>
            </div>
          </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
					<li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab"><i class="fa-solid fa-id-card"></i> Profile</a></li>
					<li class="nav-item"><a class="nav-link" href="#gametracker" data-toggle="tab"><i class="fa-solid fa-chart-line text-success"></i> Gametracker</a></li>
					<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
					<li class="nav-item"><a class="nav-link" href="#social" data-toggle="tab"><i class="fa-solid fa-thumbs-up"></i> Social</a></li>
					<?php } ?>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="profile">
                    <div class="card-header">
                <h3 class="card-title"><i class="fa-regular fa-id-card"></i> Main Informations</h3>
				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
                <div class="card-tools">
                    <button class="btn btn-danger btn-sm pull-right" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#removeaccount" onclick="$('#removeaccount-modal').modal();"><i class="fa fa-trash" aria-hidden="true"></i> Remove Account</button>  
                </div>
				<?php } ?>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <tbody>
                    <tr>
                      <td>Nickname:</td>
                      <td><font color="red" size="3"><?php echo $data->auth ?></font></td>
                      <td></td>
                      <td><?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
					  <button type="button" class="btn btn-success btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#changename" onclick="$('#changename-modal').modal();"><i class="fa fa-edit"></i> Change Nick</button>
					  <?php }?></td>
                    </tr>
					<tr>
                      <td>Grade:</td>
                      <td><font size="4"><?php
						$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY `groupAdmin` ASC");
						$groups->execute();
						while($function = $groups->fetch(PDO::FETCH_OBJ)) {
						if($function->groupAdmin == $data->Admin) { echo ' <span class="badge" style="background-color:'.$function->groupColor.'"><strong>'.$function->groupName.'</strong></span>';
							}
						}
					  ?></font></td>
                      <td></td>
                      <td><?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
	                  <button type="button" class="btn btn-success btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#manageadmin" onclick="$('#manageadmin-modal').modal();"><i class="fa fa-edit"></i> Change Grade</button>
	                  <?php }?></td>
                    </tr>
                    <tr>
                      <td>Tag:</td>
                      <td><?php
							$functii = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY `funcID` ASC");
							$functii->execute(array($data->id));
							while($badge = $functii->fetch(PDO::FETCH_OBJ)) {
								if($badge->funcPlayerID == $data->id) { echo '['.$badge->funcName.']';
							}
						}
                   		?></td>
                      <td></td>
                      <td><?php if(isset($_SESSION['user']) && this::getData('admins','Boss',$_SESSION['user']) >= 1) { ?>
					  <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#managefunctions" onclick="$('#managefunctions-modal').modal();"><i class="fa fa-edit"></i> Manage TAG</button>
					  <?php } ?></td>
                    </tr>
					<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
					<tr>
                      <td>Last IP:</td>
                      <td><font color="red"><?php echo $data->LastIP ;?></font></td>
                      <td></td>
                      <td></td>
                    </tr>
					<?php }?>
                    <tr>
                      <td>Warnings:</td>
                      <td><?php echo $data->warn; ?></td>
                      <td></td>
                      <td><form method="post">
						<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
						<button type="button" class="btn btn-success btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#givestaffwarns" onclick="$('#givestaffwarns-modal').modal();"><i class="fa fa-edit"></i> Add / Remove</button>
						<button type="submit" name="resetawarn" class="btn btn-danger btn-xs" value="<?php echo $data->id ?>"><i class="fa fa-history"></i> Reset</button>
						<?php }?>
						</form></td>
                    </tr>
					<tr>
                      <td>Last online:</td>
                      <td><?php echo $data->last_time; ?></td>
                      <td></td>
                      <td></td>
                    </tr>
					<tr>
                      <td>Email:</td>
                      <td><?php echo $data->email; ?></td>
                      <td></td>
                      <td><?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
						<button type="button" class="btn btn-success btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#changeemail" onclick="$('#changeemail-modal').modal();"><i class="fa fa-edit"></i> Change Email</button>
						<?php }?></td>
					</tr>
					<tr>
                      <td>Password:</td>
                      <td>************</td>
                      <td></td>
					  <?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
                      <td><button type="button" class="btn btn-success btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#changepassword" onclick="$('#changepassword-modal').modal();"><i class="fa fa-edit"></i> Change Password</button></td>
					  <?php }?>
                    </tr>
					
                  </tbody>
                </table>
              </div>
          	</div>
          	<div class="tab-pane" id="gametracker">
          		<div class="card-header">
          			<h3 class="card-title"><i class="fa fa-bar-chart text-success"></i> Gametracker Player Stats</h3>
          		</div>
          		<div class="card-body table-responsive p-0">
          			<table class="table table-hover text-nowrap">
          				<tbody>
          					<tr>
          						<td align="center" colspan="3"><a href="https://www.gametracker.com/player/<?php echo $data->auth ;?>/<?php echo this::$_SERVER_IP; ?>:27015/" target="_blank"><img src="http://cache.gametracker.com/player/<?php echo $data->auth ;?>/<?php echo this::$_SERVER_IP; ?>:27015/b_560x95.png" alt=""/></a><br/><br/>
								<b>Forum code to be used as signature:</b><br>
								<input type="textarea" style="width:600px" disabled value="[url=https://www.gametracker.com/player/<?php echo $data->auth ;?>/<?php echo this::$_SERVER_IP; ?>:27015/][img]http://cache.gametracker.com/player/<?php echo $data->auth ;?>/<?php echo this::$_SERVER_IP; ?>:27015/b_560x95.png[/img][/url]"></td>
							</tr>
								<th>Period</th>
								<th><center>Player Score</center></th>
								<th><center>Player Time (minutes)</center></th>
							<tr>
								<td>24 Hours:</td>
								<td align="center"><img src="https://cache.gametracker.com/images/graphs/player_score.php?nameb64=<?= base64_encode($data->auth) ?>%3D%3D&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1d"/></td>
								<td align="center"><img src="https://cache.gametracker.com/images/graphs/player_time.php?nameb64=<?= base64_encode($data->auth) ?>%3D%3D&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1d"/></td>
							</tr>
							<tr>
							  <td>7 Days:</td>
							  <td align="center"><img src="https://cache.gametracker.com/images/graphs/player_score.php?nameb64=<?= base64_encode($data->auth) ?>%3D%3D&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1w"/></td>
							  <td align="center"><img src="https://cache.gametracker.com/images/graphs/player_time.php?nameb64=<?= base64_encode($data->auth) ?>&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1w"/></td>
							</tr>
							<tr>
							  <td>30 Days:</td>
							  <td align="center"><img src="https://cache.gametracker.com/images/graphs/player_score.php?nameb64=<?= base64_encode($data->auth) ?>%3D%3D&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1m"/></td>
							  <td align="center"><img src="https://cache.gametracker.com/images/graphs/player_time.php?nameb64=<?= base64_encode($data->auth) ?>%3D%3D&host=<?php echo this::$_SERVER_IP; ?>:27015&start=-1m"/></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
          	<div class="tab-pane" id="social">
          		<div class="card-header">
          			<h3 class="card-title"><i class="fa fa-edit"></i></font> Edit Social Links</h3>
          		</div>
          		<div class="card-body table-responsive p-0">
          			<table class="table table-hover text-nowrap">
          				<thead>
          					<th></th>
          					<th align="right">Current profile link</th>
          					<th align="center">Edit</th>
          				</thead>
          				<tbody>
          					<tr>
          						<td><img src="https://i.imgur.com/8h47n4P.png" width="25" height="25">&nbsp;&nbsp;</td>
          						<td>https://www.facebook.com/<?php echo $data->Facebook; ?></td>
          						<td><button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#changefacebook" onclick="$('#changefacebook-modal').modal();"><i class="fa-brands fa-facebook-f text-info"></i> Change Facebook Link</button></td>
          					</tr>
          					<tr>
          						<td><img src="https://i.imgur.com/3R4TENj.png" width="25" height="25">&nbsp;&nbsp;</td>
          						<td>https://www.instagram.com/<?php echo $data->Instagram; ?></td>
          						<td><button type="button" class="btn btn-light btn-xs" data-toggle="modal" value="<?php echo $data->id ?>" data-toggle="modal" data-target="#changeinstagram" onclick="$('#changeinstagram-modal').modal();"><i class="fa-brands fa-instagram text-warning"></i> Change Instagram Link</button></td>
          					</tr>
          				</tbody>
          			</table>
          		</div>
          	</div>
          	<?php } ?>
            	<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
            	<div id="manageadmin" class="modal fade show" tabindex="-1" role="dialog">
				    <div class="modal-dialog modal-lg">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title">CS.SYKO.TOP</h4>
				                <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
				            </div>
							<?php
							$q = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY `groupAdmin` DESC");
							$q->execute(); ?>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="manageadmin" role="tabpanel">
				                <h3 class="text-white">Administreaza accesul pentru <font color="red"><?php echo $data->auth; ?></font>.</h3>
				                <br>
				                <?php while($function = $q->fetch(PDO::FETCH_OBJ)) { ?>
				                <?php
								if(isset($_POST['set'.$function->groupAdmin.''])) {
									if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
										$q = connect::$g_con->prepare('UPDATE `admins` SET `access` = "'.$function->groupFlags.'", `Admin` = '.$function->groupAdmin.' WHERE `id` = ?');
										$q->execute(array($data->id));

										$logwho = this::getData('admins','auth',$_SESSION['user']);
								        $logresult = " set ".$function->groupName." on ";
								        $loglast = $data->auth;

								        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.''.$loglast.'", ?, ?)');
								        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

										$notif = 'Ai fost promovat la gradul de '.$function->groupName.'.';
										$link = $_SERVER['REQUEST_URI'];
										this::makeNotification($data->id,this::getData("admins","auth",$data->id),$notif,$_SESSION['user'],this::getData("admins","auth",$_SESSION['user']),$link);

										$_SESSION['msg'] = '<div class="alert alert-success">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
											<h3><i class="fa fa-check-circle"></i> Success</h3> '.$loglast.' a fost promovat la gradul de '.$function->groupName.'
											</div>'; redirect::to('profile/'.$data->id.''); return 1;
									}
								}
								?>
				                <form method="post">
			                      <button type="submit" class="btn btn-info btn-block" style="width: 50%;" name="set<?php echo $function->groupAdmin; ?>"><?php echo $function->groupName; ?></button>
			                  	</form>
				                <br>
			                    <?php } ?>
			                    <br>
			                    <form method="post">
			                      <button type="submit" class="btn btn-danger btn-block" style="width: 50%;" name="removeadmin">Remove Admin</button>
			                    </form>
			                    <br>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				<?php }?>

        		<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="managefunctions" class="modal fade show" tabindex="-1" role="dialog">
				    <div class="modal-dialog modal-lg">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title">Manage player functions</h4>
				                <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="managefunctions" role="tabpanel">
				                <h3 class="text-red">Manage functions for <font color="red"><?php echo $data->auth ?></font> ?</h3>
				                <br>
						        <form method="post">
						            <h4>Function color:</h4>
						            <br>
						            <input type="text" name="functioncolor" placeholder="ex: #7ab2fa" class="form-control">
						            <br><br>
						            <h4>Function icon:</h4><br>
						            <br>
						            <input type="text" name="functionicon" placeholder="ex: fa fa-check" class="form-control">
						            <br><br>
						            <h4>Function name:</h4>
						            <br>
						            <input type="text" name="functionname" placeholder="ex: miss beton" class="form-control">
						            <br><br>
						            <button type="submit" class="btn btn-info btn-block" name="setplayerfunction" style="width: 50%;">
						            <i class="fa fa-check"></i> set player function
						            </button>
						            </form>
						            <hr>
						            <h3 class="text-red">Remove functions for <font color="red"><?php echo $data->auth ?></font> ?</h3>
						            <form method="post">
						              <?php $q = connect::$g_con->prepare("SELECT * FROM `panel_functions` WHERE `funcPlayerID` = ? ORDER BY funcID ASC");
						                $q->execute(array($data->id));
						                while($badge = $q->fetch(PDO::FETCH_OBJ)) {
						                  if($badge->funcPlayerID == $data->id) {
						                    echo ' <span class="badge" style="background-color:'.$badge->funcColor.';"><font style="font-family:verdana;"><i class="'.$badge->funcIcon.'" data-toggle="tooltip" data-original-title="'.$badge->funcName.'"></i> '.$badge->funcName.'</font></span>';
						                  }
						                  echo '<button name="removefunction" value="'.$badge->funcID.'" class="btn btn-sm btn-danger"><i data-toogle="remove function" title="Delete Function" class="fa fa-trash"></i></button>';
						                }
						              ?>
						            </form>
						            <hr>
						            <form action="" method="post">
						              <button type="submit" class="btn btn-danger btn-block" name="removeallfunctions" style="width: 50%;">
						              <i class="fa fa-trash"></i> remove all functions
						              </button>
						            </form>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				<?php }?>


				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="changename" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change <font color="red"><?php echo $data->auth ?></font>'s name?</h4>
								<a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="changename" role="tabpanel">
						        <form method="post">
							        <input type="text" name="nametext" class="form-control" placeholder="type new name"  required>
							        <p></p>
							        <button type="submit" class="btn btn-primary btn-block" name="setname">
							        <i class="fa fa-edit"></i> SUBMIT
							        </button>
						        </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				<?php }?>

				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="changeemail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-body">
								<form method="post">
								<a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
									<h4 class="modal-title">Change <font color="red"><?php echo $data->auth ?></font>'s email?</h4><br/><br/>
									<input class="form-control" placeholder="type new email" type="text" name="email" required>
									<p></p>
									<button type="submit" name="email_submit" class="btn btn-primary btn-block"><i class="fa fa-fa-edit"></i> SUBMIT</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php }?>

				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="changepassword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-body">
								<form method="post">
								<a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
									<h4 class="modal-title">Change <font color="red"><?php echo $data->auth ?></font>'s password?</h4>
									<center><b>Current password: <font color="red"><td><?php echo $data->password ;?></td></font></b></center><br/><br/>
									<input class="form-control" placeholder="type new password" type="text" name="password" required>
									<p></p>
									<button type="submit" name="password_submit" class="btn btn-primary btn-block"><i class="fa fa-fa-edit"></i> SUBMIT</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php }?>

				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="givestaffwarns" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
					            <h4 class="modal-title">Give/Remove Staff Warns</h4>
								<a type="button" class="close" data-dismiss="modal" aria-hidden="true">&#9447;</a>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="givestaffwarns" role="tabpanel">
					            	<form method="post">
										<?php
											if($data->warn < 3) echo '<button type="submit" class="btn btn-success" title="up" name="staffwarnup">give staff warn</button>';

											if($data->warn > 0) echo ' <button type="submit" class="btn btn-danger" title="down" name="staffwarndown">remove staff warn</button>';
										?>
									</form>
					        	</div>
					        </div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
				<div id="changefacebook" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change <font color="gold"><?php echo $data->auth ?></font>'s Facebook?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="changefacebook" role="tabpanel">
						        <form method="post">
							        <input type="text" name="facebooktext" class="form-control" placeholder="type new facebook id"  required>
							        <p></p>
							        <button type="submit" class="btn btn-primary btn-block" name="setfacebook">
							        <i class="fa fa-edit"></i> SUBMIT
							        </button>
						        </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
				<div id="changeinstagram" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change <font color="gold"><?php echo $data->auth ?></font>'s Instagram?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="changeinstagram" role="tabpanel">
						        <form method="post">
							        <input type="text" name="instagramtext" class="form-control" placeholder="type new instagram id"  required>
							        <p></p>
							        <button type="submit" class="btn btn-primary btn-block" name="setinstagram">
							        <i class="fa fa-edit"></i> SUBMIT
							        </button>
						        </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
				<div id="editavatar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change <font color="gold"><?php echo $data->auth ?></font>'s Avatar?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="editavatar" role="tabpanel">
									<form method="post" enctype="multipart/form-data" accept="image/*">
										<div class="modal-body">
											<center>
												<input type="hidden" name="name" value="<?php echo $data->auth; ?>">
												<input type="file" class="form-control input-m" placeholder="Select new avatar" name="change_avatar">
											</center>
										</div>
										<div class="modal-footer">
											<button class="btn btn-success" name="change_avatar_btn">Trimite</button>
											<button class="btn btn-danger" data-dismiss="modal">Close</button>
										</div>
									</form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				<?php }?>

				<?php if(user::isLogged() && ((auth::user()->id == $user->id) || (auth::user()->Boss != 0))) { ?>
				<div id="editcountry" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
				                <h4 class="modal-title">Change <font color="gold"><?php echo $data->auth ?></font>'s Country?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				            </div>
				            <div class="modal-body" align="center">
				                <div class="tab-pane active" id="editcountry" role="tabpanel">
						        <form method="post">
                    				<select class="form-control select2bs4" name="edittara" style="width: 50%;" title="Choose your Country" required>
                    				    <option>Romania</option>
                    				    <option disabled="disabled"></option>
                    				    <option>Afghanistan</option>
                    				    <option>Albania</option>
                    				    <option>Algeria</option>
                    				    <option>Andorra</option>
                    				    <option>Angola</option>
                    				    <option>Antigua and Barbuda</option>
                    				    <option>Argentina</option>
                    				    <option>Armenia</option>
                    				    <option>Australia</option>
                    				    <option>Austria</option>
                    				    <option>Azerbaijan</option>
                    				    <option>Bahamas</option>
                    				    <option>Bahrain</option>
                    				    <option>Bangladesh</option>
                    				    <option>Barbados</option>
                    				    <option>Belarus</option>
                    				    <option>Belgium</option>
                    				    <option>Belize</option>
                    				    <option>Benin</option>
                    				    <option>Butan</option>
                    				    <option>Bolivia</option>
                    				    <option>Bosnia and Herzegovina</option>
                    				    <option>Botswana</option>
                    				    <option>Brazil</option>
                    				    <option>Brunei</option>
                    				    <option>Bulgaria</option>
                    				    <option>Burkina Faso</option>
                    				    <option>Burundi</option>
                    				    <option>Cambodgia</option>
                    				    <option>Cameroon</option>
                    				    <option>Canada</option>
                    				    <option>Central Africa</option>
                    				    <option>Chad</option>
                    				    <option>Chile</option>
                    				    <option>China</option>
                    				    <option>Colombia</option>
                    				    <option>Congo</option>
                    				    <option>Costa Rica</option>
                    				    <option>Croatia</option>
                    				    <option>Cuba</option>
                    				    <option>Cyprus</option>
                    				    <option>Czech Republic</option>
                    				    <option>Denmark</option>
                    				    <option>Dominican Republic</option>
                    				    <option>Ecuador</option>
                    				    <option>Egypt</option>
                    				    <option>Estonia</option>
                    				    <option>Ethiopia</option>
                    				    <option>Fiji</option>
                    				    <option>Finland</option>
                    				    <option>France</option>
                    				    <option>Georgia</option>
                    				    <option>Germany</option>
                    				    <option>Ghana</option>
                    				    <option>Greece</option>
                    				    <option>Haiti</option>
                    				    <option>Honduras</option>
                    				    <option>Hungary</option>
                    				    <option>Iceland</option>
                    				    <option>India</option>
                    				    <option>Indonesia</option>
                    				    <option>Iran</option>
                    				    <option>Iraq</option>
                    				    <option>Ireland</option>
                    				    <option>Israel</option>
                    				    <option>Italy</option>
                    				    <option>Japan</option>
                    				    <option>Kazakhstan</option>
                    				    <option>Latvia</option>
                    				    <option>Lebanon</option>
                    				    <option>Liechtenstein</option>
                    				    <option>Lithuania</option>
                    				    <option>Luxembourg</option>
                    				    <option>Malaysia</option>
                    				    <option>Maldives</option>
                    				    <option>Mali</option>
                    				    <option>Malta</option>
                    				    <option>Mexico</option>
                    				    <option>Moldova</option>
                    				    <option>Monaco</option>
                    				    <option>Montenegro</option>
                    				    <option>Morocco</option>
                    				    <option>Netherlands</option>
                    				    <option>Nigeria</option>
                    				    <option>North Koreea</option>
                    				    <option>North Macedonia</option>
                    				    <option>Norway</option>
                    				    <option>Pakistan</option>
                    				    <option>Paraguay</option>
                    				    <option>Peru</option>
                    				    <option>Philippines</option>
                    				    <option>Poland</option>
                    				    <option>Portugal</option>
                    				    <option>Qatar</option>
                    				    <option>Romania</option>
                    				    <option>Russia</option>
                    				    <option>San Marino</option>
                    				    <option>Saudi Arabia</option>
                    				    <option>Senegal</option>
                    				    <option>Serbia</option>
                    				    <option>Singapore</option>
                    				    <option>Slovakia</option>
                    				    <option>Slovenia</option>
                    				    <option>South Africa</option>
                    				    <option>South Korea</option>
                    				    <option>Spain</option>
                    				    <option>Sri Lanka</option>
                    				    <option>Sudan</option>
                    				    <option>Sweden</option>
                    				    <option>Switzerland</option>
                    				    <option>Thailand</option>
                    				    <option>Tunisia</option>
                    				    <option>Turkey</option>
                    				    <option>Turkmenistan</option>
                    				    <option>Ukraine</option>
                    				    <option>UAE</option>
                    				    <option>United Kindom</option>
                    				    <option>United States of America</option>
                    				    <option>Uruguay</option>
                    				    <option>Uzbekistan</option>
                    				    <option>Venezuela</option>
                    				    <option>Vietnam</option>
                    				    <option>Yemen</option>
                    				</select>
							        <p></p>
							        <button type="submit" class="btn btn-primary btn-block" name="setcountry">
							        <i class="fa fa-edit"></i> SUBMIT
							        </button>
						        </form>
				                </div>
				            </div>
						</div>
					</div>
				</div>
				<?php }?>

				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="removeaccount" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
				    <div class="modal-dialog modal-sm">
				        <div class="modal-content">
				            <div class="modal-body">
				                <div class="tab-pane active" id="removeaccount" role="tabpanel">
				                    <form role="form" method="post" action="" id = "form">
				                        <div class="form-group">
				                            <h4 align="center">Are you sure?</h4>
				                        </div>
				                        <hr>
				                        <div align="center">
				                            <button type="submit" name="deleteaccount" action="Logout" class="btn btn-danger btn-block">Yes, delete!</button>
				                        </div>
				                    </form>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				<?php }?>
				<?php if(user::isLogged() && ((auth::user()->Boss >= 1))) { ?>
				<div id="manageboss" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
				    <div class="modal-dialog modal-sm">
				        <div class="modal-content">
				            <div class="modal-body">
				                <div class="tab-pane active" id="manageboss" role="tabpanel">
				                    <form role="form" method="post" action="" id = "form">
				                        <div class="form-group">
				                            <h4 align="center">Are you sure?</h4>
				                        </div>
				                        <hr>
				                        <div align="center">
				                        	<?php if($data->Boss == 0) { ?>
				                            <button type="submit" name="giveboss" class="btn btn-primary btn-block">Yes, give!</button>
				                            <?php } else { ?>
				                            <button type="submit" name="removeboss" class="btn btn-danger btn-block">Yes, give!</button>
				                            <?php } ?>
				                        </div>
				                    </form>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				<?php }?>
            </div>
        </div>
    </div>
</section>