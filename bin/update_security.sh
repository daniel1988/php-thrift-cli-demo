#!/usr/bin/env bash

root=$(cd "$(dirname "$0")"; cd ../; pwd)
dir=$root/bin/thrift/
svnbase=svn://127.0.0.1/security_proxy/protocols/
phpdir=$root/src/Thrift/

# 需要拉取的协议文件
files=(security)
# files=(cip_activity_service collect_invest_service social_def authentication_def)

# 删除之前的 thrift 文件和代码
rm -rf $dir/*.thrift $phpdir

if [ ! -d $dir ]; then
    mkdir -p $dir
fi

for file in ${files[@]}; do
    svn cat $svnbase/$file.thrift --username=daniel --password=123456 > $dir/$file.thrift
done

mkdir -p $phpdir

# 重新生成 php 代码
for file in $dir/*.thrift; do
    thrift -v -r --out $phpdir --gen php:server $file
done
