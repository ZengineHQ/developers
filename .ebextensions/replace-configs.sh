#!/bin/bash

# httpd ssl config
cp /vol/nfs-share/shared/httpd/conf/conf.d/ssl.conf /etc/httpd/conf.d
cp /vol/nfs-share/sandy-api/httpd/force_ssl.conf /etc/httpd/conf.d