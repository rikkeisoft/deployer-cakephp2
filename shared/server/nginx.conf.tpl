worker_processes auto;

http {
    ssl_session_cache   shared:SSL:10m;
    ssl_session_timeout 10m;
    client_body_buffer_size 32K;
    client_max_body_size 5M;
    keepalive_timeout   70;

    # your site
    # ==========================================================================

    server {
        listen [::]:80;
        server_name {{app.domain}};

        error_log /var/log/nginx/{{app.domain}}-error.log;
        access_log /var/log/nginx/{{app.domain}}-access.log;
    
        # Redirect all HTTP request to HTTPS request
        location / {
            if ($http_x_forwarded_proto != 'https') {
                return 301 https://$server_name$request_uri;
            }
            try_files $uri $uri/ /index.php?$args;
        }
    }

    server {
        listen [::]:443;
        server_name {{app.domain}};

        root {{deploy_path}}/current/webroot;
        index index.php index.html index.htm;

        error_log /var/log/nginx/{{app.domain}}-error.log;
        access_log /var/log/nginx/{{app.domain}}-access.log;

        ssl_certificate     {{deploy_path}}/shared/cert/{{app.domain}}.crt;
        ssl_certificate_key {{deploy_path}}/shared/cert/{{app.domain}}.key;

        # PHP
        location ~ \.php$ {
            try_files $uri =404;
            fastcgi_pass  unix:/var/run/php5-fpm.sock;
            fastcgi_index index.php;
            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param HTTPS on;
        }

        # Assets file
        location ~* \.(?:ico|css|js|gif|jpe?g|png)$ {
            try_files $uri $uri/ /index.php?$args;
            #expires max;
            #add_header Pragma public;
            #add_header Cache-Control “public, must-revalidate, proxy-revalidate”;
            access_log off;
            log_not_found off;
        }

        # Deny all attempts to access hidden files such as .htaccess, .htpasswd, .DS_Store (Mac).
        location ~ /\. {
            deny all;
            access_log off;
        }

    }

}