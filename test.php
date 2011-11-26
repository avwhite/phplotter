<?php include('cfunc.php'); ?>
<html>
	<head>
		<title>Test of cfunc</title>
	</head>
	<body>
		<?php
		$func = isset($_POST['func']) ? $_POST['func'] : '';
		$xmax = isset($_POST['xmax']) ? $_POST['xmax'] : '';
		$xmin = isset($_POST['xmin']) ? $_POST['xmin'] : '';
		$ymax = isset($_POST['ymax']) ? $_POST['ymax'] : '';
		$ymin = isset($_POST['ymin']) ? $_POST['ymin'] : '';
		?>
		<form method="post" action="test.php" />
			f(x)=<input type="text" name="func" value="<?php echo $func; ?>"/><br />
			xmin=<input type="text" name="xmin" value="<?php echo $xmin; ?>"/>xmax=<input type="text" name="xmax" value="<?php echo $xmax; ?>"/><br />
			ymin=<input type="text" name="ymin" value="<?php echo $ymin; ?>"/>ymax=<input type="text" name="ymax" value="<?php echo $ymax; ?>"/><br />
			<input type="submit" name="submit" />
		</form>
		<?php
		echo '<img src="plot.php?expr='.urlencode($_REQUEST['func']).'&xmin='.$_REQUEST['xmin'].'&xmax='.$_REQUEST['xmax'].'&ymin='.$_REQUEST['ymin'].'&ymax='.$_REQUEST['ymax'].'" />';
		?>
		<br />
	</body>
<html>
