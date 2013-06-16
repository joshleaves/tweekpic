<?php session_start(); ?>
<?php include "functions.php"; ?>
<?php include "config.php"; ?>
<?php include "header.php"; ?>

	<div class="container">
		<div class="content">
			<div class="page-header">
				<h1>tWeekPic <small> - <?php echo get_fortune(); ?></small></h1>
			</div>
			<div class="row">
				<div class="span10">
					<h2>Welcome to tWeekPic!</h2>
					<p>
						tWeekPic is a Twitter application that allows you to set up different profile pictures for each day of the week. Once you've set up your schedule, our application will automatically use the Twitter API to update your profile picture according to your wishes.<br />
					</p>
<?php if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'YES'): ?>
	<?php include "days.php"; ?>
<?php else: ?>
					<h2>Use tWeekPic</h2>
						To get started with tWeekPic, just <a href="?authenticate=1">sign in using your Twitter account</a>.<br />

<?php endif;?>
				</div>
				<div class="span4">
					<h3>Be our friends</h3>
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) {return;}
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
					<div class="fb-like-box" data-href="http://www.facebook.com/pages/tWeekPic/186151444794010" data-width="230" data-show-faces="true" data-stream="false" data-header="false"></div>
					<h3>Secondary content</h3>
					
				</div>
			</div>
		</div>
		<footer>
			<p>
				&copy; <a href="http://grahamgrafx.com/">Graham Smith</a> & <a href="http://dreamleaves.org/t/">Arnaud Rouyer</a> 2011 - 
				Made using <a href="http://twitter.github.com/bootstrap/">Bootstrap</a>, <a href="https://github.com/themattharris/tmhOAuth">tmhOAuth</a> and <a href="http://jquery.com/">jQuery</a>
			</p>
		</footer>
	</div> <!-- /container -->
<script>
	var _gaq=[['_setAccount','UA-23566875-3'],['_trackPageview']]; // Change UA-XXXXX-X to be your site's ID
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>

<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
	<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->

</body>
</html>
