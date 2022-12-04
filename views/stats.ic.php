<?php
require_once 'application/gameq/GameQ.php';

$servers['user_panel'] = array('cs', this::$_SERVER_IP, 27015);

$gq = new GameQ;

$gq->addServers($servers);

try {
	$data = array();
	$data = $gq->requestData();
	$stats = $data;
	$data = $data['user_panel']['players'];
	usort($data,
	  function($a, $b) {
	    return $a['score'] <= $b['score'];
	  }
	);
} catch (GameQ_Exception $e) {
	echo 'An error occurred.';
}
?>
<div class="card">
<div class="tab-content">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <table class="table table-bordered table-striped">
          <thead><br/>
            <center>
              <div class="alert alert-success">
                <?php foreach ($stats as $r) { ?>
                <?php echo $r['hostname']; ?><br>
                Players: <?php echo $r['num_players']; ?>/<?php echo $r['max_players']; ?> | Map: <?php echo $r['map']; ?> | Nextmap: <?php echo $r['amx_nextmap']; ?><br>
                Status: 
                <?php
                if ($r['gq_online'] == 1) {
                  echo '<font color="green">Online</font>';
                } else {
                  echo '<font color="red">Offline</font>';
                }
                ?>
                <?php } ?>
              </div>
            </center>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <tr>
                  <td vAlign="middle" id="mltitle"> <font color="gold"><b>ID</b></font> </td>
                  <td vAlign="middle" id="mltitle"> <font color="gold"><b>Player Name</b></font> </td>
                  <td vAlign="middle" id="mltitle"> <font color="gold"><b>Frags</b></font> </td>
                  <td vAlign="middle" id="mltitle"> <font color="gold"><b>Time</b></font> </td>
                </tr>
                <?php foreach ($data as $r) { ?>
                <tr>
                  <td vAlign="middle" id="mltitle"> <?php echo $r['id']; ?></td>
                  <td vAlign="middle" id="mltitle"> <?php echo $r['name']; ?></td>
                  <td vAlign="middle" id="mltitle"> <?php echo $r['score']; ?></td>
                  <td vAlign="middle" id="mltitle"> <?php echo gmdate("H:i:s", $r['time']); ?></td>
                </tr>
                <?php } ?>
              </table>
            </div>
          </tbody>
        </thead>
        </table>
      </div>
    </div>
</body>
</div>
</div>
</div>