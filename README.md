SimpSpector
===========

[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/simpspector/simpspector?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/simpspector/simpspector.svg?branch=master)](https://travis-ci.org/simpspector/simpspector)

**ALPHA** SimpSpector integrates with gitlab (and in the future possible github and other providers) and inspects your code, using SimpSpector Gadgets (get it?)

![Image](docs/img/dashboard.png?raw=true)
![Image](docs/img/project.png?raw=true)
![Image](docs/img/commit.png?raw=true)
![Image](docs/img/commit2.png?raw=true)

Requirements
------------

* composer
* npm
* sass

Installation
------------

It's very easy (now) :-)

```bash
bin/update
```

Development
-----------

```bash
npm start #short cut for: node_modules/.bin/gulp watch
```

phpStorm integration
--------------------
* download latest version from https://github.com/zolotov/RemoteCall/downloads and place it in phpStorm plugins directory
* open a project and test if `http://localhost:8091/?message=composer.json:11` works

Documentation
-------------

* [configuring projects to use SimpSpector](docs/simpspector.yml.md)
* [Development Environment Installation](docs/development-environment.md)
