Open-source Android Translator
==============================

OAT is a web-based interface that helps developers manage their localized strings for any Android app.

It is aimed to do what others don't:

* Handle `<![CDATA[ ]]>` tags, `formatted="false"`, `string`s, `string-array`s, `plural`s
* Handle multiple XML files (if you use `strings_main.xml`, `string_preferences.xml`, etc.)
* Organize app contexts: main menu, activity A, settings pane, etc.
* Organize app screenshots: associate one or more screenshots to a specific context
* Is free, and open-sourced

Feel free to contribute. This is the goal of that tool.

How does it work?
-----------------

There are several main sections.

***Home***

That's a dashboard of the current available languages, their translation status.  
There's also a *main language*, which is the one you use for developing. All other languages compare to this one, and the screenshot should hence be uploaded under that language.

***Contexts***

This manages the app contexts (*main screen*, *preferences activity*, *content page*, etc.); but also the screenshots that you can upload. You link a screenshot to a context. You link a text string to a context.

***Strings***

Well, that's where the work has to be done. Strings are represented along with:

* one or several screenshots, corresponding to the context of that string;
* the string name, as in `R.string.xyz`;
* the text in the *main language*, as it appears in the screenhot(s);
* the text to be translated to the target language

Hence, translators can relate the string in the main language with a screenshot in order to fully understand the context and provide a quality translation.

***Import***

Well. You upload the xml file. You pick the language. Bingo.

***Export***

Well. You pick the language. You download the xml file. Bingo.

Requirements
------------

This is a web-based PHP application, requiring a [PDO][1]-capable database.
The development has been done with:

* PHP 5.4
* MySQL 5.1
* Apache 2.2

Installing
----------

```
cd www
git clone https://github.com/BenoitDuffez/OAT.git
mv config.db.php.dist config.db.php
vi config.db.php
chmod 666 screenshots # set this to appropriate permissions
```

You should be able to edit the config file without any issue. It's just the database parameters (server, credentials and table prefix).

Upgrading
---------
Database tables use the same pattern as Android databases, meaning that they keep a DB version somewhere. When the table schema is upgraded from the code, the version is incremented and the table schema is automatically updated on the server.

This should mean that you can just `git pull`.

License
=======

```
Copyright 2013 Benoit Duffez

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

This software uses code licensed under the MIT license:

 * https://github.com/blueimp/jQuery-File-Upload

  [1]: http://php.net/intro.pdo
