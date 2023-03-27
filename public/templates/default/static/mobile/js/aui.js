var flyZommImg = function(b, a) {
	this.options = a;
	this._this = b;
	this._activity = false;
	this._opts = {
		imgSum: 0,
		rollSpeed: 200,
		screenHeight: 165,
		showBoxSpeed: 300,
		urlProperty: false,
		miscellaneous: true,
		closeBtn: false,
		hideClass: false,
		imgQuality: "original",
		slitherCallback: function() {}
	}, this._init = function() {
		var c = this;
		c._defaluts();
		c._bind()
	}, this._defaluts = function() {
		var c = $.extend(this._opts, this.options || {});
		this.opts = c;
		return c
	}, this._bind = function() {
		var c = this;
		c._bindDom();
		$("body").on("click", ".fly-zoom-box", function(d) {
			c._hideBox()
		});
		$("body").on("click", ".fly-zoom-box-close", function(d) {
			c._hideBox();
			d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-restore", function(d) {
			c._imgRestore("tap");
			d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-zoomout", function(d) {
			c._zommImg(-100, 1);
			d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-zoom", function(d) {
			c._zommImg(100, 1);
			d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-img", function(d) {
			// c._imgRestore("tap");
			// d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-tool", function(d) {
			d.stopPropagation()
		});
		$("body").on("click", ".fly-zoom-box-label", function(f) {
			var d = $(this);
			var h = d.html();
			if(h && c.groups && c.groups[c.group_name]) {
				c.group_name = h;
				c.opts.imgSum = c.groups[c.group_name].length;
				c.opts.img_index = 0;
				$(".fly-zoom-box-fix").html(1);
				$(".fly-zoom-box-length").html(c.opts.imgSum);
				if(c.opts.urlProperty) {
					var g = c.groups[c.group_name][0].dom.data(c.opts.urlProperty)
				} else {
					var g = c.groups[c.group_name][0].dom.attr("src");
				}
				$(".fly-zoom-box-img").attr("src", g);
				$(".fly-zoom-box-label").css({
					"background": "",
					"color": "#666"
				});
				d.css({
					"background": "rgba(62,69,80,1)",
					"color": "#fff"
				});
				c.oWidth = c.oHeight = null;
				c._imgRestore("oneSwitcher", c.groups[c.group_name][0].dom);
				c.onPinch = c.onRotate = null
			}
			f.stopPropagation()
		})
	}, this._bindDom = function() {
		var f = this;
		f.opts.imgSum = 0;
		f.opts.img_index = 1;
		f.groups = [];
		f.group_names = [];
		f.group_show = false;
		f._this.find("img").each(function(h) {
			var g = $(this);
			if((f.opts.hideClass && !g.hasClass(f.opts.hideClass)) || !f.opts.hideClass) {
				var i = g.data("group");
				if(i) {
					if(!f.groups[i]) {
						f.group_names.push(i);
						f.groups[i] = []
					}
					f.groups[i].push({
						"dom": g
					});
					f.group_show = true
				}
				if(!f.group_show) {
					g.data("index", f.opts.imgSum);
					f.opts.imgSum += 1
				}
			}
		});
		if(f.group_show) {
			var e = f.group_names;
			for(var d = 0; d < e.length; d++) {
				for(var c = 0; c < f.groups[e[d]].length; c++) {
					f.groups[e[d]][c].dom.attr("data-index", c);
				}
			}
		}
		this._this.find("img").on("click", function() {
			var g = $(this);
			if((f.opts.hideClass && !g.hasClass(f.opts.hideClass)) || !f.opts.hideClass) {
				f._flyBoxHtml(g);
				f._imgRestore("oneSwitcher", g);
				$("body").on("touchmove", function(h) {
					h.preventDefault();
				});
				f._touchBind(f);
				if(f._activity) {
					f.opts.slitherCallback("firstClick", g);
				}
			}
		});
	}, this._reload = function() {
		$("body").unbind("touchmove");
		this._this.find("img").unbind("click");
		this._moveUnBind();
		this._bindDom()
	}, this._flyBoxHtml = function(j) {
		var c = parseInt(j.data("index"));
		if(this.group_show) {
			this.group_name = j.data("group");
			this.opts.imgSum = this.groups[this.group_name].length;
			this.opts.img_index = c
		}
		var f = this.opts.imgSum;
		if(this.opts.urlProperty) {
			var h = j.data(this.opts.urlProperty)
		} else {
			var h = j.attr("src")
		}
		var e = "";
		e += "<div class='fly-zoom-box' style='touch-action: none;display: none;-webkit-tap-highlight-color:rgba(255,255,255,0);cursor: pointer;position: fixed;z-index: 9999;width:100% ;height:100% ;background: rgba(20,20,20,1);top:0 ;bottom: 0;right:0 ;left:0 ;'>" + "<div class='fly-zoom-box-number' style='touch-action: none;z-index: 999999;position: absolute;top: 0;padding: 20px 0 ;line-height: 26px;color: #ddd;font-size: 14px;width: 100%;text-align: center;'><span style='background: rgba(255,255,255,0.2);border-radius: 15px;color: #fff;padding: 0px 6px'><span class='fly-zoom-box-fix'>" + (c + 1) + "</span>/<span class='fly-zoom-box-length'>" + f + "</span></span></div>" + "<div class='fly-zoom-box-main' style='touch-action: none;z-index: auto;position: relative;width: 100%;height: 100%;overflow: auto'><img class='fly-zoom-box-img' data-index='" + c + "' style='touch-action: none;display: block;width: 100%;position: absolute;' src='" + h + "'></div>";
		if(this.opts.closeBtn) {
			e += "<div class='fly-zoom-box-close' style='touch-action: none;text-align: center;z-index: 99999999;position: absolute;top: 11px;color: #ddd;font-size: 26px;right: 14px;'>×</div>"
		}
		if(this.opts.miscellaneous && this.group_names.length == 0) {
			e += "<div class='fly-zoom-box-tool' style='touch-action: none;z-index: 999999;position: absolute;bottom: 10px;padding: 10px 0 ;width: 200px;line-height: 26px;color: #ddd;font-size: 14px;margin: 0 auto;right: 0;left: 0;text-align: center;background: rgba(20,20,20,0.3);border-radius: 50px'><span class='fly-zoom-box-zoomout' style='background: rgba(255,255,255,0.2);border-radius: 15px;color: #fff;padding: 0px 6px'>－</span><span class='fly-zoom-box-restore' style='background: rgba(255,255,255,0.2);border-radius: 15px;color: #fff;padding: 2px 6px;margin: 0  0 0 10px '>还原</span><span class='fly-zoom-box-close' style='background: rgba(255,255,255,0.2);border-radius: 15px;color: #fff;padding: 2px 6px;margin: 0 10px'>关闭</span><span class='fly-zoom-box-zoom' style='background: rgba(255,255,255,0.2);border-radius: 15px;color: #fff;padding: 0px 6px'>＋</span></div>"
		}
		if(this.group_names.length > 0 && this.opts.miscellaneous) {
			e += "<div class='fly-zoom-box-tool' style='touch-action: none;text-align: center;z-index: 999999;position: absolute;bottom: 10px;padding: 10px 0 ;line-height: 26px;color: #ddd;font-size: 12px;margin: 0 auto;right: 0;left: 0;background: rgba(20,20,20,0.3);'>";
			var g = this.group_names;
			for(var d = 0; d < g.length; d++) {
				e += "<span class='fly-zoom-box-label' style='width: max-content;border-radius: 5px;display: inline-block;";
				if(g[d] == this.group_name) {
					e += "background: rgba(62,69,80,1);color: #fff;"
				} else {
					e += "color: #666;"
				}
				e += "padding: 2px 9px;margin: 5px 10px 0 10px'>" + g[d] + "</span>"
			}
		}
		e += "</div>";
		$("body").append(e);
		this._showBox()
	}, this._hideBox = function() {
		$(".fly-zoom-box").hide(this._opts.showBoxSpeed, "linear", function() {
			$(this).remove()
		});
		$("body").unbind("touchmove");
		if(this._activity) {
			this.opts.slitherCallback("close", $(".fly-zoom-box-img"))
		}
		this._moveUnBind();
		this._activity = false
	}, this._showBox = function() {
		this._activity = true;
		$(".fly-zoom-box").show(this._opts.showBoxSpeed)
	}, this._rightMove = function() {
		var e = this;
		if(e.group_show) {
			var c = e.opts.img_index + 1;
			var d = e.opts.imgSum;
			if(c >= d) {
				c = 0
			}
			e.opts.img_index = c;
			var g = e.groups[e.group_name][c].dom
		} else {
			var c = parseInt($(".fly-zoom-box-img").attr("data-index"));
			c = c + 1;
			var d = this.opts.imgSum;
			if(c >= d) {
				c = 0
			}
			var g = "";
			this._this.find("img").each(function() {
				var h = $(this);
				if(h.data("index") == c) {
					g = h;
					return false
				}
			})
		}
		$(".fly-zoom-box-fix").html(c + 1);
		$(".fly-zoom-box .fly-zoom-box-img").animate({
			left: "-200%"
		}, e._opts.rollSpeed, "linear", function() {
			$(this).remove()
		});
		if(e.opts.urlProperty) {
			var f = g.data(e.opts.urlProperty)
		} else {
			var f = g.attr("src")
		}
		$(".fly-zoom-box-main").append("<img class='fly-zoom-box-img' data-index='" + c + "'  style='left:100%;display: block;position: absolute;' src='" + f + "'>");
		e._imgRestore("chage", g);
		$(".fly-zoom-box-img").animate({
			left: "0%"
		}, e._opts.rollSpeed, "linear", function() {
			e._touchBind(e);
			e._imgRestore("switcher", g)
		});
		this._moveUnBind(c);
		if(this._activity) {
			this.opts.slitherCallback("left", g)
		}
	}, this._leftMove = function() {
		var e = this;
		if(e.group_show) {
			var c = e.opts.img_index - 1;
			var d = e.opts.imgSum;
			if(c < 0) {
				c = (d - 1)
			}
			e.opts.img_index = c;
			var g = e.groups[e.group_name][c].dom
		} else {
			var c = parseInt($(".fly-zoom-box-img").attr("data-index"));
			c = c - 1;
			var d = this.opts.imgSum;
			if(c < 0) {
				c = (d - 1)
			}
			var g = "";
			this._this.find("img").each(function() {
				var h = $(this);
				if(h.data("index") == c) {
					g = h;
					return false
				}
			})
		}
		$(".fly-zoom-box-fix").html(c + 1);
		$(".fly-zoom-box-main .fly-zoom-box-img").animate({
			left: "200%"
		}, e._opts.rollSpeed, "linear", function() {
			$(this).remove()
		});
		if(e.opts.urlProperty) {
			var f = g.data(e.opts.urlProperty)
		} else {
			var f = g.attr("src")
		}
		$(".fly-zoom-box-main").append("<img class='fly-zoom-box-img' data-index='" + c + "'  style='right:100%;display: block;position: absolute;' src='" + f + "'>");
		e._imgRestore("chage", g);
		$(".fly-zoom-box-img").animate({
			right: "0%"
		}, e._opts.rollSpeed, "linear", function() {
			e._touchBind(e);
			e._imgRestore("switcher", g)
		});
		this._moveUnBind();
		if(e._activity) {
			this.opts.slitherCallback("right", g)
		}
	}, this._moveUnBind = function() {
		$("body").unbind("touchstart");
		$("body").unbind("touchend")
	}, this._touchBind = function(c) {
		var e, d;
		$("body").on("touchstart", function(i) {
			var f = i.originalEvent.touches ? i.originalEvent.touches[0] : i;
			c.startX = f.pageX;
			c.startY = f.pageY;
			window.clearTimeout(c.longTapTimeout);
			if(i.originalEvent.touches.length > 1) {
				var g = i.originalEvent.touches[1];
				var j = Math.abs(g.pageX - c.startX);
				var h = Math.abs(g.pageY - c.startY);
				c.touchDistance = c._getDistance(j, h);
				c.touchVector = {
					x: g.pageX - c.startX,
					y: g.pageY - c.startY
				}
			} else {
				c.startTime = c._getTime();
				c.longTapTimeout = setTimeout(function() {
					c._emitEvent("longtap")
				}, 800);
				if(c.previousTouchPoint) {
					if(Math.abs(c.startX - c.previousTouchPoint.startX) < 10 && Math.abs(c.startY - c.previousTouchPoint.startY) < 10 && Math.abs(c.startTime - c.previousTouchTime) < 500) {
						c._emitEvent("doubletap")
					}
				}
				c.previousTouchTime = c.startTime;
				c.previousTouchPoint = {
					startX: c.startX,
					startY: c.startY
				}
			}
		});
		$("body").on("touchmove", function(o) {
			var p = c._getTime();
			if(o.originalEvent.touches.length > 1) {
				if(c.touchVector) {
					var g = {
						x: o.originalEvent.touches[1].pageX - o.originalEvent.touches[0].pageX,
						y: o.originalEvent.touches[1].pageY - o.originalEvent.touches[0].pageY
					};
					var k = c._getRotateAngle(g, c.touchVector);
					if(k > 30) {
						c._emitEvent("rotate");
						c.touchVector.x = g.x;
						c.touchVector.y = g.y
					} else {
						var j = Math.abs(o.originalEvent.touches[0].pageX - o.originalEvent.touches[1].pageX);
						var f = Math.abs(o.originalEvent.touches[1].pageY - o.originalEvent.touches[1].pageY);
						var n = c._getDistance(j, f);
						if(c.touchDistance) {
							var h = n / c.touchDistance;
							var i = h - c.previousPinchScale;
							c._emitEvent("pinch", {
								scale: i
							});
							c.previousPinchScale = h
						}
					}
				}
			} else {
				window.clearTimeout(c.longTapTimeout);
				var q = o.originalEvent.touches ? o.originalEvent.touches[0] : o;
				var m = c.moveX === null ? 0 : q.pageX - c.moveX;
				var l = c.moveY === null ? 0 : q.pageY - c.moveY;
				c._emitEvent("move", {
					"deltaX": m,
					"deltaY": l
				});
				c.moveX = q.pageX;
				c.moveY = q.pageY
			}
			o.preventDefault()
		});
		$("body").on("touchend", function(g) {
			window.clearTimeout(c.longTapTimeout);
			var f = c._getTime();
			e = c.moveX - c.startX;
			d = c.moveY - c.startY;
			if(c.moveX !== null && Math.abs(e) > 10 || c.moveY !== null && Math.abs(d) > 10) {
				if(Math.abs(e) > Math.abs(d) && e > 70) {
					c._emitEvent("left")
				} else {
					if(Math.abs(e) > Math.abs(d) && e < -70) {
						c._emitEvent("right")
					} else {
						if(Math.abs(d) > Math.abs(e) && d > 70) {
							c._emitEvent("bottom")
						} else {
							if(Math.abs(d) > Math.abs(e) && d < -70) {
								c._emitEvent("top")
							} else {
								if(f - c.startTime < 500) {
									c._emitEvent("swipe")
								}
							}
						}
					}
				}
			} else {
				if(f - c.startTime < 2000) {
					if(f - c.startTime < 500) {
						c._emitEvent("tap")
					}
				}
			}
			c._emitEvent("touchend")
		})
	}, this._zommImg = function(y, c) {
		if(c <= 0) {
			if(isNaN(y) || Math.abs(y) > 0.2 || Math.abs(y) < 0.02) {
				return false
			}
		}
		var l = $(".fly-zoom-box-img");
		var k = l.width();
		var v = l.height();
		var f = window.screen.width;
		var z = window.screen.height - this._opts.screenHeight;
		if(c <= 0) {
			y = y * 2;
			var i = k + k * y;
			var r = v + v * y;
			var g = (f - i) / 2;
			var s = (z - r) / 2
		} else {
			var i = k + y;
			var r = v * (i / k);
			// if(i < f) {
			//     return false
			// }
			if(y > 0) {
				this.onPinch = true
			}
			var g = (f - i) / 2;
			var s = (z - r) / 2
		}
		var e = "";
		var d = "";
		if(this._opts.imgQuality == "original") {
			e = l[0].naturalWidth;
			d = l[0].naturalHeight
		} else {
			e = l[0].width;
			d = l[0].height
		}
		var q = document.body.offsetWidth;
		var m = 50;
		var j = (z - d) / 2;
		if(j <= m) {
			j = 70
		}
		var u = e;
		var p = d;
		var x = q / u;
		var t = u / p;
		if(t < 1) {
			var n = z / d * 0.8;
			u = u * n;
			p = p * n;
			if(u < (f * 0.75)) {
				u = u * (1 - u / (f * 0.75) + 1);
				p = p * (1 - p / (z * 0.75) + 1)
			}
			x = 1
		}
		// if(i < u * x) {
		//     console.log(5555)
		//     return false
		// }
		l.width(i);
		l.height(r);
		l.css({
			"top": s + "px",
			"left": g + "px"
		});
		return l
	}, this._getTime = function() {
		return new Date().getTime()
	}, this._getDistance = function(d, c) {
		return Math.sqrt(d * d + c * c)
	}, this._getRotateDirection = function(d, c) {
		return d.x * c.y - c.x * d.y
	}, this._getRotateAngle = function(i, g) {
		var j = this._getRotateDirection(i, g);
		j = j > 0 ? -1 : 1;
		var e = this._getDistance(i.x, i.y);
		var d = this._getDistance(g.x, g.y);
		var f = e * d;
		if(f === 0) {
			return 0
		}
		var c = i.x * g.x + i.y * g.y;
		var h = c / f;
		if(h > 1) {
			h = 1
		}
		if(h < -1) {
			h = -1
		}
		return Math.acos(h) * j * 180 / Math.PI
	}, this._setNumber = function(k, i, h, m, l) {
		var j;
		var p;
		if(k) {
			if(this._opts.imgQuality == "original") {
				j = k[0].naturalWidth;
				p = k[0].naturalHeight
			} else {
				j = k[0].width;
				p = k[0].height
			}
		}
		var c = (h - p) / 2;
		if(c <= m) {
			c = 70
		}
		var n = j;
		var d = p;
		var g = l / n;
		var f = n / d;
		if(f < 1) {
			var e = h / p * 0.8;
			n = n * e;
			d = d * e;
			if(n < (i * 0.75)) {
				n = n * (1 - n / (i * 0.75) + 1);
				d = d * (1 - d / (h * 0.75) + 1)
			}
			g = 1
		}
		return {
			"per": g,
			"dwidth": n,
			"dheight": d,
			"ch": c
		}
	}, this._imgRestore = function(m, j) {
		var l = this;
		l.cdomthis = j;
		var f = $(".fly-zoom-box-img");
		var g = window.screen.width;
		var e = window.screen.height - l._opts.screenHeight;
		var o = (g - l.oWidth) / 2;
		var i = (e - l.oHeight) / 2;
		var k = document.body.offsetWidth;
		var d = document.body.offsetHeight;
		var n = 50;
		if(m == "tap") {
			f.css({
				"top": l.oTop + "px",
				"width": l.oWidth + "px",
				"height": l.oHeight + "px",
				"margin": "0 auto",
				"right": "0%",
				"left": "0%"
			});
			l.onPinch = l.onRotate = null
		} else {
			if(m == "chage") {
				var c = l._setNumber(j, g, e, n, k);
				f.css({
					"top": c.ch + "px",
					"width": c.dwidth * c.per + "px",
					"height": c.dheight * c.per + "px",
					"margin": "0 auto"
				});
				l.oTop = c.ch;
				l.oWidth = c.dwidth * c.per;
				l.oHeight = c.dheight * c.per
			} else {
				if(m == "touchend") {
					if(f.width() < l.oWidth) {
						var c = l._setNumber(j, g, e, n, k);
						f.animate({
							"top": c.ch + "px",
							"width": c.oWidth + "px",
							"height": c.oHeight + "px",
							"margin": "0 auto",
							"right": "0%",
							"left": "0%"
						}, 80, "linear", function() {
							l.onPinch = l.onRotate = null
						})
					}
				} else {
					if(m == "switcher" || m == "oneSwitcher") {
						if(m == "oneSwitcher") {
							var c = l._setNumber(j, g, e, n, k);
							f.css({
								"top": c.ch + "px",
								"width": c.dwidth * c.per + "px",
								"height": c.dheight * c.per + "px",
								"margin": "0 auto",
								"right": "0%",
								"left": "0%"
							});
							l.oTop = c.ch;
							l.oWidth = c.dwidth * c.per;
							l.oHeight = c.dheight * c.per
						} else {
							f.css({
								"right": "0%",
								"left": "0%"
							})
						}
					}
				}
			}
		}
	}, this._emitEvent = function(d, j) {
		var i = this;
		switch(d) {
			case "tap":
				break;
			case "doubletap":
				i.onDoubletap = true;
				break;
			case "longtap":
				i.onLongtap = true;
				break;
			case "swipe":
				i.onSwipe = true;
				break;
			case "move":
				if(i.onPinch) {
					i.onMove = true;
					var e = $(".fly-zoom-box-img");
					var h = parseInt(e.css("top"));
					var g = parseInt(e.css("left"));
					var c = h + j.deltaY;
					var f = g + j.deltaX;
					e.css({
						"top": c + "px",
						"left": f + "px"
					})
				}
				break;
			case "pinch":
				i.onPinch = true;
				i.isPinch = true;
				i._zommImg(j.scale, 0);
				break;
			case "rotate":
				i.isRotate = true;
				i.onRotate = true;
				break;
			case "left":
				if(!i.onPinch && !i.onRotate) {
					i.onLeft = true;
					i._leftMove()
				}
				break;
			case "right":
				if(!i.onPinch && !i.onRotate) {
					i.onRight = true;
					i._rightMove()
				}
				break;
			case "top":
				if(!i.onPinch && !i.onRotate) {
					i.onTop = true
				}
				break;
			case "bottom":
				if(!i.onPinch && !i.onRotate) {
					i.onBottom = true
				}
				break;
			case "touchend":
				i._imgRestore("touchend", i.cdomthis);
				i.isPinch = i.isRotate = i.startX = i.startY = i.moveX = i.moveY = i.touchDistance = null;
				i.previousPinchScale = 1;
				break
		}
	}
};
$.fn.FlyZommImg = function(a) {
	var b = new flyZommImg(this, a);
	b._init();
	return b
};
!(function(document, window, undefined){
	"use strict";
	var aui = new Object();
	aui = {
		/***对象合并(可实现多层对象深度合并)
		   @param {Object} opts 原始参数
		   @param {Object} opt 新参数
		   @param {bool} override 是否合并重置
		   @example: aui.extend("原始参数", "新参数", true);
		 */
		extend(opts, opt, override) {
			var _this = this;
			for (var p in opt) {
				try {
					// Property in destination object set; update its value.
					if ( opt[p].constructor == Object ) {
						opts[p] = _this.extend(opts[p], opt[p]);			
					} 
					else {
						opts[p] = opt[p];			
					}				
				} catch(e) {
				  // Property in destination object not set; create it and set its value.
				  opts[p] = opt[p];				
				}
			}			
		  return opts;
		},
		/***打开新页面
		   @param {string} url 页面路径
		   @param {Object} opts 参数 {id: ''}
		   @example: aui.openWin("index.html", {id: 1})
		*/
		openWin(url, opts){
			var _this = this;
			var str = '?';
			for(var i in opts){
				if(_this.isDefine(opts[i])){
					str += i + '=' + opts[i] + '&';
				}
			}
			window.location.href = _this.isDefine(opts) ? url + str : url;
		},		
		/***关闭页面
		   @example: aui.closeWin()
		*/
		closeWin(callback){
			//直接关闭页面，并向后台发送数据
			if(typeof callback == "function"){
				if(window.addEventListener) {
					window.addEventListener("beforeunload", callback, false);
				} else {
					window.attachEvent("onbeforeunload", callback, false);
				}
			}
			window.history.back(-1);
		},
		/***截取URL中字符串(可获取中文内容)
   		    aui.getUrlstr('id');
   		*/
   		getUrlstr(str){
   			var reg = new RegExp("(^|&)" + str + "=([^&]*)(&|$)", "i");
			var r = window.location.search.substr(1).match(reg);
			if (r != null) return decodeURI(r[2]); return null;
   		},
		/***判断字符串是否为空
		   @param {string} str 变量
		   @example: aui.isDefine("变量");
		*/
		isDefine(str){
			if (str == null || str == "" || str == "undefined" || str == undefined || str == "null" || str == "(null)" || str == 'NULL' || typeof (str) == 'undefined'){
				return false;
			}else{
				str = str + "";
				str = str.replace(/\s/g, "");
				if (str == ""){return false;}
				return true;
			}
		},
		/***引入 js / css 文件
		   @example: aui.import('js/aui.picker.js')
		   @example: aui.import(['js/aui.picker.js', 'css/aui.picker.css'])
		*/
		import(url){	
			var _this = this;
			switch (url.constructor){
				case Array:
					for(const [index, item] of url.entries()){
						creat(item);
					}
					break;
				case String:
					creat(url);
					break;
				default:
					break;
			}
			function creat(file){
				if(/^.+?\.js$/.test(file))
				{ //JS文件引入
					var script = document.createElement("script");
					script.setAttribute("type", "text/javascript");
					script.setAttribute("src", file);
					document.querySelector('head').appendChild(script);
				}
				if(/^.+?\.css$/.test(file))
				{ //CSS文件引入
					var css = document.createElement('link');
					css.rel = 'stylesheet';
					css.type = 'text/css';
					css.href = file;		
					document.querySelector('head').appendChild(css);	
				}			
			}
		},		
		//生成随机数
	    random(Min, Max) {
		    var Range = Max - Min;
		    var Rand = Math.random();
		    if(Math.round(Rand * Range)==0){
		        return Min + 1;
		    }else if(Math.round(Rand * Max)==Max)
		    {
		        index++;
		        return Max - 1;
		    }else{
		        var num = Min + Math.round(Rand * Range) - 1;
		        return num;
		    }
		},
		/***去除字符串中空格
		 	@param {string} str 字符串
		 	@param {Boolean} flag {false: 去除前后空格 | true: 去除全部空格}
   			@example: aui.space(str, true);
   		*/
   		space(str, flag){
   			var result;
		    result = str.replace(/(^\s+)|(\s+$)/g, "");
		    if (flag) //flag==false -->去除前后空格；flag==true -->去除全部空格
		    {
		        result = result.replace(/\s/g, "");
		    }
		    return result;
   		},
		/*** 触摸改变元素背景色/字体色/边框
		  	@param {string} dom 元素对象 如： document.querySelector(".list")
		  	@param {string} bg 背景色("#EFEFEF")
		  	@param {string} color 字体色("#333")
		  	@param {string} border 边框("1px solid #ccc")
		  	@example: aui.touchDom(document.querySelector(".list"), '#EFEFEF');
		 */
		touchDom(dom, bg, color, border){
			var _this = this;
			var bg_old = _this.getStyle(dom).backgroundColor,
				color_old = _this.getStyle(dom).color,
				border_old = _this.getStyle(dom).border;
			dom.addEventListener("touchstart", function(e){
				_this.isDefine(bg) ? dom.style.background = bg : dom.style.background =  bg_old;
				_this.isDefine(color) ? dom.style.color = color : dom.style.color = color_old;
				_this.isDefine(border) ? dom.style.border = border : dom.style.border = border_old;
			});
			dom.addEventListener("touchmove", function(e){
				dom.style.background = bg_old;
				dom.style.color = color_old;
				dom.style.border = border_old;
			});
			dom.addEventListener("touchend", function(e){
				dom.style.background = bg_old;
				dom.style.color = color_old;
				dom.style.border = border_old;
			});
		},
		/***阻止元素默认事件
		 * @param {Object} e
		 */
		preventDefault(e){
	        if ( e && e.preventDefault ){ 
			    e.preventDefault(); 
	        }
			else{ 
			    window.event.returnValue = false; 								
			    return false; 
			} 
	   	},
		/***获取元素css样式
		 	@param {string} el dom元素 如：document.querySelector("<div>")
		 	@example: aui.getStyle(document.querySelector("div")).width;
		 */
		getStyle(el){
			return window.getComputedStyle(el, null);
		},
		/***获取标签元素 十六进制 背景色
		 	@param {string} el dom元素 如：document.querySelector("<div>")
		 	@example: aui.getHexBgColor(document.querySelector("div"));
		*/
		getHexBgColor(el){
			var str = [];
			var rgb = el.style.backgroundColor.split('(');
			for(var k = 0; k < 3; k++)
			{
				str[k] = parseInt(rgb[1].split(',')[k]).toString(16);
			}
			str = '#' + str[0] + str[1] + str[2];
			return str;
		},
		/***获取标签元素 十六进制 字体色
		    @param {string} el dom元素 如：document.querySelector("<div>")
		 	@example: aui.getHexColor(document.querySelector("div"));
		*/
		getHexColor(el){
			var str = [];
			var rgb = el.style.color.split('(');
			for(var k = 0; k < 3; k++)
			{
				str[k] = parseInt(rgb[1].split(',')[k]).toString(16);
			}
			str = '#' + str[0] + str[1] + str[2];
			return str;
		},
		/*** 根据输入的文本自动改变textarea框的高度   
		  	@param {string} el 元素对象 如： document.querySelector(".list")
		  	@param {number} maxHeight 最大高度
		  	@param {number} minHeight 最小高度
		  	@example: aui.autoTextarea(document.querySelector("#textarea"), 300, 100);
		 */
		autoTextarea(el, maxHeight, minHeight){
			el.onchange = el.oninput = el.onpaste = el.oncut = el.onkeydown = el.onkeyup = el.onfocus = el.onblur = function(){
				var height,style=this.style;
		        this.style.height = minHeight + 'px';
		        if (this.scrollHeight > minHeight)
		        {
			        if (maxHeight && this.scrollHeight > maxHeight)
			        {
			            height = maxHeight;
			            style.overflowY = 'scroll';
			        }
			        else
			        {
			            height = this.scrollHeight;
			            style.overflowY = 'hidden';
			        }
			        style.height = height + 'px';
		        }
				this.scrollTop = this.scrollHeight;
			}
		},
		//数组去重
		uniq(array){
		    var temp = []; //一个新的临时数组
		    for(var i = 0; i < array.length; i++){
		        if(temp.indexOf(array[i]) == -1){
		            temp.push(array[i]);
		        }
		    }
		    return temp;
		},
		// 复制到剪切板
		copy(str){
			var save = function (e){
				e.clipboardData.setData('text/plain',str);//clipboardData对象
				e.preventDefault();//阻止默认行为
			};
			document.addEventListener('copy',save);
			return document.execCommand("copy");//使文档处于可编辑状态，否则无效
		},		
		/* 初始化导航栏底部选中底线位置
			@param {string} nav 导航栏设置overflow-x: scroll的元素id或class
			@param {string} navItem 导航栏菜单li的元素id或class
			@param {string} navBorder 导航栏选中元素底部border样式的元素id或class
			@param {number} index 导航栏选中元素的索引 0- ...
			@example: app.resetNavBorder("#nav", '.top-navtab-item', '.nav_border', i); //初始化导航栏底部选中底线位置
		*/
		resetNavBorder: function(nav, navItem, navBorder, index){
			var _navItem = document.querySelector(navItem + ":nth-child("+ (Number(index) + 1) +")");
			var _navBorder = document.querySelector(navBorder);
			$(nav).animate({
				scrollLeft: _navItem.offsetLeft - (window.screen.width - _navItem.offsetWidth - 0) / 2
			},300);
			$(navBorder).css({
				left: _navItem.offsetLeft + (_navItem.offsetWidth / 2) - (_navBorder.offsetWidth / 2) + "px"
			});
		}		
	}
	// 将插件对象暴露给全局对象
	if(typeof module !== 'undefined' && typeof exports === 'object' && define.cmd) { module.exports = aui;}
    else if (typeof define === "function" && define.amd){define(function(){return aui;});}
    else {window.aui = aui;}
})(document, window);

/* ===============================
 	数据请求
   ===============================
 */
!(function($, document, window, undefined){
	/*** ajax数据请求接口
	  	@param {string} type 请求方式 {"get(默认)" | "GET" | "post" | "POST"}
	 	@param {string} url 请求接口地址
	  	@param {Object} data 请求时后台所需参数
	  	@param {bool} async 是否异步(true)或同步(false)请求{true(默认) | false}	  	
	  	@example: aui.ajax({type: "post", url: "", data: {}}).then(function(ret){}).then(function(err){});
	*/
	$.ajax = function({type, url, data, async}){
		var _this = this;
		// 异步对象
		var ajax;		
		window.XMLHttpRequest ? ajax =new XMLHttpRequest() : ajax=new ActiveXObject("Microsoft.XMLHTTP");
		!$.isDefine(type) ? type = "get" : type = type;
		!$.isDefine(data) ? data = {} : data = data;
		async != false ? !$.isDefine(async) ? async = true : async = async : '';
		return new Promise(function(resolve,reject){
			// get 跟post  需要分别写不同的代码
			if (type.toUpperCase()=== "GET") 
			{// get请求
				if (data) {// 如果有值
					url += '?';										
					if( typeof data === 'object' )
					{ // 如果有值 从send发送
						var convertResult = "" ;
						for(var c in data){
							convertResult += c + "=" + data[c] + "&";
						}						
						url += convertResult.substring(0,convertResult.length-1);
					}
					else
					{
						url += data;
					}
				}
				ajax.open(type, url, async); // 设置 方法 以及 url
				ajax.send(null);// send即可
			}
			else if(type.toUpperCase()=== "POST")
			{// post请求
				ajax.open(type, url); // post请求 url 是不需要改变
				ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded"); // 需要设置请求报文
				if(data)
				{ // 判断data send发送数据
					if( typeof data === 'object' ){// 如果有值 从send发送
						var convertResult = "" ;
						for(var c in data){
						  convertResult += c + "=" + data[c] + "&";
						}
						convertResult = convertResult.substring(0,convertResult.length-1);
						ajax.send(convertResult);
					}else{
						ajax.send(data);
					}
				} else
				{
					ajax.send(); // 木有值 直接发送即可
				}
			}
			// 注册事件
			ajax.onreadystatechange = function () {
				// 在事件中 获取数据 并修改界面显示
				if (ajax.readyState == 4)
				{
					if(ajax.status===200)
					{ // 返回值： ajax.responseText;
						if(ajax.response && typeof ajax.response != 'object'){
							resolve(JSON.parse(ajax.response));							
						}
						else{
							resolve(ajax.response);
						}
					}
					else
					{
						reject(ajax.status);
					}
				}
			}
		});		
	}
})(aui, document, window);

/* =============================================================
 	数据校验 （账号 + 手机号码  + 电话号码 + 密码 + 邮箱 + 银行卡号码）
   =============================================================
 */
!(function($, document, window, undefined){
	/***中文姓名检测
	 	@param {number} name 姓名
	 	@example: aui.checkName(name); //return true | false;
	*/
	$.checkName = function(name) {
        if(!name) return false;
		var re = /^[\u4E00-\u9FA5]{2,4}$/;
		if(re.test(name)) return true;
		else return false;
   }
	/***汉字验证
	 	@param {number} str 汉字内容
	 	@example: aui.checkChinese(str); //return true | false;
	*/
	$.checkChinese = function(str) {
        if(!str) return false;
		var re = /^[\u4E00-\u9FA5]+$/;
		if(re.test(str)) return true;
		else return false;
    }
	/***验证帐号是否合法。验证规则：字母、数字、下划线组成，字母开头，4-16位。
	    @param {string} account 账号
		@example: aui.checkkUser("账号");  //return true | false;
	*/
	$.checkAccount = function(account){
		var re = /^[a-zA-z]\w{3,15}$/;
	    if(re.test(account)) return true;
	    else return false;
	}
	/***手机号码检测
	 	@param {number} phone 手机号码
	 	@example: aui.checkMobile(phone); //return true | false;
	*/
	$.checkMobile = function(phone) {
        if(!phone) return false;
		var re = /^1(3[0-9]|4[57]|5[123567890]|7[3678]|8[0-9]|9[0-9])\d{8}$/;
		if(re.test(phone)) return true;
		else return false;
    }
	/***验证电话号码,验证规则：区号+号码，区号以0开头，3位或4位号码由7位或8位数字组成区号与号码之间可以无连接符，也可以“-”连接如01088888888,010-88888888,0955-7777777
	    @param {String} tel 电话号码
	 	@example: aui.checkTel(tel);  //return true | false;
	*/
	$.checkTel = function(tel){
		var re = /^0\d{2,3}-?\d{7,8}$/;
	    if(re.test(tel)) return true;
	    else return false;
	}
	/***校验  email 邮箱账号
	    @param {String} email 邮箱账号
	 	@example: aui.checkEmail(email);  //return true | false;
	*/
	$.checkEmail = function(email){
		var re = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
		if(re.test(email)) return true;
		else return false;
	}
	/***校验  password 密码
	    @param {string} pass 密码 --只能输入6-20个字母、数字、下划线
	 	@example: aui.checkPass(pass);  //return true | false;
	*/
	$.checkPass = function(pass){
		var re = /^(\w){6,20}$/;
		if(re.test(pass)) return true;
		else return false;
	}
	/***校验  password 密码 强弱
	    @param {string} pass 密码 --只能输入6-20个字母、数字、下划线
	 	@example: aui.checkPassStrength(pass);  //return 'r'-弱 | 'z'-中 | 'q'-强 | false-格式有误;
	*/
	$.checkPassStrength = function(pass){
		var r = /^(\w){6,20}$/;
		var z = /^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*]+$/;
		var q = /^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)(?![a-zA-z\d]+$)(?![a-zA-z!@#$%^&*]+$)(?![\d!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*]+$/;
		if(r.test(pass)) {
			return 'r';
		}
		else{
			if(z.test(pass)) {
				return 'z';
			}
			else{
				if(q.test(pass)){
					return 'q';
				}
				else{
					return false;
				}
			}
		}
	}
	/***校验 搜索关键字
	    @param {String} keywords 搜索内容
	 	@example: aui.checkSearch(pass);  //return true | false;
	*/
	$.checkSearch = function(keywords){
		var re = /^[^`~!@#$%^&*()+=|\\\][\]\{\}:;'\,.<>/?]{1}[^`~!@$%^&()+=|\\\][\]\{\}:;'\,.<>?]{0,19}$/;
		if(re.test(keywords)) return true;
		else return false;
	}
	/***校验 IP地址
	    @param {String} ip IP地址
	 	@example: aui.checkIP(pass);  //return true | false;
	*/
	$.checkIP = function(ip){
		var re =/^[0-9.]{1,20}$/;
		if(re.test(ip)) return true;
		else return false;
	}
	/***银行卡加密显示： XXXX **** XXXX;
	    @param {string} cardnum 银行卡号
	*/
	$.encodeCard = function(cardnum){
	    var reg = /^(\d{4})(\d*)(\d{4})$/;
	    cardnum = cardnum.replace(reg, function(a, b, c, d) {
	        return b + c.replace(/\d/g, "*") + d;
	    });
	    // console.log(cardnum);
	    return cardnum;
	}
})(aui, document, window);

