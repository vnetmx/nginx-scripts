server {
	server_name _;

	access_log   /opt/nginx/access.log;
	error_log    /opt/nginx/error.log;

	root /opt/nginx/html;
	index index.php;

	# Preparacion de cache para el sitio
	include conf/site-cache.conf;	

	# Default location
	location / {
		try_files $uri $uri/ /index.php?$args;
	}    

	# Handling PHP
	include conf/php.conf;

	# Site Locations
	include conf/site-locations.conf;
}
