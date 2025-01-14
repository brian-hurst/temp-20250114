#!/bin/bash

service nginx start
php-fpm

tail -f /dev/null