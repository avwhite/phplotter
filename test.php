<html>
	<head>
		<title>Test of cfunc</title>
	</head>
	<body>
		<form method="post" action="test.php" />
			f(x)=<input type="text" name="func" /><br />
			x-range=<input type="test" name="xr" />
			<input type="submit" />
		</form>
		<?php
		echo '<img src="plot.php?expr='.urlencode($_REQUEST['func']).'&xr='.$_REQUEST['xr'].'" />';
		?>
		<br />
	</body>
<html>
