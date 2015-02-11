<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<?
	require_once '../include/menubar.php';
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>おススメNEWセミナー | 日本ワーキング・ホリデー協会</title>

<meta name="keywords" content="オーストラリア,ニュージーランド,カナダ,カナダ,韓国,フランス,ドイツ,イギリス,アイルランド,デンマーク,台湾,香港,ビザ,取得,方法,申請,手続き,渡航,外務省,厚生労働省,最新,ニュース,大使館" />

<meta name="description" content="よくある質問：オーストラリア・ニュージーランド・カナダを初めとしたワーキングホリデー協定国の最新のビザ取得方法や渡航情報などを発信しています。" />

<meta name="author" content="Japan Association for Working Holiday Makers" />
<meta name="copyright" content="Japan Association for Working Holiday Makers" />
<link rev="made" href="mailto:info@jawhm.or.jp" />
<link rel="Top" href="../index.html" type="text/html" title="一般社団法人 日本ワーキング・ホリデー協会" />
<link rel="Author" href="mailto:info@jawhm.or.jp " title="E-mail address" />
<link href="../css/base.css" rel="stylesheet" type="text/css" />
<link href="../css/headhootg-nav.css" rel="stylesheet" type="text/css" />
<link href="../css/contents.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-easing.js"></script>
<script type="text/javascript" src="../js/scroll.js"></script>
<script type="text/javascript" src="../js/jquery.blockUI.js"></script>
<script type="text/javascript" src="../js/jquery.corner.js"></script>

<style>
.panel{
	cursor: pointer;
	position:relative;
	background-color:white;
	filter: alpha(opacity=100);
	  -moz-opacity:100;
	  opacity:100;
}
.chiiki	{
	float: left;
	cursor: pointer;
	width: 200px;
	height: 30px;
	text-align: center;
	vertical-align: middle;
	font-size: 12pt;
	color: white;
	margin: 0 5px 5px 0;
}
.button_yoyaku	{
	background-color: navy;
	color: white;
	cursor: pointer;
	padding: 0 5px 0 5px;
	margin: 0 0 3px 0;
	font-weight: bold;
}
.button_submit	{
	background: url(../images/button_submit.png) no-repeat 0 0;
	padding-left: 16px;
	cursor: pointer;
}

