SimpSpector
===========

**ALPHA** SimpSpector integrates with gitlab and inspects your code, using SimpSpector Gadgets (get it?)

![Image](../blob/master/docs/dashboard.png?raw=true)
![Image](../blob/master/docs/project.png?raw=true)
![Image](../blob/master/docs/commit.png?raw=true)
![Image](../blob/master/docs/commit2.png?raw=true)


Install Development Environment
-------------------------------

My Windows experience:
* Install Vagrant
* Install dev environment e.g. via `https://github.com/DavidBadura/LAMP`
* Install docker on the vm
* Install gitlab e.g. via `https://github.com/sameersbn/docker-gitlab`
* `git clone "git@github.com:simplethings/SimpSpector.git"` to `/var/www`
* under `/var/www/SimpSpector`
  * `composer install`
  * get private token from "User Profile -> Account"
  * gitlab url: `127.0.0.1:64001/api/v3/`
  * `php bin/console doctrine:database:create`
  * `php bin/console doctrine:migrations:migrate`
* in gitlab
  * create a test project and check it out
  * under Settings -> WebHooks check "Push events" and "Merge Request events"
  * enter `http://192.168.13.37/SimpSpector/web/app_dev.php/hooks/gitlab`
* install assets:

```bash
cd assets
bower install
npm install --dev
gulp styles
gulp js

```

phpStorm integration
--------------------
* download latest version from https://github.com/zolotov/RemoteCall/downloads and place it in phpStorm plugins directory
* open a project and test if `http://localhost:8091/?message=SomeFile.php:11` works


