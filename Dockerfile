FROM php:7-apache

ENV GITHUB_API_KEY

MAINTAINER Bryan Karaffa <BryanKaraffa@gmail.com>

RUN apt-get update && apt-get install -y \
        git

COPY . /var/www/html/

WORKDIR /var/www/html

RUN \
   git submodule init && \
   git submodule update
