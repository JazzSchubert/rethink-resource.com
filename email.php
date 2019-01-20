<?php

$serverName = $_SERVER['www.rethink-resource.com'];

if ($_POST['send']) {
	sendMail();
} elseif (($_POST['cancel']) || ($_POST['continue'])) {
	redirect('contact.html');
	exit();
} else {
	displayForm([]);
}

function displayForm($messages)
{
	$escapedEmail = htmlspecialchars($_POST['email']);
	$escapedRealName = htmlspecialchars($_POST['realname']);
	$escapedBody = htmlspecialchars($_POST['body']);
?>
<script type="text/javascript">
	console.log("<?php echo $messages?>");
	localStorage.setItem("submitSuccess", JSON.stringify("<?php echo $messages[0]?>"));
	if (localStorage.getItem("submitSuccess")) {window.location.replace("contact.html");}
</script>
<?php
}

function redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

function sendMail()
{
	$email = $_POST['email'];
	if (!preg_match("/^[\w\+\-\.\~]+\@[\-\w\.\!]+$/", $email)) {
		$messages[] = "That is not a valid email address. Perhaps you left out the @something.com part?";
	}
	$realName = $_POST['realname'];
	if (!preg_match("/^[\w\ \+\-\'\"]+$/", $realName)) {
		$messages[] = "The real name field must contain only alphabetical characters, numbers, spaces, and the + and - signs. We apologize for any inconvenience.";
	}
	$body = $_POST['body'];
    if (preg_match('/^\s*$/', $body)) {
		$messages[] = "Your message was blank. Did you mean to say something? Click the Cancel button if you do not wish to send a message.";
	}
	if (count($messages)) {
		displayForm($messages);
		return;

	} else {
	mail('info@rethink-resource.com',
		'Contact Form at rethink-resource.com',
		$body,
		'From: $realName <$email>\r\n' .
		'Reply-To: $realName <$email>\r\n');
	}
?>
<script type="text/javascript">
	localStorage.removeItem("realName");
	localStorage.removeItem("escapedEmail");
	localStorage.removeItem("escapedBody");
	localStorage.setItem("submitSuccess", JSON.stringify(true));
	console.log(localStorage.getItem("submitSuccess"));
	if (localStorage.getItem("submitSuccess")) {window.location.replace("contact.html");}
</script>
<?php

	exit();
}
?>

