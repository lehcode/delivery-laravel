# Project setup

### Requirements
1. Git
2. Composer
3. Node.js

### Project Setup

1. Create project_dir
2. `cd project_dir`
3. `git clone --recursive git@bitbucket.org:zeesofts/barq-back-end.git`
4. `composer install` will install dependencies listed in `/composer.lock`
5. Issue `php artisan ide-helper:meta` if using PhpStorm IDE. This will generate a PhpStorm meta file to add support for factory design pattern.
