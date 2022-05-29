FROM webdevops/php-apache:7.4

# Docker Build Command
# docker build -t 565209677751.dkr.ecr.us-west-2.amazonaws.com/blue-eye -f Dockerfile .



# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SYSTEM CONFIGURATION
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# Project Folder
WORKDIR /application

# Apache Document Root Folder
ENV WEB_DOCUMENT_ROOT=/application/public


# Custom Commands for Application Deployment + Apache Vhost Setup
COPY docker/provision/setup.sh          /opt/docker/provision/entrypoint.d/01-setup.sh
COPY docker/provision/application.sh    /opt/docker/provision/entrypoint.d/50-application.sh
COPY docker/config.sh                   /opt/docker/bin/config.sh
COPY docker/apache/vhost.conf           /opt/docker/etc/httpd/vhost.conf

# Copy CRONs
# COPY docker/crontab /opt/docker/etc/cron/application
# RUN docker-service-enable cron



# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# APPLICATION CONFIGURATION
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# Copy Entire Project
COPY . .

# Install Project Package Dependencies for Production
RUN composer install --optimize-autoloader --no-dev

# Install Node/NPM via NVM
# This needs to be done in one step on build process
RUN rm /bin/sh && ln -s /bin/bash /bin/sh
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash
RUN . ~/.nvm/nvm.sh \
# Install NodeJS
&& nvm install v14 \
# Install Node Dependencies --production
&& npm install \
# Compile Frontend Static Assets
&& npm run production


# Storage Permissions
RUN chown -R application:www-data /application/storage/*
RUN chown -R application:www-data /application/public/
RUN chmod -R 775 /application/bootstrap/cache/
RUN chown -R application:www-data /application/bootstrap/cache/



# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SECURITY UPGRADES
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# Upgrade Apache2 for Security
RUN echo "deb http://ftp.debian.org/debian buster-backports main" >> /etc/apt/sources.list
RUN echo "deb-src http://ftp.debian.org/debian buster-backports main" >> /etc/apt/sources.list
RUN apt-get update
RUN apt upgrade -t buster-backports apache2 -y



# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# CONTAINER CLEANUP
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# Cleanup NodeJS
RUN rm -rf /root/.nvm

# Cleanup Apt
RUN apt-get clean

# Set Container User
USER application
