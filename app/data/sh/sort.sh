# !/usr/bin/bash

# 各releaseサーバのApacheアクセスログをマージしてソート

# ファイル名を引数として取得する
name1=$1
echo $name1

# releaseフォルダ配下について検索し テンポラリとして合成する
counter=0
for file in `find  /home/ec2-user/log/release* -name \access_log-$name1`;
do
  echo $file
    if [ $counter == 0 ]; then
      cat $file > /home/ec2-user/release/${name1}.tmp
    else
      cat $file >> /home/ec2-user/release/${name1}.tmp
    fi
      counter=`expr $counter + 1`
done

# テンポラリファイルをソートし、結果を別ファイルに出力する
sort /home/ec2-user/release/${name1}.tmp | uniq |grep -v '^$'> /home/ec2-user/release/${name1}

# テンポラリ削除
rm /home/ec2-user/release/${name1}.tmp

echo finish!!_$name1