/***本地定时缓存（一段时间内有效）
 	@example: aui.setLocal('items', items, 1*24*60*60); 缓存一天内有效
*/
!(function($, document, window, undefined){
	$.setLocal = function(key,value,time){
		try{
			if(!localStorage){return false;}
			if(!time || isNaN(time)){time=60;}
			var cacheExpireDate = (new Date()-1)+time*1000;
			var cacheVal = {data: value, exp: cacheExpireDate};
			localStorage.setItem(key,JSON.stringify(cacheVal));//存入缓存值
			//console.log(key+":存入缓存，"+new Date(cacheExpireDate)+"到期");
		}catch(e){}
	}
	/**获取缓存*/
	$.getLocal = function (key){
		try{
			if(!localStorage){return false;}
			var cacheVal = localStorage.getItem(key);
			var result = JSON.parse(cacheVal);
			var now = new Date()-1;
			if(!result){return null;}//缓存不存在
			if(now>result.exp){//缓存过期
				$.remove(key);
				return "";
			}
			//console.log("get cache:"+key);
			return result.data;
		}catch(e){
			$.removeLocal(key);
			return null;
		}
	}
	/**移除缓存，一般情况不手动调用，缓存过期自动调用*/
	$.removeLocal = function(key){
		if(!localStorage){return false;}
		localStorage.removeItem(key);
	}
	/**清空所有缓存*/
	$.clearLocal = function(){
		if(!localStorage){return false;}
		localStorage.clear();
	}
})(aui, document, window);


/* ===============================
 	设备相关操作
   ===============================
 */
!(function($, document, window, undefined){
	/***判断是否为微信浏览器
	 	@example: aui.isWx(); //return true | false;
	*/
	$.isWx = function(){
		const ua = window.navigator.userAgent.toLowerCase();
	  	//通过正则表达式匹配ua中是否含有MicroMessenger字符串
	  	if(ua.match(/MicroMessenger/i) == 'micromessenger') return true;
	  	else return false;
	}
	/***判断是否为IOS系统
	 	@example: aui.isIos(); //return true | false;
	*/
	$.isIos = function(){
		var u = navigator.userAgent, app = navigator.appVersion;
	    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //g
	    var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
	    if(isIOS) return true;
	    else return false;
	}
	/***判断是否为android系统
	 	@example: aui.isAndroid(); //return true | false;
	*/
	$.isAndroid = function(){
		var u = navigator.userAgent, app = navigator.appVersion;
	    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //g
	    var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
	    if(isAndroid) return true;
	    else return false;
	}
	/***判断是否为IE浏览器
	 	@example: aui.isIe();
	*/
	$.isIe = function(){
		if (window.navigator.userAgent.toLowerCase().indexOf("msie") >= 1) return true;
	    else return false;
	}
	/***判断是否为PC
	 	@example: aui.isPC(); //return true | false;
	*/
	$.isPC = function(){
		var userAgentInfo = navigator.userAgent;
	    var Agents = ["Android", "iPhone",
	        "SymbianOS", "Windows Phone",
	        "iPad", "iPod"];
	    var flag = true;
	    for (var v = 0; v < Agents.length; v++)
	    {
	        if (userAgentInfo.indexOf(Agents[v]) > 0)
	        {
	            flag = false;
	            break;
	        }
	    }
	    return flag;
	}
	/***获取设备是否连接网络
	 	@example: aui.getLine(); //return true | false;
	*/
	$.getLine = function(){
		if(navigator.onLine) return true;
		else return false;
	}
	/***获取设备网络类型
	 	@example: aui.getNetworkType();
	*/
	$.getNetworkType = function(){
		var ua = navigator.userAgent;
        var networkStr = ua.match(/NetType\/\w+/) ? ua.match(/NetType\/\w+/)[0] : 'NetType/other';
        networkStr = networkStr.toLowerCase().replace('nettype/', '');
        var networkType;
        switch (networkStr) {
            case 'wifi': networkType = 'wifi'; break;
            case '4g': networkType = '4g'; break;
            case '3g': networkType = '3g'; break;
            case '3gnet': networkType = '3g'; break;
            case '2g': networkType = '2g'; break;
            default: networkType = 'other';
        }
        return networkType;
	}
})(aui, document, window);

/* ===============================
 	元素长按事件
	 	@param {string} warp 长按元素 ->document.querySelector(".list") 或document.querySelectorAll(".list")
	 	@param {number} time 长按时间限制 默认500ms
	 	@example: aui.longPress({warp: '', time: 500}, function(){});
   ===============================
 */
!(function($, document, window, undefined){
	var longPress = {
        opts: function(opt){
			var opts = {
				warp: '',
				time: 500
			}
			return $.extend(opts, opt, true);
		},
		on: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(!$.isDefine(_opts.warp)){
				$.toast({msg: "Long press element not set"}); return false;
			}
			var timer = null;
			if(_opts.warp.length > 1){
				for(var i = 0; i < _opts.warp.length; i++){
					_opts.warp[i].addEventListener("touchstart", function(event){
						var _self = this;
						event.preventDefault(); //仅对当前元素进行阻止触发默认事件
						timer = setTimeout(function(){
							typeof callback == "function" ?  callback(_self) : '';
						}, _opts.time);
					}, false);
					_opts.warp[i].addEventListener("touchmove", function(event){
						var _self = this;
						clearTimeout(timer); //手指移动则不执行传入的方法
					}, false);
					_opts.warp[i].addEventListener("touchend", function(event){
						clearTimeout(timer); //长按时间少于 _opts.time,则不执行传入的方法
					}, false);
				}
			}
			else{
				_opts.warp.addEventListener("touchstart", function(event){
					var _self = this;
					event.preventDefault(); //仅对当前元素进行阻止触发默认事件
					timer = setTimeout(function(){
						typeof callback == "function" ?  callback(_self) : '';
					}, _opts.time);
				}, false);
				_opts.warp.addEventListener("touchmove", function(event){
					var _self = this;
					clearTimeout(timer); //手指移动则不执行传入的方法
				}, false);
				_opts.warp.addEventListener("touchend", function(event){
					clearTimeout(timer); //长按时间少于 _opts.time,则不执行传入的方法
				}, false);
			}

		}
    }
	$.longPress = function(opt, callback){
		longPress.on(opt, callback);
	};
})(aui, document, window);

/* ===============================
 	元素拖动事件
	 	@param {string} warp 拖动元素 ->document.querySelector(".list") 或document.querySelectorAll(".list")
	 	@example: aui.drag({warp: ''}, function(){});
   ===============================
 */
!(function($, document, window, undefined){
	var drag = new Object();
	drag = {
		opts(opt){
			var opts = {
				warp: '', //--可选参数，父容器
			}
			return $.extend(opts, opt, true);
		},
		on(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			if(!$.isDefine(_opts.warp)){
				$.toast({msg: "drag element not set"}); return false;
			}
			var oL,oT,oLeft,oTop;
			var ui = {
				warp: document.querySelector('body'),
				main: _opts.warp,
				currentHeight: _opts.warp.offsetHeight,
				maxW: window.screen.width - _opts.warp.offsetWidth,
				maxH: window.screen.height - _opts.warp.offsetHeight
			}
			ui.main.addEventListener('touchstart', function(e) {
				var ev = e || window.event;
				var touch = ev.targetTouches[0];
				oL = touch.clientX - ui.main.offsetLeft;
				oT = touch.clientY - ui.main.offsetTop;
				window.addEventListener("touchmove", preventDefault, { passive: false });
			})
			ui.main.addEventListener('touchmove', function(e) {
				var _self = this;
				var ev = e || window.event;
				var touch = ev.targetTouches[0];
				oLeft = touch.clientX - oL;
				oTop = touch.clientY - oT;
				oLeft < 0 ? oLeft = 0 : oLeft >= ui.maxW ? oLeft = ui.maxW : '';
				oTop < 0 ? oTop = 0 : oTop >= ui.maxH ? oTop = ui.maxH : '';
				ui.main.style.left = oLeft + 'px';
				ui.main.style.top = oTop + 'px';
				typeof callback == "function" ?  callback({el: _self, type: 'touchmove'}) : '';
			})
			ui.main.addEventListener('touchend', function() {
				var _self = this;
				oLeft > 0 && oLeft < ui.maxW / 2 ? oLeft = 0 : oLeft > ui.maxW / 2 && oLeft < ui.maxW ? oLeft = ui.maxW : '';
				ui.main.style.left = oLeft + 'px';
				ui.main.style.transition = 'all .3s';
				var timer = setTimeout(function(){
					ui.main.style.transition = 'auto';
					clearInterval(timer);
				},300);
				ui.main.style.height = ui.currentHeight + "px";
				ui.main.style.top = oTop + 'px';
				if(ui.main.offsetTop >= window.screen.height - ui.main.offsetHeight - 100){
					ui.main.style.top = window.screen.height - ui.main.offsetHeight - 100 + "px";
				}
                window.removeEventListener("touchmove", preventDefault);
				typeof callback == "function" ?  callback({el: _self, type: 'touchend'}) : '';
			})
            function preventDefault(e){
                e.preventDefault();
            }
		}
	}
	$.drag = function(opt, callback){
		drag.on(opt, callback);
	};
})(aui, document, window);

/* ===============================
 	UI组件
   ===============================
 */
/***  loading 加载动画  */
!(function($, document, window, undefined){
	var loading = new Object();
	loading = {
		opts(opt){
			var opts = {
				warp: 'body', // --可选参数，父容器元素
				type: 1, //--可选参数，默认圆环风格(<1>、1:toast圆环风格，<2>、2:点击按钮后在按钮内显示加载动画) <3>、3:四方块水平方向旋转，
				msg: '', //--可选参数，描述内容
				mask: false, //--可选参数，是否显示遮罩，默认false
				direction: "col", //--可选参数，横向("row")或纵向("col")控制，默认纵向
				theme: 1, //--可选参数，控制风格
				style: {
					bg: '', // --可选参数，.aui-loading-main背景色(rgba(0,0,0,.6))
					color: '', //--可选参数，文字颜色
					maskBg: '', //--可选参数，遮罩层背景色(rgba(0,0,0,.3))
					zIndex: '', //--可选参数，加载弹窗.aui-loading层级
				}
			}
			return $.extend(opts, opt, true);
		},
		creat(opt){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '';
			switch (Number(_opts.type)){
				case 1: //常用风格
					_html = '<div class="aui-loading aui-loading-ring">'
						+'<div class="aui-mask"></div>'
						+'<div class="aui-loading-main">'
							+'<div class="aui-loading-animate">';
								for(var i = 0; i < 12; i++){
									_html += '<span class="span"></span>';
								}
							_html +=
							'</div>'
							+'<div class="aui-loading-msg">'+ _opts.msg +'<span class="dotting"></span></div>'
						+'</div>'
					+'</div>';
					break;
				case 2: //点击按钮后在按钮内显示加载动画
					_html += '<div class="aui-loading aui-loading-button">'
						+'<div class="aui-loading-main">'
							+'<div class="aui-loading-animate">';
								for(var i = 0; i < 12; i++){
									_html += '<span class="span"></span>';
								}
							_html +=
							'</div>'
							+'<div class="aui-loading-msg">'+ _opts.msg +'</div>'
						+'</div>'
					+'</div>';
					break;
				case 3: //四个方块旋转
					_html = '<div class="aui-loading aui-loading-squarefour">'
						+'<div class="aui-mask"></div>'
						+'<div class="aui-loading-main">'
							+'<div class="aui-loading-animate"><span class="span1"></span><span class="span2"></span><span class="span3"></span><span class="span4"></span></div>'
							+'<div class="aui-loading-msg">'+ _opts.msg +'<span class="dotting"></span></div>'
						+'</div>'
					+'</div>';
					break;
				case 4: //圆点放大缩小动画(全屏首次加载过度动画)
					_html = '<div class="aui-loading aui-loading-dots">'
					+'<div class="aui-mask"></div>'
						+'<div class="aui-loading-main">'
							+'<div class="aui-loading-dot-items">'
								+'<div class="aui-loading-dot-item" id="dot_one"></div>'
								+'<div class="aui-loading-dot-item" id="dot_two"></div>'
								+'<div class="aui-loading-dot-item" id="dot_three"></div>'
							+'</div>'
						+'</div>'
					+'</div>';
					break;
				case 5: //圆点背景过度动画-微信小程序效果(全屏首次加载过度动画)
					_html = '<div class="aui-loading aui-loading-dots-opacity">'
					+'<div class="aui-mask"></div>'
						+'<div class="aui-loading-main">'
							+'<div class="aui-loading-dot-items">'
								+'<div class="aui-loading-dot-item" id="dot_one"></div>'
								+'<div class="aui-loading-dot-item" id="dot_two"></div>'
								+'<div class="aui-loading-dot-item" id="dot_three"></div>'
							+'</div>'
						+'</div>'
					+'</div>';
					break;
				default:
					break;
			}
			//if(document.querySelector(".aui-loading")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			var ui = {
				msg: document.querySelector(".aui-loading-msg"),
				mask: document.querySelector(".aui-loading .aui-mask"),
			}
			!$.isDefine(_opts.mask) && ui.mask ? ui.mask.parentNode.removeChild(ui.mask) : '';
			!$.isDefine(_opts.msg) && ui.msg ? ui.msg.parentNode.removeChild(ui.msg) : '';
			document.querySelector(".aui-mask,.aui-loading").addEventListener("touchmove", function(e){
	            e.preventDefault();
	       	});
			_this.css(opt);
		},
		css(opt){ //样式设置
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				warp: document.querySelector(_opts.warp),
				loading: document.querySelector(".aui-loading"),
				main: document.querySelector(".aui-loading-main"),
				button: document.querySelector(".aui-loading.aui-loading-button"),
				buttonMain: document.querySelector(".aui-loading.aui-loading-button .aui-loading-main"),
				ring: document.querySelector(".aui-loading.aui-loadig-ring"),
				ringMain: document.querySelector(".aui-loading.aui-loading-ring .aui-loading-main"),
				ringSpans: document.querySelector(".aui-loading.aui-loading-ring .span"),
				squarefour: document.querySelector(".aui-loading.aui-loading-squarefour"),
				squarefourMain: document.querySelector(".aui-loading.aui-loading-squarefour .aui-loading-main"),
				animate: document.querySelector(".aui-loading-animate"),
				msg: document.querySelector(".aui-loading-msg"),
				mask: document.querySelector(".aui-loading .aui-mask"),
				spans: document.querySelector(".aui-loading.aui-loading-button .span")
			}
			$.isDefine(_opts.style.bg) ? ui.main.style.background = _opts.style.bg : '';
			$.isDefine(_opts.style.color) && ui.msg ? ui.msg.style.color = _opts.style.color : '';
			$.isDefine(_opts.style.zIndex) ? ui.main.style.zIndex = _opts.style.zIndex : '';
			$.isDefine(_opts.style.maskBg) && ui.mask ? ui.mask.style.background = _opts.style.maskBg : '';
			switch (Number(_opts.type)){
				case 1: //ring全屏布局加载动画
					$.isDefine(_opts.msg) ? ui.main.style.minWidth = ui.main.offsetHeight + 10 + "px" : '';
					if(_opts.direction == "row")
					{ //水平布局样式设置
						ui.main.style.cssText = "width: auto; min-height: auto; padding: 10px 15px 9px 15px";
						ui.ringMain.style.whiteSpace = "nowrap";
						if(ui.msg){
							ui.msg.style.cssText = "width: auto; max-width: auto; display: inline-block; height: 24px; line-height: 24px; margin: 0 0 0 10px; font-size: 15px;;";
							ui.animate.style.cssText = "display: inline-block; width: 25px; height: 25px;"
						}
					}
					for(var i = 0; i < 12; i++){
						$.isDefine(_opts.style.color) ? ui.ringSpans.parentElement.children[i].style.borderColor = _opts.style.color : '';
					}
					break;
				case 2: //button按钮加载动画
					ui.warp.style.position = $.getStyle(ui.warp).position == "static" ? "relative" : '';
					ui.button.style.cssText = "width: "+ ui.warp.offsetWidth +"px; height: "+ui.warp.offsetHeight+"px";
					ui.animate.style.marginTop = (ui.warp.offsetHeight - ui.animate.offsetHeight) / 2 - parseInt($.getStyle(ui.warp).borderWidth) + "px";
					ui.msg ? ui.msg.style.marginTop = (ui.warp.offsetHeight - ui.animate.offsetHeight) / 2 - parseInt($.getStyle(ui.warp).borderWidth) - 1 + "px" : '';
					ui.button.style.marginLeft = $.getStyle(ui.warp).border != "0px none rgb(0, 0, 0)" ? - parseInt($.getStyle(ui.warp).borderWidth) + "px" : '';
					ui.button.style.marginTop = $.getStyle(ui.warp).border != "0px none rgb(0, 0, 0)" ? - parseInt($.getStyle(ui.warp).borderWidth) + "px" : '';
					ui.buttonMain.style.borderRadius = parseInt($.getStyle(ui.warp).borderRadius) > 0 ? parseInt($.getStyle(ui.warp).borderRadius) + "px" : '';
					ui.buttonMain.style.background = $.getStyle(ui.warp).backgroundColor;
					ui.msg ? ui.msg.style.color = $.getStyle(ui.warp).color : '';
					for(var i = 0; i < 12; i++){
						ui.spans.parentElement.children[i].style.borderColor = $.getStyle(ui.warp).color;
					}
					ui.msg ? ui.msg.style.fontSize = $.getStyle(ui.warp).fontSize : '';
					ui.button.addEventListener("touchstart", function(e){
			            e.preventDefault();
			       	});
					break;
				case 3: //squarefour四方块旋转加载动画
					if(_opts.theme == 1)
					{ //小窗（可设置mask）
						ui.squarefour.classList.add('aui-loading-squarefour-style-1')
						$.isDefine(_opts.msg) ? ui.squarefourMain.style.width = ui.squarefourMain.offsetHeight + 10 + "px" : '';
					}
					else if(_opts.theme == 2)
					{ //全屏覆盖
						ui.squarefour.classList.add('aui-loading-squarefour-style-2')
					}
					break;
				default:
					break;
			}
		},
		show(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt);
			var _timer = setTimeout(function(){
				typeof callback == "function" ?  callback() : '';
				clearTimeout(_timer);
			},200);
		},
		hide(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var _timer = setTimeout(function(){
				document.querySelector(".aui-loading") ? document.querySelector(".aui-loading").parentNode.removeChild(document.querySelector(".aui-loading")) : '';				
				typeof callback == "function" ?  callback() : '';
				clearTimeout(_timer);
			},300);
		}
	}
	$.showload = function(opt, callback){
		loading.show(opt, callback);
	};
	$.hideload = function(opt, callback){
		loading.hide(opt, callback);
	};
})(aui, document, window);

/***  toast消息提示弹窗  */
!(function($, document, window, undefined){
	var toast = new Object();
	toast = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				msg: '', //--必选参数，描述内容
				icon: '', //--可选参数，图标
				direction: "col", //--可选参数，（icon参数配置后有效）横向("row")或纵向("col")控制，默认纵向
				location: 'bottom', //--可选参数，（icon参数未配置时可配置）位置	<1、bottom:位于底部，从底部弹出显示>、<2、middle:位于页面中心位置>
				duration: 2000, //--可选参数，显示时间
			}
			return $.extend(opts, opt, true);
		},
		creat(opt){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '';
			switch ($.isDefine(_opts.icon)){
				case false: //无图标
					_html = '<div class="aui-toast">'
						+'<div class="aui-toast-main">'
							+'<div class="aui-toast-msg">'+ _opts.msg +'</div>'
						+'</div>'
					+'</div>';
					break;
				case true: //有图标
					_html = '<div class="aui-toast">'
						+'<div class="aui-toast-main">'
							+'<div class="aui-toast-icon"><img src="'+ _opts.icon +'" /></div>'
							+'<div class="aui-toast-msg">'+ _opts.msg +'</div>'
						+'</div>'
					+'</div>';
					break;
				default:
					break;
			}
			// if(document.querySelector(".aui-toast")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this.css(opt);
		},
		css(opt){ //样式设置
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				warp: document.querySelector(_opts.warp),
				toast: document.querySelector(".aui-toast:last-child"),
				main: document.querySelector(".aui-toast-main"),
				icon: document.querySelector(".aui-toast-icon"),
				msg: document.querySelector(".aui-toast-msg")
			}
			switch ($.isDefine(_opts.icon)){
				case false: //无图标
					if(_opts.location == "bottom")
					{ //位于底部，从底部弹出显示
						ui.toast.classList.add('aui-toast-bottom');
					}
					else if(_opts.location == "middle")
					{ //位于页面中心位置
						ui.toast.classList.add('aui-toast-middle');
					}
					break;
				case true: //有图标
					ui.toast.classList.add('aui-toast-middle');
					if(_opts.direction == "row")
					{ //水平布局
						ui.main.style.cssText = "width: 100%; white-space: nowrap;";
						ui.msg.style.cssText = "margin-left: 10px; display: inline-block;";
						ui.icon.style.cssText = "display: inline-block;";
					}
					break;
				default:
					break;
			}
			ui.toast.style.left = (ui.warp.offsetWidth - ui.toast.offsetWidth) / 2 + "px";
		},
		show(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt);
			var timer = setTimeout(function() {
				_this.hide();
				clearTimeout(timer);
				typeof callback == "function" ?  callback() : '';
			},_opts.duration);
		},
		hide(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				toast: document.querySelector(".aui-toast")
			}
			document.querySelector(".aui-toast") ? document.querySelector(".aui-toast").parentNode.removeChild(document.querySelector(".aui-toast")) : '';
			typeof callback == "function" ?  callback() : '';
		}
	}
	$.toast = function(opt, callback){
		toast.show(opt, callback);
	};
})(aui, document, window);

/***  dialog 模态弹窗  */
!(function($, document, window, undefined){
	var dialog = new Object();
	dialog = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				title: '', //--可选参数，标题
				msg: '', //--可选参数，描述内容
				btns: ["确定"], //--可选参数，按钮，默认按钮为“确定” 分别可设置btns值为<1：['按钮1', '按钮2']>、<2：[{name: '按钮1', color: ''},{name: '按钮2', color: ''}]>
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				theme: 1, //--可选参数，主题样式，控制模态弹窗按钮显示风格
				items: [], //prompt--input框列表配置[{label: '姓名：', type: 'text', value: '(可选)', placeholder: '请输入姓名'}]
				style: {
					w: '', //--可选参数，模态窗宽度，默认80%
					h: '', //--可选参数，模态窗高度，默认"auto"自适应
					bg: '',//--可选参数，模态窗背景色，默认白色
					zIndex: '', //--可选参数，模态窗层级
					title: {
						bg: "",
						color: "",
						lineHeight: "",
						textAlign: "",
						fontSize: "",
						padding: ""
					}
				}
			}
			return $.extend(opts, opt, true);
		},
		creat(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-dialog">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-dialog-main">'
					+'<div class="aui-dialog-title">'+ _opts.title +'</div>'
					+'<div class="aui-dialog-content">'+ _opts.msg +'</div>'
					+'<div class="aui-dialog-down"></div>'
				+'</div>'
			+'</div>';
			if(document.querySelector(".aui-dialog")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			var ui = {
				main: document.querySelector(".aui-dialog-main"),
				title: document.querySelector(".aui-dialog-title"),
				mask: document.querySelector(".aui-dialog .aui-mask"),
				down: document.querySelector(".aui-dialog-down"),
				btn: document.querySelectorAll(".aui-dialog-down-btn")
			}
			!$.isDefine(_opts.title) && ui.title ? ui.title.parentNode.removeChild(ui.title) : '';
			!$.isDefine(_opts.mask) && ui.mask ? ui.mask.parentNode.removeChild(ui.mask) : '';
			for(var i in _opts.btns)
			{
				//不设置按钮字体样式///_opts.btns = ['按钮1', '按钮2']
				if(Object.prototype.toString.call(_opts.btns[i]) === "[object String]")
				{
					ui.down.insertAdjacentHTML('beforeend', '<span class="aui-dialog-down-btn" index="'+ i +'">'+ _opts.btns[i] +'</span>');
				}
				//设置按钮字体样式///_opts.btns = [{name: '按钮1', color: ''},{name: '按钮2', color: ''}]
				else if(Object.prototype.toString.call(_opts.btns[i]) === "[object Object]")
				{
					ui.down.insertAdjacentHTML('beforeend', '<span class="aui-dialog-down-btn" index="'+ i +'">'+ _opts.btns[i].name +'</span>');
					$.isDefine(_opts.btns[i].color) ? ui.down.children[i].style.color = _opts.btns[i].color : '';
				}
				ui["btn"] = document.querySelectorAll(".aui-dialog-down-btn");
				!(function(j){
					ui.btn[j].addEventListener("click", function(e){
						_this.hide(opt);
						if(!$.isDefine(_opts.input))
						{
							var timer = setTimeout(function() { //延时执行回调函数，等待当前已打开模态窗关闭后再打开新的或执行默写逻辑操作
								clearTimeout(timer);
								typeof callback == "function" ?  callback({index: j}) : '';
							},200);
						}
						else
						{ //promt输入框模态弹窗回调
							var result = [];
							if($.isDefine(_opts.items) && _opts.items.length > 0)
							{
								var list = document.querySelectorAll(".aui-dialog-input-list");
								for(var i = 0; i < _opts.items.length; i++)
								{
									result.push(list[i].children[1].children[0].value);
								}
							}
							var timer = setTimeout(function(){
								clearTimeout(timer);
								typeof callback == "function" ?  callback({index: j, data: result}) : '';
							},200);
						}
					});
				})(i);
			}
			var _timer = setTimeout(function(){
				ui.main.addEventListener("touchmove", function(e){
				    e.preventDefault();
				},{ passive: false });
				ui.mask.addEventListener("click", function(e){
				    !_opts.touchClose ? e.preventDefault() : _this.hide(opt);
				});
				ui.mask.addEventListener("touchmove", function(e){
				    e.preventDefault();
				},{ passive: false });
				clearTimeout(_timer);
			},200);
			_this.css(opt);
		},
		css(opt){ //样式设置
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				warp: document.querySelector(_opts.warp),
				dialog: document.querySelector(".aui-dialog"),
				mask: document.querySelector(".aui-dialog .aui-mask"),
				main: document.querySelector(".aui-dialog-main"),
				title: document.querySelector(".aui-dialog-title"),
				msg: document.querySelector(".aui-dialog-content"),
				down: document.querySelector(".aui-dialog-down"),
				btn: document.querySelectorAll(".aui-dialog-down-btn")
			}
			switch (Number(_opts.theme)){
				case 1: //大按钮
					ui.main.classList.add('aui-dialog-main-style-1');
					for (var i = 0; i < _opts.btns.length; i++)
					{
						ui.btn[i].style.width = "calc(100% / "+ _opts.btns.length +")";
					}
					$.isDefine(_opts.title) ? ui.msg.style.padding = "16px 20px 20px 20px" : ui.msg.style.padding = "30px 20px 26px 20px";
					break;
				case 2: //小按钮（居右分布）
					ui.main.classList.add('aui-dialog-main-style-2');
					!$.isDefine(_opts.input) && !$.isDefine(_opts.title) ? ui.msg.style.paddingTop = "40px" : '';
					$.isDefine(_opts.title) ? ui.msg.style.padding = "16px 20px 20px 20px" : ui.msg.style.padding = "40px 20px 26px 20px";
					break;
				case 3: //按钮宽度等于父级宽度100%，适用于按钮文字过多情况
					ui.main.classList.add('aui-dialog-main-style-1', 'aui-dialog-main-style-3');
					for (var i = 0; i < _opts.btns.length; i++)
					{
						ui.btn[i].style.width = "100%";
					}
					$.isDefine(_opts.title) ? ui.msg.style.padding = "16px 20px 20px 20px" : ui.msg.style.padding = "30px 20px 26px 20px";
					break;
				case 4: //带背景色按钮
					ui.main.classList.add('aui-dialog-main-style-4');
					_opts.btns.length == 1 ? ui.down.style.padding = "0 40px" : ui.down.style.padding = "0 20px";
					break;
				default:
					break;
			}
			$.isDefine(_opts.style.w) ? ui.main.style.width = _opts.style.w : '';
			$.isDefine(_opts.style.h) ? ui.msg.style.height = parseInt(_opts.style.h) - 50 + "px" : '';
			$.isDefine(_opts.style.bg) ? ui.main.style.background = _opts.style.bg : '';
			$.isDefine(_opts.style.zIndex) ? ui.main.style.zIndex = _opts.style.zIndex : '';
			if($.isDefine(_opts.title) && ui.title)
			{ //设置标题title样式（调用时title已配置情况生效）
				ui.title.style.cssText = "background: "+ _opts.style.title.bg +"; color: "+ _opts.style.title.color +"; line-height: "+ _opts.style.title.lineHeight +"; text-align: "+ _opts.style.title.textAlign +"; font-size: "+ _opts.style.title.fontSize +"; padding: "+ _opts.style.title.padding;
			}
			for (var i = 0; i < _opts.btns.length; i++)
			{
				_opts.btns[i].name == "取消" || _opts.btns[i] == "取消" ? ui.btn[i].className = "aui-dialog-down-btn aui-dialog-down-cancel-btn" : '';
				_opts.btns[i].name == "删除" || _opts.btns[i] == "删除" ? ui.btn[i].className = "aui-dialog-down-btn aui-dialog-down-delete-btn" : '';
				!function(j){
					$.touchDom(ui.btn[j], Number(_opts.theme) == 4 ? "#CDCDCD" : "#EFEFEF");
				}(i);
			}
			$.isDefine(_opts.msg) && _opts.msg.length > 15 ? ui.msg.style.textAlign = "left" : ui.msg.style.textAlign = "center";
		},
		show(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			var ui = {
				dialog: document.querySelector(".aui-dialog"),
				main: document.querySelector(".aui-dialog-main")
			}
			ui.dialog.classList.add('aui-dialog-in');
			ui.dialog.classList.remove('aui-dialog-out');
		},
		hide(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				dialog: document.querySelector(".aui-dialog"),
				main: document.querySelector(".aui-dialog-main")
			}
			ui.dialog.classList.remove('aui-dialog-in');
			ui.dialog.classList.add('aui-dialog-out');
			var timer = setTimeout(function() {
				ui.dialog ? ui.dialog.parentNode.removeChild(ui.dialog) : '';
				clearTimeout(timer);
				typeof callback == "function" ?  callback() : '';
			},200);
		},
		alert(opt, callback){ //alert单按钮模态弹窗
			var _this = this;
			_this.show(opt, callback);
		},
		confirm(opt, callback){ //confirm双按钮模态弹窗
			var _this = this;
			_this.show(opt, callback);
		},
		delete(opt, callback){ //delete删除模态弹窗
			var _this = this;
			_this.show(opt, callback);
		},
		prompt(opt, callback){ //input输入框模态弹窗
			var _this = this;
			var _opts = _this.opts(opt);
			opt["input"] = true;
			_this.show(opt, callback);
			var ui = {
				dialog: document.querySelector(".aui-dialog"),
				main: document.querySelector(".aui-dialog-main"),
				msg: document.querySelector(".aui-dialog-content"),
				btn: document.querySelectorAll(".aui-dialog-down-btn")
			}
			ui.dialog.classList.add('aui-popinput');
			var lists = '';
			if($.isDefine(_opts.items) && _opts.items.length > 0)
			{
				for(var i = 0; i < _opts.items.length; i++)
				{
					!$.isDefine(_opts.items[i].label) ? _opts.items[i].label = "" : "";
					!$.isDefine(_opts.items[i].type) ? _opts.items[i].type = "text" : "";
					!$.isDefine(_opts.items[i].value) ? _opts.items[i].value = "" : "";
					!$.isDefine(_opts.items[i].placeholder) ? _opts.items[i].placeholder = "" : "";
					lists += '<div class="aui-dialog-input-list">'
						+'<label>'+ _opts.items[i].label +'</label>'
						+'<div class="aui-dialog-input-list-input"><input type="'+ _opts.items[i].type +'" value="'+ _opts.items[i].value +'" placeholder="'+ _opts.items[i].placeholder +'" /></div>'
						+'<span class="aui-input-clear"><i></i></span>'
					+'</div>'
				}
				ui.msg.insertAdjacentHTML('beforeend', lists);
			}
			_this.css(opt);
			ui.msg.style.textAlign = "left";
			$.isDefine(_opts.title) ? ui.msg.style.padding = "10px 20px 20px 20px" : ui.msg.style.padding = "15px 20px 30px 20px";
			if($.isDefine(_opts.items) && _opts.items.length > 0)
			{
				var list = document.querySelectorAll(".aui-dialog-input-list");
				for(var i = 0; i < _opts.items.length; i++)
				{
					!(function(j){
						//输入检测
						list[j].children[1].children[0].oninput = function(e){
							var length = this.value.length;
			    			if(length > 0)
			    			{
			    				this.parentNode.parentNode.children[2].children[0].style.opacity = 1;
			    			}
			    			else
			    			{
			    				this.parentNode.parentNode.children[2].children[0].style.opacity = 0;
			    			}
						}
						//清空输入内容
						list[j].children[2].children[0].onclick = function(){
							list[j].children[1].children[0].value = "";
							this.style.opacity = 0;
						}
					})(i);
				}
			}
		}
	}
	$.hideDialog = function(opt){
		dialog.hide(opt);
	};
	$.alert = function(opt, callback){
		dialog.alert(opt, callback);
	};
	$.confirm = function(opt, callback){
		dialog.confirm(opt, callback);
	};
	$.delete = function(opt, callback){
		dialog.delete(opt, callback);
	};
	$.prompt = function(opt, callback){
		dialog.prompt(opt, callback);
	};
})(aui, document, window);

