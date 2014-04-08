server 'toolbox', user: 'root', roles: %w{app}

set :config_file, 'database.prod.php'
set :memcache_servers, %w[localhost]
