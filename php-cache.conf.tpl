		fastcgi_cache_bypass $skip_cache;
	        fastcgi_no_cache $skip_cache;

		fastcgi_cache CACHE;
		fastcgi_cache_valid  60m;
