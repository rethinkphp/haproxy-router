<?php
/**
 * @var \rethink\hrouter\CfgGenerator $this
 */
?>
global
    stats socket 0.0.0.0:9999 mode 660 level admin
    log /dev/log local0 info
    chroot /var/lib/haproxy
    stats timeout 300s
    #user haproxy
    #group haproxy
    #daemon

    #nbproc 2

    maxconn 99999

    tune.ssl.default-dh-param 2048

    # Default ciphers to use on SSL-enabled listening sockets.
    # For more information, see ciphers(1SSL). This list is from:
    #  https://hynek.me/articles/hardening-your-web-servers-ssl-ciphers/
    ssl-default-bind-ciphers ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
    ssl-default-bind-options no-sslv3

defaults
    log    global
    mode    http
    #option    httplog
    #option    dontlognull
    option forwardfor
    timeout connect 5000
    timeout client  500000
    timeout server  500000

    #errorfile 400 /etc/haproxy/errors/400.http
    #errorfile 403 /etc/haproxy/errors/403.http
    #errorfile 408 /etc/haproxy/errors/408.http
    #errorfile 500 /etc/haproxy/errors/500.http
    #errorfile 502 /etc/haproxy/errors/502.http
    #errorfile 503 /etc/haproxy/errors/503.http
    #errorfile 504 /etc/haproxy/errors/504.http

listen stats
    bind 0.0.0.0:<?= $this->setting('listen.ports.stats', 1080) . PHP_EOL ?>
    mode http
    stats enable
    stats refresh 10s
    stats hide-version
    stats realm Haproxy\ Statistics
    stats uri /haproxy_stats
    stats auth <?=$this->setting('username')?>:<?=$this->setting('password'). PHP_EOL?>

#listen https-offloading
#    bind *:81
#    mode http

#    server abc unix@/Users/seiue/projects/rethinkphp/haproxy-router/h.sock

#frontend offloading
#    bind unix@/Users/seiue/projects/rethinkphp/haproxy-router/h.sock
#    mode http

#    use_backend %[base,map_reg(<?=$this->routeMap()?>)] if {  base,map_reg(<?=$this->routeMap()?>) -m found }

frontend http-in
    bind *:<?= $this->setting('listen.ports.http', 80) . PHP_EOL ?>
    mode http

    option httplog

    log-format %ci:%cp\ [%t]\ %ft\ %b/%s\ %Tq/%Tw/%Tc/%Tr/%Tt\ %ST\ %B\ %CC\ %CS\ %tsc\ %ac/%fc/%bc/%sc/%rc\ %sq/%bq\ %hr\ %hs\ %{+Q}r

    capture request header Host len 32
    capture request header Referer len 128
    capture request header User-Agent len 128

    timeout http-keep-alive 1000

    acl letsencrypt-acl path_beg /.well-known/acme-challenge/
    use_backend letsencrypt-backend if letsencrypt-acl

    acl is_https hdr(Host),map_reg(<?=$this->httpsMap()?>) -m found
    redirect scheme https code 301 if is_https

    use_backend %[base,map_reg(<?=$this->routeMap()?>)] if {  base,map_reg(<?=$this->routeMap()?>) -m found }


frontend https-in
<?php if ($this->hasCertificates()): ?>
    bind *:<?= $this->setting('listen.ports.https', 443)?> ssl crt <?=$this->certsPath() . PHP_EOL?>
<?php else: ?>
    bind *:<?= $this->setting('listen.ports.https', 443)?>
<?php endif ?>
    mode http

    option httplog

    log-format %ci:%cp\ [%t]\ %ft\ %b/%s\ %Tq/%Tw/%Tc/%Tr/%Tt\ %ST\ %B\ %CC\ %CS\ %tsc\ %ac/%fc/%bc/%sc/%rc\ %sq/%bq\ %hr\ %hs\ %{+Q}r

    capture request header Host len 32
    capture request header Referer len 128
    capture request header User-Agent len 128

    acl letsencrypt-acl path_beg /.well-known/acme-challenge/
    use_backend letsencrypt-backend if letsencrypt-acl

    use_backend %[base,map_reg(<?=$this->routeMap()?>)] if {  base,map_reg(<?=$this->routeMap()?>) -m found }

backend letsencrypt-backend
    server letsencrypt 127.0.0.1:9812

<?php foreach ($this->services as $service):?>

backend service_<?= $service['name'] ?>

    mode http
    fullconn <?= ($service['fullconn'] ?? 9999) . PHP_EOL?>
    option forwardfor

    http-request set-header X-Forwarded-Port %[dst_port]
    http-request add-header X-Forwarded-Proto https if { ssl_fc }

<?php foreach ($service['rewrites'] ?? [] as $from => $to):?>
    reqrep ^([^\ :]*)\ <?= $from ?>     \1\ <?= $to ?>

<?php endforeach ?>

    option httpchk GET /

<?php foreach ($service->nodes as $index => $node):?>
    <?= $this->generateServer($node) . PHP_EOL?>
<?php endforeach ?>

    compression algo gzip
    compression type text/css text/html text/javascript application/javascript text/plain text/xml application/json

<?php endforeach ?>
