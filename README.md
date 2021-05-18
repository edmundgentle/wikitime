wikitime
========

This was a bit of an experiment so the code is pretty scrambled and needs refactoring. It isn't 100% accurate at determining the meaning of sentences, but tries its best.

It has been written in PHP, with some basic styling on the front end. It could do with having caching implemented, and a nicer design on the front end. I thought I'd just put the code on GitHub as I won't really have the time to make any of these changes myself any time soon.

Getting Started
---------------

This is a PHP application. It doesn't require a database or any storage, but it does need network access (to access Wikipedia) and the `curl` PHP extension installed. It should run on a Linux/Apache web server setup, with URL Rewriting enabled. Then to access it, just go to the homepage of your server (`index.php`). Enjoy!
