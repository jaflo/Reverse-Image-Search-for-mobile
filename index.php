<?php
foreach (glob("uploaded/*") as $file) {
	if (filemtime($file)<time()-43200 && $file!=="uploaded/index.html") { // older than 12hrs
		unlink($file);
	}
}
if (isset($_FILES["file"])) {
	$random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 12);
	$allowed = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if ( ( ($_FILES["file"]["type"] == "image/gif")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/jpg")
		|| ($_FILES["file"]["type"] == "image/pjpeg")
		|| ($_FILES["file"]["type"] == "image/x-png")
		|| ($_FILES["file"]["type"] == "image/png"))
		&& ($_FILES["file"]["size"] < 5000000) // 5mb
		&& in_array($extension, $allowed)) {
		if ($_FILES["file"]["error"] > 0) {
			$error = "Failed with: ".$_FILES["file"]["error"];
		} else {
			while (file_exists("uploaded/".$random.".".$extension)) {
				$random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 12);
			}
			move_uploaded_file($_FILES["file"]["tmp_name"], "uploaded/".$random.".".$extension);
			header("Location: http://images.google.com/searchbyimage?image_url=http://".
					$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."uploaded/".$random.".".$extension);
			exit;
		}
	} else {
		$error = "Invalid file";
	}
}
?>
<html>
<head>
<title>Reverse Image Search</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
<style>
body, table {
	font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	font-size: 0;
}

#formwrap {	
	display: inline-block;
	padding: 3px;
	position: relative;
	overflow: hidden;
	border-radius: 5px;
	background: #eee;
}

#progress {
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	width: 0;
	background: dodgerblue;
	border-radius: 5px 0 0 5px;
}

form {
	border: 1px solid black;
	display: inline-block;
	overflow: hidden;
	border-radius: 3px;
	background: white;
	position: relative;
}

#filewrap {
	position: relative;
	padding: 7px;
	display: inline-block;
	border-right: 1px solid black;
	font-size: 16px;
}

#filewrap span {
	max-width: 200px;
	text-overflow: ellipsis;
	white-space: nowrap;
	display: inline-block;
}

#filewrap input {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	opacity: 0;
	margin: 0;
}

#preview {
	height: 100%;
	left: 0;
	top: 0;
	position: absolute;
	background-size: cover;
	background-position: center;
}

button {
	border: 0;
	background: white;
	font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
	font-size: 16px;
	padding: 7px;
	margin: 0;
	outline: none;
	position: relative;
}

iframe {
	display: none;
}
</style>
</head>
<body>
<table style="width:100%;height:100%"><tr><td style="text-align:center;vertical-align:middle">
	<?php if (isset($error)) echo '<div id="error">'.$error.'</div>'; ?>
	<div id="formwrap">
		<div id="progress"></div>
		<form id="form" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" value="upload" name="<?php echo ini_get("session.upload_progress.name"); ?>" />
			<div id="filewrap">
				<div id="preview"></div>
				<span id="text">Choose image</span>
				<input type="file" name="file" id="file" />
			</div>
			<button type="submit">Submit</button>
		</form>
	</div>
</td></tr></table>
<iframe id="iframe" name="iframe" src="about:blank"></iframe>
<script>
document.getElementById("preview").style.width = document.getElementById("preview").clientHeight;
document.getElementById("file").onchange = function() {
	document.getElementById("text").innerHTML =
		document.getElementById("file").value.replace(/\\/g, "/").replace(/.*\//, "");
	document.getElementById("filewrap").style.paddingLeft = (document.getElementById("preview").clientHeight+7)+"px";
	if (this.files && this.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			document.getElementById("preview").style.backgroundImage = "url("+e.target.result+")";
		}
		reader.readAsDataURL(this.files[0]);
	}
}
</script>
</body>
</html>