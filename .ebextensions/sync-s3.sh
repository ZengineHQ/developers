#!/bin/bash

# Setup /vol/nfs-share
mkdir /vol
mkdir /vol/nfs-share

# Copy files from S3
/usr/bin/s3cmd --config "/root/.s3cfg" sync s3://wizehive-s3-boot /vol/nfs-share
