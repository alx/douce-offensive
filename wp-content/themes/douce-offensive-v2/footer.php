	<div style="position: absolute; height: 504px; width: 236px; top: 60px; right: 65px;">
		<div class="menu" id="menu-left" style="top: 0pt;">
			<div class="background" id="menu-left-background" style="left: -472px;"/>
			<div class="scroll" id="menu-left-scroll" style="left: -472px; top: 0px;">
				<div class="pagemenu" id="menu-left-pagemenu">
					<div class="title"><?php wp_title(); ?></div>

					<ul class="subselection">		
						<li><a class="active" href="#">Categorie</a></li>
						<?php display_categories(); ?>
					</ul>

					<p class="level-up"><a href="#">Autres catégories</a></p>
				</div>
			</div>
		</div>
		<div class="shadow-bottom">
			<div class="start"/>
		</div>
	</div>
	<div class="shadow-bottom"/>
	<div class="shadow-right"/>
</div>


<?php do_action('wp_footer'); ?>

</body>
</html>
