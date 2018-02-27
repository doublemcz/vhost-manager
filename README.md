# Manager for Nginx vhost configurations

### Usage

#### With docker
Make an alias in .bash_aliases

`alias vhost-manager="docker run -v /etc/nginx/sites-enabled/vhosts:/application/vhosts vhost-manager:latest php console"`

then you can use

`vhost-manager ls`
