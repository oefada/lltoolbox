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

after :deploy, :post_deploy
