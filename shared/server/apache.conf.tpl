# Require Apache 2.4

<Directory {{deploy_path}}/current/webroot/>
    AllowOverride All
    Options +FollowSymLinks +ExecCGI
    <RequireAny>
        Require all granted
    </RequireAny>
</Directory>

# Redirect all HTTP request to HTTPS request
<VirtualHost *:80>
    RewriteEngine   On
    RewriteCond     %{HTTP:X-Forwarded-Proto} !https
    #RewriteCond     %{HTTPS} off
    RewriteCond     %{HTTP_HOST} ^({{app.domain}})$
    RewriteRule     (.*) https://%{HTTP_HOST}%{REQUEST_URI}
</VirtualHost>

# virtual host for your site
<VirtualHost *:443>
    ServerName              {{app.domain}}
    DocumentRoot            {{deploy_path}}/current/webroot/
    ErrorLog                /var/log/httpd/{{app.domain}}-error.log
    CustomLog               /var/log/httpd/{{app.domain}}-access.log combined
    SSLEngine               On
    SSLCertificateFile      {{deploy_path}}/shared/cert/{{app.domain}}.crt
    SSLCertificateKeyFile   {{deploy_path}}/shared/cert/{{app.domain}}.key
</VirtualHost>