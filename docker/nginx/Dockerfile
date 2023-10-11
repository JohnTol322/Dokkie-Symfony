FROM alpine:3.4

RUN apk update --no-cache && apk upgrade
RUN apk add --no-cache nginx git

RUN adduser -D -g 'www' www

RUN mkdir -p /var/log/nginx
RUN mkdir -p /run/nginx
RUN touch /run/nginx/nginx.pid
RUN chown -R www: /var/lib/nginx
RUN chown -R www: /var/log/nginx
RUN chown -R www: /var/www

## forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
  && ln -sf /dev/stderr /var/log/nginx/error.log

COPY nginx.conf /etc/nginx/nginx.conf


EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
