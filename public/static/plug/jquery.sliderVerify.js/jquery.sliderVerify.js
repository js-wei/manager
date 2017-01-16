(function($){
    $.fn.drag = function(options){
        var x, drag = this, isMove = false, defaults = {
				color:'#fff',
				text:'拖动滑块验证',
				verify:'验证通过',
				callback:function(){}
        };
        var options = $.extend(defaults, options);
        var html = '<div class="drag_bg"></div>'+
                    '<div class="drag_text" onselectstart="return false;" unselectable="on">'+options.text+'</div>'+
                    '<div class="handler handler_bg"></div>';
        this.append(html);
        
        var handler = drag.find('.handler');
        var drag_bg = drag.find('.drag_bg');
        var text = drag.find('.drag_text');
        var maxWidth = drag.width() - handler.width();
        handler.mousedown(function(e){
            isMove = true;
            x = e.pageX - parseInt(handler.css('left'), 10);
        });
        $(document).mousemove(function(e){
            var _x = e.pageX - x;
            if(isMove){
                if(_x > 0 && _x <= maxWidth){
                    handler.css({'left': _x});
                    drag_bg.css({'width': _x});
                }else if(_x > maxWidth){
                    //dragOk();
					handler.removeClass('handler_bg').addClass('handler_ok_bg');
					text.text(options.verify);
					drag.css({'color': options.color});
					handler.unbind('mousedown');
					$(document).unbind('mousemove');
					$(document).unbind('mouseup');
					setTimeout(function(){
						options.callback();
					},600);
                }
            }
        }).mouseup(function(e){
            isMove = false;
            var _x = e.pageX - x;
            if(_x < maxWidth){ 
                handler.css({'left': 0});
                drag_bg.css({'width': 0});
            }
        });
    };
})(jQuery);