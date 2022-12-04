<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>

<?php
$onlineplayers = connect::$g_con->prepare("SELECT * FROM `owner_settings` WHERE `id` = 1");
$onlineplayers->execute();
$showonline = $onlineplayers->fetch(PDO::FETCH_OBJ);

$registeredplayers = connect::$g_con->prepare("SELECT * FROM `admins`");
$registeredplayers->execute();

$bannedplayers = connect::$g_con->prepare("SELECT * FROM `advanced_bans`");
$bannedplayers->execute();

$adminsplayers = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `Admin` > 0");
$adminsplayers->execute();
?>
<?php if(user::isLogged()) { ?>
    <?php
    if(auth::user()->email == "email@yahoo.com") { 
        if(isset($_POST['emailconfirm']))
        {
            if(filter_var($_POST['emailprocess'], FILTER_VALIDATE_EMAIL))
            {           
                $w = connect::$g_con->prepare('UPDATE `admins` SET `email` = ? WHERE `id` = ?');
                $w->execute(array($_POST['emailprocess'], $_SESSION['user']));
                $_SESSION['msg'] = '<div class="alert alert-success alert-white">Your email address has been confirmed with successfully!</div>';
                redirect::to(''); return 1;
            } else $_SESSION['msg'] = '<div class="alert alert-danger alert-white">Please insert an valid email form!</div>'; redirect::to(''); return 1;
        }
    }
    ?>
    <?php if(auth::user()->email == "email@yahoo.com") { ?>
    <div class="card">
        <div class="card-header">
            <h4><i class="fa fa-envelope"></i> Confirm your email address</h4>
        </div>
        <div class="card-body">
            <h5>For the security reason, you need to confirm your email address.</h5>
            <h5>Insert carefully the email!</h5>
            <form method="post" action="#">
                <div class="input-group" style="width:300px;">
                    <input class="form-control" placeholder="your email address" type="text" name="emailprocess" required>
                </div>
                <br>
                <button type="submit" name="emailconfirm" class="btn btn-primary btn-block" style="width:300px;">Confirm email address</button>
            </form>
        </div>
    </div>
    <?php } ?>
<?php } ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><?php echo $registeredplayers->RowCount(); ?></h3>
                    <p>Registered Players</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="<?php echo this::$_PAGE_URL ?>players" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo $adminsplayers->RowCount(); ?></h3>
                    <p>Active Admins</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-lock"></i>
                </div>
                <a href="<?php echo this::$_PAGE_URL ?>staff" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo $showonline->online; ?></h3>
                    <p>Online Players</p>
                </div>
                <div class="icon">
                    <i class="fa fa-chart-simple"></i>
                </div>
                <a href="<?php echo this::$_PAGE_URL ?>stats" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo $bannedplayers->RowCount(); ?></h3>
                    <p>Banned Players</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <a href="<?php echo this::$_PAGE_URL ?>banlist" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
#photo_rotate img{
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    transition: all 0.3s ease;
    padding:3px;
}
#photo_rotate img:hover {
    border-radius: 0 0 0 0;
    -moz-transform: scale(1.2) rotate(720deg);
    -webkit-transform: scale(1.2) rotate(720deg);
    -o-transform: scale(1.2) rotate(720deg);
    -ms-transform: scale(1.2) rotate(720deg);
    transform: scale(1.2) rotate(720deg);
}
</style>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <center><i class="blink blink fa-solid fa-bullhorn"></i> Server Last Announcement</center>
            </div>
            <?php
                $serverinfo = connect::$g_con->prepare("SELECT * FROM `panel_topics` WHERE `id` = 1");
                $serverinfo->execute();
                $topic = $serverinfo->fetch(PDO::FETCH_OBJ); {
            ?>
            <div class="card-body">
                <p><?php echo $topic->Topic ?></p>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-thumbs-up"></i> Social Networks
            </div>
            <div class="card-body" align="center">
                <div id="photo_rotate">
                    <a href="<?php echo this::$_FORUM_URL; ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/btcmlMI.png" width="50" height="55"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo this::$_FACEBOOK_URL; ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/8h47n4P.png" width="50" height="50"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo this::$_DISCORD_URL; ?>" target="_blank"><img class="img-fluid" src="https://i.imgur.com/gBRDi4d.png" width="50" height="50"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="steam://connect/<?php echo this::$_SERVER_IP; ?>:27015" target="_blank"><img class="img-fluid" src="https://i.imgur.com/nwmttsc.png" width="50" height="50"></a><br/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <center><i class="fa-solid fa-rss"></i> Server News</center>
                <div class="card-body">
                    <?php
                    $servernews = connect::$g_con->prepare('SELECT * FROM `panel_news` ORDER BY `id`  DESC LIMIT 5');
                    $servernews->execute();
                    $row = $servernews->fetch(PDO::FETCH_OBJ); { ?>
                    <span style="color:#03a9f3;"><strong> <?php echo $row->title; ?></strong></span>
                    <div class="pull-right">posted by <a href="<?php echo this::$_PAGE_URL ?>profile/<?php echo this::getData('admins', 'id', $row->admin) ?>"><?php echo this::getData('admins', 'auth', $row->admin) ?></a> <?php echo this::timeAgo($row->date); ?></div>
                    <p></p>
                    <span><?php echo $row->text; ?></span>
                    <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-brands fa-discord"></i> Discord Server
            </div>
            <div class="card-body">
                <center><iframe src="https://discord.com/widget?id=940691233444069406&theme=dark" width="325" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe></center>
            </div>
        </div>
    </div>
</div>