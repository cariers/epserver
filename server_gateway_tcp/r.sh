ps -ef | grep 'EP' |awk '{print$2}'|xargs kill -9
sleep 1
ulimit -c 1024000
ulimit -n 10000
exedir=`pwd`
#php $exedir/server.php >/dev/null 2>&1
