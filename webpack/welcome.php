<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if(empty(session("profile")) === false) {
	$laravelProfile = session("profile");
	$genericProfile = new stdClass();
	$genericProfile->profileId = $laravelProfile->profileId;
	$genericProfile->profileAtHandle = $laravelProfile->profileAtHandle;
	$_SESSION["profile"] = $genericProfile;
} else {
	$_SESSION["profile"] = null;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<base href="/" />
		<title>Twitter Ipsum</title>
	</head>
	<body>
		<twitter-ipsum-app>Loading&hellip;</twitter-ipsum-app>
	</body>
</html>