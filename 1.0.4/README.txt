RuneScape Community Script was developed by Sub-Zero  (or, Moparscape: SRBuckey5266 and RuneLocus: Justin H) and Ruby.

http://www.rcscript.comlu.com for support, feedback, or concerns
=========================================================================
===============================NOTICE====================================
=========================================================================

You're allowed to remove the copyright in the footer, however please
do not re-publish RCS without my permission. Also, do not provide
direct download links.

Upon installation, an account with the username Report is created. Do not
modify or delete this account. It is the account that is used which will
automatically post forum reports in the specified report section. In
no way can the account allow someone to gain administrative access to your
site. Upon every RCS installation, the account is given a randomly generated
cookie/session (so it's not the same cookie for everyone), and even if 
someone managed to get the password to the account, login is disabled for it. 

^^^^^
tl;dr - don't delete/modify the administrative user Report. It's safe & necessary.

=========================================================================
===========================CUSTOMIZATION=================================
=========================================================================

ENABLING POST COUNT UNDER POSTS
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Go into PhpMyAdmin or whatever your database manager is, and click on 
the "config" table. Change "postcount" to 1. ---- 1 = on, 0 = off



ADDING RECAPTCHA TO REGISTER PAGE
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
You can also add recaptcha to your account creation page in includes/config.php


CHANGING URL OF "PLAY" BUTTON ON HOMEPAGE FOR LOGGED IN USERS
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Go to includes/config.php and change the URL in $play_url