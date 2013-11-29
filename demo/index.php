<?php if (isset($_GET['api']) && $_POST) { 
sleep(1);
echo '{"messages":"Success","errors":""}'; // JSON
exit;
} ?><!DOCTYPE html>
<html>

<head>
<title>Wizardacious Demo</title>
<link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap.css" rel="stylesheet">
<link href="../src/css/wizardacious.css" rel="stylesheet">
<link href="assets/css/demo.css" rel="stylesheet">
</head>
<body>

<header>
    <h1>Wizardacious Demo</h1>
</header>

<section>
    <form action="?api" id="my" class="wizard">
    </form>
</section>

<footer>
    <p>Copyright&copy; Ryan Briscall</p>
</footer>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
<script src="assets/js/jquery.maskedinput.min.js"></script>
<script src="assets/js/jquery.validate.js"></script>

<script src="assets/js/wizardacious.min.js"></script>
<script>$(document).ready(function() { Wizardacious.action('my'); });</script>

</body>
</html>