/***  actionSheet操作表弹窗  */
!(function($, document, window, undefined){
	var actionSheet = new Object();
	actionSheet = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				items: [], //--必选参数，菜单列表[{name: "", color: "", fontSize: "", textAlign: ""}]
				cancle: "", //--可选参数，取消按钮
				location: 'bottom', //--可选参数，位置 <1、bottom:位于底部，从底部弹出显示>、<2、middle:位于页面中心位置>
				theme: 1, //--可选参数，主题样式
			}
			return $.extend(opts, opt, true);
		},
		creat(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-actionsheet">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-actionsheet-main">'
					+'<div class="aui-actionsheet-title">'+ _opts.title +'</div>'
					+'<ul class="aui-actionsheet-items"></ul>'
					+'<div class="aui-actionsheet-cancle" index="0">'+ _opts.cancle +'</div>'
				+'</div>'
			+'</div>';
			if(document.querySelector(".aui-actionsheet")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			var ui = {
				main: document.querySelector(".aui-actionsheet-main"),
				title: document.querySelector(".aui-actionsheet-title"),
				mask: document.querySelector(".aui-actionsheet .aui-mask"),
				items: document.querySelector(".aui-actionsheet-items"),
				item: document.querySelectorAll(".aui-actionsheet-item"),
				cancle: document.querySelector(".aui-actionsheet-cancle")
			}
			!$.isDefine(_opts.title) && ui.title ? ui.title.parentNode.removeChild(ui.title) : '';
			!$.isDefine(_opts.mask) && ui.mask ? ui.mask.parentNode.removeChild(ui.mask) : '';
			!$.isDefine(_opts.cancle) && ui.cancle ? ui.cancle.parentNode.removeChild(ui.cancle) : '';
			if($.isDefine(_opts.items))
			{
				for(var i = 0; i < _opts.items.length; i++)
				{
					ui.items.insertAdjacentHTML('beforeend', '<li class="aui-actionsheet-item" index="'+ (Number(i) + 1) +'">'+ _opts.items[i].name +'</li>');
					ui["item"] = document.querySelectorAll(".aui-actionsheet-item");
					!(function(j){
						ui.item[j].addEventListener("click", function(e){
							_this.hide(opt);
							var index = Number(this.getAttribute("index"));
							var timer = setTimeout(function() {
								clearTimeout(timer);
								typeof callback == "function" ?  callback({index: index}) : '';
							},200);
						});
					})(i);
				}
			}
			ui.cancle.addEventListener("click", function(e){
				_this.hide(opt);
				var index = Number(this.getAttribute("index"));
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback({index: index}) : '';
				},200);
			});
			ui.main.addEventListener("touchmove", function(e){
	            e.preventDefault();
	       },{ passive: false });
	       	ui.mask.addEventListener("click", function(e){
	            !_opts.touchClose ? e.preventDefault() : _this.hide(opt);
	       	});
			ui.mask.addEventListener("touchmove", function(e){
	            e.preventDefault()
	       	},{ passive: false });
			_this.css(opt);
		},
		css(opt){ //设置特定样式
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				warp: document.querySelector(_opts.warp),
				actionsheet: document.querySelector(".aui-actionsheet"),
				main: document.querySelector(".aui-actionsheet-main"),
				title: document.querySelector(".aui-actionsheet-title"),
				item: document.querySelectorAll(".aui-actionsheet-item"),
				cancle: document.querySelector(".aui-actionsheet-cancle")
			}
			switch (Number(_opts.theme)){
				case 1:
					ui.actionsheet.classList.add('aui-actionsheet-style-1');
					if(_opts.location == "bottom")
					{ //位于底部，从底部弹出显示
						ui.actionsheet.classList.add('aui-actionsheet-bottom');
					}
					else if(_opts.location == "middle")
					{ //位于页面中心位置
						ui.actionsheet.classList.add('aui-actionsheet-middle');
						ui.main.style.top = (ui.warp.offsetHeight - ui.main.offsetHeight) / 2 + "px";
						if($.isDefine(_opts.cancle))
						{
							ui.item[ui.item.length-1].style.borderBottomLeftRadius = ui.item[ui.item.length-1].style.borderBottomRightRadius = "0px";
						}
					}
					if($.isDefine(_opts.title))
					{
						ui.item[0].style.borderTopLeftRadius = ui.item[0].style.borderTopRightRadius = "0px";
						_opts.title.length >= 15 ? ui.title.style.textAlign = "left" : ui.title.style.textAlign = "center";
					}
					break;
				case 2:
					ui.actionsheet.classList.add('aui-actionsheet-style-2');
					if(_opts.location == "bottom")
					{ //位于底部，从底部弹出显示
						ui.actionsheet.classList.add('aui-actionsheet-bottom');
					}
					else if(_opts.location == "middle")
					{ //位于页面中心位置
						ui.actionsheet.classList.add('aui-actionsheet-middle');
						ui.main.style.top = (ui.warp.offsetHeight - ui.main.offsetHeight) / 2 + "px";
					}
					break;
				default:
					break;
			}
			ui.main.style.left = (ui.warp.offsetWidth - ui.main.offsetWidth) / 2 + "px";
			if($.isDefine(_opts.items))
			{
				for(var i = 0; i < _opts.items.length; i++)
				{
					$.isDefine(_opts.items[i].color) ? ui.item[i].style.color = _opts.items[i].color : "";
					$.isDefine(_opts.items[i].fontSize) ? ui.item[i].style.fontSize = _opts.items[i].fontSize : "";
					$.isDefine(_opts.items[i].textAlign) ? ui.item[i].style.textAlign = _opts.items[i].textAlign : "";
					!(function(j){
						$.touchDom(ui.item[j], "#EFEFEF");
					})(i);
				}
			}
			$.isDefine(_opts.cancle) ? $.touchDom(ui.cancle, "#EFEFEF") : '';
		},
		show(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
		},
		hide(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				actionsheet: document.querySelector(".aui-actionsheet"),
				main: document.querySelector(".aui-actionsheet-main"),
				mask: document.querySelector(".aui-actionsheet .aui-mask"),
			}
			if(_opts.theme == "style-1" && _opts.location == "bottom")
			{
				ui.main.style.animation = "aui-slide-down .2s ease-out forwards";
			}
			else if(_opts.theme == "style-2" && _opts.location == "bottom")
			{
				ui.main.style.animation = "aui-slide-down-screen .2s ease-out forwards";
			}
			else
			{
				ui.main.style.animation = "aui-fade-out .2s ease-out forwards";
			}
			ui.mask.style.animation = "aui-fade-out .2s ease-out forwards";
			var timer = setTimeout(function() {
				ui.actionsheet ? ui.actionsheet.parentNode.removeChild(ui.actionsheet) : '';
				typeof callback == "function" ?  callback() : '';
				clearTimeout(timer);
			},200);
		}
	}
	$.actionSheet = function(opt, callback){
		actionSheet.show(opt, callback);
	};
})(aui, document, window);

/***  Colorpicker颜色选择  */
!(function ($, document, window, undefined) {
	var util = {
		css: function (elem, obj) {
			for (var i in obj) {
				elem.style[i] = obj[i];
			}
		},
		hasClass: function (elem, classN) {
			var className = elem.getAttribute("class");
			return className.indexOf(classN) != -1;
		}
	};

	function Colorpicker(opt) {
		if (this === window) throw `Colorpicker: Can't call a function directly`;
		this.init(opt);
	};

	Colorpicker.prototype = {
		init(opt) {
			let { el, initColor = "rgb(255,0,0)", allMode = ['hex', 'rgb'], color = '' } = opt;
			var elem = document.getElementById(el);

			if (!(elem && elem.nodeType && elem.nodeType === 1)) {
				throw `Colorpicker: not found  ID:${el}  HTMLElement,not ${{}.toString.call(el)}`;
			}

			this.Opt = {
				...opt,
				el,
				initColor,
				allMode,
				color
			}

			this.bindElem = elem; // 绑定的元素
			this.elem_wrap = null; // 最外层容器
			this.fixedBg = null; // 拾色器后面固定定位的透明div 用于点击隐藏拾色器
			this.elem_colorPancel = null; // 色彩面板
			this.elem_picker = null; // 拾色器色块按钮
			this.elem_barPicker1 = null; // 颜色条
			this.elem_hexInput = null; // 显示hex的表单
			this.elem_showColor = null; // 显示当前颜色
			this.elem_showModeBtn = null; // 切换输入框模式按钮
			this.elem_inputWrap = null; // 输入框外层容器

			this.pancelLeft = 0;
			this.pancelTop = 0;

			this.downX = 0;
			this.downY = 0;
			this.moveX = 0;
			this.moveY = 0;

			this.pointLeft = 0;
			this.pointTop = 0;

			this.current_mode = 'hex'; // input框当前的模式

			this.rgba = { r: 0, g: 0, b: 0, a: 1 };
			this.hsb = { h: 0, s: 100, b: 100 };


			var _this = this, rgb = initColor.slice(4, -1).split(",");

			this.rgba.r = parseInt(rgb[0]);
			this.rgba.g = parseInt(rgb[1]);
			this.rgba.b = parseInt(rgb[2]);

			var body = document.getElementsByTagName("body")[0],
				div = document.createElement("div");

			div.innerHTML = this.render();
			body.appendChild(div);

			this.elem_wrap = div;
			this.fixedBg = div.children[0];
			this.elem_colorPancel = div.getElementsByClassName("color-pancel")[0];
			this.pancel_width = this.elem_colorPancel.offsetWidth;
			this.pancel_height = this.elem_colorPancel.offsetHeight;
			this.elem_picker = div.getElementsByClassName("pickerBtn")[0];
			this.elem_colorPalette = div.getElementsByClassName("color-palette")[0];
			this.elem_showColor = div.getElementsByClassName("colorpicker-showColor")[0];
			this.elem_barPicker1 = div.getElementsByClassName("colorBar-color-picker")[0];
			/*   this.elem_barPicker2 = div.getElementsByClassName("colorBar-opacity-picker")[0]; */
			this.elem_hexInput = div.getElementsByClassName("colorpicker-hexInput")[0];
			this.elem_showModeBtn = div.getElementsByClassName("colorpicker-showModeBtn")[0];
			this.elem_inputWrap = div.getElementsByClassName("colorpicker-inputWrap")[0];
			/*  this.elem_opacityPancel = this.elem_barPicker2.parentNode.parentNode.children[1]; */

			// var rect = this.bindElem.getBoundingClientRect();
			var elem = this.bindElem;
			var top = elem.offsetTop;
			var left = elem.offsetLeft;
			while (elem.offsetParent) {
				top += elem.offsetParent.offsetTop;
				left += elem.offsetParent.offsetLeft;
				elem = elem.offsetParent;
			}

			this.pancelLeft = left + this.elem_colorPalette.clientWidth;
			this.pancelTop = top + this.bindElem.offsetHeight;
			util.css(div, {
				"position": "absolute",
				"z-index": 2,
				"display": 'none',
				"left": left + "px",
				"top": top + this.bindElem.offsetHeight + "px"
			});

			this.bindMove(this.elem_colorPancel, this.setPosition, true);
			this.bindMove(this.elem_barPicker1.parentNode, this.setBar, false);
			/*  this.bindMove(this.elem_barPicker2.parentNode,this.setBar,false); */

			this.bindElem.addEventListener("click", function () {
				_this.show();
			}, false);

			this.fixedBg.addEventListener("click", function (e) {
				_this.hide();
			}, false)

			this.elem_showModeBtn.addEventListener("click", function () {
				_this.switch_current_mode();
			}, false)

			this.elem_wrap.addEventListener("input", function (e) {
				var target = e.target, value = target.value;
				_this.setColorByInput(value);
			}, false);

			this.elem_colorPalette.addEventListener("click", function (e) {
				if (e.target.tagName.toLocaleLowerCase() == "p") {
					let colorStr = e.target.style.background;
					let rgb = colorStr.slice(4, -1).split(",");
					let rgba = {
						r: parseInt(rgb[0]),
						g: parseInt(rgb[1]),
						b: parseInt(rgb[2])
					}
					switch (_this.current_mode) {
						case "hex":
							_this.setColorByInput("#" + _this.rgbToHex(rgba))
							break;
						case 'rgb':
							let inputs = _this.elem_wrap.getElementsByTagName("input")
							inputs[0].value = rgba.r;
							inputs[1].value = rgba.g;
							inputs[2].value = rgba.b;
							_this.setColorByInput(colorStr)
							/* 	_this.hsb = _this.rgbToHsb(rgba); */
							break;
					}

				}
			}, false);

			(color != '' && this.setColorByInput(color));
		},
		render: function () {
			var tpl =
				`<div style="position: fixed; top: 0px; right: 0px; bottom: 0px; left: 0px;"></div>
				<div style="position: inherit;z-index: 100;display: flex;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 2px, rgba(0, 0, 0, 0.3) 0px 4px 8px;">
					<div style='width:180px;padding:10px;background: #f9f9f9;display: flex;flex-flow: row wrap;align-content: space-around;justify-content: space-around;' class='color-palette'>
						${this.getPaletteColorsItem()}
					</div>
					<div class="colorpicker-pancel" style="background: rgb(255, 255, 255);box-sizing: initial; width: 225px; font-family: Menlo;">
						<div style="width: 100%; padding-bottom: 55%; position: relative; border-radius: 2px 2px 0px 0px; overflow: hidden;">
							<div class="color-pancel" style="position: absolute; top: 0px; right: 0px; bottom: 0px; left: 0px; background: rgb(${this.rgba.r},${this.rgba.g},${this.rgba.b})">
								<style>
									.saturation-white {background: -webkit-linear-gradient(to right, #fff, rgba(255,255,255,0));background: linear-gradient(to right, #fff, rgba(255,255,255,0));}
									.saturation-black {background: -webkit-linear-gradient(to top, #000, rgba(0,0,0,0));background: linear-gradient(to top, #000, rgba(0,0,0,0));}
								</style>
								<div class="saturation-white" style="position: absolute; top: 0px; right: 0px; bottom: 0px; left: 0px;">
									<div class="saturation-black" style="position: absolute; top: 0px; right: 0px; bottom: 0px; left: 0px;">
									</div>
									<div class="pickerBtn" style="position: absolute; top: 0%; left: 100%; cursor: default;">
										<div style="width: 12px; height: 12px; border-radius: 6px; box-shadow: rgb(255, 255, 255) 0px 0px 0px 1px inset; transform: translate(-6px, -6px);">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div style="padding: 0 16px 20px;">
							<div class="flexbox-fix" style="display: flex;align-items: center;height: 40px;">
								<div style="width: 32px;">
									<div style="width: 16px; height: 16px; border-radius: 8px; position: relative; overflow: hidden;">
										<div class="colorpicker-showColor" style="position: absolute; top: 0px; right: 0px; bottom: 0px; left: 0px; border-radius: 8px; box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 0px 1px inset; background:rgb(${this.rgba.r},${this.rgba.g},${this.rgba.b}); z-index: 2;"></div>
										<div class="" style="position: absolute; top: 0px; right: 0px; bottom: 0px; left: 0px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAMUlEQVQ4T2NkYGAQYcAP3uCTZhw1gGGYhAGBZIA/nYDCgBDAm9BGDWAAJyRCgLaBCAAgXwixzAS0pgAAAABJRU5ErkJggg==&quot;) left center;"></div>
									</div>
								</div>
								<div style="-webkit-box-flex: 1; flex: 1 1 0%;"><div style="height: 10px; position: relative;">
									<div style="position: absolute; top: 0px;right: 0px; bottom: 0px; left: 0px;">
										<div class="hue-horizontal" style="padding: 0px 2px; position: relative; height: 100%;">
											<style>
												.hue-horizontal {background: linear-gradient(to right, #f00 0%, #ff0 17%, #0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);background: -webkit-linear-gradient(to right, #f00 0%, #ff0 17%, #0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);}
												.hue-vertical {background: linear-gradient(to top, #f00 0%, #ff0 17%, #0f0 33%,#0ff 50%, #00f 67%, #f0f 83%, #f00 100%);background: -webkit-linear-gradient(to top, #f00 0%, #ff0 17%,#0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);}
											</style>
											<div  class="colorBar-color-picker" style="position: absolute; left: 0%;">
												<div style="width: 12px; height: 12px; border-radius: 6px; transform: translate(-6px, -1px); background-color: rgb(248, 248, 248); box-shadow: rgba(0, 0, 0, 0.37) 0px 1px 4px 0px;">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="flexbox-fix" style="display: flex;">
							<div class="flexbox-fix colorpicker-inputWrap" style="-webkit-box-flex: 1; flex: 1 1 0%; display: flex; margin-left: -6px;">
									${this.getInputTpl()}
							</div>
							<div class="colorpicker-showModeBtn" style="width: 32px; text-align: right; position: relative;">
								<div style="margin-right: -4px;  cursor: pointer; position: relative;">
									<svg viewBox="0 0 24 24" style="width: 24px; height: 24px; border: 1px solid transparent; border-radius: 5px;"><path fill="#333" d="M12,5.83L15.17,9L16.58,7.59L12,3L7.41,7.59L8.83,9L12,5.83Z"></path><path fill="#333" d="M12,18.17L8.83,15L7.42,16.41L12,21L16.59,16.41L15.17,15Z"></path></svg>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>`;
			return tpl;
		},
		getInputTpl: function () {
			var current_mode_html = "";
			switch (this.current_mode) {
				case 'hex':
					var hex = "#" + this.rgbToHex(this.HSBToRGB(this.hsb));
					current_mode_html += `
							<div style="padding-left: 6px; width: 100%;">
								<div style="position: relative;">
									<input class="colorpicker-hexInput" value="${hex}" spellcheck="false" style="font-size: 11px; color: rgb(51, 51, 51); width: 100%; border-radius: 2px; border: none; box-shadow: rgb(218, 218, 218) 0px 0px 0px 1px inset; height: 21px; text-align: center;">
									<span style="text-transform: uppercase; font-size: 11px; line-height: 11px; color: rgb(150, 150, 150); text-align: center; display: block; margin-top: 12px;">hex</span>
								</div>
							</div>`;
					break;
				case 'rgb':
					for (var i = 0; i < 3; i++) {
						current_mode_html +=
							`<div style="padding-left: 6px; width: 100%;">
								<div style="position: relative;">
									<input class="colorpicker-hexInput" value="${this.rgba['rgb'[i]]}" spellcheck="false" style="font-size: 11px; color: rgb(51, 51, 51); width: 100%; border-radius: 2px; border: none; box-shadow: rgb(218, 218, 218) 0px 0px 0px 1px inset; height: 21px; text-align: center;">
									<span style="text-transform: uppercase; font-size: 11px; line-height: 11px; color: rgb(150, 150, 150); text-align: center; display: block; margin-top: 12px;">${'rgb'[i]}</span>
								</div>
							</div>`;
					}
				default:
			}
			return current_mode_html;
		},
		getPaletteColorsItem: function () {
			let str = '';
			let palette = ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", "rgb(204, 204, 204)", "rgb(217, 217, 217)", "rgb(255, 255, 255)",
				"rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)", "rgb(0, 255, 255)",
				"rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)", "rgb(230, 184, 175)", "rgb(244, 204, 204)",
				"rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)",
				"rgb(217, 210, 233)", "rgb(234, 209, 220)", "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)",
				"rgb(182, 215, 168)", "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)"]
			palette.forEach(item => str += `<p style='width:20px;height:20px;background:${item};margin:0 5px;border: solid 1px #d0d0d0;'></p>`)
			return str;
		},
		setPosition(x, y) {
			var LEFT = parseInt(x - this.pancelLeft),
				TOP = parseInt(y - this.pancelTop);

			this.pointLeft = Math.max(0, Math.min(LEFT, this.pancel_width));
			this.pointTop = Math.max(0, Math.min(TOP, this.pancel_height));

			util.css(this.elem_picker, {
				left: this.pointLeft + "px",
				top: this.pointTop + "px"
			})
			this.hsb.s = parseInt(100 * this.pointLeft / this.pancel_width);
			this.hsb.b = parseInt(100 * (this.pancel_height - this.pointTop) / this.pancel_height);

			this.setShowColor();
			this.setValue(this.rgba);

		},
		setBar: function (elem, x) {
			var elem_bar = elem.getElementsByTagName("div")[0],
				rect = elem.getBoundingClientRect(),
				elem_width = elem.offsetWidth,
				X = Math.max(0, Math.min(x - rect.x, elem_width));

			if (elem_bar === this.elem_barPicker1) {
				util.css(elem_bar, {
					left: X + "px"
				});
				this.hsb.h = parseInt(360 * X / elem_width);
			} else {
				util.css(elem_bar, {
					left: X + "px"
				});
				this.rgba.a = X / elem_width;
			}

			this.setPancelColor(this.hsb.h);
			this.setShowColor();
			this.setValue(this.rgba);

		},
		setPancelColor: function (h) {
			var rgb = this.HSBToRGB({ h: h, s: 100, b: 100 });

			util.css(this.elem_colorPancel, {
				background: 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + this.rgba.a + ')'
			});
		},
		setShowColor: function () {
			var rgb = this.HSBToRGB(this.hsb);

			this.rgba.r = rgb.r;
			this.rgba.g = rgb.g;
			this.rgba.b = rgb.b;

			util.css(this.elem_showColor, {
				background: 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + this.rgba.a + ')'
			});
		},
		setValue: function (rgb) {
			var hex = "#" + this.rgbToHex(rgb);
			this.elem_inputWrap.innerHTML = this.getInputTpl();
			this.Opt.change(this.bindElem, hex);
		},
		setColorByInput: function (value) {
			var _this = this;
			switch (this.current_mode) {
				case "hex":
					value = value.slice(1);
					if (value.length == 3) {
						value = '#' + value[0] + value[0] + value[1] + value[1] + value[2] + value[2];
						this.hsb = this.hexToHsb(value);
					} else if (value.length == 6) {
						this.hsb = this.hexToHsb(value);
					}
					break;
				case 'rgb':
					var inputs = this.elem_wrap.getElementsByTagName("input"),
						rgb = {
							r: inputs[0].value ? parseInt(inputs[0].value) : 0,
							g: inputs[1].value ? parseInt(inputs[1].value) : 0,
							b: inputs[2].value ? parseInt(inputs[2].value) : 0
						};

					this.hsb = this.rgbToHsb(rgb);
			}
			this.changeViewByHsb();
		},
		changeViewByHsb: function () {
			this.pointLeft = parseInt(this.hsb.s * this.pancel_width / 100);
			this.pointTop = parseInt((100 - this.hsb.b) * this.pancel_height / 100);
			util.css(this.elem_picker, {
				left: this.pointLeft + "px",
				top: this.pointTop + "px"
			});

			this.setPancelColor(this.hsb.h);
			this.setShowColor();
			util.css(this.elem_barPicker1, {
				left: this.hsb.h / 360 * (this.elem_barPicker1.parentNode.offsetWidth) + "px"
			});

			var hex = '#' + this.rgbToHex(this.HSBToRGB(this.hsb));
			this.Opt.change(this.bindElem, hex);
		},
		switch_current_mode: function () {
			this.current_mode = this.current_mode == 'hex' ? 'rgb' : 'hex';
			this.elem_inputWrap.innerHTML = this.getInputTpl();
		},
		bindMove: function (elem, fn, bool) {
			var _this = this;

			elem.addEventListener("mousedown", function (e) {
				_this.downX = e.pageX;
				_this.downY = e.pageY;
				bool ? fn.call(_this, _this.downX, _this.downY) : fn.call(_this, elem, _this.downX, _this.downY);

				document.addEventListener("mousemove", mousemove, false);
				function mousemove(e) {
					_this.moveX = e.pageX;
					_this.moveY = e.pageY;
					bool ? fn.call(_this, _this.moveX, _this.moveY) : fn.call(_this, elem, _this.moveX, _this.moveY);
					e.preventDefault();
				}
				document.addEventListener("mouseup", mouseup, false);
				function mouseup(e) {

					document.removeEventListener("mousemove", mousemove, false)
					document.removeEventListener("mouseup", mouseup, false)
				}
			}, false);
		},
		show: function () {
			util.css(this.elem_wrap, {
				"display": "block"
			})
		},
		hide: function () {
			util.css(this.elem_wrap, {
				"display": "none"
			})
		},
		HSBToRGB: function (hsb) {
			var rgb = {};
			var h = Math.round(hsb.h);
			var s = Math.round(hsb.s * 255 / 100);
			var v = Math.round(hsb.b * 255 / 100);

			if (s == 0) {
				rgb.r = rgb.g = rgb.b = v;
			} else {
				var t1 = v;
				var t2 = (255 - s) * v / 255;
				var t3 = (t1 - t2) * (h % 60) / 60;

				if (h == 360) h = 0;

				if (h < 60) { rgb.r = t1; rgb.b = t2; rgb.g = t2 + t3 }
				else if (h < 120) { rgb.g = t1; rgb.b = t2; rgb.r = t1 - t3 }
				else if (h < 180) { rgb.g = t1; rgb.r = t2; rgb.b = t2 + t3 }
				else if (h < 240) { rgb.b = t1; rgb.r = t2; rgb.g = t1 - t3 }
				else if (h < 300) { rgb.b = t1; rgb.g = t2; rgb.r = t2 + t3 }
				else if (h < 360) { rgb.r = t1; rgb.g = t2; rgb.b = t1 - t3 }
				else { rgb.r = 0; rgb.g = 0; rgb.b = 0 }
			}

			return { r: Math.round(rgb.r), g: Math.round(rgb.g), b: Math.round(rgb.b) };
		},
		rgbToHex: function (rgb) {
			var hex = [
				rgb.r.toString(16),
				rgb.g.toString(16),
				rgb.b.toString(16)
			];
			hex.map(function (str, i) {
				if (str.length == 1) {
					hex[i] = '0' + str;
				}
			});

			return hex.join('');
		},
		hexToRgb: function (hex) {
			var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
			return { r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF) };
		},
		hexToHsb: function (hex) {
			return this.rgbToHsb(this.hexToRgb(hex));
		},
		rgbToHsb: function (rgb) {
			var hsb = { h: 0, s: 0, b: 0 };
			var min = Math.min(rgb.r, rgb.g, rgb.b);
			var max = Math.max(rgb.r, rgb.g, rgb.b);
			var delta = max - min;
			hsb.b = max;
			hsb.s = max != 0 ? 255 * delta / max : 0;
			if (hsb.s != 0) {
				if (rgb.r == max) hsb.h = (rgb.g - rgb.b) / delta;
				else if (rgb.g == max) hsb.h = 2 + (rgb.b - rgb.r) / delta;
				else hsb.h = 4 + (rgb.r - rgb.g) / delta;
			} else hsb.h = -1;
			hsb.h *= 60;
			if (hsb.h < 0) hsb.h += 360;
			hsb.s *= 100 / 255;
			hsb.b *= 100 / 255;
			return hsb;
		}
	}

	Colorpicker.create = function (opt) {
		return new Colorpicker(opt)
	}

	$.Colorpicker = Colorpicker;
})(aui, document, window);

!(function($, document, window, undefined){
	var actionMenu = new Object();
	actionMenu = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				items: [], //--必选参数，菜单列表[{name: "", icon: "", iconColor: "", img: ""}]
				html:'',
				cancel: "", //--可选参数，取消按钮
				location: 'bottom', //--可选参数，位置 <1、bottom:位于底部，从底部弹出显示>、<2、middle:位于页面中心位置>
				theme: 1, //--可选参数，主题样式
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-actionmenu">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-actionmenu-main">'
				+'<div class="aui-actionmenu-title">'+ _opts.title +'</div>'
				+'<ul class="aui-actionmenu-items"></ul>'
				+'<div class="aui-actionmenu-cancle" index="0">'+ _opts.cancle +'</div>'
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-actionmenu")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			var ui = {
				main: document.querySelector(".aui-actionmenu-main"),
				title: document.querySelector(".aui-actionmenu-title"),
				mask: document.querySelector(".aui-mask"),
				items: document.querySelector(".aui-actionmenu-items"),
				item: document.querySelectorAll(".aui-actionmenu-item"),
				cancel: document.querySelector(".aui-actionmenu-cancle")
			}
			!$.isDefine(_opts.title) && ui.title ? ui.title.parentNode.removeChild(ui.title) : '';
			!$.isDefine(_opts.mask) && ui.mask ? ui.mask.parentNode.removeChild(ui.mask) : '';
			!$.isDefine(_opts.cancle) && ui.cancel ? ui.cancel.parentNode.removeChild(ui.cancel) : '';
			if($.isDefine(_opts.items))
			{
				for(var i = 0; i < _opts.items.length; i++)
				{
					if($.isDefine(_opts.items[i].img)){
						ui.items.insertAdjacentHTML('beforeend', '<li class="aui-actionmenu-item" index="'+ (Number(i) + 1) +'"><img src="'+ _opts.items[i].img +'" /><p>'+ _opts.items[i].name +'</p></li>');
					}
					else{
						if($.isDefine(_opts.items[i].icon)){
							ui.items.insertAdjacentHTML('beforeend', '<li class="aui-actionmenu-item" index="'+ (Number(i) + 1) +'"><i class="'+ _opts.items[i].icon +'" /></i><p>'+ _opts.items[i].name +'</p></li>');
						}
						else{
							ui.items.insertAdjacentHTML('beforeend', '<li class="aui-actionmenu-item no-icon" index="'+ (Number(i) + 1) +'"><p>'+ _opts.items[i].name +'</p></li>');
						}
					}
					ui["item"] = document.querySelectorAll(".aui-actionmenu-item");
					!(function(j){
						$.touchDom(ui.item[j], "#EFEFEF");
						ui.item[j].addEventListener("click", function(e){
							_this.hide(opt);
							var index = Number(this.getAttribute("index"));
							var timer = setTimeout(function() {
								clearTimeout(timer);
								typeof callback == "function" ?  callback({index: index}) : '';
							},200);
						});
					})(i);
				}
			}
			$.touchDom(ui.cancel, "#EFEFEF");
			ui.cancel.addEventListener("click", function(e){
				_this.hide(opt);
				var index = Number(this.getAttribute("index"));
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback({index: index}) : '';
				},200);
			});
			ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.hide(opt);
			});
			ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
			_this.css(opt);
		},
		css: function(opt){ //设置特定样式
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				warp: document.querySelector(_opts.warp),
				actionmenu: document.querySelector(".aui-actionmenu"),
				main: document.querySelector(".aui-actionmenu-main"),
				title: document.querySelector(".aui-actionmenu-title"),
				item: document.querySelectorAll(".aui-actionmenu-item"),
			}
			switch (_opts.theme){
				case 1:
					ui.actionmenu.classList.add('aui-actionmenu-style-1');
					if(_opts.location == "bottom")
					{ //位于底部，从底部弹出显示
						ui.actionmenu.classList.add('aui-actionmenu-bottom');
					}
					else if(_opts.location == "middle")
					{ //位于页面中心位置
						ui.actionmenu.classList.add('aui-actionmenu-middle');
						ui.main.style.top = (ui.main.parentNode.offsetHeight - ui.main.offsetHeight) / 2 + "px";
						if($.isDefine(_opts.cancle))
						{
							ui.item[ui.item.length-1].style.borderBottomLeftRadius = ui.item[ui.item.length-1].style.borderBottomRightRadius = "0px";
						}
					}
					if($.isDefine(_opts.title))
					{
						ui.item[0].style.borderTopLeftRadius = ui.item[0].style.borderTopRightRadius = "0px";
						_opts.title.length >= 15 ? ui.title.style.textAlign = "left" : ui.title.style.textAlign = "center";
					}
					break;
				case 2:
					ui.actionmenu.classList.add('aui-actionmenu-style-2');
					if(_opts.location == "bottom")
					{ //位于底部，从底部弹出显示
						ui.actionmenu.classList.add('aui-actionmenu-bottom');
					}
					else if(_opts.location == "middle")
					{ //位于页面中心位置
						ui.actionmenu.classList.add('aui-actionmenu-middle');
						ui.main.style.top = (ui.main.parentNode.offsetHeight - ui.main.offsetHeight) / 2 + "px";
					}
					break;
				default:
					break;
			}
			ui.main.style.left = (ui.warp.offsetWidth - ui.main.offsetWidth) / 2 + "px";
			if($.isDefine(_opts.items))
			{
				for(var i = 0; i < _opts.items.length; i++)
				{
					$.isDefine(_opts.items[i].color) ? ui.item[i].style.color = _opts.items[i].color : "";
					$.isDefine(_opts.items[i].fontSize) ? ui.item[i].style.fontSize = _opts.items[i].fontSize : "";
					$.isDefine(_opts.items[i].textAlign) ? ui.item[i].style.textAlign = _opts.items[i].textAlign : "";
					$.isDefine(_opts.items[i].iconColor) ? ui.item[i].querySelector('i').style.color = _opts.items[i].iconColor : "";
				}
			}
		},
		show: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
		},
		hide: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				actionmenu: document.querySelector(".aui-actionmenu"),
				main: document.querySelector(".aui-actionmenu-main"),
				mask: document.querySelector(".aui-mask"),
			}
			if(_opts.theme == 1 && _opts.location == "bottom")
			{
				ui.main.style.animation = "aui-slide-down .2s ease-out forwards";
			}
			else if(_opts.theme == 2 && _opts.location == "bottom")
			{
				ui.main.style.animation = "aui-slide-down-screen .2s ease-out forwards";
			}
			else
			{
				ui.main.style.animation = "aui-fade-out .2s ease-out forwards";
			}
			ui.mask ? ui.mask.style.animation = "aui-fade-out .2s ease-out forwards" : '';
			var timer = setTimeout(function() {
				ui.actionmenu ? ui.actionmenu.parentNode.removeChild(ui.actionmenu) : '';
				typeof callback == "function" ?  callback() : '';
				clearTimeout(timer);
			},200);
		}
	}
	$.actionMenu = function(opt, callback){
		actionMenu.show(opt, callback);
	};
})(aui, document, window);

