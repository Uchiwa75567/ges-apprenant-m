FROM php:8.2-fpm

RUN apt-get update && apt-get install -y  nginx 
COPY default.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html
COPY . .

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]