.button_cancel	{
	background: url(../images/button_cancel.png) no-repeat 0 0;
	padding-left: 16px;
	cursor: pointer;
}
</style>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20563699-1']);
  _gaq.push(['_setDomainName', '.jawhm.or.jp']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<? fncMenuScript(); ?>
<script>
$(function () {
	$('.chiiki').corner();
	// イベント設定
	var obj = document.getElementsByTagName('div');
	for (idx=0; idx<obj.length; idx++)	{
		if (obj[idx].className == 'panel')	{
			obj[idx].onmouseover = fncover;
			obj[idx].onmouseout = fncout;
			obj[idx].onclick = fncclick;
		}
		if (obj[idx].className == 'chiiki')	{
			obj[idx].onclick = fncclick;
		}
	}
});
function fncover()	{
	var id = this.getAttribute('id');
	jQuery("#"+id).css({ opacity: "0.65" });
}
function fncout()	{
	var id = this.getAttribute('id');
	jQuery("#"+id).css({ opacity: "1" });
}
function fncclick()	{
	var id = this.getAttribute('id');
	location.href = "./newseminar.php?key="+id+"#yoyaku";
}
function fnc_yoyaku(obj)	{
	document.getElementById("btn_soushin").disabled = false;
	document.getElementById("btn_soushin").value = "送信";
	document.getElementById("div_wait").style.display = 'none';
	document.getElementById('form_title').innerHTML = obj.getAttribute('title');
	document.getElementById('txt_title').value = obj.getAttribute('title');
	document.getElementById('txt_id').value = obj.getAttribute('uid');
	$.blockUI({ message: $('#yoyakuform'),
	css: { 
		top:  ($(window).height() - 400) /2 + 'px', 
		left: ($(window).width() - 600) /2 + 'px', 
		width: '600px' 
	}
 }); 
}
function btn_cancel()	{
	$.unblockUI();
}
function btn_submit()	{
	obj = document.getElementById('txt_name');
	if (obj.value == '')	{
		alert('お名前（氏）を入力してください。');
		obj.focus();
		return false;
	}
	obj = document.getElementById('txt_name2');
	if (obj.value == '')	{
		alert('お名前（名）を入力してください。');
		obj.focus();
		return false;
	}
	obj = document.getElementById('txt_furigana');
	if (obj.value == '')	{
		alert('フリガナ（氏）を入力してください。');
		obj.focus();
		return false;
	}
	obj = document.getElementById('txt_furigana2');
	if (obj.value == '')	{
		alert('フリガナ（名）を入力してください。');
		obj.focus();
		return false;
	}
	obj = document.getElementById('txt_mail');
	if (obj.value == '')	{
		alert('メールアドレスを入力してください。');
		obj.focus();
		return false;
	}
	obj = document.getElementById('txt_tel');
	if (obj.value == '')	{
		alert('電話番号を入力してください。');
		obj.focus();
		return false;
	}

	if (!confirm('ご入力頂いた内容を送信します。よろしいですか？'))	{
		return false;
	}

	$senddata = $("form").serialize();

	document.getElementById("div_wait").style.display = '';

	document.getElementById("btn_soushin").value = "処理中...";
	document.getElementById("btn_soushin").disabled = true;

	$.ajax({
		type: "POST",
		url: "../yoyaku/yoyaku.php",
		data: $senddata,
		success: function(msg){
			document.getElementById("div_wait").style.display = 'none';
			alert(msg);
			$.unblockUI();
		},
		error:function(){
			alert('通信エラーが発生しました。');
			$.unblockUI();
		}
	});
}
</script>
</head>
<body>

<div id="yoyakuform" style="display:none; margin:15px 20px 15px 20px; font-size:10pt; cursor:default;">

	<div style="font-size:12pt; font-weight:bold; margin:0 0 8px 0;">イベント　予約フォーム</div>

	<form name="form_yoyaku">
	<table style="width:560px;">
		<tr style="background-color:lightblue;">
			<td nowrap style="text-align:right;">イベント名&nbsp;</td>
			<td id="form_title" style="text-align:left;"></td>
			<input type="hidden" name="セミナー名" id="txt_title" value="">
			<input type="hidden" name="セミナー番号" id="txt_id" value="">
		</tr>
		<tr>
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">お名前&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;">
				(氏)<input type="text" name="お名前" id="txt_name" value="" size=10>
				(名)<input type="text" name="お名前2" id="txt_name2" value="" size=10>
			</td>
		</tr>
		<tr>
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">フリガナ&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;">
				(氏)<input type="text" name="フリガナ" id="txt_furigana" value="" size=10>
				(名)<input type="text" name="フリガナ2" id="txt_furigana2" value="" size=10>
			</td>
		</tr>
		<tr style="background-color:white;">
			<td nowrap valign="top" style="border-bottom: 1px dotted pink; text-align:right;">メールアドレス&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;">
				<input type="text" name="メール" id="txt_mail" value="" size=40><br/>
				<span style="font-size:8pt;">
				※予約確認のメールをお送りします。必ず有効なアドレスを入力してください。<br/>
				</span>
			</td>
		</tr>
		<tr>
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">当日連絡の付く電話番号&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;"><input type="text" name="電話番号" id="txt_tel" value="" size=20></td>
		</tr>
		<tr style="background-color:white;">
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">興味のある国&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;"><input type="text" name="興味国" id="txt_kuni" value="" size=50></td>
		</tr>
		<tr>
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">出発予定時期&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;"><input type="text" name="出発時期" id="txt_jiki" value="" size=50></td>
		</tr>
		<tr>
			<td nowrap valign="top" style="border-bottom: 1px dotted pink; text-align:right;">同伴者有無&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;">
				<input type="checkbox" name="同伴者" id="txt_dohan"> 同伴者あり<br/>
				<span style="font-size:8pt;">
				　　※同伴者ありの場合、２人分の席を確保致します。<br/>
				　　※３名以上でご参加の場合は、メールにてご連絡ください。<br/>
				</span>
			</td>
		</tr>
		<tr>
			<td nowrap style="border-bottom: 1px dotted pink; text-align:right;">今後のご案内&nbsp;</td>
			<td style="border-bottom: 1px dotted pink; text-align:left;"><input type="checkbox" name="メール会員" id="txt_mailmem" checked> このメールアドレスをメール会員(無料)に登録する</td>
		</tr>
		<tr style="background-color:white;">
			<td nowrap style="text-align:right;">その他&nbsp;</td>
			<td style="text-align:left;"><input type="text" name="その他" id="txt_memo" value="" size=50></td>
		</tr>
	</table>
	</form>

	<div style="font-size:9pt; font-weight:bold; margin:10px 0 10px 0; border: 1px dotted navy;;">
		【携帯のメールアドレスをご利用の方へ】<br/>
		予約確認のメールをお送り致しますので、<br/>
		jawhm.or.jpからのメール（ＰＣメール）が受信できる状態にしておいてください。<br/>
	</div>

	<div id="div_wait" style="display:none;">
		<img src="../images/ajaxwait.gif">
		&nbsp;予約処理中です。しばらくお待ちください。&nbsp;
		<img src="../images/ajaxwait.gif">
	</div>

	<input type="button" class="button_cancel" value=" 取消 " onclick="btn_cancel();">　　　　　
	<input type="button" class="button_submit" value=" 送信 " id="btn_soushin" onclick="btn_submit();">

</div>

<?

	// パラメータ読み込0み
	$para = @$_GET['key'];

	// イベント読み込み
	$cal = array();

	$evt_ymd   = array();
	$evt_title = array();
	$evt_desc  = array();
	$evt_img   = array();
	$evt_btn   = array();
	$evt_id	   = array();
	$evt_date  = array();

	try {
		$ini = parse_ini_file('../../bin/pdo_mail_list.ini', FALSE);
		$db = new PDO($ini['dsn'], $ini['user'], $ini['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->query('SET CHARACTER SET utf8');
		$sql  = 'SELECT id, year(hiduke) as yy, month(hiduke) as mm, day(hiduke) as dd, title, memo, place, k_use, k_title1, k_desc1, k_stat, free, pax, booking, date_format(starttime, \'%c月%e日 (%a) %k:%i\') as start FROM event_list WHERE ';
		if ($para <> '')	{
			$sql .= ' k_desc2 like "%'.$para.'%" and ';
		}
		$sql .= 'k_use = 1 ORDER BY hiduke, starttime, id';
		$rs = $db->query($sql);
		$cnt = 0;
		while($row = $rs->fetch(PDO::FETCH_ASSOC)){
			$cnt++;
			$year	= $row['yy'];
			$month  = $row['mm'];
			$day	= $row['dd'];
			//if ($row['place'] == 'event' || $row['place'] == 'sendai' || $row['place'] == 'okinawa')	{
				// イベント
				$evt_id[] = $row['id'];
				$evt_ymd[] = $year.substr('00'.$month,-2).substr('00'.$day,-2);
				$start = $row['start'];
				$start	= mb_ereg_replace('Mon', '月', $start);
				$start	= mb_ereg_replace('Tue', '火', $start);
				$start	= mb_ereg_replace('Wed', '水', $start);
				$start	= mb_ereg_replace('Thu', '木', $start);
				$start	= mb_ereg_replace('Fri', '金', $start);
				$start	= mb_ereg_replace('Sat', '土', $start);
				$start	= mb_ereg_replace('Sun', '日', $start);
				$evt_date[] = $start;
				$evt_title[] = $row['k_title1'];
				$evt_desc[]  = $row['k_desc1'];
				if ($row['k_stat'] == 1)	{
					if ($row['booking'] >= $row['pax'])	{
						$evt_img[]   	= '<img src="../images/semi_full.jpg">';
					}else{
						$evt_img[]   	= '<img src="../images/semi_now.jpg">';
					}
				}elseif ($row['k_stat'] == 2)	{
					$evt_img[]   	= '<img src="../images/semi_full.jpg">';
				}else{
					if ($row['booking'] >= $row['pax'])	{
						$evt_img[]   	= '<img src="../images/semi_full.jpg">';
					}else{
						if ($row['booking'] >= $row['pax'] / 2)	{
							$evt_img[]   	= '<img src="../images/semi_now.jpg">';
						}else{
							$evt_img[]	= '';
						}
					}
				}
				if ($row['free'] == 1)	{
					$evt_btn[]	= '<div style="padding:5px 20px 5px 20px; border: 1px solid navy;">【こちらはメンバー様限定イベントです】<br/>メンバー登録を行って頂くとご予約が可能となります。<a href="./mem">登録はこちらからどうぞ</a></div>';
				}else{
					if ($row['k_stat'] == 2)	{
						$evt_btn[]	= '';
					}else{
						if ($row['booking'] >= $row['pax'])	{
							$evt_btn[]	= '<input class="button_yoyaku" type="button" value="キャンセル待ち" onclick="fnc_yoyaku(this);" title="[東京E]'.$start.' '.$row['k_title1'].'" uid="'.$row['id'].'">';
						}else{
							$evt_btn[]	= '<input class="button_yoyaku" type="button" value="予約" onclick="fnc_yoyaku(this);" title="[東京E]'.$start.' '.$row['k_title1'].'" uid="'.$row['id'].'">';
						}
					}
				}
				$cal[$year.$month.$day] .= '<img src="../images/sa04.jpg">';
		//	}

		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}


?>

<? fncMenuHead('<img id="top-mainimg" src="../images/seminar/top.jpg" alt="" width="970" height="170" />'); ?>

  <div id="contentsbox"> <img id="bgtop" src="../images/contents-bgtop.gif" alt="" />
  <div id="contents">

<? fncMenubar(); ?>


	<div id="maincontent">
	  <p id="topicpath"><a href="../index.html">トップ</a>　> おススメNEWセミナー</p>

<h2 class="sec-title">おすすめNEWセミナー　ご予約・詳細は以下をクリック！</h2>
<br>

		<!--<p class="text01">
		

		あなたの夢はなんですか？
		新しい年になりましたが、２０１２年のスローガンはお決まりでしょうか。<br/>
		２０１１年を振り返り、どんな１年であったか、
		これからはどんな自分になりたいか、
		そんななりたい自分になる為にはどうしたらいいのか…。
		新しい自分に会う為のきっかけになればという思いを込めて
		初夢留学・ワーホリフェアを開催致します。
		</p>-->

			<!--<div style="border: 2px dotted navy; font-size:10pt; margin: 10px 10px 10px 10px; padding: 10px 20px 10px 20px;">
			<fontsize="2" fontcolor="gray">ワーキングホリデーって何？　どんなことが出来るの？　予算はどのくらいかかるの？<br/>
			帰国してからの就職先が心配...　　初めての海外だけどワーホリで大丈夫？<br/>
			聞きたい事や、心配な事もたくさんあると思います。何でも聞いてください。<br/>
			セミナーの参加者は８割以上の方が、お１人での参加です。お気軽にご参加ください。</font>
			</div> -->
			<!--<img  src="../images/summerevent/muryouseminar.gif" /><br/>-->

		<div align="center">
<br />
			<table cellpadding="8">
				<tr>
					<td>
						<div class="panel" id="ih">
							<img src="../images/seminar/ih.gif" />
						</div>
					</td>
					<td>
						<div class="panel" id="browns">
							<img src="../images/seminar/browns.gif" />
						</div>
					</td>
					<td>
						<div class="panel" id="kgic">
							<img src="../images/seminar/kgic.gif" />
						</div>
					</td>	
				</tr>
			<tr>
					<td>
						<div class="panel" id="nzlc">
							<img src="../images/seminar/nzlc.gif" />
						</div>
					</td>
					<td>
							<img src="../images/seminar/commingsoon.gif" />
					</td>
					<td>
							<img src="../images/seminar/commingsoon.gif" />
					</td>	
				</tr>
				</table>
</div>
<h2 class="sec-title">おすすめNEWセミナー【少人数限定懇談セミナー】　ご予約・詳細は以下をクリック！</h2>

<div align="center">
<br />	
			<table cellpadding="8">
				<tr>
					<td>
						<div class="panel" id="joseigentei">
							<img src="../images/seminar/joseigentei.gif" />
						</div>
					</td>
					<td>
						<div class="panel" id="kyuushoku">
							<img src="../images/seminar/kyuushoku.gif" />
						</div>
					</td>
					<td>
						<div class="panel" id="europe">
							<img src="../images/seminar/europe.gif" />
						</div>
					</td>	
				</tr>
	<tr>
					<td>
						<div class="panel" id="auscan">
							<img src="../images/seminar/auscan.gif" />
						</div>
					</td>
					<td>
						<div class="panel" id="shikaku">
							<img src="../images/seminar/shikaku.gif" />
						</div>
					</td>
					<td>
							<img src="../images/seminar/commingsoon2.gif" />
					</td>	
				</tr>
			</table>

</div>
<center>
		<div style="height:0px;">&nbsp;</div>
</center>
<h2 class="sec-title" id="yoyaku">&nbsp;</h2>

		<div style="height:50px;">&nbsp;</div>


	<div style="padding-left:30px;">
<?
	if ($para == '')	{
?>
			<div style="border: 2px dotted pink; margin: 10px 0 10px 0; padding: 5px 10px 5px 10px; font-size:12pt;">
				興味のあるセミナーを上から選んで下さい。詳細がここにでます。
				<div style="font-size:13pt;"><a href="../seminar.html">通常セミナーはこちら</a></div>

			</div>
<?
	}else{
		if (count($evt_title) == 0)	{
?>
			<div style="border: 2px dotted pink; margin: 10px 0 10px 0; padding: 5px 10px 5px 10px; font-size:12pt;">
				現在、詳細な情報がありません。<br/>
				準備ができ次第、こちらのページでご案内いたします。<br/>
				お手数をおかけいたしますが、今しばらくお待ちください。<br/>
				<a href="../event.html">すべての会場の情報を見る場合はこちら</a><br/>
			</div>
<?
		}else{
			for ($idx=0; $idx<count($evt_title); $idx++)	{
				print '<div style="height:20px;" id="'.$evt_ymd[$idx].'"> </div>';
				print '<div style="width:620px; margin:7px 0 0 -15px; padding-left:15px; font-size:11pt; color:navy; border-left:3px solid red; border-bottom:1px solid red;">';
				if ($evt_ymd[$idx] < date('Ymd'))	{
					print '終了しました　<s>'.$evt_title[$idx].'</s>';
				}else{
					print '<table><tr><td>'.$evt_btn[$idx].'</td><td>&nbsp;</td><td>'.$evt_date[$idx].'～<br/>'.$evt_title[$idx].'</td></tr></table>';
		//			print $evt_btn[$idx].'&nbsp;&nbsp;'.$evt_title[$idx].' '.$evt_date[$idx].' '.$evt_time[$idx];
				}
				print '</div>';
				print '<table><tr><td>'.$evt_img[$idx].'</td>';
				print '<td><p>'.nl2br($evt_desc[$idx]).'</p></td></tr></table>';
			}
		}
	}
?>
	</div>


<br />
	<div style="border: 2px dotted navy; margin: 10px 0 10px 0; padding: 5px 10px 5px 10px; font-size:10pt;">
		【ご注意：スマートフォンをご利用の方へ】<br/>
		スマートフォンなど、ＰＣ以外のブラウザからご利用された場合、予約フォームが正しく機能しない場合があります。<br/>
		この場合、お手数ですが、以下の内容を toiawase@jawhm.or.jp までご連絡ください。<br/>
		　・　参加希望のイベントの会場名、日程<br/>
		　・　お名前<br/>
		　・　当日連絡の付く電話番号<br/>
		　・　興味のある国<br/>
		　・　出発予定時期<br/>
	</div>




<div style="height:30px;">&nbsp;</div>
<div style="text-align:center;">
	<img src="../images/flag01.gif">
	<img src="../images/flag03.gif">
	<img src="../images/flag09.gif">
	<img src="../images/flag05.gif">
	<img src="../images/flag06.gif">
	<img src="../images/mflag11.gif" width="40" height="26">
	<img src="../images/flag08.gif">
	<img src="../images/flag04.gif">
	<img src="../images/flag02.gif">
	<img src="../images/flag10.gif">
	<img src="../images/flag07.gif">
</div>

	<div style="height:50px;">&nbsp;</div>


	</div>
	</div>
	</div>
  </div>

<? fncMenuFooter(); ?>

</body>
</html>