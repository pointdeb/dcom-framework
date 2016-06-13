<!DOCTYPE html>
<html lang="en" class="dcom">
	<head>
		<meta charset="utf-8">
		<title>Error</title>
		<link rel="stylesheet" type="text/css" href="<?= $root ?>/layout/dcom-css/error.css">
		<link rel="stylesheet" type="text/css" href="<?= $root ?>/layout/dcom-css/main.css">
	</head>
	<body class="dcom">

		<div class="message">
			<h1>Error</h1>
			<hr>
			<p>Aw, snap! An error has occurred while processing your request.</p>
			<p><?php echo $msg; ?></p>
		</div>

	</body>
</html>