/* chatbox聊天页面底部UI插件 */
!(function($, document, window, undefined){
	$.chatbox = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: false, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				autoFocus: false, //--可选参数,是否自动获取焦点, 默认false
				events: [], //--可选参数, 配置监听事件(录音，选择附加功能...等事件监听)
				textareaMinHeight: 40, //输入框最小高度
				textareaMaxHeight: 100, //输入框最大高度
				record: { //录音功能配置
					use: true, //是否开启录音功能
					MIN_SOUND_TIME: 800, //录音最短时间限制
				},
				emotion: { //表情功能配置
					use: true, //是否开启表情功能
					path: '', //.json文件路径
					pageHasNum: 27, //一页显示按钮数量(7 * 4 - 1)
				},
				extras: { //附加功能配置
					use: true, //是否开启附加功能
					pageHasNum: 8, //一页显示按钮数量(4 * 2)
					btns: [
						/* {title: '', icon: '', img: ''} */
					],
				},
			}
			return $.extend(opts, opt, true);
		},
		//创建
		creat(){
			var _this = this;
			var _opts = _this.data;
			return new Promise(function(resolve, reject){
				var _html = '<div class="aui-chatbox">'
					+'<div class="aui-mask"></div>'
					+'<div class="aui-chatbox-main row-before">'
					+'<div class="aui-chatbox-main-warp row-after">'
					+'<div class="aui-chatbox-left">'
					+'<div class="aui-chatbox-btn aui-chatbox-record-btn active"><i class="iconfont iconyuyin"></i></div>'
					+'<div class="aui-chatbox-btn aui-chatbox-keypad-btn"><i class="iconfont iconjianpan"></i></div>'
					+'</div>'
					+'<div class="aui-chatbox-center">'
					+'<div class="aui-chatbox-center-box aui-chatbox-center-textarea-box active">'
					+'<textarea class="aui-chatbox-textarea" placeholder="" value=""></textarea>'
					+'</div>'
					+'<div class="aui-chatbox-center-box aui-chatbox-record-start "><span>按住  说话</span></div>'
					+'</div>'
					+'<div class="aui-chatbox-right">'
					+'<div class="aui-chatbox-btn aui-chatbox-emotion-btn active"><i class="iconfont iconbiaoqing1"></i></div>'
					+'<div class="aui-chatbox-btn aui-chatbox-keypad-btn"><i class="iconfont iconjianpan"></i></div>'
					+'<div class="aui-chatbox-btn aui-chatbox-extras-btn active"><i class="iconfont iconicon-"></i></div>'
					+'<div class="aui-chatbox-btn aui-chatbox-submit-btn "><span>发送</span></div>'
					+'</div>'
					+'</div>'
					+'</div>'
					+'</div>';
				if(document.querySelector(".aui-chatbox")) return;
				document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
				_this['ui'] = {
					body: document.querySelector("body"),
					chatbox: document.querySelector(".aui-chatbox"),
					main: document.querySelector(".aui-chatbox-main"),
					mian_warp: document.querySelector('.aui-chatbox-main-warp'),
					mask: document.querySelector(".aui-mask"),
					left: document.querySelector(".aui-chatbox-left"),
					left_record_btn: document.querySelector(".aui-chatbox-left .aui-chatbox-record-btn"),
					left_keypad_btn: document.querySelector(".aui-chatbox-left .aui-chatbox-keypad-btn"),
					center: document.querySelector(".aui-chatbox-center"),
					center_textarea_box: document.querySelector(".aui-chatbox-center-textarea-box"),
					center_textarea: document.querySelector(".aui-chatbox-textarea"),
					center_record_start: document.querySelector(".aui-chatbox-record-start"),
					center_record_end: document.querySelector(".aui-chatbox-record-end"),
					right: document.querySelector(".aui-chatbox-right"),
					right_emotion_btn: document.querySelector(".aui-chatbox-emotion-btn"),
					right_keypad_btn: document.querySelector(".aui-chatbox-right .aui-chatbox-keypad-btn"),
					right_extras_btn: document.querySelector(".aui-chatbox-right .aui-chatbox-extras-btn"),
					right_submit_btn: document.querySelector(".aui-chatbox-right .aui-chatbox-submit-btn"),
				}
				resolve();
			});
		},
		//初始化
		init(opt, callback){
			var _this = this;
			_this['data'] = _this.opts(opt);
			_this.creat().then(function(){
				!$.isDefine(_this.data.mask) && _this.ui.mask ? _this.ui.mask.classList.remove("active") : _this.ui.mask.classList.add("active");
				$.autoTextarea(_this.ui.center_textarea, _this.data.textareaMaxHeight, _this.data.textareaMinHeight); //跟随输入改变输入框高度
				_this.data.autoFocus ? _this.msgTextFocus() : '';
				typeof callback == 'function' ? callback() : '';
				_this.data.events.indexOf('setStyle') < 0 ? _this.setStyle() : '';
				_this.data.events.indexOf('changeInput') < 0 ? _this.changeInput() : ''; //输入检测
				_this.data.events.indexOf('changeFocus') < 0 ? _this.changeFocus() : ''; //输入框获取焦点检测
				_this.data.events.indexOf('forbidKeypadClose') < 0 ? _this.forbidKeypadClose() : ''; //解决长按“发送”按钮，导致键盘关闭的问题；
				//———————— 录音 ————————————
				_this.data.events.indexOf('showRecord') < 0 && _this.data.record.use ? _this.showRecord() : ''; //点击左侧语音按钮显示录音区域
				_this.data.events.indexOf('hideRecord') < 0 && _this.data.record.use ? _this.hideRecord() : ''; //点击左侧键盘按钮隐藏录音区域
				_this.data.events.indexOf('createRecord') < 0 && _this.data.record.use ? _this.createRecord() : ''; // 创建录音弹窗
				_this.data.events.indexOf('recordStart') < 0 && _this.data.record.use ? _this.recordStart() : ''; //开始录音(手指按下 ' 按住 说话 ' 按钮)
				_this.data.events.indexOf('recordCancel') < 0 && _this.data.record.use ? _this.recordCancel() : ''; //取消录音(手指滑动 ' 松开 结束 ' 按钮)
				_this.data.events.indexOf('recordEnd') < 0 && _this.data.record.use ? _this.recordEnd() : ''; //结束录音(手指离开 ' 松开 结束 ' 按钮)
				//———————— 表情 ————————————
				_this.data.events.indexOf('showEmotion') < 0 && _this.data.emotion.use ? _this.showEmotion() : ''; //点击右侧表情按钮显示表情选择区域
				_this.data.events.indexOf('hideEmotion') < 0 && _this.data.emotion.use ? _this.hideEmotion() : ''; //点击右侧表情按钮隐藏表情选择区域
				_this.createEmotion();
				_this.data.events.indexOf('chooseEmotionItem') < 0 && _this.data.extras.use ? _this.chooseEmotionItem() : ''; //点击选择附加功能
				//———————— 附加功能 ————————
				_this.data.events.indexOf('showExtras') < 0 && _this.data.extras.use ? _this.showExtras() : ''; //点击右侧附加功能按钮显示附加功能选择区域
				_this.data.events.indexOf('hideExtras') < 0 && _this.data.extras.use && _this.ui.extras_container ? _this.hideExtras() : ''; //隐藏附加功能选择区域
				_this.data.events.indexOf('createExtras') < 0 && _this.data.extras.use ? _this.createExtras() : ''; //创建附加功能选择区域
				_this.data.events.indexOf('chooseExtrasItem') < 0 && _this.data.extras.use ? _this.chooseExtrasItem() : ''; //点击选择附加功能
				//———————— 发送 ———————————
				_this.data.events.indexOf('submit') < 0 ? _this.submit() : ''; //发送
			});
		},
		//设置样式
		setStyle(){
			var _this = this;
			with (_this.ui){
				if(!_this.data.record.use)
				{ //不使用——录音功能
					left.style.display = 'none';
					if(!_this.data.emotion.use)
					{ //不使用——表情功能
						right_emotion_btn.classList.remove('active');
						if(!_this.data.extras.use)
						{ //不使用——附加功能
							right_extras_btn.classList.remove('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 75px';
							center.style.cssText += 'width: calc(100% - 75px - 10px);';
						}
						else
						{ //使用——附加功能
							right_extras_btn.classList.add('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 115px';
							center.style.cssText += 'width: calc(100% - 115px - 10px);';
						}
					}
					else
					{ //使用——表情功能
						right_emotion_btn.classList.add('active');
						if(!_this.data.extras.use)
						{ //不使用——附加功能
							right_extras_btn.classList.remove('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 115px';
							center.style.cssText += 'width: calc(100% - 115px - 10px);';
						}
						else
						{ //使用——附加功能
							if($.isDefine(center_textarea.value))
							{
								right_extras_btn.classList.remove('active');
								right_submit_btn.classList.add('active');
								right.style.cssText += 'width: 115px';
								center.style.cssText += 'width: calc(100% - 115px - 10px);';
							}
							else
							{
								right_extras_btn.classList.add('active');
								right_submit_btn.classList.remove('active');
								right.style.cssText += 'width: 90px';
								center.style.cssText += 'width: calc(100% - 90px - 10px);';
							}
						}
					}
				}
				else
				{ //使用——录音功能
					left.style.display = 'inline-block';
					if(!_this.data.emotion.use)
					{ //不使用——表情功能
						right_emotion_btn.classList.remove('active');
						if(!_this.data.extras.use)
						{ //不使用——附加功能
							right_extras_btn.classList.remove('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 75px;';
							center.style.cssText += 'width: calc(100% - 75px - 50px);';
						}
						else
						{ //使用——附加功能
							right_extras_btn.classList.add('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 115px';
							center.style.cssText += 'width: calc(100% - 115px - 50px);';
						}
					}
					else
					{ //使用——表情功能
						right_emotion_btn.classList.add('active');
						if(!_this.data.extras.use)
						{ //不使用——附加功能
							right_extras_btn.classList.remove('active');
							right_submit_btn.classList.add('active');
							right.style.cssText += 'width: 115px';
							center.style.cssText += 'width: calc(100% - 115px - 50px);';
						}
						else
						{ //使用——附加功能
							if($.isDefine(center_textarea.value))
							{
								right_extras_btn.classList.remove('active');
								right_submit_btn.classList.add('active');
								right.style.cssText += 'width: 115px';
								center.style.cssText += 'width: calc(100% - 115px - 50px);';
							}
							else
							{
								right_extras_btn.classList.add('active');
								right_submit_btn.classList.remove('active');
								right.style.cssText += 'width: 90px';
								center.style.cssText += 'width: calc(100% - 50px - 90px);';
							}
						}
					}
				}
				aui.touchDom(_this.ui.right_submit_btn.querySelector('span'), "#FFF", "#129611", "1px solid #129611");
			}
		},
		//输入框获取焦点
		msgTextFocus(event){
			var _this = this;
			with (_this.ui){
				center_textarea.focus();
				var _timer = setTimeout(function() {
					clearTimeout(_timer);
					center_textarea.focus();
				}, 150);
				$.isDefine(event) ? $.preventDefault(event) : $.preventDefault();
			}
		},
		//解决长按“发送”按钮，导致键盘关闭的问题；
		forbidKeypadClose(){
			var _this = this;
			with (_this.ui){
				function __selfPd(event){
					$.preventDefault(event);
				}
				function __selfFoucs(event){
					_this.msgTextFocus(event);
				}
				left_record_btn.addEventListener('touchmove', __selfPd, { passive: false });
				left_keypad_btn.addEventListener('touchmove', __selfPd, { passive: false });
				center_record_start.addEventListener('touchstart', __selfPd, { passive: false });
				right_emotion_btn.addEventListener('touchmove', __selfPd, { passive: false });
				right_keypad_btn.addEventListener('touchmove', __selfPd, { passive: false });
				right_extras_btn.addEventListener('touchmove', __selfPd, { passive: false });
				right_submit_btn.addEventListener('touchstart', __selfFoucs, { passive: false });
				right_submit_btn.addEventListener('drag', __selfFoucs, { passive: false });
				//滑动屏幕关闭键盘
				body.addEventListener("touchmove", function(event) {
					center_textarea.blur();
				}, { passive: false });
				mask.addEventListener("tap", function(event) {
					center_textarea.blur();
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					mask.classList.remove("active");
					main.style.cssText += 'bottom: 0px;';
				});
				mask.addEventListener("drag", function(event) {
					center_textarea.blur();
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					mask.classList.remove("active");
					main.style.cssText += 'bottom: 0px;';
				},{ passive: false });
			}
		},
		//输入检测
		changeInput(callback){
			var _this = this;
			with (_this.ui){
				center_textarea.addEventListener('input', function(){
					_this.setStyle();
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//输入框获取焦点检测
		changeFocus(){
			var _this = this;
			with (_this.ui){
				center_textarea.addEventListener('focus', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					mask.classList.remove("active");
					_this.data.extras.show = false;
					main.style.cssText += 'bottom: 0px;';
					this.setAttribute('readonly', 'readonly');
					setTimeout(() => {
						this.removeAttribute('readonly');
					}, 200);
				}, false);
			}
		},
		//重置聊天输入框区域
		resetChatBox(){
			var _this = this;
			with (_this.ui){
				if(_this.data.record.use)
				{
					left_record_btn.classList.add('active');
					left_keypad_btn.classList.remove('active');
					center_record_start.classList.remove('active');
				}
				center_textarea_box.classList.add('active');
				if(_this.data.emotion.use)
				{
					right_emotion_btn.classList.add('active');
					right_keypad_btn.classList.remove('active');
					emotion_container.classList.add('hide');
					emotion_container.classList.remove('show');
				}
				if(_this.data.extras.use)
				{
					extras_container.classList.add('hide');
					extras_container.classList.remove('show');
				}
			}
		},
		//点击左侧语音按钮显示录音区域
		showRecord(callback){
			var _this = this;
			with (_this.ui){
				left_record_btn.addEventListener('click', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					if(_this.data.record.use)
					{
						left_record_btn.classList.remove('active');
						left_keypad_btn.classList.add('active');
						center_record_start.classList.add('active');
					}
					center_textarea_box.classList.remove('active');
					mask.classList.remove("active");
					main.style.cssText += 'bottom: 0px;';
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//点击左侧键盘按钮隐藏录音区域
		hideRecord(callback){
			var _this = this;
			with (_this.ui){
				left_keypad_btn.addEventListener('click', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					if(_this.data.record.use)
					{
						left_record_btn.classList.add('active');
						left_keypad_btn.classList.remove('active');
						center_record_start.classList.remove('active');
					}
					center_textarea_box.classList.add('active');
					center_textarea.focus();
					mask.classList.remove("active");
					main.style.cssText += 'bottom: 0px;';
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//创建录音弹出窗
		createRecord(){
			var _this = this;
			return new Promise(function(resolve, reject){
				var _html = '<div class="aui-record-container">'
					+'<div class="aui-record-main">'
					+'<div class="aui-record-top">'
					+'<div class="aui-record-top-l"><i class="iconfont iconhuatong"></i></div>'
					+'<div class="aui-record-top-r"></div>'
					+'</div>'
					+'<div class="aui-record-tip">手指上滑，取消发送</div>'
					+'</div>'
					+'</div>';
				if(document.querySelector(".aui-record-container")) return;
				_this.ui.main.insertAdjacentHTML('beforeend', _html);
				_this.ui['record_main'] = document.querySelector('.aui-record-main');
				_this.ui['record_load'] = document.querySelector('.aui-record-top-r');
				_this.ui['record_tip'] = document.querySelector('.aui-record-tip');
				for(var i = 0; i < 8; i++)
				{
					_this.ui.record_load.insertAdjacentHTML('beforeend', '<span class="aui-record-load-span aui-record-load-span-'+ i +'" style=""></span></br>');
					if(i != 0)
					{
						document.querySelectorAll('.aui-record-load-span')[i].style.cssText +=
							'-webkit-width: calc(100% - '+ (i * 3) +'px); '
							+'width: calc(100% - '+ (i * 3) +'px);';
					}
				}
				_this.ui['record_load_span'] = document.querySelectorAll('.aui-record-load-span');
				resolve();
			});
		},
		//开启 / 关闭录音弹窗
		setSoundAlertVisable(show){
			var _this = this;
			with (_this.ui){
				if(show)
				{
					record_main.style.cssText += 'opacity: 1; display: inline-block;';
					_this.recordLoadStart(); //开启录音动画效果
				}
				else
				{
					record_main.style.cssText += 'opacity: 0;';
					var timer = setTimeout(function(){
						record_main.style.cssText += 'display: none;';
						_this.recordLoadEnd(); //关闭录音动画效果
					},200);
				}
			}
		},
		//开启录音动画效果
		recordLoadStart(){
			var _this = this;
			var index = [7, 6, 5, 4, 3, 2, 1, 0];
			_this.numOne = 0;
			_this.numTwo = 0;
			_this.recordOneTimer = null;
			_this.recordTwoTimer = null; //用于清除计时器
			for(var i = 0; i < index.length - 1; i++)
			{
				_this.ui.record_load_span[i].style.cssText += 'opacity: .2';
			}
			_this.recordOneTimer = setInterval(function(){
				_this.ui.record_load_span[index[_this.numOne]].style.cssText += 'opacity: .9';
				_this.numOne++;
				if(_this.numOne >= index.length)
				{
					_this.numOne = 0;
					clearInterval(_this.recordOneTimer);
					_this.recordTwoTimer = setInterval(function(){
						_this.ui.record_load_span[_this.numTwo].style.cssText += 'opacity: .2';
						_this.numTwo++;
						if(_this.numTwo >= index.length - 1)
						{
							_this.numTwo = 0;
							clearInterval(_this.recordTwoTimer);
							_this.recordLoadStart();
						}
					},100)
				}
			},100)
		},
		//关闭录音动画效果
		recordLoadEnd(){
			var _this = this;
			_this.numOne = 0;
			_this.numTwo = 0;
			clearInterval(_this.recordOneTimer);
			clearInterval(_this.recordTwoTimer);
		},
		//开始录音(手指按下 ' 按住 说话 ' 按钮)
		recordStart(callback){
			var _this = this;
			with (_this.ui){
				center_record_start.addEventListener('hold', function(event) {
					_this.data.recordStart = true;
					_this.data.recordCancel = false;
					_this.setSoundAlertVisable(true); //开启录音弹窗
					center_record_start.querySelector('span').innerText = '松开 结束';
					center_record_start.classList.add('aui-chatbox-record-end');
					record_tip.innerHTML = "手指上划，取消发送";
					_this.startTimestamp = (new Date()).getTime();
					if(_this.stopTimer)clearTimeout(_this.stopTimer);
					typeof callback == 'function' ? callback({status: 0, msg: '录音开始'}) : '';
				}, false);
			}
		},
		//取消录音(手指滑动 ' 松开 结束 ' 按钮)
		recordCancel(callback){
			var _this = this;
			with (_this.ui){
				center_record_start.addEventListener('drag', function(event) {
					if (Math.abs(event.detail.deltaY) > 50)
					{
						if (!_this.data.recordCancel)
						{
							_this.data.recordCancel = true;
							if (!record_tip.classList.contains("cancel"))
							{
								record_tip.classList.add("cancel");
							}
							record_tip.innerHTML = "松开手指，取消发送";
							center_record_start.querySelector('span').innerText = '松开 结束';
							center_record_start.classList.add('aui-chatbox-record-end');
							_this.recordLoadEnd();//关闭录音动画效果
							//设备录音结束逻辑
							typeof callback == 'function' ? callback({status: 0, msg: '松开手指，取消发送'}) : '';
						}
					}
					else
					{
						if (_this.data.recordCancel)
						{
							_this.data.recordCancel = false;
							if (record_tip.classList.contains("cancel"))
							{
								record_tip.classList.remove("cancel");
							}
							record_tip.innerHTML = "手指上划，取消发送";
							_this.recordLoadStart(); //开启录音动画效果
						}
					}
				}, true);
			}
		},
		//结束录音(手指离开 ' 松开 结束 ' 按钮)
		recordEnd(callback){
			var _this = this;
			with (_this.ui){
				center_record_start.addEventListener('release', function(event) {
					if (record_tip.classList.contains("cancel"))
					{
						var _timer = setTimeout(function(){
							record_tip.classList.remove("cancel");
							record_tip.innerHTML = "手指上划，取消发送";
							clearTimeout(_timer);
						},200)
					}
					_this.stopTimestamp = (new Date()).getTime();
					center_record_start.querySelector('span').innerText = '按住 说话';
					center_record_start.classList.remove('aui-chatbox-record-end');
					if (_this.stopTimestamp - _this.startTimestamp < _this.data.record.MIN_SOUND_TIME)
					{
						record_tip.innerHTML = "录音时间太短";
						_this.data.recordCancel = true;
						_this.recordLoadEnd(); //关闭录音动画效果
						//设备录音结束逻辑
						typeof callback == 'function' ? callback({status: 10001, msg: '录音时间太短'}) : '';
						_this.stopTimer = setTimeout(function(){
							//关闭录音弹窗
							_this.setSoundAlertVisable(false);
						},500);
					}else{
						//关闭录音弹窗
						_this.setSoundAlertVisable(false);
						if(_this.data.recordCancel == false)
						{
							//设备录音结束逻辑
							typeof callback == 'function' ? callback({status: 0, msg: '录音结束'}) : '';
						}
					}
				}, false);
			}
		},
		//点击右侧表情按钮显示表情选择区域
		showEmotion(callback){
			var _this = this;
			with (_this.ui){
				right_emotion_btn.addEventListener('click', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					if(_this.data.emotion.use)
					{
						right_emotion_btn.classList.remove('active');
						right_keypad_btn.classList.add('active');
						_this.ui.mask.classList.add("active");
						main.style.cssText += 'bottom: 260px;';
						emotion_container.classList.remove('hide');
						emotion_container.classList.add('show');
					}
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//点击右侧键盘按钮隐藏表情选择区域
		hideEmotion(callback){
			var _this = this;
			with (_this.ui){
				right_keypad_btn.addEventListener('click', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					_this.data.extras.show = false;
					center_textarea.focus();
					if(_this.data.emotion.use)
					{
						right_emotion_btn.classList.add('active');
						right_keypad_btn.classList.remove('active');
						emotion_container.classList.add('hide');
						emotion_container.classList.remove('show');
						mask.classList.remove("active");
						main.style.cssText += 'bottom: 0px;';
					}
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//创建表情选择区域
		createEmotion(){
			var _this = this;
			return new Promise(function(resolve, reject){
				var _html = '<div class="aui-emotion-container hide">'
					+'<div class="aui-emotion-main mui-slider">'
					+'<div class="aui-emotion-pages mui-slider-group"></div>'
					+'<div class="aui-emotion-paginations mui-slider-indicator"></div>'
					+'</div>'
					+'</div>';
				if(document.querySelector(".aui-emotion-container")) return;
				_this.ui.main.insertAdjacentHTML('beforeend', _html);
				_this.ui['emotion_container'] = document.querySelector('.aui-emotion-container');
				_this.ui['emotion_main'] = document.querySelector('.aui-emotion-main');
				_this.ui['emotion_pages'] = document.querySelector('.aui-emotion-pages');
				_this.ui['emotion_paginations'] = document.querySelector('.aui-emotion-paginations');
				function getEmotion(){
					return  new Promise(function(callback){
						$.ajax({url: _this.data.emotion.path + _this.data.emotion.file, type: 'get'}).then(function(ret){
							callback(ret);
						});
					});
				}
				getEmotion().then(function(ret){
					var btns = [], _html = '';
					if(!ret){return}
					for(var i = 0, len = ret.length; i < len; i += _this.data.emotion.pageHasNum)
					{
						btns.push(ret.slice(i, i + _this.data.emotion.pageHasNum));
					}
					for(var i = 0; i < btns.length; i++)
					{
						_this.ui.emotion_pages.insertAdjacentHTML('beforeend', '<div class="mui-slider-item aui-emotion-page"></div>');
						_html = '';
						for(var j = 0; j < btns[i].length; j++)
						{
							_html +=  '<div class="aui-emotion-item" pindex="'+ i +'" index="'+ (i * _this.data.emotion.pageHasNum + j) +'" data-name="'+btns[i][j].name+'" data-text="'+btns[i][j].text+'">'
								+'<div class="aui-emotion-item-img"><img src="'+ _this.data.emotion.path + btns[i][j].name +'.png"></div>'
								+'</div>'
						}
						_html += '<div class="aui-emotion-page-delete"><i class="iconfont iconjianpanshanchu"></i></div>'
						document.querySelectorAll('.aui-emotion-page')[i].insertAdjacentHTML('beforeend', _html);
						_this.ui['emotion_item'] = document.querySelectorAll('.aui-emotion-item');
						if(btns.length > 1)
						{
							_this.ui.emotion_paginations.insertAdjacentHTML('beforeend', '<div class="mui-indicator aui-emotion-pagination"></div>');
							document.querySelectorAll('.aui-emotion-pagination')[0].classList.add('mui-active');
						}
					}
					//表情删除
					_this.ui['emotion_delete'] = document.querySelectorAll('.aui-emotion-page-delete');
					for(var i = 0; i < _this.ui.emotion_delete.length; i++)
					{
						_this.ui.emotion_delete[i].onclick = function(){
							var length = this.dataset.text.length;
							var _arr = _this.ui.center_textarea.value.split('[');
							length = _arr[_arr.length - 1].length + 1;
							_this.ui.center_textarea.value = _this.ui.center_textarea.value.substring(0, _this.ui.center_textarea.value.length - length);
						}
					}
					_this.ui['emotion_item'] = document.querySelectorAll('.aui-emotion-item');
					if(btns.length > 1)
					{
						var slider = _this.ui.emotion_main;
						var group = slider.querySelector('.aui-emotion-pages');
						var items = mui('.aui-emotion-page', group);
						//克隆第一个节点
						var first = items[0].cloneNode(true);
						first.classList.add('mui-slider-item-duplicate');
						//克隆最后一个节点
						var last = items[items.length - 1].cloneNode(true);
						last.classList.add('mui-slider-item-duplicate');
						//处理是否循环逻辑，若支持循环，需支持两点：
						//1、在.mui-slider-group节点上增加.mui-slider-loop类
						//2、重复增加2个循环节点，图片顺序变为：N、1、2...N、1
						var sliderApi = mui(slider).slider();
					}
				});
				resolve();
			});
		},
		//点击选择表情
		chooseEmotionItem(callback){
			var _this = this;
			_this.ui['emotion_item'] = document.querySelectorAll('.aui-emotion-item');
			var _timer = setTimeout(function(){
				clearTimeout(_timer);
				with (_this.ui){
					for(var i = 0; i < emotion_item.length; i++)
					{
						aui.touchDom(emotion_item[i].querySelector('.aui-emotion-item-img'), "#CDCDCD")
						!(function(index){
							emotion_item[index].onclick = function(){
								//console.log(this.dataset.name);
								center_textarea.value = center_textarea.value + this.dataset.text;
								for(var i = 0; i < emotion_delete.length; i++)
								{
									emotion_delete[i].setAttribute('data-text', this.dataset.text);
								}
								if(center_textarea.scrollHeight > _this.data.textareaMinHeight)
								{
									if(center_textarea.scrollHeight > _this.data.textareaMaxHeight)
									{
										center_textarea.style.height = _this.data.textareaMaxHeight + 'px';
									}
									else{
										center_textarea.style.height = center_textarea.scrollHeight + 'px';
									}
								}
								center_textarea.scrollTop = center_textarea.scrollHeight;
								// right_emotion_btn.classList.add('active');
								if(_this.data.extras.use)
								{ //使用——附加功能
									if($.isDefine(center_textarea.value))
									{
										right_extras_btn.classList.remove('active');
										right_submit_btn.classList.add('active');
										right.style.cssText += 'width: 115px';
										center.style.cssText += 'width: calc(100% - 115px - 50px);';
									}
								}
								var result = {
									status: 0,
									msg: '表情选择',
									data: {
										index: index,
										name: this.dataset.name,
										text: this.dataset.text
									}
								};
								typeof callback == 'function' ? callback(result) : '';
							};
						})(i);
					}
				}
			},300);
		},
		//点击右侧附加功能按钮显示附加功能选择区域
		showExtras(callback){
			var _this = this;
			with (_this.ui){
				right_extras_btn.addEventListener('click', function(){
					_this.resetChatBox(); //重置聊天输入框区域
					//console.log(_this.data.extras.show);
					if(_this.data.extras.use && !_this.data.extras.show)
					{
						_this.data.extras.show = true;
						mask.classList.add("active");
						main.style.cssText += 'bottom: 260px;';
						extras_container.classList.remove('hide');
						extras_container.classList.add('show');
					}
					else
					{
						_this.data.extras.show = false;
						center_textarea.focus();
						_this.hideExtras(); //隐藏附加功能选择区域
					}
					typeof callback == 'function' ? callback() : '';
				}, false);
			}
		},
		//隐藏附加功能选择区域
		hideExtras(){
			var _this = this;
			with (_this.ui){
				if(_this.data.extras.use)
				{
					_this.data.extras.show = false;
					mask.classList.remove("active");
					main.style.cssText += 'bottom: 0px;';
					extras_container.classList.add('hide');
					extras_container.classList.remove('show');
				}
			}
		},
		//创建附加功能弹窗
		createExtras(){
			var _this = this;
			return new Promise(function(resolve, reject){
				var _html = '<div class="aui-extras-container hide">'
					+'<div class="aui-extras-main mui-slider">'
					+'<div class="aui-extras-pages mui-slider-group"></div>'
					+'<div class="aui-extras-paginations mui-slider-indicator"></div>'
					+'</div>'
					+'</div>';
				if(document.querySelector(".aui-extras-container")) return;
				_this.ui.main.insertAdjacentHTML('beforeend', _html);
				_this.ui['extras_container'] = document.querySelector('.aui-extras-container');
				_this.ui['extras_main'] = document.querySelector('.aui-extras-main');
				_this.ui['extras_pages'] = document.querySelector('.aui-extras-pages');
				_this.ui['extras_paginations'] = document.querySelector('.aui-extras-paginations');
				var btns = [], _html = '';
				for(var i = 0, len = _this.data.extras.btns.length; i < len; i += _this.data.extras.pageHasNum)
				{
					btns.push(_this.data.extras.btns.slice(i, i + _this.data.extras.pageHasNum));
				}
				for(var i = 0; i < btns.length; i++)
				{
					_this.ui.extras_pages.insertAdjacentHTML('beforeend', '<div class="mui-slider-item aui-extras-page"></div>');
					_html = '';
					for(var j = 0; j < btns[i].length; j++)
					{
						_html +=  '<div class="aui-extras-item" pindex="'+ i +'" index="'+ (i * _this.data.extras.pageHasNum + j) +'">';
						if(btns[i][j].img)
						{
							_html += '<div class="aui-extras-item-img"><img src="'+ btns[i][j].img +'"></div>';
						}
						if(btns[i][j].icon)
						{
							_html += '<div class="aui-extras-item-icon"><i class="iconfont '+ btns[i][j].icon +'"></i></div>';
						}
						_html += '<p>'+ btns[i][j].title +'</p>'
							+'</div>'
					}
					document.querySelectorAll('.aui-extras-page')[i].insertAdjacentHTML('beforeend', _html);
					_this.ui['extras_item'] = document.querySelectorAll('.aui-extras-item');
					if(btns.length > 1)
					{
						_this.ui.extras_paginations.insertAdjacentHTML('beforeend', '<div class="mui-indicator aui-extras-pagination"></div>');
						document.querySelectorAll('.aui-extras-pagination')[0].classList.add('mui-active');
					}
				}
				if(btns.length > 1)
				{
					var slider = _this.ui.extras_main;
					var group = slider.querySelector('.aui-extras-pages');
					var items = mui('.aui-extras-page', group);
					//克隆第一个节点
					var first = items[0].cloneNode(true);
					first.classList.add('mui-slider-item-duplicate');
					//克隆最后一个节点
					var last = items[items.length - 1].cloneNode(true);
					last.classList.add('mui-slider-item-duplicate');
					//处理是否循环逻辑，若支持循环，需支持两点：
					//1、在.mui-slider-group节点上增加.mui-slider-loop类
					//2、重复增加2个循环节点，图片顺序变为：N、1、2...N、1
					var sliderApi = mui(slider).slider();
				}
				resolve();
			});
		},
		//点击选择附加功能
		chooseExtrasItem(callback){
			var _this = this;
			_this.ui['extras_item'] = document.querySelectorAll('.aui-extras-item');
			var _timer = setTimeout(function(){
				clearTimeout(_timer);
				with (_this.ui){
					for(var i = 0; i <_this.data.extras.btns.length; i++)
					{
						if(_this.data.extras.use)
						{
							$.isDefine(_this.data.extras.btns[i].img)
								? aui.touchDom(extras_item[i].querySelector('.aui-extras-item-img'), "#CDCDCD")
								: aui.touchDom(extras_item[i].querySelector('.aui-extras-item-icon'), "#CDCDCD");
							!(function(index){
								extras_item[index].onclick = function(){
									_this.hideExtras(); //隐藏附加功能选择区域
									var result = {
										status: 0,
										msg: '附加功能选择',
										data: {
											index: index,
											title: _this.data.extras.btns[index].title,
											icon: _this.data.extras.btns[index].icon,
											img: _this.data.extras.btns[index].img
										}
									};
									typeof callback == 'function' ? callback(result) : '';
								};
							})(i);
						}
					}
				}
			},300);
		},
		//发送
		submit(callback){
			var _this = this;
			with (_this.ui){
				right_submit_btn.addEventListener('release', function(event) {
					_this.msgTextFocus(event);
					event.preventDefault();
					center_textarea.style.height = _this.data.textareaMinHeight + 'px';
					var result = {
						status: 0,
						msg: '操作成功',
						data: {
							value: center_textarea.value
						}
					};
					var text = center_textarea.value;
					center_textarea.value = '';
					_this.setStyle();
					typeof callback == 'function' ? callback(result) : '';
				}, false);
			}
		},
		//事件监听
		addEventListener({name = ''}, callback){
			var _this = this;
			!_this.data.events ? _this.data.events = [] : '';
			_this.data.events.push(name);
			with (_this.ui){
				switch (name){
					case 'changeInput': //输入检测
						_this.changeInput(callback);
						break;
					case 'showRecord': //点击左侧语音按钮显示录音区域
						_this.showRecord(callback);
						break;
					case 'hideRecord': //点击左侧键盘按钮隐藏录音区域
						_this.hideRecord(callback);
						break;
					case 'recordStart': //开始录音(手指按下 ' 按住 说话 ' 按钮)
						_this.recordStart(callback);
						break;
					case 'recordCancel': //取消录音(手指滑动 ' 松开 结束 ' 按钮)
						_this.recordCancel(callback);
						break;
					case 'recordEnd': //结束录音(手指离开 ' 松开 结束 ' 按钮)
						_this.recordEnd(callback);
						break;
					case 'showEmotion': //点击右侧表情按钮显示表情选择区域
						_this.showEmotion(callback);
						break;
					case 'hideEmotion': //点击右侧表情按钮隐藏表情选择区域
						_this.hideEmotion(callback);
						break;
					case 'chooseEmotionItem': //点击选择表情
						_this.chooseEmotionItem(callback);
						break;
					case 'showExtras': //点击右侧附加功能按钮显示附加功能选择区域
						_this.showExtras(callback);
						break;
					case 'hideExtras': //隐藏附加功能选择区域
						_this.hideExtras(callback);
						break;
					case 'chooseExtrasItem': //点击选择附加功能
						_this.chooseExtrasItem(callback);
						break;
					case 'submit': //发送
						_this.submit(callback);
						break;
					default:
						break;
				}
			}
		},
	}
})(aui, document, window);

/* 19位或16位银行卡号码验证
	 @param {number} bankno 银行卡号码 19位 或 16位
 */
!(function($, document, window, undefined){
	$.checkBankNo = function(bankno) {
		var lastNum = bankno.substr(bankno.length - 1, 1); //取出最后一位（与luhn进行比较）
		var first15Num = bankno.substr(0, bankno.length - 1); //前15或18位
		var newArr = new Array();
		for (var i = first15Num.length - 1; i > -1; i--) { //前15或18位倒序存进数组
			newArr.push(first15Num.substr(i, 1));
		}
		var arrJiShu = new Array(); //奇数位*2的积 <9
		var arrJiShu2 = new Array(); //奇数位*2的积 >9
		var arrOuShu = new Array(); //偶数位数组
		for (var j = 0; j < newArr.length; j++) {
			if ((j + 1) % 2 == 1) { //奇数位
				if (parseInt(newArr[j]) * 2 < 9) arrJiShu.push(parseInt(newArr[j]) * 2);
				else arrJiShu2.push(parseInt(newArr[j]) * 2);
			} else //偶数位
				arrOuShu.push(newArr[j]);
		}
		var jishu_child1 = new Array(); //奇数位*2 >9 的分割之后的数组个位数
		var jishu_child2 = new Array(); //奇数位*2 >9 的分割之后的数组十位数
		for (var h = 0; h < arrJiShu2.length; h++) {
			jishu_child1.push(parseInt(arrJiShu2[h]) % 10);
			jishu_child2.push(parseInt(arrJiShu2[h]) / 10);
		}
		var sumJiShu = 0; //奇数位*2 < 9 的数组之和
		var sumOuShu = 0; //偶数位数组之和
		var sumJiShuChild1 = 0; //奇数位*2 >9 的分割之后的数组个位数之和
		var sumJiShuChild2 = 0; //奇数位*2 >9 的分割之后的数组十位数之和
		var sumTotal = 0;
		for (var m = 0; m < arrJiShu.length; m++) {
			sumJiShu = sumJiShu + parseInt(arrJiShu[m]);
		}
		for (var n = 0; n < arrOuShu.length; n++) {
			sumOuShu = sumOuShu + parseInt(arrOuShu[n]);
		}
		for (var p = 0; p < jishu_child1.length; p++) {
			sumJiShuChild1 = sumJiShuChild1 + parseInt(jishu_child1[p]);
			sumJiShuChild2 = sumJiShuChild2 + parseInt(jishu_child2[p]);
		}
		//计算总和
		sumTotal = parseInt(sumJiShu) + parseInt(sumOuShu) + parseInt(sumJiShuChild1) + parseInt(sumJiShuChild2);
		//计算luhn值
		var k = parseInt(sumTotal) % 10 == 0 ? 10 : parseInt(sumTotal) % 10;
		var luhn = 10 - k;
		if (lastNum == luhn) {
			return true; //luhn验证通过
		} else {
			return false; //银行卡号必须符合luhn校验
		}
	}
})(aui, document, window);

/* ===============================
	 * 身份证号校验
 * ===============================
 */
!(function($, document, window, undefined){
	/***校验身份证号
	 @param {String} personnumber 身份证号码
	 @example: aui.checkIdcard(pass);  //return true | false;
	 */
	$.checkIdcard = function(personnumber) {
		personnumber = personnumber.toUpperCase();
		//身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X。
		if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(personnumber))) {
			return false;
		}
		//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
		//下面分别分析出生日期和校验位
		var len, re;
		len = personnumber.length;
		if (len == 15) {
			re = new RegExp(/^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/);
			var arrSplit = personnumber.match(re);
			//检查生日日期是否正确
			var dtmBirth = new Date('19' + arrSplit[2] + '/' + arrSplit[3] + '/' + arrSplit[4]);
			var bGoodDay;
			bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
			if (!bGoodDay) {
				return false;
			}
			else {
				//将15位身份证转成18位
				//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
				var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
				var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
				var nTemp = 0, i;
				personnumber = personnumber.substr(0, 6) + '19' + personnumber.substr(6, personnumber.length - 6);
				for (i = 0; i < 17; i++) {
					nTemp += personnumber.substr(i, 1) * arrInt[i];
				}
				personnumber += arrCh[nTemp % 11];
				return true;
			}
		}
		if (len == 18) {
			re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
			var arrSplit = personnumber.match(re);
			//检查生日日期是否正确
			var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
			var bGoodDay;
			bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
			if (!bGoodDay) {
				return false;
			} else {
				//检验18位身份证的校验码是否正确。
				//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
				var valnum;
				var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
				var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
				var nTemp = 0, i;
				for (i = 0; i < 17; i++) {
					nTemp += personnumber.substr(i, 1) * arrInt[i];
				}
				valnum = arrCh[nTemp % 11];
				if (valnum != personnumber.substr(17, 1)) {
					return false;
				}
				return true;
			}
		}
		return false;
	}
	/***通过身份证号获取年龄
	 @param {String} identityCard 身份证号码
	 @example: aui.getAge(identityCard);  //return '年龄';
	 */
	$.getAge = function(identityCard) {
		var len = (identityCard + "").length;
		if (len == 0) {
			return 0;
		} else {
			if ((len != 15) && (len != 18))//身份证号码只能为15位或18位其它不合法
			{
				return 0;
			}
		}
		var strBirthday = "";
		if (len == 18)//处理18位的身份证号码从号码中得到生日和性别代码
		{
			strBirthday = identityCard.substr(6, 4) + "/" + identityCard.substr(10, 2) + "/" + identityCard.substr(12, 2);
		}
		if (len == 15) {
			strBirthday = "19" + identityCard.substr(6, 2) + "/" + identityCard.substr(8, 2) + "/" + identityCard.substr(10, 2);
		}
		//时间字符串里，必须是“/”
		var birthDate = new Date(strBirthday);
		var nowDateTime = new Date();
		var age = nowDateTime.getFullYear() - birthDate.getFullYear();
		//再考虑月、天的因素;.getMonth()获取的是从0开始的，这里进行比较，不需要加1
		if (nowDateTime.getMonth() < birthDate.getMonth() || (nowDateTime.getMonth() == birthDate.getMonth() && nowDateTime.getDate() < birthDate.getDate())) {
			age--;
		}
		return age;
	}
})(aui, document, window);

/* ===============================
	 * 中文数字转阿拉伯数字
	 * @param {string} str 中文数字 “一百一十九”
	 * @example: aui.chineseToNumber('中文数字');
 * ===============================
 */
!(function($, document, window, undefined){
	$.chineseToNumber = function (str){
		var chnNumChar = {零:0, 一:1, 二:2, 三:3, 四:4, 五:5, 六:6, 七:7, 八:8, 九:9};
		var chnNameValue = {
			十:{value:10, secUnit:false},
			百:{value:100, secUnit:false},
			千:{value:1000, secUnit:false},
			万:{value:10000, secUnit:true},
			亿:{value:100000000, secUnit:true}
		}
		var rtn = 0;
		var section = 0;
		var number = 0;
		var secUnit = false;
		var str = str.split('');
		for(var i = 0; i < str.length; i++){
			var num = chnNumChar[str[i]];
			if(typeof num !== 'undefined'){
				number = num;
				if(i === str.length - 1){
					section += number;
				}
			}else{
				var unit = chnNameValue[str[i]].value;
				secUnit = chnNameValue[str[i]].secUnit;
				if(secUnit){
					section = (section + number) * unit;
					rtn += section;
					section = 0;
				}else{
					section += (number * unit);
				}
				number = 0;
			}
		}
		return rtn + section;
	}
})(aui, document, window);

function dateshow(){
	$("#datePlugin").animate({ opacity:1}, 300);
	$("#datePlugin").addClass("show");
}
function datehide(){
	$("#datePlugin").animate({ opacity:0}, 300);
	setTimeout(function(){$("#datePlugin").remove()},300);
}

(function ($) {
	$.fn.date = function (options,Ycallback,Ncallback) {
		//插件默认选项
		var that = $(this);
		var docType = $(this).is('input');
		var datetime = false;
		var nowdate = new Date();
		var indexY=1,indexM=1,indexD=1;
		var indexH=1,indexI=1,indexS=0;
		var initY=parseInt((nowdate.getFullYear()))-1900;
		var initM=parseInt(nowdate.getMonth()+"")+1;
		var initD=parseInt(nowdate.getDate()+"");
		var initH=parseInt(nowdate.getHours());
		var initI=parseInt(nowdate.getMinutes());
		var initS=parseInt(nowdate.getSeconds());
		var yearScroll=null,monthScroll=null,dayScroll=null;
		var HourScroll=null,MinuteScroll=null,SecondScroll=null;
		$.fn.date.defaultOptions = {
			title: '选择日期', 				//标题
			beginyear:1900,                 //日期--年--份开始
			endyear:nowdate.getFullYear()+50,                   //日期--年--份结束
			beginmonth:1,                   //日期--月--份结束
			endmonth:12,                    //日期--月--份结束
			beginday:1,                     //日期--日--份结束
			endday:31,                      //日期--日--份结束
			beginhour:0,
			endhour:23,
			beginminute:00,
			endminute:59,
			curdate:true,                   //打开日期是否定位到当前日期
			theme: 'datetime',                    //控件样式（1：日期，2：日期+时间）
			type: 0,  						//控制是否显示选择开始时间+选择结束时间按钮(0: 显示（默认） 1: 不显示)
			mode:null,                       //操作模式（滑动模式）
			event:"click",                    //打开日期插件默认方式为点击后后弹出日期
			show:true,
			isChooseStart: true, //默认选择开始时间
			result: [], //结果
		}
		//用户选项覆盖插件默认选项
		var opts = $.extend( true, {}, $.fn.date.defaultOptions, options );
		if(opts.theme === "datetime" || opts.theme === 2){datetime = true;}
		if(!opts.show){
			that.unbind('click');
		}
		else{
			//绑定事件（默认事件为获取焦点）
			document.querySelector(that.selector).onclick = function () {
				createUL();      //动态生成控件显示的日期
				init_iScrll();   //初始化iscrll
				extendOptions(); //显示控件
				that.blur();
				if(datetime){
					showdatetime();
					refreshTime();
				}
				refreshDate();
				bindButton();
				if(opts.type == 0){
					$("#datetype").show();
					$("#datemark").css({top: '180px'});
					$("#timemark").css({top: '330px'});
					$("#datetitle h1").css({lineHeight: '70px'});
				}
				else if(opts.type == 1){
					$("#datetype").hide();
					$("#datemark").css({top: '100px'});
					$("#timemark").css({top: '250px'});
					$("#datetitle h1").css({lineHeight: '60px'});
				}
			}
		};
		function refreshDate(){
			yearScroll.refresh();
			monthScroll.refresh();
			dayScroll.refresh();
			resetInitDete();
			yearScroll.scrollTo(0, initY*50, 100, true);
			monthScroll.scrollTo(0, initM*50-50, 100, true);
			dayScroll.scrollTo(0, initD*50-50, 100, true);
		}
		function refreshTime(){
			HourScroll.refresh();
			MinuteScroll.refresh();
			SecondScroll.refresh();
			initH=initH;
			HourScroll.scrollTo(0, initH*40, 100, true);
			MinuteScroll.scrollTo(0, initI*40 - 0, 100, true);
			SecondScroll.scrollTo(0, initS*40 - 0, 100, true);
			initH=parseInt(nowdate.getHours());
		}
		function resetIndex(){
			indexY=1;
			indexM=1;
			indexD=1;
		}
		function resetInitDete(){
			if(opts.curdate){return false;}
			else if(that.val()===""){return false;}
			initY = parseInt(that.val().substr(0,4))-opts.beginyear;
			initM = parseInt(that.val().substr(5,2));
			initD = parseInt(that.val().substr(8,2));
		}
		function bindButton(){
			resetIndex();
			$("#dateconfirm").unbind('click').click(function () {
				var datestr = $("#yearwrapper ul li:eq("+indexY+")").html().substr(0,$("#yearwrapper ul li:eq("+indexY+")").html().length-1)+"-"+
					$("#monthwrapper ul li:eq("+indexM+")").html().substr(0,$("#monthwrapper ul li:eq("+indexM+")").html().length-1)+"-"+
					$("#daywrapper ul li:eq("+Math.round(indexD)+")").html().substr(0,$("#daywrapper ul li:eq("+Math.round(indexD)+")").html().length-1);
				if(datetime){
					datestr+=" "+$("#Hourwrapper ul li:eq("+indexH+")").html().substr(0,$("#Minutewrapper ul li:eq("+indexH+")").html().length-1)+":"+
						$("#Minutewrapper ul li:eq("+indexI+")").html().substr(0,$("#Minutewrapper ul li:eq("+indexI+")").html().length-1);
					indexS=0;
				}

				if(Ycallback===undefined){
					if(docType){that.val(datestr);}else{that.find("i").eq(0).html(datestr);}
				}else{
					if(opts.result.length<=1){
						opts.result[1] = $(".dateTab").eq(1).find(".start-time").text();
					}
					Ycallback(opts.result);
				}
				datehide();
			});
			var Y = parseInt((nowdate.getFullYear()));
			var M = (parseInt(nowdate.getMonth()+"")+1) < 10 ? '0' + (parseInt(nowdate.getMonth()+"")+1) : parseInt(nowdate.getMonth()+"")+1;
			var D= parseInt(nowdate.getDate()+"") < 10 ? '0' + parseInt(nowdate.getDate()+"") : parseInt(nowdate.getDate()+"");
			// var h= parseInt(nowdate.getHours()+"") < 10 ? '0' + parseInt(nowdate.getHours()+"") : parseInt(nowdate.getHours()+"") > 12 ? (parseInt(nowdate.getHours()+"") - 12 < 10 ? '0' + parseInt(nowdate.getHours()+"") - 12 : parseInt(nowdate.getHours()+"") - 12) : parseInt(nowdate.getHours()+"");
			if(parseInt(nowdate.getHours()+"") < 10){
				var h = '0' + parseInt(nowdate.getHours()+"");
			}
			else{
				// if(parseInt(nowdate.getHours()+"") > 12){
				// 	if(parseInt(nowdate.getHours()+"") - 12 < 10){
				// 		var h = '0' + (parseInt(nowdate.getHours()+"") - 12);
				// 	}
				// 	else{
				// 		var h = parseInt(nowdate.getHours()+"") - 12;
				// 	}
				// }
				// else{
				var h = parseInt(nowdate.getHours()+"");
				// }
			}
			var m= parseInt(nowdate.getMinutes()+"") < 10 ? '0' + parseInt(nowdate.getMinutes()+"") : parseInt(nowdate.getMinutes()+"");
			var s= parseInt(nowdate.getSeconds()+"") < 10 ? '0' + parseInt(nowdate.getSeconds()+"") : parseInt(nowdate.getSeconds()+"");
			if(datetime){
				$(".dateTab").change().find(".start-time").text(Y + '-' + M + '-' + D + '  ' + h + ':' + m + ':' + s);
			}
			else{
				$(".dateTab").change().find(".start-time").text(Y + '-' + M + '-' + D);
			}
			$("#datecancle").click(function () {
				datehide();
				//$("#datePlugin").hide();
				typeof Ncallback == 'function' ? Ncallback() : '';
			});
			$(".dateTab").click(function () {
				if(Number($(this).attr("index")) == 0){
					opts['isChooseStart'] = true;
				}
				else{
					opts['isChooseStart'] = false;
				}
				$("#datetype .dateTab").change().removeClass("active");
				$("#datetype .dateTab").eq(Number($(this).attr("index"))).addClass("active");
			});
		}
		function extendOptions(){
			dateshow();
			//$("#dateshadow").show();
		}
		//日期滑动
		function init_iScrll() {
			var strY=0;
			var strM=0;
			yearScroll = new iScroll("yearwrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function () {
					indexY = (this.y/50)*(-1)+1;
					strY = $("#yearwrapper ul li:eq("+indexY+")").html().substr(0,$("#yearwrapper ul li:eq("+indexY+")").html().length-1);
					strM = $("#monthwrapper ul li:eq("+indexM+")").html().substr(0,$("#monthwrapper ul li:eq("+indexM+")").html().length-1)
					opts.endday = checkdays(strY,strM);
					$("#daywrapper ul").html(createDAY_UL());
					dayScroll.refresh();
					$("#monthwrapper").find("li").eq(crentMonthindex).addClass("crently").siblings().removeClass("crently");
					$("#daywrapper").find("li").eq(crentDayindex).addClass("crently").siblings().removeClass("crently");
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}});
			monthScroll = new iScroll("monthwrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function (){
					indexM = (this.y/50)*(-1)+1;
					strY = $("#yearwrapper ul li:eq("+indexY+")").html().substr(0,$("#yearwrapper ul li:eq("+indexY+")").html().length-1);
					strM = $("#monthwrapper ul li:eq("+indexM+")").html().substr(0,$("#monthwrapper ul li:eq("+indexM+")").html().length-1);
					opts.endday = checkdays(strY,strM);
					$("#daywrapper ul").html(createDAY_UL());
					dayScroll.refresh();
					$("#daywrapper").find("li").eq(crentDayindex).addClass("crently").siblings().removeClass("crently");
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}});
			dayScroll = new iScroll("daywrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function () {
					indexD = (this.y/50)*(-1)+1;
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}});
		}
		function showdatetime(){
			init_iScroll_datetime();
			addTimeStyle();
			// console.log(opts.theme);
			// console.log(datetime);
			$("#datescroll_datetime").show();
			$("#timemark").show();
			$("#Hourwrapper ul").html(createHOURS_UL());
			$("#Minutewrapper ul").html(createMINUTE_UL());
			$("#Secondwrapper ul").html(createSECOND_UL());
		}

		//日期+时间滑动
		function init_iScroll_datetime(){
			HourScroll = new iScroll("Hourwrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function () {
					indexH = Math.round((this.y/50)*(-1))+1;
					$("#Hourwrapper").find("li").eq(crentHourindex).addClass("crently").siblings().removeClass("crently");
					HourScroll.refresh();
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}})
			MinuteScroll = new iScroll("Minutewrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function () {
					indexI = Math.round((this.y/50)*(-1))+1;
					$("#Minutewrapper").find("li").eq(crentMinuteindex).addClass("crently").siblings().removeClass("crently");
					HourScroll.refresh();
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}})
			SecondScroll = new iScroll("Secondwrapper",{snap:"li",vScrollbar:false,
				onScrollEnd:function () {
					indexS = Math.round((this.y/50)*(-1));
					$("#Secondwrapper").find("li").eq(crentSecondindex).addClass("crently").siblings().removeClass("crently");
					HourScroll.refresh();
					if(opts.isChooseStart){
						if(datetime){
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][0] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][0]);
					}
					else{
						if(datetime){
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2) + '  ' + $("#Hourwrapper .crently").text().substr(0,2) + ':' + $("#Minutewrapper .crently").text().substr(0,2) + ':' + $("#Secondwrapper .crently").text().substr(0,2);
						}
						else{
							opts['result'][1] = $("#yearwrapper .crently").text().substr(0,4) + '-' + $("#monthwrapper .crently").text().substr(0,2) + '-' + $("#daywrapper .crently").text().substr(0,2);
						}
						$(".dateTab.active span:nth-child(2)").text(opts['result'][1]);
					}
				}})
		}
		function checkdays (year,month){
			var new_year = year;    //取当前的年份
			var new_month = month++;//取下一个月的第一天，方便计算（最后一天不固定）
			if(month>12)            //如果当前大于12月，则年份转到下一年
			{
				new_month -=12;        //月份减
				new_year++;            //年份增
			}

			var new_date = new Date(new_year,new_month,1);                //取当年当月中的第一天
			return (new Date(new_date.getTime()-1000*60*60*24)).getDate();//获取当月最后一天日期
		}
		function  createUL(){
			CreateDateUI();
			$("#yearwrapper ul").html(createYEAR_UL());
			$("#monthwrapper ul").html(createMONTH_UL());
		}
		var str = '';
		function CreateDateUI(){
			str = ''+
				'<div id="datePlugin">'+
				'<div id="datePage" class="page">'+
				'<section>'+
				'<div id="datetitle"><h1>'+ opts.title +'</h1></div>'+
				'<div id="datetype">'+
				'<div class="dateTab active" index="0"><span>开始时间</span><span class="start-time"></span></div>'+
				'<div class="dateTab" index="1"><span>结束时间</span><span class="start-time"></span></div>'+
				'</div>'+
				'<div id="datemark"><a id="markyear"></a><a id="markmonth"></a><a id="markday"></a></div>'+
				'<div id="timemark"><a id="markhour"></a><a id="markminut"></a><a id="marksecond"></a></div>'+
				'<div id="datescroll">'+
				'<div id="yearwrapper" class="col-after">'+
				'<ul></ul>'+
				'</div>'+
				'<div id="monthwrapper" class="col-after">'+
				'<ul></ul>'+
				'</div>'+
				'<div id="daywrapper">'+
				'<ul></ul>'+
				'</div>'+
				'</div>'+
				'<div id="datescroll_datetime">'+
				'<div id="Hourwrapper" class="col-after">'+
				'<ul></ul>'+
				'</div>'+
				'<div id="Minutewrapper" class="col-after">'+
				'<ul></ul>'+
				'</div>'+
				'<div id="Secondwrapper">'+
				'<ul></ul>'+
				'</div>'+
				'</div>'+
				'</section>'+
				'<footer id="dateFooter">'+
				'<div id="setcancle">'+
				'<ul>'+
				'<li id="dateconfirm">确定</li>'+
				'<li id="datecancle"><img src="https://xbjz1.oss-cn-beijing.aliyuncs.com/upload/default/gz-close.png" /></li>'+
				'</ul>'+
				'</div>'+
				'</footer>'+
				'</div>'+
				'</div>';
			if($("#datePlugin").length <=0){
				$("body").append(str);

			}
			document.querySelector("#datePlugin").addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			dateshow();
		}
		function addTimeStyle(){
			$("#datePage").css("height","380px");
			$("#datePage").css("right","0");
			$("#yearwrapper").css("position","absolute");
			$("#yearwrapper").css("bottom","200px");
			$("#monthwrapper").css("position","absolute");
			$("#monthwrapper").css("bottom","200px");
			$("#daywrapper").css("position","absolute");
			$("#daywrapper").css("bottom","200px");
		}
		//创建 --年-- 列表
		function createYEAR_UL(){
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginyear; i<=opts.endyear;i++){
				str+='<li>'+i+'年</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
		//创建 --月-- 列表
		function createMONTH_UL(){
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginmonth;i<=opts.endmonth;i++){
				if(i<10){
					i="0"+i
				}
				str+='<li>'+i+'月</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
		//创建 --日-- 列表
		function createDAY_UL(){
			$("#daywrapper ul").html("");
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginday;i<=opts.endday;i++){
				if(i<10){
					i="0"+i
				}
				str+='<li>'+i+'日</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
		//创建 --时-- 列表
		function createHOURS_UL(){
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginhour;i<=opts.endhour;i++){
				if(i<10){
					i="0"+i
				}
				str+='<li>'+i+'时</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
		//创建 --分-- 列表
		function createMINUTE_UL(){
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginminute;i<=opts.endminute;i++){
				if(i<10){
					i="0"+i
				}
				str+='<li>'+i+'分</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
		//创建 --分-- 列表
		function createSECOND_UL(){
			var str="<li>&nbsp;</li>";
			for(var i=opts.beginminute;i<=opts.endminute;i++){
				if(i<10){
					i="0"+i
				}
				str+='<li>'+i+'秒</li>'
			}
			return str+"<li>&nbsp;</li>";;
		}
	}
})(jQuery);

!(function($, document, window, undefined) {
	var events = {
		opts: {
			eventArr: ['eventswipeleft', 'eventswiperight', 'eventslideup', 'eventslidedown', 'eventclick', 'eventlongpress'],
		},
		//touchstart事件，delta记录开始触摸位置
		touchStart: function(event) {
			var _this = this;
			_this.delta = {};
			_this.delta.x = event.touches[0].pageX;
			_this.delta.y = event.touches[0].pageY;
			_this.delta.time = new Date().getTime();
		},
		/**
		 * touchend事件，计算两个事件之间的位移量
		 * 1、如果位移量很小或没有位移，看做点击事件
		 * 2、如果位移量较大，x大于y，可以看做平移，x>0,向右滑，反之向左滑。
		 * 3、如果位移量较大，x小于y，看做上下移动，y>0,向下滑，反之向上滑
		 * 这样就模拟的移动端几个常见的时间。
		 * */
		touchEnd: function(event) {
			var _this = this;
			let delta = _this.delta;
			delete _this.delta;
			let timegap = new Date().getTime() - delta.time;
			delta.x -= event.changedTouches[0].pageX;
			delta.y -= event.changedTouches[0].pageY;
			if (Math.abs(delta.x) < 5 && Math.abs(delta.y) < 5) {
				if (timegap < 1000) {
					if (_this['eventclick']) {
						_this['eventclick'].map(function(fn) {
							fn(event);
						});
					}
				} else {
					if (_this['eventlongpress']) {
						_this['eventlongpress'].map(function(fn) {
							fn(event);
						});
					}
				}
				return;
			}
			if (Math.abs(delta.x) > Math.abs(delta.y)) {
				if (delta.x > 0) {
					if (_this['eventswipeleft']) {
						_this['eventswipeleft'].map(function(fn) {
							fn(event);
						});
					}
				} else {
					_this['eventswiperight'].map(function(fn) {
						fn(event);
					});
				}
			} else {
				if (delta.y < 0) {
					if (_this['eventslidedown']) {
						_this['eventslidedown'].map(function(fn) {
							fn(event);
						});
					}
				} else {
					_this['eventslideup'].map(function(fn) {
						fn(event);
					});
				}
			}
		},
		bindEvent: function(dom, type, callback) {
			var _this = this;
			if (!dom) {
				aui.toast({msg: 'dom is null or undefined'}); return;
			}
			let flag = _this.opts.eventArr.some(key => dom[key]);
			if (!flag) {
				dom.addEventListener('touchstart', _this.touchStart);
				dom.addEventListener('touchend', _this.touchEnd);
			}
			if (!dom['event' + type]) {
				dom['event' + type] = [];
			}
			dom['event' + type].push(callback);
		},
		removeEvent: function(dom, type, callback) {
			var _this = this;
			if (dom['event' + type]) {
				for (let i = 0; i < dom['event' + type].length; i++) {
					if (dom['event' + type][i] === callback) {
						dom['event' + type].splice(i, 1);
						i--;
					}
				}
				if (dom['event' + type] && dom['event' + type].length === 0) {
					delete dom['event' + type];
					let flag = _this.opts.eventArr.every(key => !dom[key]);
					if (flag) {
						dom.removeEventListener('touchstart', _this.touchStart);
						dom.removeEventListener('touchend', _this.touchEnd);
					}
				}
			}
		},

	}
	$.on = function(dom, type, callback){
		events.bindEvent(dom, type, callback);
	};
	$.off = function(dom, type, callback){
		events.removeEvent(dom, type, callback);
	};
})(aui, document, window);

!(function($, document, window, undefined){
	/**
	 * 获取昨天
	 * 返回格式为 yyyy-mm-dd的日期的数组，如：['2014-01-25']
	 */
	$.getYesterday = function(){
		//昨天的时间
		var day = new Date();
		day.setTime(day.getTime()-24*60*60*1000);
		var yesterday = day.getFullYear()+"-" + (day.getMonth()+1) + "-" + day.getDate();
		//console.log(yesterday);
		return yesterday;
	}
	// 获取指定月份最后一天 (年-月-日) 参数date如：2018-08
	$.getCurrentMonthLast = function(date){
		var _this = this;
		var endDate = new Date(date); //date 是需要传递的时间如：2018-08
		var month=endDate.getMonth();
		var nextMonth=++month;
		var nextMonthFirstDay=new Date(endDate.getFullYear(),nextMonth,1);
		var oneDay=1000*60*60*24;
		var dateString=new Date(nextMonthFirstDay-oneDay);
		//console.log(dateString) //Wed Oct 31 2018 00:00:00 GMT+0800 (中国标准时间)
		return dateString.toLocaleDateString().replace(new RegExp('/','g'),"-"); //toLocaleDateString() 返回 如：2018/8/31										 
	}
	/**
	 * 获取本周一到本周日
	 * 返回格式为 yyyy-mm-dd的日期的数组，如：['2014-01-25']
	 */
	$.getDateCurrentweek = function(){
		var date = new Date();
		function formatDate(date) {
			var myyear = date.getFullYear();
			var mymonth = date.getMonth() + 1;
			var myweekday = date.getDate();
			if (mymonth < 10) {
				mymonth = "0" + mymonth;
			}
			if (myweekday < 10) {
				myweekday = "0" + myweekday;
			}
			return (myyear + "-" + mymonth + "-" + myweekday);
		}
		var date = new Date();
		var nowTime = date.getTime() ;
		var day = date.getDay();
		var oneDayTime = 24*60*60*1000 ;
		var MondayTime = nowTime - (day-1)*oneDayTime ;//显示周一
		var SundayTime =  nowTime + (7-day)*oneDayTime ;//显示周日
		var data = [formatDate(new Date(MondayTime)), formatDate(new Date(SundayTime))];
		//console.log(formatDate(new Date(MondayTime)));
		//console.log(formatDate(new Date(SundayTime)))
		return data;
	}
	/**
	 * 获取上周一到上周日
	 * 返回格式为 yyyy-mm-dd的日期的数组，如：['2014-01-25']
	 */
	$.getDateLastweek = function(){
		var date = new Date();
		function formatDate(date) {
			var myyear = date.getFullYear();
			var mymonth = date.getMonth() + 1;
			var myweekday = date.getDate();
			if (mymonth < 10) {
				mymonth = "0" + mymonth;
			}
			if (myweekday < 10) {
				myweekday = "0" + myweekday;
			}
			return (myyear + "-" + mymonth + "-" + myweekday);
		}
		var dateTime = date.getTime(); // 获取现在的时间
		var dateDay = date.getDay();
		var oneDayTime = 24 * 60 * 60 * 1000;
		var proWeekList = [];
		for(var i = 0; i < 7; i++){
			var time = dateTime - (dateDay + (7 - 1 - i)) * oneDayTime;
			proWeekList[i] = formatDate(new Date(time)); //date格式转换为yyyy-mm-dd格式的字符串
		}
		var data = [proWeekList[0], proWeekList[6]];
		//console.log(data);
		return data;
	}
	// 获取本月第一天和最后一天
	$.getCurrentMonth = function(){
		/**
		 *对Date的扩展，将 Date 转化为指定格式的String
		 *月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
		 *年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
		 *例子：
		 *(new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
		 *(new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
		 */
		Date.prototype.format = function (fmt) {
			var o = {
				"M+": this.getMonth() + 1, //月份
				"d+": this.getDate(), //日
				"h+": this.getHours(), //小时
				"m+": this.getMinutes(), //分
				"s+": this.getSeconds(), //秒
				"q+": Math.floor((this.getMonth() + 3) / 3), //季度
				"S": this.getMilliseconds() //毫秒
			};
			if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
			for (var k in o)
				if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			return fmt;
		}
		var day = new Date();
		day.setDate(1);//本月第一天
		var currentMonthFirstDay = day.format("yyyy-MM-dd");
		day.setMonth(day.getMonth()+1);//下个月
		day.setDate(day.getDate() - 1);//下个月第一天减1得到本月最后一天
		var currentMonthLastDay = day.format("yyyy-MM-dd");
		var data = [currentMonthFirstDay, currentMonthLastDay];
		//console.log(data);
		return data;
	}
	// 获取上月第一天和最后一天
	$.getLastMonth = function(){
		var nowdays = new Date();
		var year = nowdays.getFullYear();
		var month = nowdays.getMonth();
		if(month==0){
			month = 12;
			year = year-1;
		}
		if(month<10){
			month = '0'+month;
		}
		var myDate = new Date(year,month,0);
		var startDate = year+'-'+month+'-01'; //上个月第一天
		var endDate = year+'-'+month+'-'+myDate.getDate();//上个月最后一天
		var data = [startDate, endDate];
		//console.log(data);
		return data;
	}
	/***倒计时
	 @param {number} nowtime 当前时间时间戳
	 @param {number} endtime 结束时间时间戳
	 @example: aui.countdown('时间戳');
	 */
	$.countdown = function(nowtime, endtime, callback){
		var _this = this;
		var timer = null;
		var times = parseInt((endtime - nowtime) / 1000);
		timer=setInterval(function(){
			var day=0, hour=0, minute=0, second=0;//时间默认值
			if(times > 0){
				day = Math.floor(times / (60 * 60 * 24));
				hour = Math.floor(times / (60 * 60)) - (day * 24);
				minute = Math.floor(times / 60) - (day * 24 * 60) - (hour * 60);
				second = Math.floor(times) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
			}
			if (day <= 9) day = '0' + day;
			if (hour <= 9) hour = '0' + hour;
			if (minute <= 9) minute = '0' + minute;
			if (second <= 9) second = '0' + second;
			//console.log(minute+"分钟："+second+"秒");
			// var new_time = day + '天' + hour + '小时' + minute + '分' + second + '秒';
			typeof callback == "function" ? callback({day: day, hour: hour, minute: minute, second: second}) : '';
			times--;
		},1000);
		if(times<=0){
			clearInterval(timer);
		}
	}
	/***时间戳转换成几分钟前，几小时前，几天前，xxxx(年)-xx(月)-xx(日) xx(时): xx(分): xx(秒)
	 @param {string} timestamp 时间戳
	 @example: aui.formatMsgTime('时间戳');
	 */
	$.formatMsgTime = function(timestamp) {
		var dateTime = new Date(timestamp);
		var year = dateTime.getFullYear();
		var month = dateTime.getMonth() + 1;
		var day = dateTime.getDate();
		var hour = dateTime.getHours();
		var minute = dateTime.getMinutes();
		var second = dateTime.getSeconds();
		var now = new Date();
		var now_new = Date.parse(now.toDateString());  //typescript转换写法
		var milliseconds = 0;
		var timeSpanStr;
		milliseconds = now_new - timestamp;
		if (milliseconds <= 1000 * 60 * 1)
		{
			timeSpanStr = '刚刚';
		}
		else if (1000 * 60 * 1 < milliseconds && milliseconds <= 1000 * 60 * 60)
		{
			timeSpanStr = Math.round((milliseconds / (1000 * 60))) + '分钟前';
		}
		else if (1000 * 60 * 60 * 1 < milliseconds && milliseconds <= 1000 * 60 * 60 * 24)
		{
			timeSpanStr = Math.round(milliseconds / (1000 * 60 * 60)) + '小时前';
		}
		else if (1000 * 60 * 60 * 24 < milliseconds && milliseconds <= 1000 * 60 * 60 * 24 * 15)
		{
			timeSpanStr = Math.round(milliseconds / (1000 * 60 * 60 * 24)) + '天前';
		}
		else if (milliseconds > 1000 * 60 * 60 * 24 * 15 && year == now.getFullYear())
		{
			timeSpanStr = month + '-' + day + ' ' + hour + ':' + minute;
		}
		else
		{
			timeSpanStr = year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
		}
		return timeSpanStr;
	}
})(aui, document, window);

var crentYearindex=0;
var crentMonthindex=0;
var crentDayindex=0;
var crentHourindex=0;
var crentMinuteindex=0;
var crentSecondindex=0;
(function() {
	var m = Math,
		mround = function(r) {
			return r >> 0;
		},
		vendor = (/webkit/i).test(navigator.appVersion) ? 'webkit': (/firefox/i).test(navigator.userAgent) ? 'Moz': 'opera' in window ? 'O': '',
		isAndroid = (/android/gi).test(navigator.appVersion),
		isIDevice = (/iphone|ipad/gi).test(navigator.appVersion),
		isPlaybook = (/playbook/gi).test(navigator.appVersion),
		isTouchPad = (/hp-tablet/gi).test(navigator.appVersion),
		has3d = 'WebKitCSSMatrix' in window && 'm11' in new WebKitCSSMatrix(),
		hasTouch = 'ontouchstart' in window && !isTouchPad,
		hasTransform = vendor + 'Transform' in document.documentElement.style,
		hasTransitionEnd = isIDevice || isPlaybook,
		nextFrame = (function() {
			return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
				function(callback) {
					return setTimeout(callback, 1);
				}
		})(),
		cancelFrame = (function() {
			return window.cancelRequestAnimationFrame || window.webkitCancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame || window.msCancelRequestAnimationFrame || clearTimeout
		})(),
		RESIZE_EV = 'onorientationchange' in window ? 'orientationchange': 'resize',
		START_EV = hasTouch ? 'touchstart': 'mousedown',
		MOVE_EV = hasTouch ? 'touchmove': 'mousemove',
		END_EV = hasTouch ? 'touchend': 'mouseup',
		CANCEL_EV = hasTouch ? 'touchcancel': 'mouseup',
		WHEEL_EV = vendor == 'Moz' ? 'DOMMouseScroll': 'mousewheel',
		trnOpen = 'translate' + (has3d ? '3d(': '('),
		trnClose = has3d ? ',0)': ')',
		iScroll = function(el, options) {
			var that = this,
				doc = document,
				i;
			that.wrapper = typeof el == 'object' ? el: doc.getElementById(el);
			that.wrapper.style.overflow = 'hidden';
			that.scroller = that.wrapper.children[0];
			that.options = {
				hScroll: true,
				vScroll: true,
				x: 0,
				y: 0,
				bounce: true,
				bounceLock: false,
				momentum: true,
				lockDirection: true,
				useTransform: true,
				useTransition: false,
				topOffset: 0,
				checkDOMChanges: false,
				hScrollbar: true,
				vScrollbar: true,
				fixedScrollbar: isAndroid,
				hideScrollbar: isIDevice,
				fadeScrollbar: isIDevice && has3d,
				scrollbarClass: '',
				zoom: false,
				zoomMin: 1,
				zoomMax: 4,
				doubleTapZoom: 2,
				wheelAction: 'scroll',
				snap: false,
				snapThreshold: 1,
				onRefresh: null,
				onBeforeScrollStart: function(e) {
					e.preventDefault();
				},
				onScrollStart: null,
				onBeforeScrollMove: null,
				onScrollMove: null,
				onBeforeScrollEnd: null,
				onScrollEnd: null,
				onTouchEnd: null,
				onDestroy: null,
				onZoomStart: null,
				onZoom: null,
				onZoomEnd: null
			};
			for (i in options) that.options[i] = options[i];
			that.x = that.options.x;
			that.y = that.options.y;
			that.options.useTransform = hasTransform ? that.options.useTransform: false;
			that.options.hScrollbar = that.options.hScroll && that.options.hScrollbar;
			that.options.vScrollbar = that.options.vScroll && that.options.vScrollbar;
			that.options.zoom = that.options.useTransform && that.options.zoom;
			that.options.useTransition = hasTransitionEnd && that.options.useTransition;
			if (that.options.zoom && isAndroid) {
				trnOpen = 'translate(';
				trnClose = ')';
			}
			that.scroller.style[vendor + 'TransitionProperty'] = that.options.useTransform ? '-' + vendor.toLowerCase() + '-transform': 'top left';
			that.scroller.style[vendor + 'TransitionDuration'] = '0';
			that.scroller.style[vendor + 'TransformOrigin'] = '0 0';
			if (that.options.useTransition) that.scroller.style[vendor + 'TransitionTimingFunction'] = 'cubic-bezier(0.33,0.66,0.66,1)';
			if (that.options.useTransform) that.scroller.style[vendor + 'Transform'] = trnOpen + that.x + 'px,' + that.y + 'px' + trnClose;
			else that.scroller.style.cssText += ';position:absolute;top:' + that.y + 'px;left:' + that.x + 'px';
			if (that.options.useTransition) that.options.fixedScrollbar = true;
			that.refresh();
			that._bind(RESIZE_EV, window);
			that._bind(START_EV);
			if (!hasTouch) {
				that._bind('mouseout', that.wrapper);
				if (that.options.wheelAction != 'none') that._bind(WHEEL_EV);
			}
			if (that.options.checkDOMChanges) that.checkDOMTime = setInterval(function() {
					that._checkDOMChanges();
				},
				500);
		};
	iScroll.prototype = {
		enabled: true,
		x: 0,
		y: 0,
		steps: [],
		scale: 1,
		currPageX: 0,
		currPageY: 0,
		pagesX: [],
		pagesY: [],
		aniTime: null,
		wheelZoomCount: 0,
		handleEvent: function(e) {
			var that = this;
			switch (e.type) {
				case START_EV:
					if (!hasTouch && e.button !== 0) return;
					that._start(e);
					break;
				case MOVE_EV:
					that._move(e);
					break;
				case END_EV:
				case CANCEL_EV:
					that._end(e);
					break;
				case RESIZE_EV:
					that._resize();
					break;
				case WHEEL_EV:
					that._wheel(e);
					break;
				case 'mouseout':
					that._mouseout(e);
					break;
				case 'webkitTransitionEnd':
					that._transitionEnd(e);
					break;
			}
		},
		_checkDOMChanges: function() {
			if (this.moved || this.zoomed || this.animating || (this.scrollerW == this.scroller.offsetWidth * this.scale && this.scrollerH == this.scroller.offsetHeight * this.scale)) return;
			this.refresh();
		},
		_scrollbar: function(dir) {
			var that = this,
				doc = document,
				bar;
			if (!that[dir + 'Scrollbar']) {
				if (that[dir + 'ScrollbarWrapper']) {
					if (hasTransform) that[dir + 'ScrollbarIndicator'].style[vendor + 'Transform'] = '';
					that[dir + 'ScrollbarWrapper'].parentNode.removeChild(that[dir + 'ScrollbarWrapper']);
					that[dir + 'ScrollbarWrapper'] = null;
					that[dir + 'ScrollbarIndicator'] = null;
				}
				return;
			}
			if (!that[dir + 'ScrollbarWrapper']) {
				bar = doc.createElement('div');
				if (that.options.scrollbarClass) bar.className = that.options.scrollbarClass + dir.toUpperCase();
				else bar.style.cssText = 'position:absolute;z-index:100;' + (dir == 'h' ? 'height:7px;bottom:1px;left:2px;right:' + (that.vScrollbar ? '7': '2') + 'px': 'width:7px;bottom:' + (that.hScrollbar ? '7': '2') + 'px;top:2px;right:1px');
				bar.style.cssText += ';pointer-events:none;-' + vendor + '-transition-property:opacity;-' + vendor + '-transition-duration:' + (that.options.fadeScrollbar ? '350ms': '0') + ';overflow:hidden;opacity:' + (that.options.hideScrollbar ? '0': '1');
				that.wrapper.appendChild(bar);
				that[dir + 'ScrollbarWrapper'] = bar;
				bar = doc.createElement('div');
				if (!that.options.scrollbarClass) {
					bar.style.cssText = 'position:absolute;z-index:100;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);-' + vendor + '-background-clip:padding-box;-' + vendor + '-box-sizing:border-box;' + (dir == 'h' ? 'height:100%': 'width:100%') + ';-' + vendor + '-border-radius:3px;border-radius:3px';
				}
				bar.style.cssText += ';pointer-events:none;-' + vendor + '-transition-property:-' + vendor + '-transform;-' + vendor + '-transition-timing-function:cubic-bezier(0.33,0.66,0.66,1);-' + vendor + '-transition-duration:0;-' + vendor + '-transform:' + trnOpen + '0,0' + trnClose;
				if (that.options.useTransition) bar.style.cssText += ';-' + vendor + '-transition-timing-function:cubic-bezier(0.33,0.66,0.66,1)';
				that[dir + 'ScrollbarWrapper'].appendChild(bar);
				that[dir + 'ScrollbarIndicator'] = bar;
			}
			if (dir == 'h') {
				that.hScrollbarSize = that.hScrollbarWrapper.clientWidth;
				that.hScrollbarIndicatorSize = m.max(mround(that.hScrollbarSize * that.hScrollbarSize / that.scrollerW), 8);
				that.hScrollbarIndicator.style.width = that.hScrollbarIndicatorSize + 'px';
				that.hScrollbarMaxScroll = that.hScrollbarSize - that.hScrollbarIndicatorSize;
				that.hScrollbarProp = that.hScrollbarMaxScroll / that.maxScrollX;
			} else {
				that.vScrollbarSize = that.vScrollbarWrapper.clientHeight;
				that.vScrollbarIndicatorSize = m.max(mround(that.vScrollbarSize * that.vScrollbarSize / that.scrollerH), 8);
				that.vScrollbarIndicator.style.height = that.vScrollbarIndicatorSize + 'px';
				that.vScrollbarMaxScroll = that.vScrollbarSize - that.vScrollbarIndicatorSize;
				that.vScrollbarProp = that.vScrollbarMaxScroll / that.maxScrollY;
			}
			that._scrollbarPos(dir, true);
		},
		_resize: function() {
			var that = this;
			setTimeout(function() {
					that.refresh();
				},
				isAndroid ? 200 : 0);
		},
		_pos: function(x, y) {
			x = this.hScroll ? x: 0;
			y = this.vScroll ? y: 0;
			if (this.options.useTransform) {
				this.scroller.style[vendor + 'Transform'] = trnOpen + x + 'px,' + y + 'px' + trnClose + ' scale(' + this.scale + ')';
			} else {
				x = mround(x);
				y = mround(y);
				this.scroller.style.left = x + 'px';
				this.scroller.style.top = y + 'px';
			}
			this.x = x;
			this.y = y;
			this._scrollbarPos('h');
			this._scrollbarPos('v');
		},
		_scrollbarPos: function(dir, hidden) {

			var that = this,
				pos = dir == 'h' ? that.x: that.y,
				size;

			if (!that[dir + 'Scrollbar']) return;
			pos = that[dir + 'ScrollbarProp'] * pos;
			if (pos < 0) {
				if (!that.options.fixedScrollbar) {
					size = that[dir + 'ScrollbarIndicatorSize'] + mround(pos * 3);
					if (size < 8) size = 8;
					that[dir + 'ScrollbarIndicator'].style[dir == 'h' ? 'width': 'height'] = size + 'px';
				}
				pos = 0;
			} else if (pos > that[dir + 'ScrollbarMaxScroll']) {

				if (!that.options.fixedScrollbar) {
					size = that[dir + 'ScrollbarIndicatorSize'] - mround((pos - that[dir + 'ScrollbarMaxScroll']) * 3);
					if (size < 8) size = 8;
					that[dir + 'ScrollbarIndicator'].style[dir == 'h' ? 'width': 'height'] = size + 'px';
					pos = that[dir + 'ScrollbarMaxScroll'] + (that[dir + 'ScrollbarIndicatorSize'] - size);
				} else {
					pos = that[dir + 'ScrollbarMaxScroll'];
				}
			}

			that[dir + 'ScrollbarWrapper'].style[vendor + 'TransitionDelay'] = '0';
			that[dir + 'ScrollbarWrapper'].style.opacity = hidden && that.options.hideScrollbar ? '0': '1';
			that[dir + 'ScrollbarIndicator'].style[vendor + 'Transform'] = trnOpen + (dir == 'h' ? pos + 'px,0': '0,' + pos + 'px') + trnClose;
		},
		_start: function(e) {
			var that = this,
				point = hasTouch ? e.touches[0] : e,
				matrix,
				x,
				y,
				c1,
				c2;
			if (!that.enabled) return;
			if (that.options.onBeforeScrollStart) that.options.onBeforeScrollStart.call(that, e);
			if (that.options.useTransition || that.options.zoom) that._transitionTime(0);
			that.moved = false;
			that.animating = false;
			that.zoomed = false;
			that.distX = 0;
			that.distY = 0;
			that.absDistX = 0;
			that.absDistY = 0;
			that.dirX = 0;
			that.dirY = 0;
			if (that.options.zoom && hasTouch && e.touches.length > 1) {
				c1 = m.abs(e.touches[0].pageX - e.touches[1].pageX);
				c2 = m.abs(e.touches[0].pageY - e.touches[1].pageY);
				that.touchesDistStart = m.sqrt(c1 * c1 + c2 * c2);
				that.originX = m.abs(e.touches[0].pageX + e.touches[1].pageX - that.wrapperOffsetLeft * 2) / 2 - that.x;
				that.originY = m.abs(e.touches[0].pageY + e.touches[1].pageY - that.wrapperOffsetTop * 2) / 2 - that.y;
				if (that.options.onZoomStart) that.options.onZoomStart.call(that, e);
			}
			if (that.options.momentum) {
				if (that.options.useTransform) {
					matrix = getComputedStyle(that.scroller, null)[vendor + 'Transform'].replace(/[^0-9-.,]/g, '').split(',');
					x = matrix[4] * 1;
					y = matrix[5] * 1;
				} else {
					x = getComputedStyle(that.scroller, null).left.replace(/[^0-9-]/g, '') * 1;
					y = getComputedStyle(that.scroller, null).top.replace(/[^0-9-]/g, '') * 1;
				}
				if (x != that.x || y != that.y) {
					if (that.options.useTransition) that._unbind('webkitTransitionEnd');
					else cancelFrame(that.aniTime);
					that.steps = [];
					that._pos(x, y);
				}
			}
			that.absStartX = that.x;
			that.absStartY = that.y;
			that.startX = that.x;
			that.startY = that.y;
			that.pointX = point.pageX;
			that.pointY = point.pageY;
			that.startTime = e.timeStamp || Date.now();
			if (that.options.onScrollStart) that.options.onScrollStart.call(that, e);
			that._bind(MOVE_EV);
			that._bind(END_EV);
			that._bind(CANCEL_EV);
		},
		_move: function(e) {
			var that = this,
				point = hasTouch ? e.touches[0] : e,
				deltaX = point.pageX - that.pointX,
				deltaY = point.pageY - that.pointY,
				newX = that.x + deltaX,
				newY = that.y + deltaY,
				c1,
				c2,
				scale,
				timestamp = e.timeStamp || Date.now();
			if (that.options.onBeforeScrollMove) that.options.onBeforeScrollMove.call(that, e);
			if (that.options.zoom && hasTouch && e.touches.length > 1) {
				c1 = m.abs(e.touches[0].pageX - e.touches[1].pageX);
				c2 = m.abs(e.touches[0].pageY - e.touches[1].pageY);
				that.touchesDist = m.sqrt(c1 * c1 + c2 * c2);
				that.zoomed = true;
				scale = 1 / that.touchesDistStart * that.touchesDist * this.scale;
				if (scale < that.options.zoomMin) scale = 0.5 * that.options.zoomMin * Math.pow(2.0, scale / that.options.zoomMin);
				else if (scale > that.options.zoomMax) scale = 2.0 * that.options.zoomMax * Math.pow(0.5, that.options.zoomMax / scale);
				that.lastScale = scale / this.scale;
				newX = this.originX - this.originX * that.lastScale + this.x,
					newY = this.originY - this.originY * that.lastScale + this.y;
				this.scroller.style[vendor + 'Transform'] = trnOpen + newX + 'px,' + newY + 'px' + trnClose + ' scale(' + scale + ')';
				if (that.options.onZoom) that.options.onZoom.call(that, e);
				return;
			}
			that.pointX = point.pageX;
			that.pointY = point.pageY;
			if (newX > 0 || newX < that.maxScrollX) {
				newX = that.options.bounce ? that.x + (deltaX / 2) : newX >= 0 || that.maxScrollX >= 0 ? 0 : that.maxScrollX;
			}
			if (newY > that.minScrollY || newY < that.maxScrollY) {
				newY = that.options.bounce ? that.y + (deltaY / 2) : newY >= that.minScrollY || that.maxScrollY >= 0 ? that.minScrollY: that.maxScrollY;
			}
			if (that.absDistX < 6 && that.absDistY < 6) {
				that.distX += deltaX;
				that.distY += deltaY;
				that.absDistX = m.abs(that.distX);
				that.absDistY = m.abs(that.distY);
				return;
			}
			if (that.options.lockDirection) {
				if (that.absDistX > that.absDistY + 5) {
					newY = that.y;
					deltaY = 0;
				} else if (that.absDistY > that.absDistX + 5) {
					newX = that.x;
					deltaX = 0;
				}
			}
			that.moved = true;
			that._pos(newX, newY);
			that.dirX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
			that.dirY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;
			if (timestamp - that.startTime > 300) {
				that.startTime = timestamp;
				that.startX = that.x;
				that.startY = that.y;
			}
			if (that.options.onScrollMove) that.options.onScrollMove.call(that, e);
		},
		_end: function(e) {
			if (hasTouch && e.touches.length != 0) return;
			var that = this,
				point = hasTouch ? e.changedTouches[0] : e,
				target,
				ev,
				momentumX = {
					dist: 0,
					time: 0
				},
				momentumY = {
					dist: 0,
					time: 0
				},
				duration = (e.timeStamp || Date.now()) - that.startTime,
				newPosX = that.x,
				newPosY = that.y,
				distX,
				distY,
				newDuration,
				snap,
				scale;
			that._unbind(MOVE_EV);
			that._unbind(END_EV);
			that._unbind(CANCEL_EV);
			if (that.options.onBeforeScrollEnd) that.options.onBeforeScrollEnd.call(that, e);
			if (that.zoomed) {
				scale = that.scale * that.lastScale;
				scale = Math.max(that.options.zoomMin, scale);
				scale = Math.min(that.options.zoomMax, scale);
				that.lastScale = scale / that.scale;
				that.scale = scale;
				that.x = that.originX - that.originX * that.lastScale + that.x;
				that.y = that.originY - that.originY * that.lastScale + that.y;
				that.scroller.style[vendor + 'TransitionDuration'] = '200ms';
				that.scroller.style[vendor + 'Transform'] = trnOpen + that.x + 'px,' + that.y + 'px' + trnClose + ' scale(' + that.scale + ')';
				that.zoomed = false;
				that.refresh();
				if (that.options.onZoomEnd) that.options.onZoomEnd.call(that, e);
				return;
			}
			if (!that.moved) {
				if (hasTouch) {
					if (that.doubleTapTimer && that.options.zoom) {
						clearTimeout(that.doubleTapTimer);
						that.doubleTapTimer = null;
						if (that.options.onZoomStart) that.options.onZoomStart.call(that, e);
						that.zoom(that.pointX, that.pointY, that.scale == 1 ? that.options.doubleTapZoom: 1);
						if (that.options.onZoomEnd) {
							setTimeout(function() {
									that.options.onZoomEnd.call(that, e);
								},
								200);
						}
					} else {
						that.doubleTapTimer = setTimeout(function() {
								that.doubleTapTimer = null;
								target = point.target;
								while (target.nodeType != 1) target = target.parentNode;
								if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA') {
									ev = document.createEvent('MouseEvents');
									ev.initMouseEvent('click', true, true, e.view, 1, point.screenX, point.screenY, point.clientX, point.clientY, e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, 0, null);
									ev._fake = true;
									target.dispatchEvent(ev);
								}
							},
							that.options.zoom ? 250 : 0);
					}
				}
				that._resetPos(200);
				if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
				return;
			}
			if (duration < 300 && that.options.momentum) {

				momentumX = newPosX ? that._momentum(newPosX - that.startX, duration, -that.x, that.scrollerW - that.wrapperW + that.x, that.options.bounce ? that.wrapperW: 0) : momentumX;
				momentumY = newPosY ? that._momentum(newPosY - that.startY, duration, -that.y, (that.maxScrollY < 0 ? that.scrollerH - that.wrapperH + that.y - that.minScrollY: 0), that.options.bounce ? that.wrapperH: 0) : momentumY;
				newPosX = that.x + momentumX.dist;
				newPosY = that.y + momentumY.dist;
				if ((that.x > 0 && newPosX > 0) || (that.x < that.maxScrollX && newPosX < that.maxScrollX)) momentumX = {
					dist: 0,
					time: 0
				};
				if ((that.y > that.minScrollY && newPosY > that.minScrollY) || (that.y < that.maxScrollY && newPosY < that.maxScrollY)) momentumY = {
					dist: 0,
					time: 0
				};
			}
			if (momentumX.dist || momentumY.dist) {
				newDuration = m.max(m.max(momentumX.time, momentumY.time), 10);
				if (that.options.snap) {

					distX = newPosX - that.absStartX;
					distY = newPosY - that.absStartY;

					if (m.abs(distX) < that.options.snapThreshold && m.abs(distY) < that.options.snapThreshold) {
						that.scrollTo(that.absStartX, that.absStartY, 200);
					} else {
						snap = that._snap(newPosX, newPosY);

						newPosX = snap.x;
						newPosY = snap.y;
						newDuration = m.max(snap.time, newDuration);
					}
				}
				that.scrollTo(mround(newPosX), mround(newPosY), newDuration);
				if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
				return;
			}
			if (that.options.snap) {
				distX = newPosX - that.absStartX;
				distY = newPosY - that.absStartY;
				if (m.abs(distX) < that.options.snapThreshold && m.abs(distY) < that.options.snapThreshold) that.scrollTo(that.absStartX, that.absStartY, 200);
				else {
					snap = that._snap(that.x, that.y);
					if (snap.x != that.x || snap.y != that.y) that.scrollTo(snap.x, snap.y, snap.time);
				}
				if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
				return;
			}
			that._resetPos(200);
			if (that.options.onTouchEnd) that.options.onTouchEnd.call(that, e);
		},
		_resetPos: function(time) {
			var that = this,
				resetX = that.x >= 0 ? 0 : that.x < that.maxScrollX ? that.maxScrollX: that.x,
				resetY = that.y >= that.minScrollY || that.maxScrollY > 0 ? that.minScrollY: that.y < that.maxScrollY ? that.maxScrollY: that.y;
			if (resetX == that.x && resetY == that.y) {
				if (that.moved) {
					that.moved = false;
					if (that.options.onScrollEnd) that.options.onScrollEnd.call(that);
				}
				if (that.hScrollbar && that.options.hideScrollbar) {
					if (vendor == 'webkit') that.hScrollbarWrapper.style[vendor + 'TransitionDelay'] = '300ms';
					that.hScrollbarWrapper.style.opacity = '0';
				}
				if (that.vScrollbar && that.options.hideScrollbar) {
					if (vendor == 'webkit') that.vScrollbarWrapper.style[vendor + 'TransitionDelay'] = '300ms';
					that.vScrollbarWrapper.style.opacity = '0';
				}
				return;
			}
			that.scrollTo(resetX, resetY, time || 0);
		},
		_wheel: function(e) {
			var that = this,
				wheelDeltaX, wheelDeltaY, deltaX, deltaY, deltaScale;
			if ('wheelDeltaX' in e) {
				wheelDeltaX = e.wheelDeltaX / 12;
				wheelDeltaY = e.wheelDeltaY / 12;
			} else if ('detail' in e) {
				wheelDeltaX = wheelDeltaY = -e.detail * 3;
			} else {
				wheelDeltaX = wheelDeltaY = -e.wheelDelta;
			}
			if (that.options.wheelAction == 'zoom') {
				deltaScale = that.scale * Math.pow(2, 1 / 3 * (wheelDeltaY ? wheelDeltaY / Math.abs(wheelDeltaY) : 0));
				if (deltaScale < that.options.zoomMin) deltaScale = that.options.zoomMin;
				if (deltaScale > that.options.zoomMax) deltaScale = that.options.zoomMax;
				if (deltaScale != that.scale) {
					if (!that.wheelZoomCount && that.options.onZoomStart) that.options.onZoomStart.call(that, e);
					that.wheelZoomCount++;
					that.zoom(e.pageX, e.pageY, deltaScale, 400);
					setTimeout(function() {
							that.wheelZoomCount--;
							if (!that.wheelZoomCount && that.options.onZoomEnd) that.options.onZoomEnd.call(that, e);
						},
						400);
				}
				return;
			}
			deltaX = that.x + wheelDeltaX;
			deltaY = that.y + wheelDeltaY;
			if (deltaX > 0) deltaX = 0;
			else if (deltaX < that.maxScrollX) deltaX = that.maxScrollX;
			if (deltaY > that.minScrollY) deltaY = that.minScrollY;
			else if (deltaY < that.maxScrollY) deltaY = that.maxScrollY;
			that.scrollTo(deltaX, deltaY, 0);
		},
		_mouseout: function(e) {
			var t = e.relatedTarget;
			if (!t) {
				this._end(e);
				return;
			}
			while (t = t.parentNode) if (t == this.wrapper) return;
			this._end(e);
		},
		_transitionEnd: function(e) {
			var that = this;
			if (e.target != that.scroller) return;
			that._unbind('webkitTransitionEnd');
			that._startAni();
		},
		_startAni: function() {
			var that = this,
				startX = that.x,
				startY = that.y,
				startTime = Date.now(),
				step,
				easeOut,
				animate;
			if (that.animating) return;
			if (!that.steps.length) {
				that._resetPos(400);
				return;
			}
			step = that.steps.shift();
			if (step.x == startX && step.y == startY) step.time = 0;
			that.animating = true;
			that.moved = true;
			if (that.options.useTransition) {
				that._transitionTime(step.time);
				that._pos(step.x, step.y);
				that.animating = false;
				if (step.time) that._bind('webkitTransitionEnd');
				else that._resetPos(0);
				return;
			}
			animate = function() {
				var now = Date.now(),
					newX,
					newY;
				if (now >= startTime + step.time) {
					that._pos(step.x, step.y);
					that.animating = false;
					if (that.options.onAnimationEnd) that.options.onAnimationEnd.call(that);
					that._startAni();
					return;
				}
				now = (now - startTime) / step.time - 1;
				easeOut = m.sqrt(1 - now * now);
				newX = (step.x - startX) * easeOut + startX;
				newY = (step.y - startY) * easeOut + startY;
				that._pos(newX, newY);
				if (that.animating) that.aniTime = nextFrame(animate);

				//鏃ユ湡褰撳墠鏁堟灉 娣诲姞 By SHY 2016-7-5
				//褰撳墠Y鍧愭爣
				//step.y
				var crentid=$(that.wrapper).attr("id");
				var crentmovey=Math.abs(step.y);
				// console.log(crentid);
				if(crentmovey>=0)
				{
					crentindex=crentmovey/50;
					crentindex_2 = crentmovey/40;
					crentindex_2 = Math.round(crentindex_2);
					if(crentid=="yearwrapper")
					{
						crentYearindex=crentindex+1;
					}
					else if(crentid=="monthwrapper")
					{
						crentMonthindex=crentindex+1;
					}
					else if(crentid=="daywrapper")
					{
						crentDayindex=crentindex+1;
					}
					else if(crentid=="Hourwrapper")
					{
						crentHourindex=crentindex_2+1;
					}
					else if(crentid=="Minutewrapper")
					{
						crentMinuteindex=crentindex_2+1;
					}
					else if(crentid=="Secondwrapper")
					{
						crentSecondindex=crentindex_2+1;
					}
				}
				//console.log(crentHourindex);
				$("#yearwrapper").find("li").eq(crentYearindex).addClass("crently").siblings().removeClass("crently");
				$("#monthwrapper").find("li").eq(crentMonthindex).addClass("crently").siblings().removeClass("crently");
				$("#daywrapper").find("li").eq(crentDayindex).addClass("crently").siblings().removeClass("crently");
				$("#Hourwrapper").find("li").eq(crentHourindex).addClass("crently").siblings().removeClass("crently");
				$("#Minutewrapper").find("li").eq(crentMinuteindex).addClass("crently").siblings().removeClass("crently");
				$("#Secondwrapper").find("li").eq(crentSecondindex).addClass("crently").siblings().removeClass("crently");
			};
			animate();
		},
		_transitionTime: function(time) {
			time += 'ms';
			this.scroller.style[vendor + 'TransitionDuration'] = time;
			if (this.hScrollbar) this.hScrollbarIndicator.style[vendor + 'TransitionDuration'] = time;
			if (this.vScrollbar) this.vScrollbarIndicator.style[vendor + 'TransitionDuration'] = time;
		},
		_momentum: function(dist, time, maxDistUpper, maxDistLower, size) {
			var deceleration = 0.0006,
				speed = m.abs(dist) / time,
				newDist = (speed * speed) / (2 * deceleration),
				newTime = 0,
				outsideDist = 0;
			if (dist > 0 && newDist > maxDistUpper) {
				outsideDist = size / (6 / (newDist / speed * deceleration));
				maxDistUpper = maxDistUpper + outsideDist;
				speed = speed * maxDistUpper / newDist;
				newDist = maxDistUpper;
			} else if (dist < 0 && newDist > maxDistLower) {
				outsideDist = size / (6 / (newDist / speed * deceleration));
				maxDistLower = maxDistLower + outsideDist;
				speed = speed * maxDistLower / newDist;
				newDist = maxDistLower;
			}
			newDist = newDist * (dist < 0 ? -1 : 1);
			newTime = speed / deceleration;
			return {
				dist: newDist,
				time: mround(newTime)
			};
		},
		_offset: function(el) {
			var left = -el.offsetLeft,
				top = -el.offsetTop;
			while (el = el.offsetParent) {
				left -= el.offsetLeft;
				top -= el.offsetTop;
			}
			if (el != this.wrapper) {
				left *= this.scale;
				top *= this.scale;
			}
			return {
				left: left,
				top: top
			};
		},
		_snap: function(x, y) {
			var that = this,
				i, l, page, time, sizeX, sizeY;
			page = that.pagesX.length - 1;
			for (i = 0, l = that.pagesX.length; i < l; i++) {
				if (x >= that.pagesX[i]) {
					page = i;
					break;
				}
			}
			if (page == that.currPageX && page > 0 && that.dirX < 0) page--;
			x = that.pagesX[page];
			sizeX = m.abs(x - that.pagesX[that.currPageX]);
			sizeX = sizeX ? m.abs(that.x - x) / sizeX * 500 : 0;
			that.currPageX = page;
			page = that.pagesY.length - 1;
			for (i = 0; i < page; i++) {
				if (y >= that.pagesY[i]) {
					page = i;
					break;
				}
			}

			if (page == that.currPageY && page > 0 && that.dirY < 0) page--;
			y = that.pagesY[page];
			sizeY = m.abs(y - that.pagesY[that.currPageY]);
			sizeY = sizeY ? m.abs(that.y - y) / sizeY * 500 : 0;
			that.currPageY = page;
			time = mround(m.max(sizeX, sizeY)) || 200;

			return {
				x: x,
				y: y,
				time: time
			};
		},
		_bind: function(type, el, bubble) { (el || this.scroller).addEventListener(type, this, !!bubble);
		},
		_unbind: function(type, el, bubble) { (el || this.scroller).removeEventListener(type, this, !!bubble);
		},
		destroy: function() {
			var that = this;
			that.scroller.style[vendor + 'Transform'] = '';
			that.hScrollbar = false;
			that.vScrollbar = false;
			that._scrollbar('h');
			that._scrollbar('v');
			that._unbind(RESIZE_EV, window);
			that._unbind(START_EV);
			that._unbind(MOVE_EV);
			that._unbind(END_EV);
			that._unbind(CANCEL_EV);
			if (!that.options.hasTouch) {
				that._unbind('mouseout', that.wrapper);
				that._unbind(WHEEL_EV);
			}
			if (that.options.useTransition) that._unbind('webkitTransitionEnd');
			if (that.options.checkDOMChanges) clearInterval(that.checkDOMTime);
			if (that.options.onDestroy) that.options.onDestroy.call(that);
		},
		refresh: function() {
			var that = this,
				offset, i, l, els, pos = 0,
				page = 0;
			if (that.scale < that.options.zoomMin) that.scale = that.options.zoomMin;
			that.wrapperW = that.wrapper.clientWidth || 1;
			that.wrapperH = that.wrapper.clientHeight || 1;
			that.minScrollY = -that.options.topOffset || 0;
			that.scrollerW = mround(that.scroller.offsetWidth * that.scale);
			that.scrollerH = mround((that.scroller.offsetHeight + that.minScrollY) * that.scale);
			that.maxScrollX = that.wrapperW - that.scrollerW;
			that.maxScrollY = that.wrapperH - that.scrollerH + that.minScrollY;
			that.dirX = 0;
			that.dirY = 0;
			if (that.options.onRefresh) that.options.onRefresh.call(that);
			that.hScroll = that.options.hScroll && that.maxScrollX < 0;
			that.vScroll = that.options.vScroll && (!that.options.bounceLock && !that.hScroll || that.scrollerH > that.wrapperH);
			that.hScrollbar = that.hScroll && that.options.hScrollbar;
			that.vScrollbar = that.vScroll && that.options.vScrollbar && that.scrollerH > that.wrapperH;
			offset = that._offset(that.wrapper);
			that.wrapperOffsetLeft = -offset.left;
			that.wrapperOffsetTop = -offset.top;
			if (typeof that.options.snap == 'string') {
				that.pagesX = [];
				that.pagesY = [];
				els = that.scroller.querySelectorAll(that.options.snap);
				for (i = 0, l = els.length; i < l; i++) {
					pos = that._offset(els[i]);
					pos.left += that.wrapperOffsetLeft;
					pos.top += that.wrapperOffsetTop;
					that.pagesX[i] = pos.left < that.maxScrollX ? that.maxScrollX: pos.left * that.scale;
					that.pagesY[i] = pos.top < that.maxScrollY ? that.maxScrollY: pos.top * that.scale;
				}
			} else if (that.options.snap) {
				that.pagesX = [];
				while (pos >= that.maxScrollX) {
					that.pagesX[page] = pos;
					pos = pos - that.wrapperW;
					page++;
				}
				if (that.maxScrollX % that.wrapperW) that.pagesX[that.pagesX.length] = that.maxScrollX - that.pagesX[that.pagesX.length - 1] + that.pagesX[that.pagesX.length - 1];
				pos = 0;
				page = 0;
				that.pagesY = [];
				while (pos >= that.maxScrollY) {
					that.pagesY[page] = pos;
					pos = pos - that.wrapperH;
					page++;
				}
				if (that.maxScrollY % that.wrapperH) that.pagesY[that.pagesY.length] = that.maxScrollY - that.pagesY[that.pagesY.length - 1] + that.pagesY[that.pagesY.length - 1];
			}
			that._scrollbar('h');
			that._scrollbar('v');
			if (!that.zoomed) {
				that.scroller.style[vendor + 'TransitionDuration'] = '0';
				that._resetPos(200);
			}
		},
		scrollTo: function(x, y, time, relative) {
			var that = this,
				step = x,
				i, l;
			that.stop();
			if (!step.length) step = [{
				x: x,
				y: y,
				time: time,
				relative: relative
			}];
			for (i = 0, l = step.length; i < l; i++) {
				if (step[i].relative) {
					step[i].x = that.x - step[i].x;
					step[i].y = that.y - step[i].y;
				}
				that.steps.push({
					x: step[i].x,
					y: step[i].y,
					time: step[i].time || 0
				});
			}
			that._startAni();
		},
		scrollToElement: function(el, time) {
			var that = this,
				pos;
			el = el.nodeType ? el: that.scroller.querySelector(el);
			if (!el) return;
			pos = that._offset(el);
			pos.left += that.wrapperOffsetLeft;
			pos.top += that.wrapperOffsetTop;
			pos.left = pos.left > 0 ? 0 : pos.left < that.maxScrollX ? that.maxScrollX: pos.left;
			pos.top = pos.top > that.minScrollY ? that.minScrollY: pos.top < that.maxScrollY ? that.maxScrollY: pos.top;
			time = time === undefined ? m.max(m.abs(pos.left) * 2, m.abs(pos.top) * 2) : time;
			that.scrollTo(pos.left, pos.top, time);
		},
		scrollToPage: function(pageX, pageY, time) {
			var that = this,
				x, y;
			time = time === undefined ? 400 : time;
			if (that.options.onScrollStart) that.options.onScrollStart.call(that);
			if (that.options.snap) {
				pageX = pageX == 'next' ? that.currPageX + 1 : pageX == 'prev' ? that.currPageX - 1 : pageX;
				pageY = pageY == 'next' ? that.currPageY + 1 : pageY == 'prev' ? that.currPageY - 1 : pageY;
				pageX = pageX < 0 ? 0 : pageX > that.pagesX.length - 1 ? that.pagesX.length - 1 : pageX;
				pageY = pageY < 0 ? 0 : pageY > that.pagesY.length - 1 ? that.pagesY.length - 1 : pageY;
				that.currPageX = pageX;
				that.currPageY = pageY;
				x = that.pagesX[pageX];
				y = that.pagesY[pageY];
			} else {
				x = -that.wrapperW * pageX;
				y = -that.wrapperH * pageY;
				if (x < that.maxScrollX) x = that.maxScrollX;
				if (y < that.maxScrollY) y = that.maxScrollY;
			}
			that.scrollTo(x, y, time);
		},
		disable: function() {
			this.stop();
			this._resetPos(0);
			this.enabled = false;
			this._unbind(MOVE_EV);
			this._unbind(END_EV);
			this._unbind(CANCEL_EV);
		},
		enable: function() {
			this.enabled = true;
		},
		stop: function() {
			if (this.options.useTransition) this._unbind('webkitTransitionEnd');
			else cancelFrame(this.aniTime);
			this.steps = [];
			this.moved = false;
			this.animating = false;
		},
		zoom: function(x, y, scale, time) {
			var that = this,
				relScale = scale / that.scale;
			if (!that.options.useTransform) return;
			that.zoomed = true;
			time = time === undefined ? 200 : time;
			x = x - that.wrapperOffsetLeft - that.x;
			y = y - that.wrapperOffsetTop - that.y;
			that.x = x - x * relScale + that.x;
			that.y = y - y * relScale + that.y;
			that.scale = scale;
			that.refresh();
			that.x = that.x > 0 ? 0 : that.x < that.maxScrollX ? that.maxScrollX: that.x;
			that.y = that.y > that.minScrollY ? that.minScrollY: that.y < that.maxScrollY ? that.maxScrollY: that.y;
			that.scroller.style[vendor + 'TransitionDuration'] = time + 'ms';
			that.scroller.style[vendor + 'Transform'] = trnOpen + that.x + 'px,' + that.y + 'px' + trnClose + ' scale(' + scale + ')';
			that.zoomed = false;
		},
		isReady: function() {
			return ! this.moved && !this.zoomed && !this.animating;
		}
	};
	if (typeof exports !== 'undefined') exports.iScroll = iScroll;
	else window.iScroll = iScroll;
})();

/*
	 * keypad数字键盘
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.keypad.open({
	    	type: 'point', //1、number | 2、point | 3、idcard
	    	num: 2, //小数点保留几位
	    	mask: false,
	    	// value: document.querySelector('#text').value
	    }, function(ret){
	    	console.log(ret);
	    	document.querySelector('#text').value = ret.result;
	    });
 * ===============================
 */
!(function($, document, window, undefined){
	$.keypad = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				type: 'number', //类型, "number"—纯数字键盘 || "point"—带小数点键盘 || "idcard"—输入身份证号键盘
				value: '',
				num: 2, //控制小数点后保留两位
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
			}
			return $.extend(opts, opt, true);
		},
		//创建键盘元素
		creat(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-keypad">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-keypad-main">'
				+'<div class="aui-keypad-top row-before row-after"><i class="iconfont icondown1"></i></div>'
				+'<ul class="aui-keypad-middle"></ul>'
				+'</div>'
				+'</div>';
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				keypad: document.querySelector(".aui-keypad"),
				main: document.querySelector(".aui-keypad-main"),
				mask: document.querySelector(".aui-mask"),
				top:  document.querySelector(".aui-keypad-top"),
				middle: document.querySelector(".aui-keypad-middle"),
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.style.cssText = 'background: transparent;' : '';
			for(let i = 1; i <= 9; i++)
			{
				var _item = '<li class="aui-keypad-item aui-keypad-number" id="'+ i +'">'+ i +'</li>';
				_this.ui.middle.insertAdjacentHTML('beforeend', _item);
			}
			switch (_opts.type){
				case "number":
					var _item = '<li class="aui-keypad-item aui-keypad-hide" id="number"></li>';
					_this.ui.middle.insertAdjacentHTML('beforeend', _item);
					break;
				case "point":
					var _item = '<li class="aui-keypad-item aui-keypad-point" id=".">.</li>';
					_this.ui.middle.insertAdjacentHTML('beforeend', _item);
					_this.ui['point'] = document.querySelector(".aui-keypad-point");
					aui.touchDom(_this.ui.point, "rgba(235,235,235,1)");
					break;
				case "idcard":
					var _item = '<li class="aui-keypad-item aui-keypad-card" id="x">x</li>';
					_this.ui.middle.insertAdjacentHTML('beforeend', _item);
					_this.ui['card'] = document.querySelector(".aui-keypad-card");
					aui.touchDom(_this.ui.card, "rgba(235,235,235,1)");
					break;
				default:
					var _item = '<li class="aui-keypad-item aui-keypad-hide" id="number"></li>';
					_this.ui.middle.insertAdjacentHTML('beforeend', _item);
					break;
			}
			var _item = '<li class="aui-keypad-item aui-keypad-number aui-keypad-zero" id="0">0</li>'
				+'<li class="aui-keypad-item aui-keypad-clear" id="clear"><i class="iconfont iconjianpanshanchu"></i></li>';
			_this.ui.middle.insertAdjacentHTML('beforeend', _item);
			_this.ui['number'] = document.querySelectorAll(".aui-keypad-number");
			_this.ui['clear'] = document.querySelector(".aui-keypad-clear");
		},
		//打开键盘
		open(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			_this.ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.close(opt);
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
			_this.ui.top.addEventListener("click", function(e){
				_this.close(opt, callback);
			});
			for(var i = 0; i < _this.ui.number.length; i++){
				aui.touchDom(_this.ui.number[i], "rgba(235,235,235,1)");
			}
			aui.touchDom(_this.ui.top, "rgba(235,235,235,1)");
			aui.touchDom(_this.ui.clear, "rgba(250,250,250,1)");
			_this.clickEvents(opt, callback);
		},
		//关闭键盘
		close(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			//关闭键盘时检验最后一位是否为小数点
			if(_this.result && _this.result.indexOf('.') != -1)
			{
				if(_this.result.toString().split(".")[1].length == 0)
				{
					_this.result = _this.result.substr(0, _this.result.length - 1);
				}
			}
			_this.ui && _this.ui.mask ? _this.ui.mask.style.animation = "aui-fade-out .2s ease-out forwards" : '';
			_this.ui ? _this.ui.main.style.cssText = 'animation: aui-slide-down-screen .3s ease-out forwards;' : '';
			var timer = setTimeout(function() {
				_this.ui ? _this.ui.keypad.style.cssText = 'animation: aui-fade-out .2s ease-out forwards;' : '';
				document.querySelector(".aui-keypad") ? document.querySelector(".aui-keypad").parentNode.removeChild(document.querySelector(".aui-keypad")) : '';
				typeof callback == "function" ?  callback({result: _this.result}) : '';
				clearTimeout(timer);
			},150);
		},
		//键盘相关事件处理
		clickEvents(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			_this.result  = _opts.value;
			//数字点击
			for(var i = 0; i < _this.ui.number.length; i++){
				!(function(index){
					_this.ui.number[index].onclick = function(){
						if(_opts.type == "point")
						{
							if(_this.result == '00')
							{
								_this.result = '0';
							}
						}
						if(_this.result.indexOf('.') != -1) //控制小数点后可输入位数
						{
							if(_this.result.toString().split(".")[1].length < _opts.num)
							{
								_this.result += _this.ui.number[index].innerText;
							}
						}
						else
						{
							_this.result += _this.ui.number[index].innerText;
						}
						typeof callback == 'function' ? callback({index: _this.ui.number[index].id, result: _this.result}) : '';
					}
				})(i);
			}
			//小数点点击
			if(_opts.type == "point")
			{
				_this.ui.point.onclick = function(){
					if(_this.result == '00')
					{
						_this.result = '0';
					}
					if(_this.result.indexOf('.') == -1 && _this.result.length > 0)
					{
						_this.result += _this.ui.point.innerText;
						typeof callback == 'function' ? callback({index: _this.ui.point.id, result: _this.result}) : '';
					}
				}
			}
			//输入身份证号键盘
			if(_opts.type == 'idcard')
			{
				_this.ui.card.onclick = function(){
					if(_this.result.length == 17)
					{
						_this.result += _this.ui.card.innerText;
						typeof callback == 'function' ? callback({index: _this.ui.card.id, result: _this.result}) : '';
					}
				}
			}
			//删除已输入内容
			_this.ui.clear.onclick = function(){
				if(_this.result.length > 0)
				{
					_this.result = _this.result.substr(0, _this.result.length - 1);
					typeof callback == 'function' ? callback({index: _this.ui.clear.id, result: _this.result}) : '';
				}
			}
		},
	}
})(aui, document, window);

/*
	 * 数字金额转中文大写格式
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @param {number} money 金额
	 * @example: aui.moneyToChinese(199.99); //壹佰玖拾玖元玖角玖分;
 * ===============================
 */
!(function($, document, window, undefined){
	$.moneyToChinese = function(money) {
		var cnNums = new Array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'); //汉字的数字
		var cnIntRadice = new Array('', '拾', '佰', '仟'); //基本单位
		var cnIntUnits = new Array('', '万', '亿', '兆'); //对应整数部分扩展单位
		var cnDecUnits = new Array('角', '分', '毫', '厘'); //对应小数部分单位
		var cnInteger = '整'; //整数金额时后面跟的字符
		var cnIntLast = '元'; //整型完以后的单位
		var maxNum = 999999999999999.9999; //最大处理的数字
		var IntegerNum; //金额整数部分
		var DecimalNum; //金额小数部分
		var ChineseStr = ''; //输出的中文金额字符串
		var parts; //分离金额后用的数组，预定义
		var Symbol = ''; //正负值标记
		if(money == '') {
			return '';
		}

		money = parseFloat(money);
		if(money >= maxNum) {
			alert('超出最大处理数字');
			return '';
		}
		if(money == 0) {
			ChineseStr = cnNums[0] + cnIntLast + cnInteger;
			return ChineseStr;
		}
		if(money < 0) {
			money = -money;
			Symbol = '负 ';
		}
		money = money.toString(); //转换为字符串
		if(money.indexOf('.') == -1) {
			IntegerNum = money;
			DecimalNum = '';
		}
		else {
			parts = money.split('.');
			IntegerNum = parts[0];
			DecimalNum = parts[1].substr(0, 4);
		}
		if(parseInt(IntegerNum, 10) > 0) { //获取整型部分转换
			var zeroCount = 0;
			var IntLen = IntegerNum.length;
			for(var i = 0; i < IntLen; i++) {
				var n = IntegerNum.substr(i, 1);
				var p = IntLen - i - 1;
				var q = p / 4;
				var m = p % 4;
				if(n == '0') {
					zeroCount++;
				}
				else {
					if(zeroCount > 0) {
						ChineseStr += cnNums[0];
					}
					zeroCount = 0; //归零
					ChineseStr += cnNums[parseInt(n)] + cnIntRadice[m];
				}
				if(m == 0 && zeroCount < 4) {
					ChineseStr += cnIntUnits[q];
				}
			}
			ChineseStr += cnIntLast;
			//整型部分处理完毕
		}
		if(DecimalNum != '') { //小数部分
			var decLen = DecimalNum.length;
			for(var i = 0; i < decLen; i++) {
				var n = DecimalNum.substr(i, 1);
				if(n != '0') {
					ChineseStr += cnNums[Number(n)] + cnDecUnits[i];
				}
			}
		}
		if(ChineseStr == '') {
			ChineseStr += cnNums[0] + cnIntLast + cnInteger;
		}
		else if(DecimalNum == '') {
			ChineseStr += cnInteger;
		}
		ChineseStr = Symbol + ChineseStr;

		return ChineseStr;
	}

})(aui, document, window);

/*
	 * parabola抛物线(加入购物车)
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.parabola({
			origin: '', //@param  {[object]} origin [起点元素]
			target: '', //@param  {[object]} target [目标点元素]
			element: '', //@param  {[object]} element [要运动的元素]
			radian: '', //@param  {[number]} radian [抛物线弧度]
			time: '', //@param  {[number]} time [动画执行时间]
			callback: '', //@param  {[function]} callback [抛物线执行完成后回调]
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.parabola = {
		opts: function(opt){
			var opts = {
				origin: '', //@param  {[object]} origin [起点元素]
				target: '', //@param  {[object]} target [目标点元素]
				element: '', //@param  {[object]} element [要运动的元素]
				radian: '', //@param  {[number]} radian [抛物线弧度]
				time: '', //@param  {[number]} time [动画执行时间]
				callback: '', //@param  {[function]} callback [抛物线执行完成后回调]
			}
			return $.extend(opts, opt, true);
		},
		//初始化
		init(opt){
			var _this = this;
			_this.$ = function(selector) {
				return document.querySelector(selector);
			};
			if(_this.timer){return false;};
			_this.b = 0;
			_this.INTERVAL = 15;
			_this.config = _this.opts(opt) || {};
			// 起点
			_this.origin = _this.$(_this.config.origin) || null;
			// 终点
			_this.target = _this.$(_this.config.target) || null;
			// 运动的元素
			_this.element = _this.$(_this.config.element) || null;
			// 曲线弧度
			_this.radian = _this.config.radian || 0.010;
			// 运动时间(ms)
			_this.time = _this.config.time || 1000;
			var scrollTop = document.documentElement.scrollTop||document.body.scrollTop;
			_this.originX = _this.origin.getBoundingClientRect().left;
			_this.originY = _this.origin.getBoundingClientRect().top + scrollTop;
			_this.targetX = _this.target.getBoundingClientRect().left + _this.target.getBoundingClientRect().width/2 - _this.element.getBoundingClientRect().width/2;
			_this.targetY = _this.target.getBoundingClientRect().top + scrollTop;

			_this.diffx = _this.targetX - _this.originX;
			_this.diffy = _this.targetY - _this.originY;
			_this.speedx = _this.diffx / _this.time;

			// 已知a, 根据抛物线函数 y = a*x*x + b*x + c 将抛物线起点平移到坐标原点[0, 0]，终点随之平移，那么抛物线经过原点[0, 0] 得出c = 0;
			// 终点平移后得出：y2-y1 = a*(x2 - x1)*(x2 - x1) + b*(x2 - x1)
			// 即 diffy = a*diffx*diffx + b*diffx;
			// 可求出常数b的值
			_this.b = (_this.diffy - _this.radian * _this.diffx * _this.diffx) / _this.diffx;
			_this.element.style.left = `${_this.originX}px`;
			_this.element.style.top = `${_this.originY}px`;
		},
		// 确定动画方式
		moveStyle() {
			var _this = this;
			var moveStyle = 'position',
				testDiv = document.createElement('input');
			if('placeholder' in testDiv) {
				['', 'ms', 'moz', 'webkit'].forEach(function(pre) {
					var transform = pre + (pre ? 'T' : 't') + 'ransform';
					if(transform in testDiv.style) {
						moveStyle = transform;
					}
				});
			}
			return moveStyle;
		},
		//移动
		move() {
			var start = new Date().getTime(),
				moveStyle = this.moveStyle(),
				_this = this;
			if(_this.timer){return false;};
			_this.element.style.left = `${_this.originX}px`;
			_this.element.style.top = `${_this.originY}px`;
			_this.element.style[moveStyle] = 'translate(0px,0px)';
			_this.element.style.display = 'inline-block';
			_this.timer = null;
			_this.timer = setInterval(function() {
				if(new Date().getTime() - start > _this.time) {
					_this.element.style.left = `${_this.targetX}px`;
					_this.element.style.top = `${_this.targetY}px`;
					_this.element.style.display = 'none';
					typeof _this.config.callback === 'function' && _this.config.callback();
					clearInterval(_this.timer);
					_this.timer = null;
					return;
				}
				var x = _this.speedx * (new Date().getTime() - start);
				var y = _this.radian * x * x + _this.b * x;
				if(moveStyle === 'position') {
					_this.element.style.left = `${x + _this.originX}px`;
					_this.element.style.top = `${y + _this.originY}px`;
				} else {
					if(window.requestAnimationFrame) {
						window.requestAnimationFrame(function(){
							_this.element.style[moveStyle] =
								'translate(' + x + 'px,' + y + 'px)';
						});
					} else {
						_this.element.style[moveStyle] =
							'translate(' + x + 'px,' + y + 'px)';
					}
				}
			}, _this.INTERVAL);
			return _this;
		}
	}
})(aui, document, window);

/*
	 * 选择列表插件
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.picker.open({
	        title: '选择区域',
	        layer: 2, //二级联动
	        data: _this.cityData, //城市数据
	    },function(ret){
	        //console.log(ret);
	        if(ret.status == 1){
	            aui.picker.close(function(){
	                _this.shiquData = ret.data;
	            });
	        }
	    })
 * ===============================
 */
!(function($, document, window, undefined){
	$.picker = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				title: '', //--可选参数，标题
				layer: 1, //--可选参数，控制几级联动,默认1级
				data: [], //--必选参数，数据 如：[{text: '', adcode: '', children: [{text: '', adcode: ''}]}]
			}
			return $.extend(opts, opt, true);
		},
		// 创建弹窗UI元素
		creat: function(opt){
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-picker">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-picker-main">'
				+'<div class="aui-picker-header">'
				+'<div class="aui-picker-title">'+ (_opts.title ? _opts.title : '') +'</div>'
				+'<div class="aui-picker-close iconfont iconclose"></div>'
				+'</div>'
				+'<div class="aui-picker-nav"></div>'
				+'<div class="aui-picker-content">'
				+'<ul class="aui-picker-lists"></ul>'
				+'</div>'
				+'</div>'
				+'</div>';
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this.ui = {
				picker: document.querySelector(".aui-picker"),
				main: document.querySelector(".aui-picker-main"),
				mask: document.querySelector(".aui-picker").querySelector('.aui-mask'),
				title: document.querySelector(".aui-picker-title"),
				closeBtn: document.querySelector(".aui-picker-close"),
				nav: document.querySelector(".aui-picker-nav"),
				lists: document.querySelector(".aui-picker-lists"),
			}
			var lists = "", navitem = '';
			for(var i = 0; i < _opts.layer; i++){
				if(i==0){
					lists += '<li class="aui-picker-list active" index="'+ i +'"><div class="aui-picker-list-warp" index="'+ i +'"></div></li>';
					navitem +='<div class="aui-picker-navitem active">请选择</div>';
				}
				else{
					lists += '<li class="aui-picker-list" index="'+ i +'"><div class="aui-picker-list-warp" index="'+ i +'"></div></li>';
					navitem +='<div class="aui-picker-navitem">请选择</div>';
				}
			}
			navitem +='<span class="aui-picker-navborder"></span>';
			_this.ui.lists.insertAdjacentHTML('beforeend', lists);
			_this.ui.nav.insertAdjacentHTML('beforeend', navitem);
			_this.ui["list"] = document.querySelectorAll(".aui-picker-list");
			_this.ui["navItem"] = document.querySelectorAll(".aui-picker-navitem");
			_this.ui["navborder"] = document.querySelector(".aui-picker-navborder");
			var item = '';
			if(_opts.data && _opts.data.length > 0){
				for(var i = 0; i < _opts.data.length; i++){
					item += '<div class="aui-picker-item" index="'+ i +'">'+ _opts.data[i].text +'</div>';
				}
				_this.ui.list[0].querySelector(".aui-picker-list-warp").insertAdjacentHTML('beforeend', item);
				_this.ui["item0"] = document.querySelectorAll(".aui-picker-list:nth-child(1) .aui-picker-list-warp .aui-picker-item");
			}
		},
		// 初始化弹窗
		open: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(!document.querySelector(".aui-picker")){
				_this.creat(opt);
			}
			_this.ui.picker.style.display = "inline-block";
			_this.ui.mask.style.animation = "aui-fade-in .2s ease-out forwards";
			_this.ui.main.style.animation = "aui-slide-up-screen .2s ease-out forwards";
			// 导航栏菜单点击
			for(var i = 0; i < _opts.layer; i++){
				!(function(index){
					_this.ui.navItem[index].addEventListener("click", function(){
						_this.navTab(index, opt);
					},false);
				})(i);
			}
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault();
			}, { passive: false })
			// 关闭按钮关闭弹窗
			_this.ui.closeBtn.addEventListener("click", function(){
				_this.close(callback);
			}, false);
			_this.ui.mask.addEventListener("click", function(){
				_this.close(callback);
			}, false);
			_this.firstItemClick(opt, callback);
		},
		// 隐藏弹窗
		close: function(callback){
			var _this = this;
			_this.ui.mask.style.animation = "aui-fade-out .2s ease-out forwards";
			_this.ui.main.style.animation = "aui-slide-down-screen .2s ease-out forwards";
			var timer = setTimeout(function(){
				//_this.ui.picker.style.display = "none";
				document.querySelector(".aui-picker") ? document.querySelector(".aui-picker").parentNode.removeChild(document.querySelector(".aui-picker")) : '';
				typeof callback == "function" ?  callback({status: 0, data: _this.result}) : '';
				clearTimeout(timer);
			},200);
		},
		// 处理导航栏切换
		navTab: function(index, opt){
			var _this = this;
			var _opts = _this.opts(opt);
			for(var j = 0; j < _opts.layer; j++){
				_this.ui.navItem[j].classList.remove("active");
				_this.ui.list[j].classList.remove("active");
			}
			_this.ui.navItem[index].classList.add("active");
			_this.ui.list[index].classList.add("active");
			_this.ui.navborder.style.transition = 'left .3s';
			_this.ui.navborder.style.left = _this.ui.navItem[index].offsetLeft + (_this.ui.navItem[index].offsetWidth / 2) - (_this.ui.navborder.offsetWidth / 2) + "px";
		},
		// 一级分类点击
		firstItemClick: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(_this.ui["item0"]){
				for(var i = 0; i < _this.ui["item0"].length; i++){
					!(function(index){
						_this.ui["item0"][index].addEventListener("click", function(){
							var _self = this;
							var pindex = Number(_self.parentNode.getAttribute("index"));
							var index = Number(_self.getAttribute("index"));
							// 当前点击选项设为选中且其他取消选中
							for(var j = 0; j < _this.ui["item0"].length; j++){
								_this.ui["item0"][j].classList.remove("active");
							}
							_self.classList.add("active");
							for(var j = 0; j < _opts.layer; j++){
							}
							_this.ui.navItem[pindex].innerText = _opts.data[index].text;
							_this.ui.navborder.style.left = _this.ui.navItem[pindex].offsetLeft + (_this.ui.navItem[pindex].offsetWidth / 2) - (_this.ui.navborder.offsetWidth / 2) + "px";
							var timer = setTimeout(function(){
								if((pindex+1) < _opts.layer){ //若多级联动超过一级
									_this.ui.navItem[pindex+1].innerText = "请选择";
									_this.navTab(pindex + 1, opt);
									var item = '';
									if(_opts.data[index].children && _opts.data[index].children.length > 0){
										for(var i = 0; i < _opts.data[index].children.length; i++){
											item += '<div class="aui-picker-item" index="'+ i +'">'+ _opts.data[index].children[i].text +'</div>';
										}
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").innerHTML = "";
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").scrollTop = 0;
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").insertAdjacentHTML('beforeend', item);
									}
									else{
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").innerHTML = "";
									}
									_this.ui["item1"] = document.querySelectorAll(".aui-picker-list:nth-child(2) .aui-picker-list-warp .aui-picker-item");
									_this.twoItemClick(opt, callback);
								}
								_this['result'] = {};
								for(var i in _opts.data[index]){
									_this['result'][''+i] = _opts.data[index][i]
								}
								_this['firstPindex'] = pindex;
								_this['firstIndex'] = index;
								if(pindex+1 >= _opts.layer){
									typeof callback == "function" ?  callback({status: 1, data: _this.result}) : '';
								}
								clearTimeout(timer);
							},200);
						},false);
					})(i);
				}

			}
		},
		// 二级分类点击
		twoItemClick: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(_this.ui["item1"]){
				for(var i = 0; i < _this.ui["item1"].length; i++){
					!(function(index){
						_this.ui["item1"][index].addEventListener("click", function(){
							var _self = this;
							var pindex = Number(_self.parentNode.getAttribute("index"));
							var index = Number(_self.getAttribute("index"));
							// 当前点击选项设为选中且其他取消选中
							for(var j = 0; j < _this.ui["item1"].length; j++){
								_this.ui["item1"][j].classList.remove("active");
							}
							_self.classList.add("active");
							_this.ui.navItem[pindex].innerText = _opts.data[_this.firstIndex].children[index].text;
							_this.ui.navborder.style.left = _this.ui.navItem[pindex].offsetLeft + (_this.ui.navItem[pindex].offsetWidth / 2) - (_this.ui.navborder.offsetWidth / 2) + "px";
							var timer = setTimeout(function(){
								if((pindex+1) < _opts.layer){ //若多级联动超过二级
									_this.ui.navItem[pindex+1].innerText = "请选择";
									_this.navTab(pindex + 1, opt);
									var item = '';
									if(_opts.data[_this.firstIndex].children[index].children && _opts.data[_this.firstIndex].children[index].children.length > 0){
										for(var i = 0; i < _opts.data[_this.firstIndex].children[index].children.length; i++){
											item += '<div class="aui-picker-item" index="'+ i +'">'+ _opts.data[_this.firstIndex].children[index].children[i].text +'</div>';
										}
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").innerHTML = "";
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").scrollTop = 0;
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").insertAdjacentHTML('beforeend', item);
									}
									else{
										_this.ui.list[pindex+1].querySelector(".aui-picker-list-warp").innerHTML = "";
									}
									_this.ui["item2"] = document.querySelectorAll(".aui-picker-list:nth-child(3) .aui-picker-list-warp .aui-picker-item");
									_this.threeItemClick(opt, callback);
								}
								_this['result']["children"] = {};
								for(var i in _opts.data[_this.firstIndex].children[index]){
									_this['result']["children"][''+i] = _opts.data[_this.firstIndex].children[index][i]
								}
								_this['twoPindex'] = pindex;
								_this['twoIndex'] = index;
								if(pindex+1 >= _opts.layer){
									typeof callback == "function" ?  callback({status: 1, data: _this.result}) : '';
								}
								clearTimeout(timer);
							},200);
						},false);
					})(i);
				}
			}
		},
		// 三级分类点击
		threeItemClick: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(_this.ui["item2"]){
				for(var i = 0; i < _this.ui["item2"].length; i++){
					!(function(index){
						_this.ui["item2"][index].addEventListener("click", function(){
							var _self = this;
							var pindex = Number(_self.parentNode.getAttribute("index"));
							var index = Number(_self.getAttribute("index"));
							// 当前点击选项设为选中且其他取消选中
							for(var j = 0; j < _this.ui["item2"].length; j++){
								_this.ui["item2"][j].classList.remove("active");
							}
							_self.classList.add("active");
							_this.ui["navItem"][pindex].innerText = _opts.data[_this.firstIndex].children[_this.twoIndex].children[index].text;
							_this.ui.navborder.style.left = _this.ui.navItem[pindex].offsetLeft + (_this.ui.navItem[pindex].offsetWidth / 2) - (_this.ui.navborder.offsetWidth / 2) + "px";
							var timer = setTimeout(function(){
								_this['result']["children"]["children"] = {};
								for(var i in _opts.data[_this.firstIndex].children[_this.twoIndex].children[index]){
									_this['result']["children"]["children"][''+i] = _opts.data[_this.firstIndex].children[_this.twoIndex].children[index][i]
								}
								_this['threePindex'] = pindex;
								_this['threeIndex'] = index;
								typeof callback == "function" ?  callback({status: 1, data: _this.result}) : '';
								clearTimeout(timer);
							},200);
						},false);
					})(i);
				}
			}
		},
	}
})(aui, document, window);

