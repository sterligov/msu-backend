FROM debian:buster-slim

RUN apt-get update && apt-get install -y \
    git \
    wget \
    gcc \
    libpcre3 \
    libpcre3-dev \
    libssl-dev \
    libzip-dev \
    libgeoip-dev \
    build-essential

WORKDIR /nginx-configure

ARG NGINX_VERSION

RUN wget "http://nginx.org/download/nginx-${NGINX_VERSION}.tar.gz" && \
    tar -xzvf nginx-${NGINX_VERSION}.tar.gz

RUN git clone https://github.com/vision5/ngx_devel_kit
RUN git clone https://github.com/openresty/set-misc-nginx-module
RUN wget https://people.freebsd.org/~osa/ngx_http_redis-0.3.9.tar.gz && \
    tar -xvzf ngx_http_redis-0.3.9.tar.gz


WORKDIR /nginx-configure/nginx-${NGINX_VERSION}

RUN ./configure \
        --conf-path=/etc/nginx/nginx.conf --error-log-path=/var/log/nginx/error.log \
        --http-client-body-temp-path=/var/lib/nginx/body --http-fastcgi-temp-path=/var/lib/nginx/fastcgi \
        --http-log-path=/var/log/nginx/access.log --http-proxy-temp-path=/var/lib/nginx/proxy --lock-path=/var/lock/nginx.lock \
        --pid-path=/var/run/nginx.pid --with-pcre-jit --with-http_gzip_static_module --with-http_ssl_module \
        --with-http_geoip_module --without-http_scgi_module --without-http_split_clients_module \
        --with-http_stub_status_module --without-http_ssi_module \
        --without-http_userid_module --without-http_uwsgi_module \
        --add-module=/nginx-configure/ngx_devel_kit \
        --add-module=/nginx-configure/set-misc-nginx-module \
        --add-module=/nginx-configure/ngx_http_redis-0.3.9

RUN make -j2
RUN make install

WORKDIR /
RUN rm -rf nginx-configure
RUN mkdir -p /var/lib/nginx/{body,fastcgi}

EXPOSE 80 443

CMD ["/usr/local/nginx/sbin/nginx", "-g", "daemon off;"]



