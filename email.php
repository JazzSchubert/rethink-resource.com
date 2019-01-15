<?php
# Don't put anything above the previous line, not even blank space

# Copyright 2007, Thomas Boutell and Boutell.Com, Inc. You
# MAY use this code in your own projects. You MAY NOT
# represent this code as your own work. If you wish to share
# this code with others, please do so by sharing the
# following URL:
#
# http://www.boutell.com/newfaq/creating/email.html

# OPTIONAL: use Accountable to prefill the user's real name and
# email address. Requires that you install and configure
# Accountable first. If your site has no use for accounts elsewhere
# this isn't worth the trouble.
#
# If you ARE using Accountable, uncomment the "require" line below.
# You will probably have to fix the path to find login.php in the
# appropriate folder.
# 
# See: http://www.boutell.com/newfaq/creating/accounts.html

#require "login.php";

#require '/home/boutell/html/tools/accountable/login.php';

# OPTIONAL: use my Captcha system to prevent automated
# abuse of the contact form. You'll need to install and
# configure the captcha code first. 
#
# If you DO have my captcha installed, uncomment the "require" line
# below. You will probably have to fix the path to find captcha.php
# in the appropriate folder.
#
# See: http://www.boutell.com/newfaq/creating/captcha.html

#require "captcha.php";

# The person who receives the email messages
#$recipient = 'example@example.com';

# Usually $_SERVER['SERVER_NAME'] is fine, but if you have an unusual
# hosting setup, you might need to set this manually to 'www.mysite.com'
#$serverName = $_SERVER['SERVER_NAME'];

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
	# Import $login object from accountable. If we're not using
	# accountable this is null, which is not a problem

	# Re-display existing input so that the user doesn't have to enter
	# things again when correcting a problem with just one field.
	# Make sure etc. entered by the user are not treated 
	# as part of the HTML of the page

	# If the email address and real name haven't been entered yet,
	# check $_SESSION for them. If Accountable is in use, we can
	# pre-fill these fields for the user.
	$escapedEmail = htmlspecialchars($_POST['email']);
	$escapedRealName = htmlspecialchars($_POST['realname']);
	$escapedSubject = htmlspecialchars($_POST['subject']);
	$escapedBody = htmlspecialchars($_POST['body']);
?>
<html>
<script type="text/javascript">
	localStorage.setItem("submitSuccess", JSON.stringify(implode("<?php echo $messages?>")));
</script>
</html>
<?php
	redirect('contact.html');
}

function redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);

    exit();
}

function sendMail()
{
	$messages = array();
	$email = $_POST['email'];
	# Allow only reasonable email addresses. Don't let the
	# user trick us into backscattering spam to many people.
	# Make sure the user remembered the @something.com part
	if (!preg_match("/^[\w\+\-\.\~]+\@[\-\w\.\!]+$/", $email)) {
		$messages[] = "That is not a valid email address. Perhaps you left out the @something.com part?";
	}
	$realName = $_POST['realname'];
	if (!preg_match("/^[\w\ \+\-\'\"]+$/", $realName)) {
		$messages[] = "The real name field must contain only alphabetical characters, numbers, spaces, and the + and - signs. We apologize for any inconvenience.";
	}
	$subject = $_POST['subject'];
	# CAREFUL: don't allow hackers to sneak line breaks and additional
	# headers into the message and trick us into spamming for them!
	$subject = preg_replace('/\s+/', ' ', $subject);
	# Make sure the subject isn't blank (apart from whitespace)
	if (preg_match('/^\s*$/', $subject)) {
		$messages[] = "Please specify a subject for your message.";
	}
	
	$body = $_POST['body'];
	# Make sure the message has a body
        if (preg_match('/^\s*$/', $body)) {
		$messages[] = "Your message was blank. Did you mean to say something? Click the Cancel button if you do not wish to send a message.";
	}
	if (count($messages)) {
		# There were errors, so re-display the form with
		# the error messages and let the user correct
		# the problem

		displayForm($messages);
		return;

	} else {
?>
<html>
<script type="text/javascript">
	localStorage.removeItem("realName");
	localStorage.removeItem("escapedEmail");
	localStorage.removeItem("escapedBody");
	localStorage.setItem("submitSuccess", JSON.stringify(true));
	console.log(localStorage.getItem("submitSuccess"));
</script>
</html>
<?php
	}
	# No errors - send the email	
	mail('webmaster@rethink-resource.com',
		$subject,
		$body,
		"From: $realName <$email>\r\n" .
		"Reply-To: $realName <$email>\r\n");
	# Thank the user and invite them to continue, at which point
	# we direct them to the page they came from. Don't allow
	# unreasonable characters in the URL
	$escapedReturnUrl = htmlspecialchars($_POST['returnurl']);
	redirect('contact.html');
	exit();
}
?>

