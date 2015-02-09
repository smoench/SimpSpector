Development Environment Installation
------------------------------------

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
  * `cd web/css && ln -s ../../assets/bower_components/semantic/dist/themes .`
* in gitlab
  * create a test project and check it out
  * under Settings -> WebHooks check "Push events" and "Merge Request events"
  * enter `http://192.168.13.37/SimpSpector/web/app_dev.php/hooks/gitlab`
* install assets:

```bash
$ cd assets
$ bower install
$ npm install
$ gulp styles js
```

fig config

```yaml
postgresql:
  image: sameersbn/postgresql:9.1-1
  environment:
    - DB_USER=gitlab
    - DB_PASS=secretpassword
    - DB_NAME=gitlabhq_production
gitlab:
  image: sameersbn/gitlab:7.4.3
  links:
   - redis:redisio
   - postgresql:postgresql
  ports:
   - "64001:80"
   - "64000:22"
  environment:
   - GITLAB_HOST=gitlab
redis:
  image: sameersbn/redis:latest
```
GitLab standard Login

Username: root
Password: 5iveL!fe
