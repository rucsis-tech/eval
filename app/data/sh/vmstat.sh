# !/usr/bin/bash

# CPU使用率のみを出力
vmstat 1 2 | sed '1, 3d' | awk '{print $13 + $14}' > /tmp/ec2.cpu

# Load Average
uptime | awk '{print $10}' | sed -e "s/,\$//" > /tmp/ec2.lav

# api-CPU使用率
cp /mnt/efs/vmstat/api.cpu /tmp/api.cpu


# 日付付き出力ログ
mm=`date '+%M'`
if [ $mm = "00" ]; then
    vmstat 1 2 | awk '{ print strftime("%Y/%m/%d %H:%M:%S"), $0 }' >> /home/ec2-user/vmstat.log
else
    vmstat 1 2 |  sed '1, 3d' | awk '{ print strftime("%Y/%m/%d %H:%M:%S"), $0 }' >> /home/ec2-user/vmstat.log
fi

