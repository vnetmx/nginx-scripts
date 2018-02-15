#
        location ~ \.php$ {
                try_files $uri =404;
                include fastcgi_params;
                fastcgi_pass 127.0.0.1:9000;

                include conf/php-cache.conf;
        }

