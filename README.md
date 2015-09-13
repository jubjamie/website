# About
The original Backstage website (known as `version 2`) was developed by Colin Paxton several years ago and later maintained by Lee Stone. It has been well used by the membership, and has been continually improved by several members resulting in a feature-rich system.

Unfortunately this site was built on some best practices which are now outdated. This means that the site uses some deprecated and insecure PHP functionality and the mixing of PHP and HTML makes both the file structure and website's styling almost impossible to manage. 

It was decided that a new version of the website should be developed to "modernise" the site and make future updates much easier to implement.

## The new website
Ben Jones (bmj23) began development of the new website in January 2015. This re-development would not aim to introduce any new functionality; instead it would focus on producing the same functionality using a framework to aid organisation and collaboration. This would also include a newer, more 'modern' style, which would make use of [Bootstrap](http://getbootstrap.com/) to make it responsive to the viewport size.

This website would be `Version 4` to avoid confusion over a `Version 3` planned by Lee.

This is the `git` repository of this newer website.

# Development
A huge bonus of using `git` to manage the website is that anyone can clone the repository and work on a feature.

If you wish to do so, follow the instructions below.

## Installation
*   Clone the repo using `git clone`
*   Install the dependencies using `composer install`
*   Run `npm install` to enable Laravel Elixir (Node.js required)
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
