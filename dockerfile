FROM openswoole/swoole:4.11-php8.1-alpine as swoole

RUN apk update \
    && apk add --update nodejs npm \
    && docker-php-ext-install pcntl

COPY ./ /app

WORKDIR /app
RUN npm install chokidar

CMD ["/bin/sh"]