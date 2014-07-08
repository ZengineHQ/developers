#!/bin/bash

# install s3cmd
if ([ ! -e /etc/yum.repos.d/s3tools.repo ]) then
    pushd /etc/yum.repos.d
    wget -q http://s3tools.org/repo/RHEL_6/s3tools.repo
    yum -y install s3cmd
    popd
fi

# install mod_ssl
yum -y install mod_ssl