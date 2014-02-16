<?php



class EventDispatcher
{
	//初始化
	public function __construct()
	{

	}

	public function dispatch()
	{
		while (true)
		{
			//调用Service
			User::init()->auth($user, $password);
		}
	}
}









class GetewayLogic
{
	public function onConnect()
	{
		//向Session维护服务（SKS）发送连信息
		Service::SKS()->connect($session);
	}

	public function onClose()
	{
		//告知Session维护服务（SKS）关闭联接
		Service::SKS()->close($session);
	}

	public function onData()
	{
		//收到用户协议，发往
		Service::SKS()->close($session);
	}
}