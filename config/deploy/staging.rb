server 'uat-internal-luxurylink', user: 'root', roles: %w{app}

set :config_file, 'database.uat.php'

set :memcache_servers, %w[localhost]
