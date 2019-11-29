FROM php:7-apache

MAINTAINER Bryan Karaffa <BryanKaraffa@gmail.com>

RUN apt-get update && apt-get install -y \
        git

COPY . /var/www/html/

RUN \
   cd /var/www/html/ && \
   git submodule init && \
   git submodule update
