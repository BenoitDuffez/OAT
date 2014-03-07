<?php

setHtmlTitle('OAT - Help');

?>
<h1>Welcome to the OAT service</h1>
<h2>About</h2>
<p>OAT is an open-source software. Please check <a href="https://github.com/BenoitDuffez/OAT">the GitHub project</a></p>
<?php

switch ($GLOBALS['user']->role) {
case Role::ANONYMOUS:
?>
<p>In order to help translate, please <a href="%PATH%/account/register.html">register</a> for an account.</p>
<?php
	break;

case Role::ADMINISTRATOR:
?>
<h3>Getting started</h3>
<ol>
    <li>Go <a href="%PATH%/import/">import</a> all your strings.xml file(s)</li>
    <li>Start <a href="%PATH%/translate/">translating</a></li>
</ol>
<p>Yep, that's it! :)</p>
<h3>Advanced usage</h3>
<ul>
    <li>Add <a href="%PATH%/contexts/">contexts</a> that are the main sections of your app. While you're there,
        associate the strings with contexts.
    </li>
    <li>Associate <a href="%PATH%/screenshots/">screenshots</a> with the contexts, to help for translation. Screenshots
        will automatically apear below the translation boxes.
    </li>
</ul>
<?php

case Role::REGISTERED:
?>
<h2>Translate</h2>
<p>Go to the <a href="%PATH%/">home page</a> and start translating!</p>
<?php
	break;
}

