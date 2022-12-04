</section>
</div>
	<footer class="main-footer">
		<strong>Copyright &copy; Syko.</strong>
		All rights reserved.
		<div class="float-right d-none d-sm-inline-block">
			<div class="pull-right hidden-xs" id="timpload"></div>
			<script>
			  new Date().getFullYear()>2022 && document.write("-"+new Date().getFullYear());
			  window.onload = function () {
			    var loadTime = window.performance.timing.domContentLoadedEventEnd-window.performance.timing.navigationStart;
			    document.getElementById("timpload").innerHTML = 'This page took '+loadTime/1000+' seconds to render | Developed by <a href="https://syko.top/">Syko</a> v1.1.0'
			}
			</script>
		</div>
	</footer>
</div>

<script src="<?php echo this::$_PAGE_URL ;?>resources/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo this::$_PAGE_URL ;?>resources/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo this::$_PAGE_URL ;?>resources/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?php echo this::$_PAGE_URL ;?>resources/assets/js/adminlte.min.js"></script>
</body>
</html>

<?php ob_flush(); ?>