# chatter - A full-stack Messaging Tool

This is a Web Application that can run on any device -

based on Laravel 5.6 with Laravel Echo, using Pusher. SPA front-end using Vue.js.

## Prerequisites

Some basic understanding of the [Laravel](https://laravel.com) architecture is mandatory.

A free [Pusher](https://pusher.com) account is necessary in order for this to work!

## Installation

1. `git clone https://github.com/matthiku/chatter.git`
1. `composer install`
1. `cp .env.example .env`
1. _fill out .env!_
1. _create a database_
1. `php artisan key:generate`
1. `php artisan migrate`
1. `npm install`
1. `php artisan passport:keys`
1. `npm run watch` _(for development only)_
1. `npm run production`

Note: the first user to register on a new installation will have the id of 1 and will be the Admin.

Screenshot from ChatterBox on a mobile device:

![screenshot](https://raw.githubusercontent.com/matthiku/chatter/master/public/static/mobile-screenshot-small.png)

(C) 2018 Matthias Kuhs, Ireland