/*
	 * popdownmenu底部弹出窗口
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.popdownMenu({
			mask: true,
			touchClose: true,
			html: '',
			theme: 1,
		},function(ret){
			console.log(ret.index);
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.popdownMenu = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				html: [], //--必选参数，菜单列表[{name: "", icon: ""}]
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-popdownmenu">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-popdownmenu-main">'
				+_opts.html
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-popdownmenu")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this.ui = {
				warp: document.querySelector(_opts.warp),
				popdownmenu: document.querySelector(".aui-popdownmenu"),
				main: document.querySelector(".aui-popdownmenu-main"),
				mask: document.querySelector(".aui-mask")
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			/*_this.ui.main.addEventListener("touchmove", function(e){
	            e.preventDefault();
	       	},{ passive: false });*/
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.close(opt, callback);
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
			_this.css(opt);
		},
		css: function(opt){ //设置特定样式
			var _this = this;
			var _opts = _this.opts(opt);
			_this.ui.main.style.left = (_this.ui.warp.offsetWidth - _this.ui.main.offsetWidth) / 2 + "px";
		},
		open: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			_this.ui.popdownmenu.classList.add("show");
			var timer = setTimeout(function() {
				typeof callback == "function" ?  callback({type: 'open'}) : '';
				clearTimeout(timer);
			},200);
		},
		close: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			_this.ui.popdownmenu.classList.add("hide");
			var timer = setTimeout(function() {
				_this.ui.popdownmenu ? _this.ui.popdownmenu.parentNode.removeChild(_this.ui.popdownmenu) : '';
				typeof callback == "function" ?  callback({type: 'close'}) : '';
				clearTimeout(timer);
			},200);
		}
	}
})(aui, document, window);

