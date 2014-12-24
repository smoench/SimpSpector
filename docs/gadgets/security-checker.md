Security Checker
================

This gadget checks your composer.lock if you have installed packages with security vulnerabilities. 

**parameters**

* directory
    * which folder has the composer.lock, relative to the projects root folder.
    * defaults to './'
* level
    * which level have this issue
    * possible values: notice, warning, error or critical
    * default: critical

configuration snippet

```yml
security-checker:
  director: ./
  level: critical
```