<?php include('cfunc.php'); ?>
<html>
	<head>
		<title>Test of cfunc</title>
	</head>
	<body>
		<?php echo null / 2; ?>
		<form method="post" action="test.php" />
			f(x)=<input type="text" name="func" /><br />
			xmin=<input type="text" name="xmin" />xmax=<input type="text" name="xmax" /><br />
			ymin=<input type="text" name="ymin" />ymax=<input type="text" name="ymax" />
			<input type="submit" />
		</form>
		<?php
		echo '<img src="plot.php?expr='.urlencode($_REQUEST['func']).'&xmin='.$_REQUEST['xmin'].'&xmax='.$_REQUEST['xmax'].'&ymin='.$_REQUEST['ymin'].'&ymax='.$_REQUEST['ymax'].'" />';
		?>
		<br />
	</body>
<html>