/*
	 * popover弹出菜单
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	 	aui.popover.open({
			warp: '.aui-header-right',
			items: [], //[{name: "", color: "", icon: "", fontSize: "", textAlign: ""}]
			mask: true,
			location: 'bottom'
		},function(ret){
			console.log(ret);
		})
 * ===============================
 */
!(function($, document, window, undefined){
	$.popover = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				items: [], //--必选参数，菜单列表[{name: "", color: "", icon: "iconfont icongfont-right", iconColor: '', img: "", fontSize: "", textAlign: ""}]
				location: 'top',
				mask: false,
				touchClose: true
			}
			return $.extend(opts, opt, true);
		},
		// 创建弹窗UI元素
		creat: function(opt){
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-popover">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-popover-main"><ul class="aui-popover-items"></ul><span class="aui-popover-triangle"></span></div>'
				+'</div>';
			document.querySelector('body').insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				warp: document.querySelector(_opts.warp),
				popover: document.querySelector('.aui-popover'),
				main: document.querySelector('.aui-popover-main'),
				mask: document.querySelector('.aui-mask'),
				lists: document.querySelector('.aui-popover-items'),
				triangle: document.querySelector('.aui-popover-triangle')
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			_opts.warp == 'body' ? _this.ui.triangle.parentNode.removeChild(_this.ui.triangle) : '';
			if(_opts.items.length <= 0) { $.toast({msg: "Parameter 'items' not set"}); return false; }
			var _html = '';
			for(var i in _opts.items){
				if($.isDefine(_opts.items[i].icon))
				{
					_html += '<li class="aui-popover-item row-after"><i class="iconfont '+ _opts.items[i].icon +'"></i><span>'+ _opts.items[i].name +'</span></li>'
				}
				else
				{
					if($.isDefine(_opts.items[i].img)){
						_html += '<li class="aui-popover-item row-after"><img src="'+ _opts.items[i].img +'"><span>'+ _opts.items[i].name +'</span></li>'
					}
					else{
						_html += '<li class="aui-popover-item row-after"><span>'+ _opts.items[i].name +'</span></li>'
					}
				}
			}
			_this.ui.lists.insertAdjacentHTML('beforeend', _html);
			_this.ui['item'] = document.querySelectorAll('.aui-popover-item');
			_this.css(opt);
		},
		//样式设置
		css: function(opt){
			var _this = this;
			var _opts = _this.opts(opt);
			//设置菜单弹窗位置
			switch (_opts.location){
				case 'top': //使用时设置弹窗显示到触发元素“上”方
					if(_this.ui.warp.offsetTop >= _this.ui.main.offsetHeight + 10)
					{ //触发元素距顶部距离大于弹窗高度 + 20，则设置位置位于触发元素“上”方
						_this.ui.main.style.cssText += "top: "+ (_this.ui.warp.offsetTop - _this.ui.main.offsetHeight - 10 )+"px;";
						_this.ui.triangle.style.cssText += "top: "+ (_this.ui.main.offsetHeight - 10) +"px;";
					}
					else
					{ //否则反之
						_this.ui.main.style.cssText += "top: "+ (_this.ui.warp.offsetTop + _this.ui.warp.offsetHeight + 10) +"px;";
						_this.ui.triangle.style.cssText += "top: -6px;";
					}
					break;
				case 'bottom': //使用时设置弹窗显示到触发元素“下”方
					if(window.screen.height - _this.ui.warp.offsetHeight - _this.ui.warp.offsetTop >= _this.ui.main.offsetHeight + 10)
					{ //触发元素距底部距离大于弹窗高度 + 20，则设置位置位于触发元素“下”方
						_this.ui.main.style.cssText += "top: "+ (_this.ui.warp.offsetTop + _this.ui.warp.offsetHeight + 10) +"px;";
						_this.ui.triangle.style.cssText += "top: -6px;";
					}
					else
					{ //否则反之
						_this.ui.main.style.cssText += "top: "+ (_this.ui.warp.offsetTop - _this.ui.main.offsetHeight - 10) +"px;";
						_this.ui.triangle.style.cssText += "top: "+ (_this.ui.main.offsetHeight - 10) +"px;";
					}
					break;
				default:
					break;
			}
			_this.ui.main.style.cssText += "left: "+ (_this.ui.warp.offsetLeft + _this.ui.warp.offsetWidth / 2 - _this.ui.main.offsetWidth / 2) +"px;";
			_this.ui.triangle.style.cssText += "left: "+ ((_this.ui.main.offsetWidth - 12) / 2) +"px;";
			if(_this.ui.main.offsetLeft + _this.ui.main.offsetWidth >= window.screen.width)
			{ //超出右边界
				_this.ui.main.style.cssText += "left: auto; right: 7px;";
				_this.ui.triangle.style.cssText += "left: auto; right: "+ (window.screen.width - _this.ui.warp.offsetLeft - _this.ui.warp.offsetWidth / 2 - Math.sqrt(_this.ui.triangle.offsetWidth * _this.ui.triangle.offsetWidth * 2) + 5) +"px;";
			}
			if(_this.ui.main.offsetLeft <= 0)
			{ //超出左边界
				_this.ui.main.style.cssText += "left: 7px; right: auto;";
				_this.ui.triangle.style.cssText += "left: "+ (_this.ui.warp.offsetLeft + _this.ui.warp.offsetWidth / 2 - Math.sqrt(_this.ui.triangle.offsetWidth * _this.ui.triangle.offsetWidth * 2) + 5) +"px; right: auto";
			}
			if(_opts.items.length > 0)
			{
				for(var i in _opts.items)
				{
					$.isDefine(_opts.items[i].color) ? _this.ui.item[i].querySelector("span").style.color = _opts.items[i].color : "";
					$.isDefine(_opts.items[i].fontSize) ? _this.ui.item[i].querySelector("span").style.fontSize = _opts.items[i].fontSize : "";
					$.isDefine(_opts.items[i].textAlign) ? _this.ui.item[i].style.textAlign = _opts.items[i].textAlign : "";
					$.isDefine(_opts.items[i].iconColor) ? _this.ui.item[i].querySelector("i").style.color = _opts.items[i].iconColor : "";
				}
			}
		},
		// 初始化弹窗
		open: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			if(!document.querySelector(".aui-popover")){
				_this.creat(opt);
			}
			_this.ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			_this.ui.popover.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			if(!$.isDefine(_opts.mask)){
				_this.ui.popover.addEventListener("click", function(e){
					!_opts.touchClose ? e.preventDefault() : _this.close();
				});
			}
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.close();
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			for (var i = 0; i < _opts.items.length; i++)
			{
				!function(j){
					$.touchDom(_this.ui.item[j], "#EFEFEF");
					_this.ui.item[j].addEventListener("click", function(e){
						var _self = this;
						_this.close();
						var timer = setTimeout(function(){
							clearTimeout(timer);
							typeof callback == "function" ?  callback({el: _self, index: j}) : '';
						},200);
					});
				}(i);
			}
		},
		// 隐藏弹窗
		close: function(){
			var _this = this;
			_this.ui.main.style.animation = "aui-fade-out .2s ease-out forwards";
			_this.ui.mask ? _this.ui.mask.style.animation = "aui-fade-out .2s ease-out forwards" : '';
			var timer = setTimeout(function() {
				_this.ui.popover ? _this.ui.popover.parentNode.removeChild(_this.ui.popover) : '';
				clearTimeout(timer);
			},200);
		},

	}
})(aui, document, window);

