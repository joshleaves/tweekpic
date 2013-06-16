<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>tWeekpic</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <!-- Le styles -->
  <link rel="stylesheet" href="bootstrap.min.css">
  <link rel="stylesheet" href="my.css">
  <link rel="stylesheet" href="fileuploader.css">
  <style type="text/css">
    /* Override some defaults */
    html, body {
      background-color: #eee;
    }
    body {
      padding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
    }
    .container > footer p {
      text-align: center; /* center align it with the container */
    }
    .container {
      width: 820px;
    }

    /* The white background content wrapper */
      .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
        -webkit-border-radius: 0 0 6px 6px;
           -moz-border-radius: 0 0 6px 6px;
                border-radius: 0 0 6px 6px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

      /* Page header tweaks */
      .page-header {
        background-color: #f5f5f5;
        padding: 20px 20px 10px;
        margin: -20px -20px 20px;
      }

      /* Styles you shouldn't keep as they are for displaying this base example only */
      .content .span10,
      .content .span4 {
        min-height: 500px;
      }
      /* Give a quick and non-cross-browser friendly divider */
      .content .span4 {
        margin-left: 0;
        padding-left: 19px;
        border-left: 1px solid #eee;
      }

      .topbar .btn {
        border: 0;
      }
    </style>

    <!-- Le fav and touch icons -->
  <link rel="shortcut icon" href="images/favicon.ico">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <!--<script src="js/jquery-1.6.2.min.js"></script>-->
  <script src="js/modernizr-2.0.min.js"></script>
  <script src="js/respond.min.js"></script>
  <script src="js/fileuploader.js"></script>
<?php if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'YES'): ?>
  <script>

    var uploaders = new Array();
    var inc = 0;
    $(document).ready(function() {
        $('span.delete').click(function () {
          var that = $(this);
          var day = that.attr('day');
          if (!that.hasClass('disabled')) {
            that.addClass('disabled').text('Please wait...');
            $.ajax({
              url: "upload.php",
              type: "GET",
              data: "del=" + day,
              success: function (res) {
                that.text('Delete');
                if (res == "1") {
                  $('span.advertise[day=' + day +']').addClass('disabled important').removeClass('success').text('OFF');
                  $('img[day='+day+']').attr('src', 'images/none.png').css({'width': '48px', 'height': '48px', 'margin': '0px'});
                } else {
                  that.removeClass('disabled');
                  alert("Error deleting. Try again.");
                }
              }
            });
          }
        });

        $('span.advertise').click(function (){
          var that = $(this);
          var day = that.attr('day');
          var newstate = (that.text() == 'ON' ? 'off' : 'on');
          if (!(that.hasClass('disabled'))) {
            that.removeClass('important').removeClass('success').text('Please wait...').addClass('disabled');
            $.ajax({
              url: "upload.php",
              type: "GET",
              data: newstate + "=" + day,
              success: function (res) {
                that.removeClass('disabled');
                if (res == "1") {
                  that.addClass((newstate == 'on' ? 'success' : 'important')).text((newstate == 'on' ? 'ON' : 'OFF'));
                } else {
                  newstate = (newstate == 'on' ? 'off' : 'on');
                  that.addClass((newstate == 'on' ? 'success' : 'important')).text((newstate == 'on' ? 'ON' : 'OFF'));
                  alert('Error while changing state.');
                }
              }
              });
            }
        });

        $('.file-uploader').each(function () {
          var that = $(this);
          var day = that.attr('day');
          uploaders[inc] = new qq.FileUploader({
            element: document.getElementById('file-uploader-' + day),
            action: 'upload.php?day=' + day,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
            sizeLimit: 1024*1024,
            onSubmit: function (id, filename) {
              $('span.advertise[day='+day+']').addClass('disabled');
              $('span.delete[day='+day+']').addClass('disabled');
              $('img[day='+day+']').attr('src', 'loading.gif').css({'width': '30px', 'height': '30px', 'margin': '9px'});
            },
            onComplete: function(id, fileName, responseJSON){
              $('span.advertise[day='+day+']').removeClass('disabled');
              $('span.delete[day='+day+']').removeClass('disabled');
              $('img[day='+day+']').attr('src', 'images/' + responseJSON.filename).css({'width': '48px', 'height': '48px', 'margin': '0px'});
              $('span.advertise[day=' + day + ']').removeClass('disabled');
              $('span.delete[day=' + day + ']').removeClass('disabled');
            }
          });
          inc++;
        });
    });
  </script>
<?php endif; ?>
</head>
<body>
  <div class="topbar">
		<div class="topbar-inner">
			<div class="container">
				<div id="logo">
					<a class="brand" href="/tweekpic/">tWeekPic</a>
				</div>
				<ul class="nav">
					<li class="active">
            <a href="/tweekpic/">Home</a>
          </li>
					<li>
            <a href="#help">Help</a>
          </li>
				</ul>
				<ul class="secondary-nav">
<?php if (isset($_SESSION['logged']) && $_SESSION['logged'] == 'YES'): ?>
					<li id="session">
						<a href="http://twitter.com/<?php echo $_SESSION['screen_name']; ?>">
							<img src="<?php echo $_SESSION['avatar']; ?>" /><?php echo $_SESSION['screen_name']; ?>
						</a>
					</li>
					<li>
						<a href="?wipe=1">Sign out</a>
					</li>
          <li>
            <a href="?forget=1">Forget me</a>
          </li>
<?php else: ?>	
					<li>
						<a href="?authenticate=1">Sign in using Twitter</a>
					</li>
<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>