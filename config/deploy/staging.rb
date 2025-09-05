################################################################################
## Setup Environment
################################################################################

# The Git branch this environment should be attached to.
set :branch, 'development'

# The environment's name. To be used in commands and other references.
set :stage, :staging

# The URL of the website in this environment.
set :stage_url, 'http://demo.onedesigns.com'

# The environment's server credentials
server '178.62.19.125', user: 'enlightenment', roles: %w(web app db)

# The deploy path to the website on this environment's server.
set :deploy_to, '/home/enlightenment/capistrano'
