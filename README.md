# About
The original Backstage website (known as `version 2`) was developed by Colin Paxton several years ago and later maintained by Lee Stone. It has been well used by the membership, and has been continually improved by several members resulting in a feature-rich system.

Unfortunately this site was built on some best practices which are now outdated. This means that the site uses some deprecated and insecure PHP functionality and the mixing of PHP and HTML makes both the file structure and website's styling almost impossible to manage. 

It was decided that a new version of the website should be developed to "modernise" the site and make future updates much easier to implement.

## The new website
Ben Jones (bmj23) began development of the new website in January 2015. This re-development would not aim to introduce any new functionality; instead it would focus on producing the same functionality using a framework to aid organisation and collaboration. This would also include a newer, more 'modern' style, which would make use of [Bootstrap](http://getbootstrap.com/) to make it responsive to the viewport size.

This website would be `Version 4` to avoid confusion over a `Version 3` planned by Lee.

This is the `git` repository of this newer website.

## Migration
While the functionality will remain the same, this newer website will adjust the database structure slightly for 2 reasons:

1. General database structure improvements
2. Preparation for future improvements

As a result the site cannot simply use the current database, meaning that the data will need to be automatically migrated over. This will require that the current site is LOCKED and SHUTDOWN for a period of time whilst the database is migrated and the migration is tested.

The migration will take place during a time of low-activity to reduce its impact on our clients and a clear warning will be provided for a significant amount of time leading up to this process.

## Development issues
The re-development was originally planned to be completed in time for the new Semester in October 2015. This would have allowed the switch to take place during the holiday, which is when the site's usage is at its lowest.

Unfortunately development issues resulted in a change of framework, to [Laravel 5](http://laravel.com/). This meant that a significant amount of code would need to be re-worked and the deadline would be pushed back by at least several months.

## Completion date
There is currently no concrete release date. However it is hoped that the newer site can be implemented during 'Inter-Semester Break' after the January 2016 exams, as this is another period of low-activity for the website.

# Development
A huge bonus of using `git` to manage the website is that anyone can clone the repository and work on a feature.

If you wish to do so, follow the instructions below.

## Installation
*   Clone the repo using `git clone`
*   Install the dependencies using `composer install`
*   Create your *.env* file using the included *.env.example* as a template

	> This site requires both `mysql` and `smtp` settings  
		This also requires an App ID (`FACEBOOK_APP_ID`) and Secret (`FACEBOOK_APP_SECRET`) for Facebook's Graph API
*   Run `php artisan key:generate` and `php artisan migrate`
*   Run `php artisan db:seed` to insert the default data

	> This will insert the su2bc account, default webpages and initial committee structure.
*   Run the PHP server or configure your own server to point to the `public` directory

## Disclaimer
While anyone can make changes and submit pull requests, I do assume a sufficient level of knowledge on the use of PHP, Apache/nginx, MySQL, git, composer and Laravel to install this repo and get it running.

# License
This website uses code from Laravel and various packages, which retain their original licenses. The code developed specifically for this app is covered by the GNU General Public License v2 (see the included LICENCE file).
