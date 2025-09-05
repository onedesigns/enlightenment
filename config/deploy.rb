################################################################################
## Setup project
################################################################################

# Lock the project to Capistrano 3.11.0
lock '3.11.0'

# An identifying name for the application to be used by Capistrano
set :application, 'enlightenment'
set :repo_url, 'git@bitbucket.org:onedesigns/enlightenment.git'
set :repo_tree, 'public'


################################################################################
## Setup Capistrano
################################################################################

set :log_level, :debug
set :keep_releases, 2
set :use_sudo, false
set :ssh_options, {
    forward_agent: true,
    port: 4996
}
