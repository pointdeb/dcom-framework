<!DOCTYPE html>
	<html class="dcom">
		<head>
			<title> Lemur\'F</title>
			<link rel="icon" type="image/png" href="<?= $root ?>/layout/dcom-css/img/kungfu.ico" />
			<link rel="stylesheet" href="<?= $root ?>/layout/bootstrap-3.3.6/dist/css/bootstrap-theme.min.css" type="text/css">
			<link rel="stylesheet" href="<?= $root ?>/layout/bootstrap-3.3.6/dist/css/bootstrap.min.css" type="text/css">
			<link rel="stylesheet" href="<?= $root ?>/layout/dcom-css/main.css" type="text/css">
		</head>
		<body class="dcom round">
			<h2 class="title">Dcom-db</h2>
			<hr>
			<form method="post" id="initConf">
				<div class="form-group">
				  	<label for="host">DB host:</label>
					<input type="text" name="host" id="host" class="form-control" id="host" />
				  	<label for="user">DB User:</label>
					<input type="text" name="user" class="form-control" id="user" />
				  	<label for="pass">DB Password:</label>
					<input type="text" name="pass" class="form-control" id="pass" />
				  	<label for="dbname">DB Name:</label>
					<input type="text" name="dbname" class="form-control" id="dbname" />
					<button type="submit" name="confirm" class="btn btn-primary glyphicon glyphicon-floppy-save"> Run</button>
				</div>
			</form>
			<?php
				use dcom\components\config\Config;
				if (isset($_POST['confirm'])) {
					if(Config::setConf($_POST)){
						/*$controller=new dcom\controllers\Controller();
		        $db=$controller->get('framework::DbBuilder');
		        $db->setName($array['dbname']);
		        $db->setDataType();
		        $db->execute();*/
						header('location:?');
					}
				}
			?>
			<script type="text/javascript" src="<?= $root ?>/layout/dcom-js/jquery-1.11.2.min.js"></script>
			<script type="text/javascript" src="<?= $root ?>/layout/bootstrap-3.3.6/dist/js/bootstrap.min.js"></script>
		</body>
	</html>
