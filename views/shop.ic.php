<?php if(this::getSpec("panel_settings","Maintenance","ID", 1)) { ?>
    <?php if(!isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1 || isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) < 1) { ?>
        <?php redirect::to('maintenance'); ?>
    <?php } ?>
<?php } ?>
<?php 
	$itemshop = connect::$g_con->prepare('SELECT * FROM `panel_shop` ORDER BY `itemID` ASC');
	$itemshop->execute(); 
	$item = $itemshop->fetch(PDO::FETCH_OBJ);
?>
<?php
require_once './application/PayPal/autoload.php';

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
       'AUGA6ExQytlEXBjdxm7yIzjOwXkvMa3-13i1Hr7tlHRR-b5Kdl5ViV-4svuLwU4xBBkHFWXnI8V5nsLU',
       'ENR0tgWQhVxMoTWOYQEXYBWsbxAcJPd90AofixneHlF4UAeuEJ-dNAbWZ3dhigxQgN5zUSLYux5vP97E'
    )
);

$apiContext->setConfig(
    array(
        'log.LogEnabled' => true,
        'log.FileName' => 'PayPal.log',
        'mode' => 'sandbox', // live daca ai paypal on
        'log.LogLevel' => 'DEBUG'
    )
);

$payer = new \PayPal\Api\Payer();
$amount = new \PayPal\Api\Amount();
$transaction = new \PayPal\Api\Transaction();
$redirectUrls = new \PayPal\Api\RedirectUrls();
$payment = new \PayPal\Api\Payment();
$execute = new \PayPal\Api\PaymentExecution();

$data = array();

if(isset($_GET['buy'])) {
	$buy_id = $_GET['buy'];
	$item_price = this::get_item_shop_price_with_id($buy_id);

	$url_succes = "https://".$_SERVER['HTTP_HOST']."/shop&buy_id=".$buy_id."&status=succes";
	$url_reject = "https://".$_SERVER['HTTP_HOST']."/shop&buy_id=".$buy_id."&status=reject";

	if(user::isLogged()) {
		//$search_results = array_search($general->get_username_from_id($_SESSION['id']), array_column($data, 'name'));
		if(this::get_online_status(this::get_username_from_id($_SESSION['user'])) == 1)  {
			this::sweetalert("Error!", "Trebuie sa te deconectezi din joc pentru a achizitiona ceva de pe panel.", "error");
			redirect::to('shop'); return 1;
		} else {
			if(this::get_item_shop_value_with_id($buy_id) != this::get_user_access_from_name(this::get_username_from_id($_SESSION['user']))) {
				$payer->setPaymentMethod("paypal");

				$item = $buy_id;
				$itemPrice = $item_price;
				$itemCurrency = 'EUR';

				$payer->setPaymentMethod('paypal');
				$amount->setTotal($itemPrice);
				$amount->setCurrency($itemCurrency);
				$transaction->setAmount($amount);
				$redirectUrls->setReturnUrl($url_succes)
				    ->setCancelUrl($url_reject);
				$payment->setIntent('sale')
				    ->setPayer($payer)
				    ->setTransactions(array($transaction))
				    ->setRedirectUrls($redirectUrls);

				try {
				    $payment->create($apiContext);
				} catch (Exception $ex) {
					this::sweetalert("Error!", "A aparut o eroare la logarea in portofelul administratorului, incearca mai tarziu.", "error");
					redirect::to('shop'); return 1;
				}

				$approvalUrl = $payment->getApprovalLink();
				header("location:".$approvalUrl);
			} else {
				this::sweetalert("Error!", "Achizitia nu a putut fi finalizata, deoarece detineti acest privilegiu.", "error");
				redirect::to('shop'); return 1;
			}
		}
	} else {
		this::sweetalert("Error!", "Obiectele din shop pot fi cumparate doar de utilizatorii logati.", "error");
		redirect::to('shop'); return 1;
	}
}

if(isset($_GET['buy_id']) && isset($_GET['status']) === 'reject') {
	this::sweetalert("Success!", "Achizitia nu a putut fi finalizata.", "success");
	redirect::to('shop'); return 1;
}

