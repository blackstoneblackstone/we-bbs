(function($){
	$.fn.extend({
		'captcha': function(options){
			var opts = $.extend({}, defaluts, options); //使用jQuery.extend覆盖插件默认参数
			var $this = this;
			if(!$('#captcha-container').length){
				$('body').append('<div id="captcha-container">'+
					'<div class="captcha-imgbox">'+
						'<img class="captcha-img" alt="验证码加载失败，请点击刷新按钮">'+
					'</div>'+
					'<div class="captcha-title"></div>'+
					'<div class="captcha-refresh-box">'+
						'<div class="captcha-refresh-line captcha-refresh-line-left"></div>'+
						'<a href="javascript:;" class="captcha-refresh-btn" title="刷新"></a>'+
						'<div class="captcha-refresh-line captcha-refresh-line-right"></div>'+
					'</div>'+
				'</div>');
				$('body').append('<div id="captcha-mask"></div>');
				$('#captcha-mask').click(function(){
					$('#captcha-container').hide();
					$(this).hide();
				});
				$('#captcha-container .captcha-refresh-btn').click(function(){
					$this.captcha(opts);
				});
			}
			$('#captcha-container, #captcha-mask').show();
			$('#captcha-container .captcha-imgbox .step').remove();
			$('#captcha-container .captcha-imgbox .captcha-img').attr('src', opts.src + '?' + new Date().getTime()).on('load', function(){
				var thisObj = $(this);
				var text = $.cookie('captcha_text').split(',');
				var title = '请依次点击';
				var t = [];
				for(var i = 0; i < text.length; i++){
					t.push('“<span>'+text[i]+'</span>”');
				}
				title += t.join('、');
				$('#captcha-container .captcha-title').html(title);
				var xyArr = [];
				thisObj.off('mousedown').on('mousedown', function(e){
					e.preventDefault();
					thisObj.off('mouseup').on('mouseup', function(e){
						$('#captcha-container .captcha-title span:eq('+xyArr.length+')').addClass('captcha-clicked');
						xyArr.push(e.offsetX + ',' + e.offsetY);
						$('#captcha-container .captcha-imgbox').append('<span class="step" style="left:' + (e.offsetX - 13) + 'px;top:' + (e.offsetY - 13) + 'px">' + xyArr.length + '</span>')
						if(xyArr.length == text.length){
							var captchainfo = [xyArr.join('-'), thisObj.width(), thisObj.height()].join(';');
							$.ajax({
								type: 'POST',
								url: opts.src,
								data: {
									do : 'check',
									info : captchainfo
								}
							}).done(function(result){
								if(result.code){
									$this.val(captchainfo).data('ischeck', true);
									$('#captcha-container .captcha-title').html(opts.success_tip);
									setTimeout(function(){
										$('#captcha-container, #captcha-mask').hide();
										opts.callback(result);
									}, 1500);
								}else{
									$('#captcha-container .captcha-title').html(opts.error_tip);
									setTimeout(function(){
										$this.captcha(opts);
									}, 1500);
								}
							});
						}
					});
				});
			});
			return this;
		},
		'captchaCheck': function(){
			var ischeck = false;
			if(this.data('ischeck') == true){
				ischeck = true;
			}
			return ischeck;
		},
		'captchaReset': function(){
			this.val('').removeData('ischeck');
			return this;
		}
	});
	//默认参数
	var defaluts = {
		src: baseUrl+'/tools/captcha',
		success_tip: '验证成功！',
		error_tip: '未点中正确区域，请重试！',
		callback: function(){}
	};
})(window.jQuery);