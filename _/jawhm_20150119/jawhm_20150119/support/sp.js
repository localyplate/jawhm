(function(){
	var ua = navigator.userAgent;
	if (localStorage 
		 && !localStorage.getItem("sp_flag") 
		 && (ua.indexOf('iPhone') > 0 || ua.indexOf('iPod') > 0 || ua.indexOf('Android') > 0 )) {
		if(confirm('スマートフォン用サイトを表示しますか？')) {
			location.href = 'http://www.jawhm.or.jp/s/go/5623';
		}else{
			localStorage.setItem("sp_flag",true);
		}
	}
})();