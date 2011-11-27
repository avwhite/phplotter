<?php include('cfunc.php'); ?>
<html>
	<head>
		<title>Test of cfunc</title>
		<style type="text/css">
		body {
			width:500;
			text-align:center;
			margin-left:auto;
			margin-right:auto;
		}
		h2 {
			font-size:10pt;
			font-style:italic;
			margin-top:2px;
		}
		h1 {
			margin-bottom:2px;
		}
		</style>
	</head>
	<body>
		<div id="content">
			<h1>Phplotter</h1>
			<h2>A function plotter written in php</h2>

			<div id="input">
				<?php
				$func = isset($_POST['func']) ? $_POST['func'] : '';
				$xmax = isset($_POST['xmax']) ? $_POST['xmax'] : '';
				$xmin = isset($_POST['xmin']) ? $_POST['xmin'] : '';
				$ymax = isset($_POST['ymax']) ? $_POST['ymax'] : '';
				$ymin = isset($_POST['ymin']) ? $_POST['ymin'] : '';
				?>
				<form method="post" action="test.php" />
					f(x)=<input type="text" name="func" value="<?php echo $func; ?>"/><br />
					xmin=<input type="text" name="xmin" value="<?php echo $xmin; ?>"/>
					xmax=<input type="text" name="xmax" value="<?php echo $xmax; ?>"/><br />
					ymin=<input type="text" name="ymin" value="<?php echo $ymin; ?>"/>
					ymax=<input type="text" name="ymax" value="<?php echo $ymax; ?>"/><br />
					<input type="submit" name="submit" value="PLOT!"/>
				</form>
			</div>

			<div id="output">
				<?php
				if(isset($_REQUEST['submit'])) {
					echo '<img src="plot.php?expr='.urlencode($_REQUEST['func']).'&xmin='.$_REQUEST['xmin'].'&xmax='.$_REQUEST['xmax'].'&ymin='.$_REQUEST['ymin'].'&ymax='.$_REQUEST['ymax'].'" />';
				}
				?>
			</div>
		</div>
	</body>
<html>