if(isset($_GET['buy_id']) && isset($_GET['status']) && isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
	$paymentId = $_GET["paymentId"];
	$payerId = $_GET["PayerID"];
	$buy_id = $_GET['buy_id'];

	if(user::isLogged()) {
		$peym = $payment->get($paymentId, $apiContext);
		$execute->setPayerId($payerId);

		try {
			$result = $peym->execute($execute, $apiContext);
			$price = $result->transactions[0]->amount->total;
			$payID = $result->transactions[0]->related_resources[0]->sale->parent_payment;
			if(this::get_shop_log_paymentid($payID) != true) {
				if(this::get_user_query_from_shop($buy_id, $price) == true) {
					this::set_shop_log($price, 'succes', $payID);
					this::sweetalert("Success!", "<strong>Tranzactie efectuata cu succes.</strong> Ai platit <b>".$price."€.</b>", "success");
					redirect::to('shop'); return 1;
				} else {
					this::set_shop_log($price, 'reject / frauda', $payID);
					this::sweetalert("Error!", "Achizitia nu a putut fi finalizata, contactati ownerul in cazul in care ati ramas fara suma de bani, pentru incercarea de fraudare, banii nu se returneaza.", "error");
					redirect::to('shop'); return 1;
				}
			} else {
				this::sweetalert("Success!", "<strong>Tranzactie efectuata cu succes.</strong> Ai platit <b>".$price."€.</b>", "success");
				redirect::to('shop'); return 1;
			}
		} catch(Exception $e) {
			$data = json_decode($e->getData());
			//this::sweetalert("Error!", "Ceva n-a mers bine!", "error");
			//redirect::to('shop'); return 1;
		}
	}
}
?>
<script type="text/javascript" src="<?php echo this::$_PAGE_URL ?>resources/ckeditor/ckeditor.js"></script>
<?php if(!isset(this::$_url[1])) { ?>
<div class="row">
	<div class="col-xs-12 col-md-12">
		<div class="card">
			<div class="card-header bg-dark text-white">
	    <h4><i class="fa fa-shopping-cart" aria-hidden="true"></i> Shop</h4>
					<div class="pull-right">
						<button type="button" class="btn btn-outline-success waves-effect waves-light" data-toggle="modal" data-target="#buypremiumpoints">
							<span><i class="fa fa-info-circle"></i></span> Shop Information
						</button>
						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<a href="<?php echo this::$_PAGE_URL ?>shop?updatebuyshopinfo" class="btn btn-outline-success waves-effect waves-dark"><i class="fa fa-edit"></i> Edit Information</a>
						<a href="<?php echo this::$_PAGE_URL ?>shop?updateshopinfo" class="btn btn-outline-success waves-effect waves-dark"><i class="fa fa-edit"></i> Edit Shop Information</a>
						<button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#addnewitem">
							<span><i class="fa fa-plus"></i></span> Add New Item
						</button>
						<?php }?>
					</div>
			</div>
			<div class="card-body">
				<div class="col-lg-12">
					<?php
	                    $q = connect::$g_con->prepare("SELECT `Topic` FROM `panel_topics` WHERE `id` = 4");
	                    $q->execute();
	                    $shopinfo = $q->fetch(PDO::FETCH_OBJ);
	                ?>
					
					<?php if(isset($_GET['updateshopinfo'])) { ?>
	                    <?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	                    <form method="post">
	                        <textarea name="shopinformations" id="shopinformations" class="form-control" rows="16" required><?php echo $shopinfo->Topic ?></textarea>
	                        <script>CKEDITOR.replace('shopinformations');</script>
	                        <br>
	                        <input type="submit" name="updateshopinformations" value="Update Shop Informations" class="btn btn-info pull-right"/>
	                    </form>
	                    <?php } else { ?>
	                        <?php redirect::to('shop'); ?>
	                    <?php } ?>
	                <?php } else { ?>
	                    <?php echo $shopinfo->Topic ?>
						
	                <?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div id="buypremiumpoints" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Contact details & Donation methods</h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="buypremiumpoints" role="tabpanel">
		            <?php
	                    $qw = connect::$g_con->prepare("SELECT `Topic` FROM `panel_topics` WHERE `id` = 5");
	                    $qw->execute();
	                    $buyshop = $qw->fetch(PDO::FETCH_OBJ);
	                ?>
					
					<?php if(isset($_GET['updatebuyshopinfo'])) { ?>
	                    <?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	                    <form method="post">
	                        <textarea name="buyshopinformations" id="buyshopinformations" class="form-control" rows="16" required><?php echo $buyshop->Topic ?></textarea>
	                        <script>CKEDITOR.replace('buyshopinformations');</script>
	                        <br>
	                        <input type="submit" name="updatebuyshopinformations" value="Update Shop Informations" class="btn btn-info pull-right"/>
	                    </form>
	                    <?php } else { ?>
	                        <?php redirect::to('shop'); ?>
	                    <?php } ?>
	                <?php } else { ?>
	                    <?php echo $buyshop->Topic ?>
	                <?php } ?>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	
	<?php 
		$panelshop = connect::$g_con->prepare('SELECT * FROM `panel_shop` ORDER BY `itemID` ASC');
		$panelshop->execute(); ?>
	<?php while($shop = $panelshop->fetch(PDO::FETCH_OBJ)) { ?>
	<div class="col-lg-4 col-xs-4">
		<div class="card">
				<div class="card-header text-center">
    				<h4><?php echo $shop->itemName ?></h4>
    			</div>
    			<div class="card-body">
					<div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-success text-lg">
							<a href="#" data-toggle="modal" data-target="#buyitem<?php echo $shop->itemID ?>">
								BUY
							</a>
                        </div>
                      </div>
					<div class="text-center">
						<h3><b><?php echo $shop->itemPrice ?></b></h3>
						<h4><b>Euro</b></h4>

						<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
						<h4><a href="#" data-toggle="modal" data-target="#edititem<?php echo $shop->itemID ?>">
						<i class="fa fa-edit fa-lg text-info"></i> </a> |
						<a href="#" data-toggle="modal" data-target="#removeitem<?php echo $shop->itemID ?>">
						<i class="fa fa-trash fa-lg text-danger"></i> </a></h4>
						<?php }?>

					</div>
				</div>
				<div class="card-footer text-center">
					<a href="#" data-toggle="modal" data-target="#info<?php echo $shop->itemID ?>">
					<h4>Click here for more informations <i class="fa fa-hand-o-left"></i></h4></a>
				</div>
			</div>
		</div>

	<div id="info<?php echo $shop->itemID ?>" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title"><?php echo $shop->itemName ?></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="<?php echo $shop->itemName ?>" role="tabpanel">
		            	<br>
		            	<?php echo $shop->itemText ?>
					</div>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="buyitem<?php echo $shop->itemID ?>" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title"><?php echo $shop->itemName ?></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="<?php echo $shop->itemName ?>" role="tabpanel">
	                	<b>Esti sigur ca vrei sa cumperi <font color="red"><?php echo $shop->itemName ?></font> pentru suma de <font color="red"><?php echo $shop->itemPrice ?></font> Euro?</b>
		            	<br>
		            	<br>
						<a href="shop&buy=<?php echo $shop->itemID ?>" class="btn btn-success btn-md" style="margin-left:39.5%;">
							<i class="fa fa-shopping-cart"></i> Buy
						</a>
					</div>
	            </div>
	        </div>
	    </div>
	</div>

	<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	<div id="edititem<?php echo $shop->itemID ?>" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Edit <font color="gold"><?php echo $shop->itemName ?></font></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="edititem<?php echo $shop->itemID ?>" role="tabpanel">
		            <form method="post">
						<h5>Item name:</h5>
						<input type="text" name="edititemname" value="<?php echo $shop->itemName ?>" class="form-control">
						<p></p>
						<h5>Item price(Euro):</h5>
						<input type="text" name="edititemprice" value="<?php echo $shop->itemPrice ?>" class="form-control">
						<p></p>
						<h5>Item informations:</h5>
						<textarea name="edititeminfo" id="edititeminfo<?php echo $shop->itemID ?>" class="form-control" rows="10"><?php echo $shop->itemText ?></textarea>
						<script>CKEDITOR.replace('edititeminfo<?php echo $shop->itemID ?>');</script>
						<p></p>
						<div align="center">
						<button type="submit" class="btn btn-info btn-block" value="<?php echo $shop->itemID ?>" name="editthisitem" style="width: 50%;">
						<i class="fa fa-edit"></i> SUBMIT
						</button>
						</div>
					</form>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php } ?>

	<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	<div id="removeitem<?php echo $shop->itemID ?>" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Remove <font color="gold"><?php echo $shop->itemName ?></font></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="removeitem<?php echo $shop->itemID ?>" role="tabpanel">
	                <div align="center">
	                <h4>Are you sure if you want to remove this item?</h4>
	                <p></p>
		            <form method="post">
						<button type="submit" class="btn btn-danger btn-block" value="<?php echo $shop->itemID ?>" name="removethisitem" style="width: 50%;">
						<i class="fa fa-trash"></i> Remove This Item
						</button>
					</form>
					</div>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php } ?>

	<?php } ?>
	</div>

	<?php if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) { ?>
	<div id="addnewitem" class="modal fade show" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Add New Item</h4>
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="tab-pane active" id="addnewitem" role="tabpanel">
		            <form method="post">
						<h5>Item name:</h5>
						<input type="text" name="itemname" placeholder="write here item name" class="form-control">
						<p></p>
						<h5>Item price(Euro):</h5>
						<input type="text" name="itemprice" placeholder="ex: 250" class="form-control">
						<p></p>
						<h5>Item informations:</h5>
						<textarea class="form-control" rows="10" name="iteminformations" placeholder="write item informations here"></textarea>
						<script>CKEDITOR.replace('iteminformations');</script>
						<p></p>
						<div align="center">
						<button type="submit" class="btn btn-info btn-block" name="publishnewitem" style="width: 50%;">
						<i class="fa fa-check"></i> SUBMIT
						</button>
						</div>
					</form>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php } ?>

	<?php
		if(isset($_POST['publishnewitem'])) {
			if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('INSERT INTO `panel_shop` (`itemName`, `itemText`, `itemPrice`) VALUES (?, ?, ?)');
			$q->execute(array($purifier->purify(this::xss_clean($_POST['itemname'])), $purifier->purify(this::xss_clean($_POST['iteminformations'])), $purifier->purify(this::xss_clean($_POST['itemprice']))));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " added a new product in shop";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$_SESSION['msg'] = '<div class="alert alert-success">
	    		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	    		<h3><i class="fa fa-check-circle"></i> Success</h3> A new item was added in shop with successfully!
	    		</div>';
			redirect::to('shop'); return 1;
			}
		}
		if(isset($_POST['removethisitem'])) {
			if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('DELETE FROM `panel_shop` WHERE `itemID` = ?');
			$q->execute(array($_POST['removethisitem']));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " edited shop product";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$_SESSION['msg'] = '<div class="alert alert-success">
	    		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	    		<h3><i class="fa fa-check-circle"></i> Success</h3> Item deleted form shop with successfully!
	    		</div>';
			redirect::to('shop'); return 1;
			}
		}
		if(isset($_POST['editthisitem'])) {
			if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare('UPDATE `panel_shop` SET `itemName` = ? , `itemText` = ?, `itemPrice` = ?  WHERE `itemID` = ?');
			$q->execute(array($purifier->purify(this::xss_clean($_POST['edititemname'])), $purifier->purify(this::xss_clean($_POST['edititeminfo'])), $purifier->purify(this::xss_clean($_POST['edititemprice'])), $_POST['editthisitem']));

			$logwho = this::getData('admins','auth',$_SESSION['user']);
	        $logresult = " removed shop product";

	        $insertlog = connect::$g_con->prepare('INSERT INTO `panel_logs` (`logText`, `logBy`, `logIP`) VALUES ("'.$logwho.''.$logresult.'", ?, ?)');
	        $insertlog->execute(array(this::getData('admins','id',$_SESSION['user']), $_SERVER['REMOTE_ADDR']));

			$_SESSION['msg'] = '<div class="alert alert-success">
	    		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
	    		<h3><i class="fa fa-check-circle"></i> Success</h3> Item was edited with successfully!
	    		</div>';
			redirect::to('shop'); return 1;
			}
		}

		if(isset($_POST['updateshopinformations'])) {
			if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 4");
			$q->execute(array($_POST['shopinformations']));

			$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
			<h3><i class="fa fa-check-circle"></i> Success</h3> Shop Informations was updated with successfully!
			</div>';
			redirect::to('shop'); return 1;
			}
		}

		if(isset($_POST['updatebuyshopinformations'])) {
			if(isset($_SESSION['user']) && this::getData('admins', 'Boss', $_SESSION['user']) >= 1) {
			$q = connect::$g_con->prepare("UPDATE `panel_topics` SET `Topic` = ? WHERE `id` = 5");
			$q->execute(array($_POST['buyshopinformations']));

			$_SESSION['msg'] = '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><font color="black">x</font></button>
			<h3><i class="fa fa-check-circle"></i> Success</h3> Shop Informations was updated with successfully!
			</div>';
			redirect::to('shop'); return 1;
			}
		}
	?>
</div>
<?php } ?>