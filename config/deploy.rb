# config valid only for Capistrano 3.1
lock '3.1.0'

set :application, 'toolbox.luxurylink.com'
set :repo_url, 'ssh://git@git.luxurylink.com:7999/lltg/toolbox.git'

# Default branch is :master
ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }

set :deploy_to, "/var/www/deployments/toolbox.luxurylink.com"
set :scm, :git
set :format, :pretty
set :log_level, :info
set :pty, false
set :keep_releases, 2

task :post_deploy do
  on roles(:app) do
    execute "cd #{deploy_to}/current/app/config && ln -s #{fetch(:config_file)} database.php"
    execute "cd #{deploy_to}/current/app/vendors && ln -s /var/www/appshared ."
    execute "cd #{deploy_to}/current/app/webroot && ln -s /mnt/images images"
    execute "cd #{deploy_to}/current/app && chmod -R 777 tmp"
  end
end

task :clear_cache do
    set :TIMESTAMP, Time.now.to_i
    set :CACHEDIR, fetch(:deploy_to) + "/current/app/tmp/cache"
    set :CACHEDIR_GARBAGE, fetch(:CACHEDIR).to_s + "_garbage_" + fetch(:TIMESTAMP).to_s
    set :CLEAR_CACHE_CMD, "mv " + fetch(:CACHEDIR) + " " + fetch(:CACHEDIR_GARBAGE) + " && mkdir " + fetch(:CACHEDIR) + " && chmod 777 " + fetch(:CACHEDIR)

    on roles(:app) do
        execute fetch(:CLEAR_CACHE_CMD)
    end
end

after :deploy, :post_deploy
