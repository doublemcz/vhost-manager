# Manager for Nginx vhost configurations

### Usage

#### With docker
Make an alias in .bash_aliases

`alias vhost-manager="docker run -v /etc/nginx:/application/nginx doublem/vhost-manager php console"`

then you can use

`vhost-manager ls`

You will see similar result:

```
╔════════════════╤════════╤═══════════════════════════╤═════╤════════════════════════╤═════════════════════╗
║ Domain         │ Listen │ Root                      │ SSL │ Proxy pass             │ File                ║
╟────────────────┼────────┼───────────────────────────┼─────┼────────────────────────┼─────────────────────╢
║ another.gl     │ 443    │ /var/www/another.gl       │ on  │ http://127.0.0.1:24006 │ another.gl.conf     ║
║ another.gl     │ 80     │                           │     │                        │ another.gl.conf     ║
║ another.gl     │ 80     │                           │     │                        │ test.conf           ║
║ baz.foo.bar    │ 443    │ /var/www/another.gl/slash │ on  │                        │ baz.foo.bar.conf    ║
║ baz.foo.bar    │ 80     │ /var/www/baz.foor.bar/www │     │                        │ baz.foo.bar.conf    ║
║ poop.gl        │ 10080  │                           │     │                        │ poop.com.conf       ║
║ poop.gl        │ 10443  │                           │ on  │ http://localhost:14001 │ poop.com.conf       ║
║ test.vhost.com │ 443    │                           │ on  │ http://127.0.0.1:4453  │ test.vhost.com.conf ║
║ test.vhost.com │ 80     │                           │     │ http://127.0.0.1:33452 │ test.vhost.com.conf ║
╚════════════════╧════════╧═══════════════════════════╧═════╧════════════════════════╧═════════════════════╝

```
