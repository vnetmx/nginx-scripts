#!/bin/bash

APPDIR=$(pwd)
WORKDIR=/opt
SOURCES=/opt/sources
MODPHP=$2
VER=$1

if [ $# -ne 2 ];
then 
 exit 2
fi

_build_php()
{
 cd ${SOURCES}/php-src
 git checkout PHP-$1
 git reset --hard origin/PHP-$1
 ./buildconf
 # con psql
 #./configure --prefix=/opt/php-$1 --with-pdo-pgsql --with-zlib-dir --with-freetype-dir --enable-mbstring --with-libxml-dir=/usr --enable-soap --enable-calendar --with-curl --with-mcrypt --with-zlib --with-gd --with-pgsql --disable-rpath --enable-inline-optimization --with-bz2 --with-zlib --enable-sockets --enable-sysvsem --enable-sysvshm --enable-pcntl --enable-mbregex --with-mhash --enable-zip --with-pcre-regex --with-mysqli --with-pdo-mysql --with-mysqli --with-png-dir=/usr --enable-gd-native-ttf --with-openssl --with-fpm-user=nginx --with-fpm-group=nginx --with-libdir=lib64 --enable-ftp --with-kerberos --with-gettext --with-gd --with-jpeg-dir=/usr/lib/ --enable-fpm 
 configure="./configure \
	--prefix=/opt/php-$1 \
        --with-zlib-dir \
        --with-freetype-dir \
        --enable-mbstring \
        --with-libxml-dir=/usr \
        --enable-soap \
        --enable-calendar \
        --with-curl \
        --with-mcrypt \
        --with-zlib \
        --with-gd \
        --disable-rpath \
        --enable-inline-optimization \
        --with-bz2 \
        --with-zlib \
        --enable-sockets \
        --enable-sysvsem \
        --enable-sysvshm \
        --enable-pcntl \
        --enable-mbregex \
        --with-mhash \
        --enable-zip \
        --with-pcre-regex \
        --with-mysqli \
        --with-pdo-mysql \
        --with-mysqli \
        --with-png-dir=/usr \
        --enable-gd-native-ttf \
        --with-openssl \
        --with-fpm-user=www-data \
        --with-fpm-group=www-data \
        --with-libdir=lib64 \
        --enable-ftp \
        --with-kerberos \
        --with-gettext \
	--with-jpeg-dir=/usr/lib/ \
        --enable-fpm"
 if [ -f /opt/httpd-$MODPHP/bin/apxs ];
 then
   configure="$configure --with-apxs2=/opt/httpd-$MODPHP/bin/apxs"
 fi
 $configure
 make clean && make && make install
 cp php.ini-development /opt/php-$1/lib/php.ini
 echo "[global]" > /opt/php-$1/etc/php-fpm.conf
 echo "pid = run/php-fpm.pid" >> /opt/php-$1/etc/php-fpm.conf
 echo "include=/opt/php-$1/etc/php-fpm.d/*.conf" >> /opt/php-$1/etc/php-fpm.conf
 mv /opt/php-$1/etc/php-fpm.d/www.conf.default /opt/php-$1/etc/php-fpm.d/www.conf
 sed -i s/127.0.0.1:9000/127.0.0.1:90${1//.}/g /opt/php-$1/etc/php-fpm.d/www.conf
 if [ -f ${APPDIR}/php-fpm.init ];
 then
    cp ${APPDIR}/php-fpm.init /etc/init.d/php-$1
    sed -i s/PHPVER/$1/g /etc/init.d/php-$1
    chmod +x /etc/init.d/php-$1
 fi
 if [ ! -L /usr/bin/php$1 ];
 then
    ln -s /opt/php-$1/bin/php /usr/bin/php$1
 fi
 if [ -f /opt/httpd-$MODPHP/bin/apxs ];
 then
   if [ -f libs/libphp${1:0:1}.so ];
   then
     cp libs/libphp${1:0:1}.so  /opt/httpd-$MODPHP/modules/
     cp ${APPDIR}/httpd-php${1:0:1}.conf /opt/httpd-$MODPHP/conf/extra/httpd-php.conf
   fi
 fi


}

_git_php() {
 if [ ! -d ${SOURCES}/php-src ];
 then
   git clone http://git.php.net/repository/php-src.git ${SOURCES}/php-src
 fi
 cd ${SOURCES}/php-src
 git fetch --all
}

_git_ext() {
 git clone https://github.com/mkoppanen/imagick.git ${SOURCES}/imagick
 cd ${SOURCES}/imagick
 git reset --hard
 make clean
 /opt/php-$1/bin/phpize && ./configure --with-php-config=/opt/php-$1/bin/php-config && make && make install && echo "extension=imagick.so" >> /opt/php-$1/lib/php.ini
}



_git_php
if [ -x /etc/init.d/php-${VER} ];
then
  /etc/init.d/php-${VER} stop
fi
_build_php ${VER}
_git_ext ${VER}
/etc/init.d/php-${VER} start
