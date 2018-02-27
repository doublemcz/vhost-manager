# Manager for Nginx vhost configurations

### Usage

#### With docker
Make an alias in .bash_aliases

`alias vhost-manager="docker run -d -v path-to-nginx-dir:/application/nginx/vhosts doublemcz/vhost-manager console"`

then you can use

`vhost-manager ls`
