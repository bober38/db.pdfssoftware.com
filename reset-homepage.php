<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Resetting homepage</title>
	<link rel="stylesheet" href="/css/service_page.css">
	
</head>

<body>
<div class="all">

	<div class="clear">&nbsp;</div>

	<div class="dashboard-wrapper">

		<div class="dashboard-top"></div>

		<div class="dashboard">

            <h2>Resetting homepage</h2>
            <br>
            <h3>For Internet Explorer 7 and above</h3>
            <ol>
                <li>Navigate to the webpage you would like to set as the home page.</li>
                <li>Click the arrow to the right of the <strong>Home button</strong>, and then click <strong>Add or Change Home Page</strong>.</li>
                <li>In the Add or Change Home Page dialog box, do one of the following:
                    <ul>
                        <li>To make the current webpage your only home page, click <strong>Use this webpage as your only home page</strong>.</li>
                        <li>To start a home page tab set or to add the current webpage to your set of home page tabs, click <strong>Add this webpage to your home page tabs</strong>.</li>
                        <li>To replace your existing home page or home page tab set with the webpages you currently have open, click <strong>Use the current tab
                            set as your home page</strong>. This option will only be available if you have more than one tab open in Internet Explorer.</li>
                    </ul>
                </li>
                <li>Click <strong>Apply</strong> to save your changes.</li>
            </ol>
            <br>
            <h3>For Mozilla Firefox users</h3>
            <ol>
                <li>Navigate to the page you want set as your home page.</li>
                <li>At the top of the Firefox window on the menu bar, click on the <strong>Tools</strong>, and select <strong>Options</strong>.</li>
                <li>If it isn't already selected, click the <strong>Main</strong> icon. The Main options panel is displayed.</li>
                <li>Click on <strong>Use Current Page</strong>.&nbsp; <em>(If you have a group of tabs open, the entire group of tabs will be set as your home page.
                    You can also use the <strong>Use Bookmark</strong> button to set one of your bookmarks as your home page.)</em></li>
                <li>Click <strong>Ok</strong> to close the Options window</li>
            </ol>
            <br>
            <h3>For Chrome users</h3>
            <ol>
                <li>Click the Chrome menu <img src="images/chrome_menu.png" alt="Chrome menu panel"> on the browser toolbar.</li>
                <li>Select <strong>Settings</strong>.<br>
                    <ul>
                        <li>
                            <strong>Add the home button to the browser toolbar</strong><br>
                            Home page button is off by default. Select the "Show Home button" checkbox in the "Appearance" section to show it on the browser toolbar.
                        </li>
                        <li>
                            <strong>Set your home page</strong><br>
                            When the "Show Home button" checkbox is selected, a web address appears below it.
                            If you want the Home page button to open up a different webpage, click <strong>Change</strong> to enter a link.
                            You can also choose the <a href="http://support.google.com/chrome/bin/answer.py?hl=en&answer=95451">New Tab page</a> as your home page.
                        </li>
                    </ul>
                </li>

            </ol>
            <br>
            <h3>For Safari users</h3>
            <ol>
                <li>Click on Safari in your Safari menu, located at the top of your screen. When the drop-down menu appears, choose <strong>Preferences</strong>.</li>
                <li>Select <strong>General</strong> from the Preferences menu, and you will notice a section labeled Home Page in the main window of the Preferences dialog.</li>
                <li>Edit the input box to your selected homepage URL or click <strong>"Set to Current Page"</strong> to use the page you are currently viewing as the new homepage</li>
            </ol>
		</div>

		<div class="dashboard-bottom"></div>
	</div>

	<? $contactus_domain=basename($_SERVER['HTTP_HOST']); include('/var/www/vhosts/common/include/contactlib_addr.php'); ?>

	<div class="footer">
		<a href="#" onclick="show_cont();return false;">Contact Us</a>&nbsp;&nbsp;
		|&nbsp;&nbsp;<a href="/privacypolicy.php">Privacy Policy</a>
	</div>

	<div class="clear"></div>

</div>
</body>
</html>
