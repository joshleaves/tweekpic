<?php
	$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

	$i = 7;
	if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'YES')
	{
		$i = 0;
		?>
		<h2>Use tWeekPic</h2>
		<ul>
			<li>Click on the picture before a day to select a picture for this day.</li>
			<li>Advertising for tWeekPic will send a tweet that day saying your profile pic changed thanks to our service, nothing more! This will bother you so the option is <span class="label important">OFF</span> by default.<br />Click on the <span class="label important">OFF</span> button to turn it to <span class="label success">ON</span>.</li>
			<li>The big red <span class="label important">DELETE</span> button will remove the image you set to use for this day.</li>
		</ul>
		<?php
	}
	echo '<ul class="unstyled day-list">';
	while ($i < 7)
	{
		$short = strtolower(substr($days[$i], 0, 3));
		$req = mysql_query("SELECT pic_path, do_ad FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}' AND day='$short'");
		$dis = "disabled";
		if (mysql_num_rows($req) == 1) { $reg = mysql_fetch_assoc($req); $dis = ""; }
		else { $reg = array('pic_path'=>'none.png', 'do_ad'=>'off'); }
?>
	<li id="avatar-<?php echo $short; ?>" class="day" day="<?php echo $short; ?>" class="<?php echo $dis; ?>">
		<img src="images/<?php echo $reg['pic_path']; ?>" day ="<?php echo $short; ?>">
		<div class="day-desc">
			<div>
				<strong><?php echo $days[$i]; ?></strong>
				<span class="delete btn danger <?php echo $dis; ?>" style="float:right;" day="<?php echo $short; ?>">Delete</span>
			</div>
			<div id="file-uploader-<?php echo $short; ?>" day="<?php echo $short; ?>" class="file-uploader">       
				<noscript>          
					<p>Please enable JavaScript to use file uploader.</p>
				</noscript>         
			</div>
			<div class="advertise">
				Advertise on this day?
				<?php if ($reg['do_ad'] == 'off'): ?>
					<span class="label important advertise <?php echo $dis; ?>" day="<?php echo $short; ?>">OFF</span>
				<?php else: ?>
					<span class="label success advertise" day="<?php echo $short; ?>">ON</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="day-button">	
		</div>
	</li>
<?php
		$i++;
	}
	echo '</ul>';
?>