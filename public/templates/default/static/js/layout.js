jQuery(function($){
	//导航tab切换
	$('.newnavbar ul li').click(function(){
		$(this).addClass('cur').siblings().removeClass('cur');
	});
	$('.suspension ul li').click(function(){
		$(this).addClass('cur').siblings().removeClass('cur');
	});

})