/*
	 * poptopmenu底部弹出窗口
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.poptopMenu({
			mask: true,
			touchClose: true,
			html: '',
			theme: 1,
		},function(ret){
			console.log(ret.index);
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.poptopMenu = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				html: [], //--必选参数，菜单列表[{name: "", icon: ""}]
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-poptopmenu">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-poptopmenu-main">'
				+_opts.html
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-poptopmenu")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				warp: document.querySelector(_opts.warp),
				poptopmenu: document.querySelector(".aui-poptopmenu"),
				main: document.querySelector(".aui-poptopmenu-main"),
				mask: document.querySelector(".aui-mask")
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.close(opt, callback);
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
			_this.css(opt);
		},
		css: function(opt){ //设置特定样式
			var _this = this;
			var _opts = _this.opts(opt);
		},
		open: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			_this.ui.poptopmenu.classList.add("show");
			var timer = setTimeout(function() {
				typeof callback == "function" ?  callback({type: 'open'}) : '';
				clearTimeout(timer);
			},200);
		},
		close: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			_this.ui.poptopmenu.classList.add("hide");
			var timer = setTimeout(function() {
				document.querySelectorAll(".aui-poptopmenu").length>0 ? _this.ui.poptopmenu.parentNode.removeChild(_this.ui.poptopmenu) : '';
				typeof callback == "function" ?  callback({type: 'close'}) : '';
				clearTimeout(timer);
			},200);
		}
	}
})(aui, document, window);

/*
	 * poster广告弹窗
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.poster({
			image: 'https://xbjz1.oss-cn-beijing.aliyuncs.com/upload/default/share.png'
		});
 * ===============================
 */
!(function($, document, window, undefined){
	var poster = new Object();
	poster = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				image: '', //图片
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-poster">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-poster-main">'
				+'<img class="aui-poster-img" src="'+ _opts.image +'">'
				+'<img class="aui-poster-close" src="https://xbjz1.oss-cn-beijing.aliyuncs.com/upload/default/gz-close.png">'
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-poster")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				poster: document.querySelector(".aui-poster"),
				main: document.querySelector(".aui-poster-main"),
				mask: document.querySelector(".aui-mask"),
				image: document.querySelector(".aui-poster-img"),
				closeBtn: document.querySelector(".aui-poster-close")
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			_this.ui.image.addEventListener("click", function(e){
				_this.hide(opt);
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback() : '';
				},200);
			});
			_this.ui.closeBtn.addEventListener("click", function(e){
				_this.hide(opt);
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback() : '';
				},200);
			});
			_this.ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.hide(opt);
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
		},
		show: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			_this.ui.poster.style.cssText = 'display: inline-block;';
			_this.ui.mask ? _this.ui.mask.style.animation = "aui-fade-in .2s ease-out forwards" : '';
			_this.ui.main.style.cssText = 'animation: aui-slide-up_to_middle .3s ease-out forwards;';
		},
		hide: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			_this.ui.poster.style.cssText = 'animation: aui-fade-out .2s ease-out forwards;';
			var timer = setTimeout(function() {
				_this.ui.poster ? _this.ui.poster.parentNode.removeChild(_this.ui.poster) : '';
				typeof callback == "function" ?  callback() : '';
				clearTimeout(timer);
			},150);
		}
	}
	$.poster = function(opt, callback){
		poster.show(opt, callback);
	};
})(aui, document, window);

