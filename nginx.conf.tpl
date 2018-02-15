user www-data;
pid run/nginx.pid;
worker_processes auto;
worker_rlimit_nofile 100000;
error_log log/error.log crit;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
	include       conf/mime.types;
	default_type  application/octet-stream;

	open_file_cache max=200000 inactive=20s; 
	open_file_cache_valid 30s; 
	open_file_cache_min_uses 2;
	open_file_cache_errors on;
	
	access_log off;

	sendfile on;
	tcp_nopush on;
	tcp_nodelay on;

	gzip on;
	gzip_min_length 10240;
	gzip_proxied expired no-cache no-store private auth;
	gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/json application/xml;
	gzip_comp_level 6;
	gzip_disable msie6;

	reset_timedout_connection on;
	client_body_timeout 10;
	client_header_timeout  3m;
	send_timeout 2;
	keepalive_timeout 30;
	keepalive_requests 100000;

	server_tokens off;
	
	include conf/custom/*.conf;
	include conf/sites-enabled/*.site;
}
