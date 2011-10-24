<?php include('cfunc.php'); ?>
<html>
	<head>
		<title>Test of cfunc</title>
	</head>
	<body>
		<form method="post" action="test.php" />
			f(x)=<input type="text" name="func" /><br />
			x-range=<input type="text" name="xr" /><br />
			y-range=<input type="text" name="yr" />
			<input type="submit" />
		</form>
		<?php
		echo createFunc($_REQUEST['func'])->evalu(1)."<br />";
		echo '<img src="plot.php?expr='.urlencode($_REQUEST['func']).'&xr='.$_REQUEST['xr'].'&yr='.$_REQUEST['yr'].'" />';
		?>
		<br />
	</body>
<html>