/*
	 * progress进度条
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.progress.init({
			el: document.getElementById("canvas"), //绘制对象
			width: 150, //宽度
			height: 150, //高度
			lineWidth: 3, //线宽
			percent: 100, //绘制百分比, 范围[0, 100]
			forecolor: ['#FF7777', '#FF3333'], //前景色(运动圆环颜色)
			bgcolor: ["#FFF"], //背景色
			color: '#FF5555', //数字颜色
			fontSize: 20
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.progress = {
		opts(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				el: '', //绘制对象
				width: 150, //宽度
				height: 150, //高度
				lineWidth: 3, //线款
				percent: 100, //绘制百分比, 范围[0, 100]
				forecolor: ['#FF7777', '#FF3333'], //前景色(运动圆环颜色)
				bgcolor: ["#FFF"], //背景色
				color: '#FF5555', //数字颜色
				fontSize: 20
			}
			return $.extend(opts, opt, true);
		},
		init(opt) {
			var _this = this;
			_this.data = _this.opts(opt);
			_this.context = _this.data.el.getContext("2d");
			_this.data.el.style.cssText += 'width: ' + _this.data.width + 'px; height: ' + _this.data.height + 'px;';
			_this.center_x = _this.data.el.width / 2;
			_this.center_y = _this.data.el.height / 2;
			_this.rad = Math.PI*2/100;
			var speed = 0;
			//执行动画
			(function drawFrame(){
				window.requestAnimationFrame(drawFrame);
				_this.context.clearRect(0, 0, _this.data.el.width, _this.data.el.height);
				_this.backgroundCircle();
				_this.text(speed);
				_this.foregroundCircle(speed);
				if(speed >= _this.data.percent){return};
				speed += 1;
			}());
		},
		// 绘制背景圆圈
		backgroundCircle(){
			var _this = this;
			_this.context.save();
			_this.context.beginPath();
			_this.context.lineWidth = _this.data.lineWidth; //设置线宽
			var radius = _this.center_x - _this.context.lineWidth;
			_this.context.lineCap = "round";
			// _this.context.strokeStyle = _this.data.bgcolor;
			if(typeof _this.data.bgcolor === 'string')
			{
				_this.context.strokeStyle = _this.data.bgcolor;
			}
			else
			{
				var g = _this.context.createLinearGradient(_this.center_x, 0,_this.center_x, _this.center_y); //创建渐变对象  渐变开始点和渐变结束点
				_this.data.bgcolor.map(function(item, index){
					g.addColorStop(index / _this.data.bgcolor.length, item); //添加颜色点
				});
				_this.context.strokeStyle = g; //使用渐变对象作为圆环的颜色
			}
			_this.context.arc(_this.center_x, _this.center_y, radius, 0, Math.PI*2, false);
			_this.context.stroke();
			_this.context.closePath();
			_this.context.restore();
		},
		//绘制运动圆环
		foregroundCircle(n){
			var _this = this;
			_this.context.save();
			if(typeof _this.data.forecolor === 'string')
			{
				_this.context.strokeStyle = _this.data.forecolor;
			}
			else
			{
				var g = _this.context.createLinearGradient(_this.center_x, 0,_this.center_x, _this.center_y); //创建渐变对象  渐变开始点和渐变结束点
				_this.data.forecolor.map(function(item, index){
					g.addColorStop(index / _this.data.forecolor.length, item); //添加颜色点
				});
				_this.context.strokeStyle = g; //使用渐变对象作为圆环的颜色
			}
			_this.context.lineWidth = _this.data.lineWidth; //设置线宽
			_this.context.lineCap = "round";
			var radius = _this.center_x - _this.context.lineWidth;
			_this.context.beginPath();
			_this.context.arc(_this.center_x, _this.center_y, radius , -Math.PI/2, -Math.PI/2 +n*_this.rad, false); //用于绘制圆弧_this.context.arc(x坐标，y坐标，半径，起始角度，终止角度，顺时针/逆时针)
			_this.context.stroke();
			_this.context.closePath();
			_this.context.restore();
		},
		//绘制文字
		text(n){
			var _this = this;
			_this.context.save(); //save和restore可以保证样式属性只运用于该段canvas元素
			_this.context.fillStyle = _this.data.color;
			_this.context.font = _this.data.fontSize + "px Helvetica";
			var text_width = _this.context.measureText(n.toFixed(0)+"%").width;
			_this.context.fillText(n.toFixed(0)+"%", _this.center_x-text_width/2, _this.center_y + _this.data.fontSize/2);
			_this.context.restore();
		}
	}
})(aui, document, window);

/*
	 * 下拉列表选择框
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	 	aui.selectMenu.open({
			warp: '.orderby-items',
			layer: layer, // 1,2,3...
			mask: true,
			style: {
				itemStyle: {color: "#333", textAlign: 'left', isLine: false}
			},
			data: data,
			select: function(ret){ //点击时获取下级数据
				//console.log(ret); //{value: '0', text: '昨天'}
				// ajax() ——> return获取结果
			},
		}, function(ret){
			console.log(ret)
			aui.selectMenu.close(function(){
				...
			});
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.selectMenu = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				data: [], //--必选参数，菜单列表[{value: '', text: ''}]
				layer: 1, //--必选参数, 控制组件为几级，默认一级
				mask: true, //--可选参数, 是否显示遮罩
				touchClose: true, //--可选参数，遮罩是否可关闭窗口
				checkedMore: false, //--可选参数，是否多选(默认单选，多选限制最后一级可多选)
				before: null, //打开弹窗前执行
				select: null, //一级以上点击选择后执行，获取下级数据并return
				style: { //部分样式设置参数
					width: '',
					height: '',
					left: '',
					top: '',
					padding: '',
					background: '',
					borderRadius: '',
					itemStyle: {
						textAlign: '',
						fontSize: '',
						color: '',
						isLine: false, //是否显示分割线
					},
				},
			}
			return $.extend(opts, opt, true);
		},
		data: [], //存储数据（二维数组）
		// 创建弹窗UI元素
		creat: function(opt){
			var _this = this;
			var _opts = _this.opts(opt);
			_this.data[0] = _opts.data;
			var _html = '<div class="aui-selectmenu">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-selectmenu-main">'
				+'<ul class="aui-selectmenu-lists"></ul>'
				+'</div>'
				+'</div>';
			document.querySelector('body').insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				warp: document.querySelector(_opts.warp),
				selectmenu: document.querySelector('.aui-selectmenu'),
				main: document.querySelector('.aui-selectmenu-main'),
				mask: document.querySelector('.aui-mask'),
				lists: document.querySelector('.aui-selectmenu-lists'),
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			if(_opts.checkedMore){ //若参数配置组件可多选，添加重置+确定按钮
				var _html = '<div class="aui-selectmenu-down">'
					+'<div class="aui-selectmenu-down-btn reset">重置</div>'
					+'<div class="aui-selectmenu-down-btn confirm">确定</div>'
					+'</div>';
				_this.ui.main.insertAdjacentHTML('beforeend', _html);
				_this.ui["resetBtn"] = document.querySelector(".aui-selectmenu-down-btn.reset");
				_this.ui["confirmBtn"] = document.querySelector(".aui-selectmenu-down-btn.confirm");
			}
			var lists = "";
			//根据layer添加列表
			for(var i = 0; i < _opts.layer; i++){
				lists += '<li class="aui-selectmenu-list" index="'+ i +'"><div class="aui-selectmenu-list-warp" index="'+ i +'"></div></li>';
			}
			_this.ui.lists.insertAdjacentHTML('beforeend', lists);
			_this.ui["list"] = document.querySelectorAll(".aui-selectmenu-list");
			//添加一级列表内容
			var item = '';
			if(_opts.data && _opts.data.length > 0){
				for(var i = 0; i < _opts.data.length; i++){
					if(_opts.style.itemStyle && _opts.style.itemStyle.isLine && i!=_opts.data.length-1){
						item += '<div class="aui-selectmenu-item row-after" index="'+ i +'">'+ _opts.data[i].text +'</div>';
					}
					else{
						item += '<div class="aui-selectmenu-item" index="'+ i +'">'+ _opts.data[i].text +'</div>';
					}
				}
				_this.ui.list[0].querySelector(".aui-selectmenu-list-warp").insertAdjacentHTML('beforeend', item);
				_this.ui["item0"] = document.querySelectorAll(".aui-selectmenu-list:nth-child(1) .aui-selectmenu-list-warp .aui-selectmenu-item");
			}
			_this.css(opt);
		},
		//样式设置
		css: function(opt){
			var _this = this;
			var _opts = _this.opts(opt);
			//设置弹窗样式
			if(_opts.style.top){
				_this.ui.main.style.cssText += 'top: '+ _opts.style.top +';';
			}
			else{
				_this.ui.main.style.cssText += 'top: '+ (_this.ui.warp.offsetTop + _this.ui.warp.offsetHeight) +'px;';
			}
			for(var m = 0; m < _this.data.length; m++){
				_this.ui.lists.style.background = _this.ui.list[m].style.background = _opts.style.background ? _opts.style.background : '';
			}
			for(var i in _opts.style){
				if(i != 'itemStyle'){
					_this.ui.main.style[i] = _opts.style[i];
				}
				else{
					for(var j in _opts.style[i]){
						for(var m = 0; m < _this.data.length; m++){
							for(var n = 0; n < _this.data[m].length; n++){
								_this.ui.list[m].querySelectorAll(".aui-selectmenu-item")[n].style[j] = _opts.style[i][j];
							}
						}
					}
				}
			}
		},
		// 打开弹窗
		open: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			typeof _opts.before == "function" ?  _opts.before() : '';
			if(!document.querySelector(".aui-selectmenu")){
				_this.creat(opt);
			}
			_this.ui.selectmenu.style.display = 'inline-block';
			_this.ui.selectmenu.classList.remove("hide");
			_this.ui.selectmenu.classList.add("show");
			_this.click(opt, callback); //引用列表点击事件
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.close(callback);
			});
			if(_opts.checkedMore){
				//重置
				_this.ui.resetBtn.onclick = function(){
					for(var n = 0; n < _this.data[_this.data.length-1].length; n++){
						_this.ui.list[_this.data.length-1].querySelectorAll(".aui-selectmenu-item")[n].classList.remove("active");
					}
					callback({status: 0, data: []})
				}
				//确定
				_this.ui.confirmBtn.onclick = function(){
					callback({status: 0, data: []})
				}
			}
		},
		// 隐藏弹窗
		close: function(callback){
			var _this = this;
			_this.ui.selectmenu.classList.remove("show");
			_this.ui.selectmenu.classList.add("hide");
			var timer = setTimeout(function(){
				_this.ui && _this.ui.selectmenu ? _this.ui.selectmenu.style.display = 'none' : '';
				//_this.ui.selectmenu ? _this.ui.selectmenu.parentNode.removeChild(_this.ui.selectmenu) : '';
				typeof callback == "function" ?  callback() : '';
				clearTimeout(timer);
			},200);
		},
		//注销弹窗
		remove: function(callback){
			var _this = this;
			_this.data = [];
			document.querySelectorAll('.aui-selectmenu').length > 0 ? _this.ui.selectmenu.parentNode.removeChild(_this.ui.selectmenu) : '';
			typeof callback == "function" ?  callback() : '';
		},
		//item列表点击事件
		click: function(opt, callback){
			var _this = this;
			var _opts = _this.opts(opt);
			for(var m = 0; m < _opts.layer; m++){
				!(function(M){
					var items = _this.ui.list[M].querySelectorAll(".aui-selectmenu-item");
					for(var i = 0; i < items.length; i++){
						!(function(i){
							items[i].onclick = function(){
								var _self = this;
								var pindex = Number(_self.parentNode.getAttribute("index"));
								var index = Number(_self.getAttribute("index"));
								//当前点击选项设为选中且其他取消选中（多选模式下点击已选项取消当前选中状态）
								function changeClass(){
									if(!_opts.checkedMore){ //单选
										for(var j = 0; j < _this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item").length; j++){
											_this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item")[j].classList.remove("active");
										}
										_self.classList.add("active");
									}
									else{ //多选
										if(_opts.layer > pindex+1){ //存在下级
											for(var j = 0; j < _this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item").length; j++){
												_this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item")[j].classList.remove("active");
											}
											_self.classList.add("active");
										}
										else{ //最后一级
											if(_this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item")[index].className.indexOf("active")>=0){
												_self.classList.remove("active");
											}
											else{
												_self.classList.add("active");
											}
										}
									}
								}
								//获取下级列表数据
								var data = typeof _opts.select == "function" ? _opts.select({status: 0, pindex: pindex, layer: _opts.layer, data: _this.data[pindex][index]}) : [];
								if(_opts.layer > pindex+1 && aui.isDefine(data)){ //存在下级
									var arr = []
									for(var w = 0; w < _this.data.length; w++){
										w <= pindex ? arr.push(_this.data[w]) : '';
									}
									_this.data = arr;
									_this.data[pindex+1] = data;
									var item = '';
									for(var n = 0; n < _this.data[pindex+1].length; n++){
										//判断是否显示分割线
										if(_opts.style.itemStyle && _opts.style.itemStyle.isLine && i != _this.data[pindex+1][n].length-1){
											item += '<div class="aui-selectmenu-item row-after" index="'+ n +'">'+ _this.data[pindex+1][n].text +'</div>';
										}
										else{
											item += '<div class="aui-selectmenu-item" index="'+ n +'">'+ _this.data[pindex+1][n].text +'</div>';
										}
									}
									//点击选择时先清空下级原有元素，再插入新元素
									_this.ui.list[pindex+1].querySelector(".aui-selectmenu-list-warp").innerHTML = '';
									if(data.length > 0)
									{
										_this.ui.list[pindex+1].querySelector(".aui-selectmenu-list-warp").insertAdjacentHTML('beforeend', item);
									}
									_this.css(_opts); // 为新添加的列表元素设置样式
									if(_this.ui.list[pindex+1].offsetLeft < window.screen.width)
									{ //关闭当前选择列表之后的所有下级列表
										for(var n = 0; n < _this.ui.list.length; n++){
											if(n >= pindex){
												_this.ui.list[n+1] ? _this.ui.list[n+1].style.left = "100vw" : '';
												_this.ui.main.style.height = _opts.checkedMore ? _this.ui.list[pindex].offsetHeight + 50 + "px" : _this.ui.list[pindex].offsetHeight + 'px';
												_this.ui.lists.style.height = _this.ui.list[pindex].offsetHeight + "px";
											}
										}
									}
									//打开当前选择列表的下级列表
									openNextList();
									function openNextList(){
										changeClass();// 当前点击选项设为选中且其他取消选中
										var left = _opts.layer >= 4 ? window.screen.width / _opts.layer : 100;
										_this.ui.list[pindex+1].style.left = _this.ui.list[pindex].offsetLeft + left + "px";
										_this.ui.list[pindex+1].style.width = _this.ui.list[pindex].offsetWidth - left + "px";
										if(_this.ui.list[pindex + 1].offsetHeight > _this.ui.list[pindex].offsetHeight)
										{ //当下级列表总高度大于当前列表总高度
											_this.ui.main.style.height = _opts.checkedMore ? _this.ui.list[pindex + 1].offsetHeight + "px" : _this.ui.list[pindex + 1].offsetHeight + "px";
											_this.ui.lists.style.height = _this.ui.list[pindex + 1].offsetHeight + "px";
										}
										else{
											_this.ui.main.style.height = _opts.checkedMore ? _this.ui.list[pindex].offsetHeight + "px" : _this.ui.list[pindex].offsetHeight + "px";
											_this.ui.lists.style.height =
												_this.ui.list[pindex + 1].style.height = _this.ui.list[pindex].offsetHeight + "px";
										}
									}
									//为新添加的列表元素添加事件
									var newitems = _this.ui.list[pindex+1].querySelectorAll(".aui-selectmenu-item");
									for(var g = 0; g < newitems.length; g++){
										!(function(G){
											newitems[G].onclick = items[0].onclick;
										})(g);
									}
								}
								else
								{ //当前点击为最后一级——> 执行回调，返回所选数据
									changeClass();// 当前点击选项设为选中且其他取消选中
									var result = [];
									for(var j = 0; j < _this.data.length; j++){
										result[j] = [];
										for(var n = 0; n < _this.data[j].length; n++){
											if(_this.ui.list[j].querySelectorAll(".aui-selectmenu-item")[n].className.indexOf("active")>=0){
												result[j].push(_this.data[j][n]);
											}
										}
									}
									//当最后一级已选内容为空
									if(result[result.length -1].length == 0){
										result = [];
									}
									if(!_opts.checkedMore){ //单选模式下最后一级列表内容点击选择后就执行回调
										callback({status: 0, data: result})
									}
									else{
										//重置
										_this.ui.resetBtn.onclick = function(){
											for(var n = 0; n < _this.data[pindex].length; n++){
												_this.ui.list[pindex].querySelectorAll(".aui-selectmenu-item")[n].classList.remove("active");
											}
											result = [];
											callback({status: 0, data: result})
										}
										//确定
										_this.ui.confirmBtn.onclick = function(){
											callback({status: 0, data: result})
										}
									}
								}
							}
						})(i);
					}
				})(m);
			}
		}
	}
})(aui, document, window);

/*
	 * 侧滑菜单
	 * versions 1.0.0
	 * cl15095344637@163.com
	 * @example:
	    aui.sidemenu.init({
	    	warp: '.aui-container',
	    	content: '#aui-sidemenu-wapper',
	    	position: _this.position.data[_this.position.currentIndex].position,
	    	moveType: _this.list.data[_this.list.currentIndex].moveType,
	    	moves: ['.aui-container'],
	    	mask: true,
	    	maskTapClose: true,
	    	drag: {
	    		use: true,
	    		//start: _this.dragcallback,
	    		//move: _this.dragcallback,
	    		end: function(ret){
	    			console.log(ret)
	    		}
	    	},
	    	style: {
	    		w: '70vw',
	    		h: '100vh',
	    		bg: '#333'
	    	},
	    }).then(function(ret){
	    	console.log(ret)
	    });
		aui.sidemenu.setData({ //设置配置数据
			position: 'right',
			moveType: 'main-move'
		}).then(function(ret){
			//console.log(ret)
		});
 * ===============================
 */
!(function($, document, window, undefined){
	$.sidemenu = {
		data: {
			warp: 'body', //--可选参数，父容器
			content: '', //--必选参数，侧滑菜单元素
			moves: [], //--可选参数，跟随拖动元素；[header——页面头部, .content——页面内容部分] (moveType设置为"all-move" 或 "menu-move"时，此参数必须配置)
			moveType: 'main-move', // ['main-move': '主页面移动，侧滑菜单固定'] ['menu-move': '侧滑菜单移动，主页面固定'] ['all-move': '主页面+侧滑菜单都移动']
			mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
			maskTapClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
			position: 'left', //--可选参数，侧滑菜单初始化位置，默认位于页面左侧 [left: '左侧', right: '右侧']
			speed: 10, //--可选参数，打开、关闭页面速度[值越大，速度越快]
			drag: {
				use: true, //--可选参数，是否开启拖动打开、关闭菜单[true: 开启 | false: 关闭]
				start: null, //--可选参数，开始拖动回调
				move: null, //--可选参数，拖动中回调
				end: null, //--可选参数，拖动结束
			},
			style: {
				w: '80vw',
				h: '100vh',
				bg: '#333'
			},
		},
		opts: function(opt){
			return $.extend(this.data, opt, true);
		},
		init: function(opt){ //初始化
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				Promise.all([
					_this._creat(opt),
					_this._setStyle(opt),
					_this.drag(opt)
				]).then(function(ret){
					//console.log(ret);
					resolve({status: 0, data: {event: 'init'}});
				});
			});
		},
		setData: function(opt){ //设置菜单配置数据
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				Promise.all([
					_this._creat(opt),
					_this._setStyle(opt),
					_this.drag(opt)
				]).then(function(ret){
					//console.log(ret);
					resolve({status: 0, data: {event: 'setData'}});
				});
			});
		},
		query: function(el){
			return document.querySelector(el);
		},
		_creat: function(opt){ //创建
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				var _html = '<aside class="aui-sidemenu hide">'
					+'<div class="aui-sidemenu-main">'+ _this.query(_this.data.content).innerHTML || '' +'</div>'
					+'</aside>';
				_this.query(".aui-sidemenu") ? _this.query(".aui-sidemenu").parentNode.removeChild(_this.query(".aui-sidemenu")) : '';
				_this.query('body').insertAdjacentHTML('beforeend', _html);
				_this.query(_this.data.content).style.display = 'none';
				_this.ui = {
					warp: _this.query(_this.data.warp) || '',
					container: _this.query('.aui-sidemenu') || '',
					main: _this.query('.aui-sidemenu-main') || '',
					mask: _this.query('.aui-sidemenu').querySelector('.aui-mask') || ''
				};
				resolve({status: 0, data: {event: 'creat'}});
			});
		},
		_setStyle: function(opt){ //设置样式
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				switch (_this.data.moveType){
					case 'main-move': //主页面移动，菜单不动
						_this.ui.container.classList.add('fixed');
						_this.ui.container.classList.remove('move');
						_this.ui.container.classList.remove('scale');
						_this.query('body').style.background = '';
						break;
					case 'menu-move': //主页面不动，菜单移动
						_this.ui.container.classList.remove('fixed');
						_this.ui.container.classList.add('move');
						_this.ui.container.classList.remove('scale');
						_this.query('body').style.background = '';
						break;
					case 'all-move': //整体移动
						_this.ui.container.classList.remove('fixed');
						_this.ui.container.classList.add('move');
						_this.ui.container.classList.remove('scale');
						_this.query('body').style.background = '';
						break;
					case 'scale-move': //缩放式侧滑(类手机QQ)
						_this.ui.container.classList.add('fixed');
						_this.ui.container.classList.add('scale');
						_this.ui.container.classList.remove('move');
						_this.ui.warp.style.transformOrigin = _this.data.position + ' center';
						_this.query('body').style.background = _this.data.style.bg;
						break;
					default:
						break;
				}
				switch (_this.data.position){
					case 'left': //位于页面左侧
						_this.ui.container.classList.add('left');
						_this.ui.container.classList.remove('right');
						break;
					case 'right': //位于页面右侧
						_this.ui.container.classList.remove('left');
						_this.ui.container.classList.add('right');
						break;
					default:
						break;
				}
				_this.ui.warp.style.position = 'relative';
				_this.ui.warp.style.zIndex = '1';
				_this.ui.warp.style.transform = '';
				_this.ui.container.style.width = _this.data.style.w;
				_this.ui.container.style.height = _this.data.style.h;
				_this.ui.container.style.background = _this.data.style.bg;
				resolve({status: 0, data: {event: 'setStyle'}});
			});
		},
		open: function(opt){ //打开
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				var i = Math.abs(_this.oLeft);
				clearInterval(window.openTimer);
				clearInterval(window.closeTimer);
				window.openTimer = setInterval(function(){
					i += _this.data.speed;
					switch (_this.data.position){
						case 'left': //位于页面左侧
							if(i >= _this.ui.container.offsetWidth)
							{
								i = _this.ui.container.offsetWidth;
								_this.oLeft = _this.ui.container.offsetWidth;
								clearInterval(window.openTimer);
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.container.style.left = -_this.ui.container.offsetWidth + i + 'px';
							}
							if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
							{
								_this.data.moves.forEach(function(item, index){
									_this.query(item).style.left = i + 'px';
								})
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.mask ? _this.ui.mask.style.left = i - 1 + 'px' : '';
							}
							if(_this.data.moveType == 'scale-move')
							{
								if(i * 0.2 / _this.ui.container.offsetWidth < 0){
									var scaleNum = 0;
								}
								else if(i * 0.2 / _this.ui.container.offsetWidth > 0.2){
									var scaleNum = 0.2;
								}
								else{
									var scaleNum = i * 0.2 / _this.ui.container.offsetWidth;
								}
								scaleNum = Math.floor(scaleNum * 100) / 100;
								_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
								_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
							}
							break;
						case 'right': //位于页面右侧
							if(i >= _this.ui.container.offsetWidth)
							{
								i = _this.ui.container.offsetWidth;
								_this.oLeft = -_this.ui.container.offsetWidth;
								clearInterval(window.openTimer);
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.container.style.right = -_this.ui.container.offsetWidth + i + 'px';
							}
							if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
							{
								_this.data.moves.forEach(function(item, index){
									_this.query(item).style.left = 'auto';
									_this.query(item).style.right = i + 'px';
								})
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.mask ? _this.ui.mask.style.left = 'auto' : '';
								_this.ui.mask ? _this.ui.mask.style.right = i - 1 + 'px' : '';
							}
							if(_this.data.moveType == 'scale-move')
							{
								if(i * 0.2 / _this.ui.container.offsetWidth < 0){
									var scaleNum = 0;
								}
								else if(i * 0.2 / _this.ui.container.offsetWidth > 0.2){
									var scaleNum = 0.2;
								}
								else{
									var scaleNum = i * 0.2 / _this.ui.container.offsetWidth;
								}
								scaleNum = Math.floor(scaleNum * 100) / 100;
								_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
								_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
							}
							break;
						default:
							break;
					}

				},5);
				_this._show(opt);
				resolve({status: 0, data: {event: 'open'}});
			});
		},
		close: function(opt){ //关闭
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				var i = Math.abs(_this.oLeft);
				clearInterval(window.openTimer);
				clearInterval(window.closeTimer);
				window.closeTimer = setInterval(function(){
					i -= _this.data.speed;
					switch (_this.data.position){
						case 'left': //位于页面左侧
							if(i <= 0)
							{
								i = 0;
								_this.oLeft = 0;
								clearInterval(window.closeTimer);
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.container.style.left = -_this.ui.container.offsetWidth + i + 'px';
							}
							if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
							{
								_this.data.moves.forEach(function(item, index){
									_this.query(item).style.left = i + 'px';
								})
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.mask ? _this.ui.mask.style.left = i - 1 + 'px' : '';
							}
							if(_this.data.moveType == 'scale-move')
							{
								if(i * 0.2 / _this.ui.container.offsetWidth < 0){
									var scaleNum = 0;
								}
								else if(i * 0.2 / _this.ui.container.offsetWidth > 0.2){
									var scaleNum = 0.2;
								}
								else{
									var scaleNum = i * 0.2 / _this.ui.container.offsetWidth;
								}
								scaleNum = Math.floor(scaleNum * 100) / 100;
								_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
								_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
							}
							break;
						case 'right': //位于页面右侧
							if(i <= 0)
							{
								i = 0;
								_this.oLeft = 0;
								clearInterval(window.closeTimer);
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.container.style.right = -_this.ui.container.offsetWidth + i + 'px';
							}
							if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
							{
								_this.data.moves.forEach(function(item, index){
									_this.query(item).style.left = 'auto';
									_this.query(item).style.right = i + 'px';
								})
							}
							if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
							{
								_this.ui.mask ? _this.ui.mask.style.left = 'auto' : '';
								_this.ui.mask ? _this.ui.mask.style.right = i - 1 + 'px' : '';
							}
							if(_this.data.moveType == 'scale-move')
							{
								if(i * 0.2 / _this.ui.container.offsetWidth < 0){
									var scaleNum = 0;
								}
								else if(i * 0.2 / _this.ui.container.offsetWidth > 0.2){
									var scaleNum = 0.2;
								}
								else{
									var scaleNum = i * 0.2 / _this.ui.container.offsetWidth;
								}
								scaleNum = Math.floor(scaleNum * 100) / 100;
								_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
								_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
							}
							break;
						default:
							break;
					}

				},5)
				_this._hide(opt);
				resolve({status: 0, data: {event: 'close'}});
			});
		},
		_show: function(opt){ //显示
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				_this.ui.container.classList.add('show');
				_this.ui.container.classList.remove('hide');
				!_this.query(".aui-mask") ? _this.ui.warp.insertAdjacentHTML('beforeend', '<div class="aui-mask"></div>') : '';
				_this.ui.mask = _this.query(".aui-mask");
				if(_this.data.maskTapClose)
				{
					if($.isDefine(_this.ui.mask))
					{
						_this.ui.mask.onclick = function(){ //点击遮罩关闭菜单
							_this.close({speed: 10});
						}
					}
				}
				resolve({status: 0, data: {event: 'show'}});
			});
		},
		_hide: function(opt){ //隐藏
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				_this.ui.container.classList.add('hide');
				_this.ui.container.classList.remove('show');
				_this.query(".aui-mask") ? _this.query(".aui-mask").parentNode.removeChild(_this.query(".aui-mask")) : '';
				resolve({status: 0, data: {event: 'hide'}});
			});
		},
		drag: function(opt){ //拖动
			var _this = this;
			_this.data = _this.opts(opt);
			return new Promise(function(resolve, reject){
				_this.oL = 0, _this.oLeft = 0, _this.oT = 0, _this.oTop = 0;
				if(!_this.data.drag.use){return;}
				_this._touchstart(opt);
				_this._touchmove(opt);
				_this._touchend(opt);
				resolve({status: 0, data: {event: 'drag'}});
			});
		},
		_touchstart: function(opt){
			var _this = this;
			_this.data = _this.opts(opt);
			_this.ui.warp.ontouchstart = function(e){
				var ev = e || window.event;
				var touch = ev.targetTouches[0];
				switch (_this.data.position){
					case 'left': //位于页面左侧
						if(_this.oLeft == 0)
						{
							_this.oL = touch.clientX;
						}
						else{
							_this.oL = touch.clientX - (_this.ui.container.offsetWidth - Math.abs(_this.ui.container.offsetLeft));
						}
						break;
					case 'right': //位于页面右侧
						if(_this.oLeft == 0)
						{
							_this.oL = touch.clientX;
						}
						else{
							_this.oL = touch.clientX + (_this.ui.warp.offsetWidth - Math.abs(_this.ui.container.offsetLeft));
						}
						break;
					default:
						break;
				}
				_this.oT = touch.clientY - _this.ui.container.offsetTop;
				clearInterval(window.openTimer);
				clearInterval(window.closeTimer);
				typeof _this.data.drag.start === 'function' ? _this.data.drag.start({status: 0, msg: '拖动开始', data: {event: 'drag'}}) : '';
			}
		},
		_touchmove: function(opt){
			var _this = this;
			_this.data = _this.opts(opt);
			_this.ui.warp.ontouchmove = function(e) {
				var _self = this;
				var ev = e || window.event;
				var touch = ev.targetTouches[0];
				_this.oLeft = touch.clientX - _this.oL;
				_this.oTop = touch.clientY - _this.oT;
				if(Math.abs(_this.oTop) - Math.abs(_this.oLeft) > 1){_this.oLeft = _this.oTop = 0; return;}
				switch (_this.data.position){
					case 'left': //位于页面左侧
						if(_this.oLeft < 0)
						{
							_this.oLeft = 0;
							_this._hide(opt);
						}
						else if(_this.oLeft > _this.ui.container.offsetWidth)
						{
							_this.oLeft = _this.ui.container.offsetWidth;
							_this._show(opt);
						}
						if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
						{
							_this.ui.container.style.left = -_this.ui.container.offsetWidth + _this.oLeft + 'px';
						}
						if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
						{
							_this.data.moves.forEach(function(item, index){
								_this.query(item).style.left = _this.oLeft + 'px';
								_this.query(item).style.right = 'auto';
							})
						}
						if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
						{
							_this.ui.mask ? _this.ui.mask.style.left = _this.oLeft - 1 + 'px' : '';
						}
						if(_this.data.moveType == 'scale-move')
						{
							if(_this.oLeft * 0.2 / _this.ui.container.offsetWidth < 0){
								var scaleNum = 0;
							}
							else if(_this.oLeft * 0.2 / _this.ui.container.offsetWidth > 0.2){
								var scaleNum = 0.2;
							}
							else{
								var scaleNum = _this.oLeft * 0.2 / _this.ui.container.offsetWidth;
							}
							scaleNum = Math.floor(scaleNum * 1000) / 1000;
							_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
							_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
						}
						break;
					case 'right': //位于页面右侧
						if(_this.oLeft < -_this.ui.container.offsetWidth)
						{
							_this.oLeft = -_this.ui.container.offsetWidth;
							_this._show(opt);
						}
						else if(_this.oLeft >= 0)
						{
							_this.oLeft = 0;
							_this._hide(opt);
						}
						if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'all-move')
						{
							_this.ui.container.style.right = -_this.ui.container.offsetWidth - _this.oLeft + 'px';
						}
						if(_this.data.moveType == 'main-move' || _this.data.moveType == 'all-move' || _this.data.moveType == 'scale-move')
						{
							_this.data.moves.forEach(function(item, index){
								_this.query(item).style.left = 'auto';
								_this.query(item).style.right = -_this.oLeft + 'px';
							})
						}
						if(_this.data.moveType == 'menu-move' || _this.data.moveType == 'main-move' || _this.data.moveType == 'all-move')
						{
							_this.ui.mask ? _this.ui.mask.style.left = 'auto' : '';
							_this.ui.mask ? _this.ui.mask.style.right = -_this.oLeft - 1 + 'px' : '';
						}
						if(_this.data.moveType == 'scale-move')
						{
							if(-_this.oLeft * 0.2 / _this.ui.container.offsetWidth < 0){
								var scaleNum = 0;
							}
							else if(-_this.oLeft * 0.2 / _this.ui.container.offsetWidth > 0.2){
								var scaleNum = 0.2;
							}
							else{
								var scaleNum = -_this.oLeft * 0.2 / _this.ui.container.offsetWidth;
							}
							scaleNum = Math.floor(scaleNum * 1000) / 1000;
							_this.ui.container.style.transform = 'scale('+ (0.8 + scaleNum) +')';
							_this.ui.warp.style.transform = 'scale('+  (1 - scaleNum) +')';
						}
						break;
					default:
						break;
				}
				typeof _this.data.drag.move === 'function' ? _this.data.drag.move({status: 0, msg: '拖动中', data: {event: 'drag'}}) : '';
			}
		},
		_touchend: function(opt){
			var _this = this;
			_this.data = _this.opts(opt);
			_this.ui.warp.ontouchend = function() {
				var _self = this;
				if(Math.abs(_this.oTop) - Math.abs(_this.oLeft) > 1){_this.oLeft = _this.oTop = 0; _this.close({speed: 10}); return;}
				switch (_this.data.position){
					case 'left': //位于页面左侧
						if(_this.oLeft < _this.ui.container.offsetWidth / 2)
						{
							_this.close(opt);
							var msg = '菜单关闭';
						}
						else
						{
							_this.open(opt);
							var msg = '菜单开启';
						}
						break;
					case 'right': //位于页面右侧
						if(Math.abs(_this.oLeft) < _this.ui.container.offsetWidth / 2)
						{
							_this.close(opt);
							var msg = '菜单关闭';
						}
						else
						{
							_this.open(opt);
							var msg = '菜单开启';
						}
						break;
					default:
						break;
				}
				typeof _this.data.drag.end === 'function' ? _this.data.drag.end({status: 0, msg: msg, data: {event: 'drag'}}) : '';
			}
		},
	}
})(aui, document, window);

/*
	雪花飘落效果
 */
!(function($, document, window, undefined){
	var pluginName = "snow",
		defaults = {
			warp: 'body',
			el: '<i class="iconfont iconxuehua snow"></i>',
			num: 100,
			width: 0, //（固定参数） 直径
			minWidth: 5, // 最大直径
			maxWidth: 20, // 最小直径
			opacity: 0, //（固定参数） 透明度
			x: 0, //（固定参数） 水平位置
			y: 0, //（固定参数） 重置位置
			z: 0, //（固定参数） z轴位置
			sx: 0, //（固定参数） 水平速度
			sy: 0, //（固定参数） 垂直速度
			dir: 'r', // 倾斜方向
			isSwing: false, // 是否左右摇摆
			stepSx: 0.02, // 左右摇摆的步长
			swingRadian: 1, // 左右摇摆的正弦函数x变量
			swingStep: 0.01, // 左右摇摆的正弦x步长
			maxSpeed: 4, // 最大速度
			minSpeed: 1, // 最小速度
			quickMaxSpeed: 10, // 快速划过的最大速度
			quickMinSpeed: 8, // 快速划过的最小速度
			quickWidth: 20, // 快速划过的宽度
			quickOpacity: 0.2, // 快速划过的透明度
			windowWidth: window.innerWidth, //窗口宽度
			windowHeight: window.innerHeight //窗口高度
		};

	var Snow = function(opt) {
		this._defaults = defaults;
		this._name = pluginName;
		this.opts = opt;
		this.init();
	}

	Snow.prototype = {
		init: function (reset) {
			var isQuick = Math.random() > 0.8;
			this.isSwing = Math.random() > 0.8;
			this.width = isQuick ? this.opts.quickWidth : Math.floor(Math.random() * this.opts.maxWidth + this.opts.minWidth);
			this.opacity = isQuick ? this.opts.quickOpacity : Math.random();
			this.x = Math.floor(Math.random() * (this.opts.windowWidth - this.width));
			this.y = Math.floor(Math.random() * (this.opts.windowHeight - this.width));

			if(reset && Math.random() > 0.8) {
				this.x = -this.width;
			}
			else if(reset) {
				this.y = -this.width;
			}

			this.sy = isQuick
				? Math.random() * this.opts.quickMaxSpeed + this.opts.quickMinSpeed
				: Math.random() * this.opts.maxSpeed + this.opts.minSpeed;
			this.sx = this.opts.dir === 'r' ? this.sy : -this.sy;
			this.z = isQuick ? Math.random() * 300 + 200 : 0;
			this.swingStep = 0.01 * Math.random();
			this.swingRadian = Math.random() * (1.1 - 0.9) + 0.9;
		},
		setStyle: function() {
			this.el.style.cssText = `
        		position: absolute;
	            left: 0;
	            top: 0;
	            display: inline-block;
	            width: ${this.width}px;
	            height: ${this.width}px;
	            opacity: ${this.opacity};
	            pointer-events: none;
	            transform: translate(${this.x}px, ${this.y}px);
        	`;
			this.el.querySelector('i').style.cssText = `
        		width: ${this.width}px;
	            height: ${this.width}px;
	            font-size: ${this.width}px;
	            display: inline-block;
	            color: #FFFFFF;
        	`;
		},
		render: function() {
			this.el = document.createElement('span');
			this.el.classList.add('snow-item');
			this.el.innerHTML = this.opts.el;
			this.setStyle();
			document.querySelector(this.opts.warp).appendChild(this.el);
		},
		move: function() {
			if(this.isSwing) {
				if(this.swingRadian > 1.1 || this.swingRadian < 0.9) {
					this.swingStep = -this.swingStep;
				}
				this.swingRadian += this.swingStep;
				this.x += this.sx * Math.sin(this.swingRadian * Math.PI);
				this.y -= this.sy * Math.cos(this.swingRadian * Math.PI);
			}
			else {
				this.x += this.sx;
				this.y += this.sy;
			}
			// 完全离开窗口执行init初始化，另外还需要修改一下init方法，因为重新出现我们是希望它的y坐标为0或者小于0，这样就不会又凭空出现的感觉，而是从天上下来的
			if(this.x < -this.width
				|| this.x > this.opts.windowWidth
				|| this.y > this.opts.windowHeight
			) {
				this.init(true);
				this.setStyle();
			}
			this.el.style.transform = `translate3d(${this.x}px, ${this.y}px, ${this.z}px)`;
		}
	};

	var Snows = function(opt) {
		this.opts = $.extend(defaults, opt, true);
		this.snowList = [];
		this.createSnows();
		this.moveSnow();
	};

	Snows.prototype = {
		createSnows: function() {
			this.snowList = [];

			for(var i = 0; i < this.opts.num; i++) {
				var snow = new Snow(this.opts);
				snow.render();
				this.snowList.push(snow);
			}
		},
		moveSnow: function() {
			window.requestAnimationFrame(() => {
				this.snowList.forEach((item) => {
					item.move();
				})
				this.moveSnow();
			})
		}
	};
	//将snow挂载到aui
	$.snows = function(opt) {
		new Snows(opt);
	};
})(aui, document, window);

/*
	微信网页相关操作
 */
!(function($, document, window, undefined){
	/***  微信分享提示弹窗  */
	var wxShareModal = new Object();
	wxShareModal = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				img: "https://xbjz1.oss-cn-beijing.aliyuncs.com/upload/default/fenxiang.png"
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-wxshare">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-wxshare-main">'
				+'<ul class="aui-wxshare-img"><img src="'+ _opts.img +'"></ul>'
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-wxshare")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			var ui = {
				main: document.querySelector(".aui-wxshare-main"),
				mask: document.querySelector(".aui-mask"),
			}
			!$.isDefine(_opts.mask) && ui.mask ? ui.mask.parentNode.removeChild(ui.mask) : '';
			typeof callback == "function" ?  callback() : '';
			ui.main.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.hide(opt);
			});
			ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.hide(opt);
			});
			ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			_this.css(opt);
		},
		css: function(opt){ //设置特定样式
			var _this = this;

		},
		show: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
		},
		hide: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			var ui = {
				wxshare: document.querySelector(".aui-wxshare"),
				main: document.querySelector(".aui-wxshare-main"),
				mask: document.querySelector(".aui-mask"),
			}
			ui.main.style.animation = "aui-fade-out .2s ease-out forwards";
			ui.mask ? ui.mask.style.animation = "aui-fade-out .2s ease-out forwards" : '';
			var timer = setTimeout(function() {
				ui.wxshare ? ui.wxshare.parentNode.removeChild(ui.wxshare) : '';
				clearTimeout(timer);
			},200);
		}
	}
	$.wxShareModal = function(opt, callback){
		wxShareModal.show(opt, callback);
	};
	//隐藏右上角按钮
	$.wxHideOptionMenu = function(){
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('hideOptionMenu');
		});
	}
	//显示右上角按钮
	$.wxShowOptionMenu = function(){
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('showOptionMenu');
		});
	}
	//隐藏微信底部自带导航栏
	$.wxHideToolbar = function(){
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('hideToolbar');
		});
	}
	//显示微信底部自带导航栏
	$.wxShowToolbar = function(){
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('showToolbar');
		});
	}
	//分享配置
	$.wxConfig = function(data){
		wx.config({
			debug: false,
			appId: data.appId,
			timestamp: data.timestamp,
			nonceStr: data.nonceStr,
			signature: data.signature,
			jsApiList: [
				"onMenuShareTimeline",//分享朋友圈接口
				"onMenuShareAppMessage",//分享给朋友接口
				"onMenuShareQQ",
				'onMenuShareWeibo',
				'getLocation'
			]
		})
	}
	//分享
	$.wxShare = function({imgUrl, link, title, desc}){
		wx.ready(function () {
			var shareData = {
				"imgUrl" : imgUrl, // 分享显示的缩略图地址
				"link" :  link,    // 分享地址
				"title" : title,   // 分享标题
				"desc" : desc,     // 分享描述
			}
			wx.onMenuShareTimeline(shareData);
			wx.onMenuShareAppMessage(shareData);
			wx.onMenuShareQQ(shareData);
			wx.onMenuShareWeibo(shareData);
		})
	}
})(aui, document, window);

/*自定义弹窗公告*/
!(function($, document, window, undefined){
	var announce = new Object();
	announce = {
		opts: function(opt){
			var opts = {
				warp: 'body', //--可选参数，父容器
				mask: true, //--可选参数，是否显示遮罩，默认true-显示，false-隐藏
				touchClose: true, //--可选参数，触摸遮罩是否关闭模态弹窗，默认true-关闭，false-不可关闭
				title: '', //标题
				msg:''//内容
			}
			return $.extend(opts, opt, true);
		},
		creat: function(opt, callback){ //创建
			var _this = this;
			var _opts = _this.opts(opt);
			var _html = '<div class="aui-poster">'
				+'<div class="aui-mask"></div>'
				+'<div class="aui-poster-main bg-white p-3 rounded">'
				+'<div class="aui-poster-title text-center font-weight-bold mb-2 font-10 text-danger aw-content">'+ _opts.title +'</div>'
				+'<div class="aui-poster-msg aw-content">'+ _opts.msg +'</div>'
				+'<img class="aui-poster-close" src="https://xbjz1.oss-cn-beijing.aliyuncs.com/upload/default/gz-close.png">'
				+'</div>'
				+'</div>';
			if(document.querySelector(".aui-poster")) return;
			document.querySelector(_opts.warp).insertAdjacentHTML('beforeend', _html);
			_this['ui'] = {
				poster: document.querySelector(".aui-poster"),
				main: document.querySelector(".aui-poster-main"),
				mask: document.querySelector(".aui-mask"),
				title: document.querySelector(".aui-poster-title"),
				msg: document.querySelector(".aui-poster-msg"),
				closeBtn: document.querySelector(".aui-poster-close")
			}
			!$.isDefine(_opts.mask) && _this.ui.mask ? _this.ui.mask.parentNode.removeChild(_this.ui.mask) : '';
			_this.ui.msg.addEventListener("click", function(e){
				_this.hide(opt);
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback() : '';
				},200);
			});
			_this.ui.closeBtn.addEventListener("click", function(e){
				_this.hide(opt);
				var timer = setTimeout(function() {
					clearTimeout(timer);
					typeof callback == "function" ?  callback() : '';
				},200);
			});
			_this.ui.main.addEventListener("touchmove", function(e){
				e.preventDefault();
			},{ passive: false });
			_this.ui.mask.addEventListener("click", function(e){
				!_opts.touchClose ? e.preventDefault() : _this.hide(opt);
			});
			_this.ui.mask.addEventListener("touchmove", function(e){
				e.preventDefault()
			},{ passive: false });
		},
		show: function(opt, callback){ //显示
			var _this = this;
			var _opts = _this.opts(opt);
			_this.creat(opt, callback);
			_this.ui.poster.style.cssText = 'display: inline-block;';
			_this.ui.mask ? _this.ui.mask.style.animation = "aui-fade-in .2s ease-out forwards" : '';
			_this.ui.main.style.cssText = 'animation: aui-slide-up_to_middle .3s ease-out forwards;';
		},
		hide: function(opt, callback){ //隐藏
			var _this = this;
			var _opts = _this.opts(opt);
			_this.ui.poster.style.cssText = 'animation: aui-fade-out .2s ease-out forwards;';
			var timer = setTimeout(function() {
				_this.ui.poster ? _this.ui.poster.parentNode.removeChild(_this.ui.poster) : '';
				typeof callback == "function" ?  callback() : '';
				clearTimeout(timer);
			},150);
		}
	}
	$.announce = function(opt, callback){
		announce.show(opt, callback);
	};
})(aui, document, window);
