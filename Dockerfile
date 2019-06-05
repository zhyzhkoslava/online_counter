FROM nginx:latest

ADD nginx.conf /etc/nginx/nginx.conf
ADD nginx.vh.default.conf /etc/nginx/conf.d/default.conf
