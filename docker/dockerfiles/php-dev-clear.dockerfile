FROM debian:latest as php-dev-clear

ARG PHP_REPO_URL
ARG PHP_REPO_GPG_URL
ARG PHP_REPO_SOURCE_LIST_DIST
ARG PHP_VERSION
ARG TEMP_PACKAGES

ENV PHP_REPO_URL=${PHP_REPO_URL}
ENV PHP_REPO_GPG_URL=${PHP_REPO_GPG_URL}
ENV PHP_REPO_SOURCE_LIST_DIST=${PHP_REPO_SOURCE_LIST_DIST}
ENV PHP_VERSION=${PHP_VERSION}
ENV TEMP_PACKAGES=${TEMP_PACKAGES}

RUN apt update && apt upgrade -y && \
    apt install -y --no-install-recommends ${TEMP_PACKAGES}
    # Installing PHP
RUN echo "deb ${PHP_REPO_URL} $(lsb_release -sc) main" > ${PHP_REPO_SOURCE_LIST_DIST} && \
    curl -fsSL ${PHP_REPO_GPG_URL} | gpg --dearmor -o /etc/apt/trusted.gpg.d/php.gpg && \
    apt update && apt install -y --no-install-recommends \
        php${PHP_VERSION} \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-dom \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-opcache \
        php${PHP_VERSION}-cli
    # Installing Composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    # Installing Git
RUN apt update && apt install -y --no-install-recommends git
    # Installing 7-zip
RUN apt update && apt install -y --no-install-recommends p7zip-full
    # Temp packages removing
RUN apt purge -y ${TEMP_PACKAGES} && apt autoremove -y && apt clean && rm -rf /var/lib/apt/lists/*

CMD ["tail", "-f", "/dev/null"]