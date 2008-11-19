	<div id="footer">

	</div><!-- footer -->

	<div class="snap-to-fit"></div>

</div><!-- page -->

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("div.nav_photo").click(function() {
			$("#main_photo").attr("src",$(this).attr("url"));
		});
	});
</script>

<?php do_action('wp_footer'); ?>

</body>
</html>
