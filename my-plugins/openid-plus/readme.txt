=== OpenID Plus for bbPress ===
Tags: shivanand sharma, OpenID
Contributors: Shivanand Sharma, _ck_, Steve Love
Requires at least: 1
Tested up to: 1.0.2
Stable tag: trunk
Donate link: http://www.binaryturf.com/donate-and-contribute/

Adds OpenID login support to bbPress so users may login using an identity from another provider. 

== Description ==

Adds OpenID login support to bbPress so users may login using an identity from another provider. 

Give your members the ability to instantly login (while silently registering in the background if they are logging in the first time) with an OpenID provider.
Account registration is silent and happens automatically if an existing profile is not matched.

== Installation ==

* This plugin requires CURL to be installed with SSL support on your server.
   Check your PHPINFO for a CURL section which should have the word OpenSSL listed.

* Add the entire `openidplus/` folder to bbPress' `my-plugins/` directory.

* Activate

* Visit the configuration page

* Test the following once the above is completed:

1. Logout of the forum
2. Visit the registeration page
3. input your Openid URL and try to register
4. A successful authentication shall silently register you and log you in.
5. See the configuration page for further customization.

== Frequently Asked Questions ==

= What is OpenID =

* http://en.wikipedia.org/wiki/OpenID

* http://openid.net/

= How do I modify my alternate login to handle OpenID ? =

* You can add an input field called `openid_identity` to any form and the plugin will automatically pickup on any entry.

== License ==

* CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

== Donate ==

* http://www.binaryturf.com/donate-and-contribute/

== Changelog ==

= Version 1.0 (2010-March-11) =

* Public beta test