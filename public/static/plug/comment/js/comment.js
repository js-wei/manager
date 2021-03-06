/**
 * 评论插件低版本使用jQuery 1.8以后使用
 * 插件提供了时间/时间戳互转方法:$.timeStamp()和$.getTime()
 * @param {Object} $
 * @param {Object} undefined
 */
(function($,undefined){
	 $.extend({
        timeStamp:function(date=''){
        	var timestamp = Date.parse(new Date(date));  
			timestamp = timestamp / 1000;
			return timestamp;
        },
        getTime:function(nS,format='yyyy-MM-dd h:i:s'){
        	var d = new Date(parseInt(nS) * 1000);
        	var date = {  
              "M+": d.getMonth() + 1,  
              "d+": d.getDate(),  
              "h+": d.getHours(),  
              "m+": d.getMinutes(), 
              "i+": d.getMinutes(), 
              "s+": d.getSeconds(),  
              "q+": Math.floor((d.getMonth() + 3) / 3),  
              "S+": d.getMilliseconds()  
	       };  
	       if (/(y+)/i.test(format)) {  
	             format = format.replace(RegExp.$1, (d.getFullYear() + '').substr(4 - RegExp.$1.length));  
	       }  
	       for (var k in date) {  
              if (new RegExp("(" + k + ")").test(format)) {  
                     format = format.replace(RegExp.$1, RegExp.$1.length == 1  
                            ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));  
              }  
	       }  
	       return format;  
		},
        dataTemplate:function(template,data){
        	template = template.toLowerCase();
		    var outPrint="";  
		    var matchs = template.match(/\{[a-zA-Z]+\}/gi); 
		    var temp="";  
		    for(var j = 0 ; j < matchs.length ;j++){  
		        if(temp == "")  temp = template;  
		        var re_match = matchs[j].replace(/[\{\}]/gi,"");  
		        temp = temp.replace(matchs[j],data[re_match]);  
		    }  
		    outPrint += temp;  
		    return outPrint;  
		} 
  	});
	$.fn.serializeJson = function(){  
	     var obj = {};  
	     var count = 0;  
	     $.each( this.serializeArray(), function(i,o){  
	         var n = o.name, v = o.value;  
	         count++;  
	         obj[n] = obj[n] === undefined ? v: $.isArray( obj[n] ) ? obj[n].concat( v ): [ obj[n], v ];  
	     });  
	     obj.hasCount = count + "";
	     obj.time = Date.parse(new Date())/ 1000;
	     return $.parseJSON(JSON.stringify(obj));  
	};
	$.fn.comment = function(options,param){
		var otherArgs = Array.prototype.slice.call(arguments, 1);
		if (typeof options == 'string') {
			var fn = this[0][options];
			if($.isFunction(fn)){
				return fn.apply(this, otherArgs);
			}else{
				throw ("comment - No such method: " + options);
			}
		}

		return this.each(function(){
			
			var para = {};   
			var self = this;
			var fCode = 0;
			
			var defaults = {
				'reply':true,
				"agoComment":[],
				'header':'留下的脚印',
				"callback":function(comment){},
				'tpl':'<div id="comment{id}" class="comment"<a class="avatar"><img src="images/foot.png"></a><div class="content"><a class="author">{username}</a><div class="metadata"><span class="date">{time}</span></div><div class="text">{content}</div>{reply}</div></div>',
				'form':'<div class="ui large form "><input type="hidden" id="fid" name="fid" value="{fid}" /><div class="two fields"><div class="field" ><input type="text" name="username" id="userName" /><label class="userNameLabel" for="userName">用户名</label></div><div class="field" ><input type="text" name="email" id="userEmail" /><label class="userEmailLabel" for="userName">邮箱</label></div></div><div class="contentField field" ><textarea name="content" id="commentContent"></textarea><label class="commentContentLabel" for="commentContent">评论内容</label></div><div id="submitComment" class="ui button teal submit labeled icon"><i class="icon edit"></i> 提交评论</div></div>',
			};
			
			para = $.extend(defaults,options);
		
			this.init = function(){
				this.createAgoCommentHtml();  
			};

			this.createAgoCommentHtml = function(){
				
				var html = '';
				html += '<div id="commentItems" class="comments" style="margin-bottom:10px;">';
				if(para.header!=''){
					html += '<div class="text" style="font-size:2rem;padding-bottom:10px;border-bottom: 1px solid #DFDFDF;">'+para.header+'</div>';
				}
				html += '</div>';
				$(self).append(html);
				
				$.each(para.agoComment, function(k, v){
					
					var topStyle = "";
					if(k>0){
						topStyle = "topStyle";
					}
					
					if(para.reply){
						para.tpl = para.tpl.replace('{reply}','<div class="actions"><a class="reply" href="javascript:void(0)" selfID="{id}" >回复</a></div>');
					}else{
						para.tpl = para.tpl.replace('{reply}','');
					}
					
					var item =$.dataTemplate(para.tpl,v);
					
					if(v.sortID==0){  
						$("#commentItems").append(item);
					}else{ 
						if($("#comment"+v.sortID).find(".comments").length==0){ 
							var comments = '';
							comments += '<div id="comments'+v.sortID+'" class="comments">'+item+'</div>';
							$("#comment"+v.sortID).append(comments);
						}else{
							$("#comments"+v.sortID).append(item);
						}
					}
				});
				this.createFormCommentHtml();
			};
			this.createFormCommentHtml = function(){
				var boxHtml = '';
				$(self).append('<div id="commentFrom"></div>');
				var tpl = para.form;
				if(tpl.indexOf('{fid}')>-1){
					tpl = tpl.replace('{fid}',0);
				}

				if(para.reply){
					var form = para.form;
					if(form.indexOf('{fid}')>-1){
						form = form.replace('{fid}',0);
					}
					boxHtml += '<form id="replyBoxAri" class="form">'+ form + '</form>';
				}
				
				$("#commentFrom").append(boxHtml);
	            this.addEvent();
			};
			

			this.addEvent = function(){
				this.replyClickEvent();
				this.cancelReplyClickEvent();
				this.addFormEvent();
			};
			
	
			this.replyClickEvent = function(){
				$(self).find(".actions .reply").on("click", function(){

					fCode = $(this).attr("selfid");
					$(self).find(".cancel").remove();

					self.removeAllCommentFrom();
	
					$(this).parent(".actions").append('<a class="cancel" href="javascript:void(0)">取消回复</a>');

					self.addReplyCommentFrom(fCode);
					$("#publicComment").off("click");
					$("#publicComment").on("click",function(){
						var result = $('form').serializeJson();
						para.callback(result);
					});
				});
			};

			this.cancelReplyClickEvent = function(){
				$(self).find(".actions .cancel").off("click");
				$('.actions').on("click",'.cancel' ,function(){
					$(self).find(".cancel").remove();
					self.removeAllCommentFrom();
					self.addRootCommentFrom();
				});
			};
			
		
			this.addFormEvent = function(){
				$("#submitComment").off("click");
				$("body").on("click",'#submitComment',function(){
					var result = $('form').serializeJson();
					para.callback(result);
				});
			};
			
			
			this.removeAllCommentFrom = function(){
				if($(self).find("#replyBox")[0]){
					
					$(self).find("#replyBox").remove();
				}
				
				if($(self).find("#replyBoxAri")[0]){
					$(self).find("#replyBoxAri").remove();
				}
			};
			
			
			this.addReplyCommentFrom = function(id){
				var boxHtml = '';
				var tpl = para.form;
				if(tpl.indexOf('{fid}')>-1){
					tpl = tpl.replace('{fid}',id);
				}
				boxHtml += '<form id="replyBox" class="form">'+tpl+'</form>';
				$(self).find("#comment"+id).find(">.content").after(boxHtml);
			};
			
			this.addRootCommentFrom = function(){
				var boxHtml = '';
				if(para.reply){
					var tpl = para.form;
					if(tpl.indexOf('{fid}')>-1){
						tpl = tpl.replace('{fid}',0);
					}
					boxHtml += '<form id="replyBoxAri" class="ui reply form">'+tpl+'</form>';
				}
				
				$(self).find("#commentFrom").append(boxHtml);
			};
			this.getCommentFId = function(){
				return parseInt(fCode);
			};
			this.setCommentAfter = function(param){
				$(self).find(".cancel").remove();
				self.addNewComment(param);
				self.removeAllCommentFrom();
				self.addRootCommentFrom();
			};
			this.addNewComment = function(param){
				var topStyle = "";
				if(parseInt(fCode)!=0){
					topStyle = "topStyle";
				}
				
				if(para.reply){
					para.tpl = para.tpl.replace('{reply}','<div class="actions"><a class="reply" href="javascript:void(0)" selfID="{id}" >回复</a></div>');
				}else{
					para.tpl = para.tpl.replace('{reply}','');
				}
				
				var item = $.dataTemplate(para.tpl,param);
				
				if(parseInt(fCode)==0){
					$("#commentItems").append(item);
				}else{
					if($("#comment"+fCode).find(".comments").length==0){  // 没有
						var comments = '';
						comments += '<div id="comments'+fCode+'" class="comments">'+ item +'</div>';
						$("#comment"+fCode).append(comments);
					}else{  // 有
						$("#comments"+fCode).append(item);
					}
				}
			};
			this.init();
		});
	};
})(jQuery);