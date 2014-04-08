server 'uat-internal', user: 'root', roles: %w{app}

set :config_file, 'database.uat.php'
set :memcache_servers, %w[localhost]
