<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow"> 
<title>個別カウンセリング（新規会員登録）</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery.activity-indicator-1.0.0.min.js"></script> 
<style type="text/css">
textarea{
	width:100%;
}
input.normal,select.normal,textarea.normal {
	background-color: #E0FFFF;
}
option.color0 {
	color:#696969;
}
option.color1 {
	color:#0000ff;
}
option.color2 {
	color:#ff0000;
}
</style>
<SCRIPT type="text/javascript" language="JavaScript"> 
<!--
function winclose(){
	//「閉じようとしています」を表示させないため（２行追加）
　　var w=window.open("","_top");
　　w.opener=window;

	window.close(); // サブウィンドウを閉じる
}
//ローディングくるくる
function kurukuru(){
	jQuery(function($){
		$.fixedActivity(true)
	});
//	jQuery(function($){
//		$.fixedActivity(false)
//	});
}
jQuery(function($){
	$.fixedActivity = function(show){
		var o = $.fixedActivity;
		var body = $('body'),win = $(window);

		//ローディング中画面を透過にさせるラッパー要素
		if(!o.pageWrapper){
			o.pageWrapper = body.wrapInner('<div/>').find('> div').eq(0);
		}

		//アイコン表示
		body.activity(show);

		//表示位置を画面中央に設定
		if(show){
			//IE8以下だとshape、モダンブラウザだとdivになる
			var icon = body.find('> *').eq(0);
			icon.css({
				margin :0,
				position:'fixed',
				top:(win.height() - icon.height()) / 2,
				left:(win.width() - icon.width()) / 2
			});
		}

		//画面透過の切り替え
		o.pageWrapper.css({opacity: show ? .3 : 1});
	}
});
// -->
</script>
</head>
<body bgcolor="white">
<?php
	//***共通情報************************************************************************************************
	//画面情報
	//当画面の画面ＩＤ
	$gmn_id = 'kbtcounseling_trk_new0.php';
	//遷移ＯＫとする遷移元の画面ＩＤ
	$ok_gmn = array('kbtcounseling_trk_serch.php','kbtcounseling_trk_serch_kkn0.php','kbtcounseling_trk_new1.php');

	$err_flg = 0;	//0:エラーなし　1:サーバー接続エラー　2:画面遷移エラー　3:引数エラー　4以降は画面毎に設定

	$tabindex = 0;	//タブインデックス

	//日付関係
	require( '../zz_datetime.php' );

	//祝日情報
	require_once('../jp-holiday.php');

	if( $now_youbi == 0 || $dt->is_jp_holiday == true ){
		//日曜・祝日
		$zs_youbi_color = 'red';
	}else if( $now_youbi == 6 ){
		//土曜
		$zs_youbi_color = 'blue';
	}else{
		//平日
		$zs_youbi_color = 'black';
	}

	mb_language("Ja");
	mb_internal_encoding("utf8");

	//***コーディングはここから**********************************************************************************

	//引数の入力
	$prc_gmn = $_POST['prc_gmn'];
	$lang_cd = $_POST['lang_cd'];
	$office_cd = $_POST['office_cd'];
	$staff_cd = $_POST['staff_cd'];
	$select_office_cd = $_POST['select_office_cd'];
	$select_ymd = $_POST['select_ymd'];
	$select_jknkbn = $_POST['select_jknkbn'];
	$select_staff_cd = $_POST['select_staff_cd'];	//未入力可（カウンセラーを指定した場合のみ設定される）

	//サーバー接続
	require( './zs_svconnect.php' );
	
	//接続結果
	if( $svconnect_rtncd == 1 ){
		$err_flg = 1;
	}else{
		//画面ＩＤのチェック
		if( !in_array($prc_gmn , $ok_gmn) ){
			$err_flg = 2;
		}else{
			//引数入力チェック
			if ( $lang_cd == "" || $office_cd == "" || $staff_cd == "" || $select_office_cd == "" || $select_ymd == "" || $select_jknkbn == "" ){
				$err_flg = 3;
			}else{
				//メンテナンス期間チェック
				require( './zs_mntchk.php' );
		
				if( $mntchk_flg == 1 || $mntchk_flg == 2 ){
					$err_flg = 80;	//メンテナンス中
				
				}else{
					//ログインチェック
					require( './zs_staff_loginchk.php' );	
					if ($LC_rtncd == 1){
						$err_flg = 9;
					}
				}
			}
		}
	}

	//エラー発生時
	if( $err_flg != 0 ){
		//エラー画面編集
		require( './zs_errgmn.php' );

	//エラーなし
	}else{
		
		//画像事前読み込み
		print('<img src="./img_' . $lang_cd . '/btn_modoru_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_shinki_kaiin_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_kns1_kaiinno_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_kns2_kaiinnm_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_kns3_mail_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_kns4_kaiintel_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_trk_2.png" width="0" height="0" style="visibility:hidden;">');

		//店舗メニューボタン表示
		require( './zs_menu_button.php' );
		
		//ページ編集
		//初期値セット
		$kaiin_nm1 = '';		//会員名（姓）
		$kaiin_nm2 = '';		//会員名（名）
		$kaiin_nm_k1 = '';		//会員名カナ（セイ）
		$kaiin_nm_k2 = '';		//会員名カナ（メイ）
		$kaiin_tel = '';		//会員電話番号
		$kaiin_mail = '';		//会員メールアドレス
		$kaiin_kyoumi = '未使用';	//興味のある国
		$kaiin_jiki = '未使用';		//出発予定時期
		$kaiin_soudan = '';		//相談内容
		if( $prc_gmn == 'kbtcounseling_trk_new1.php' || $prc_gmn == 'kbtcounseling_trk_new2.php' ){
			$kaiin_nm1 = $_POST['kaiin_nm1'];			//会員名（姓）
			$kaiin_nm2 = $_POST['kaiin_nm2'];			//会員名（名）
			$kaiin_nm_k1 = $_POST['kaiin_nm_k1'];		//会員名カナ（セイ）
			$kaiin_nm_k2 = $_POST['kaiin_nm_k2'];		//会員名カナ（メイ）
			$kaiin_tel = $_POST['kaiin_tel'];			//会員電話番号
			$kaiin_mail = $_POST['kaiin_mail'];			//会員メールアドレス
			$kaiin_kyoumi = $_POST['kaiin_kyoumi'];		//興味のある国
			$kaiin_jiki = $_POST['kaiin_jiki'];			//出発予定時期
			$kaiin_soudan = $_POST['kaiin_soudan'];		//相談内容
		}


		$select_youbi_cd = date("w", mktime(0, 0, 0, sprintf("%d",substr($select_ymd,4,2)), sprintf("%d",substr($select_ymd,6,2)), sprintf("%d",substr($select_ymd,0,4))));

		//オフィスマスタの取得
		$query = 'select OFFICE_NM,START_YOUBI from M_OFFICE where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '";';
		$result = mysql_query($query);
		if (!$result) {
			$err_flg = 4;
			
			//エラーメッセージ表示
			require( './zs_errgmn.php' );
			
			//**ログ出力**
			$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
			$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
			$log_office_cd = $office_cd;	//オフィスコード
			$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
			$log_naiyou = 'オフィスマスタの参照に失敗しました。';	//内容
			$log_err_inf = $query;			//エラー情報
			require( './zs_log.php' );
			//************
			
		}else{
			$row = mysql_fetch_array($result);
			$Moffice_office_nm = $row[0];		//オフィス名
			$Moffice_start_youbi = $row[1];	//開始曜日（ 0:日曜始まり 1:月曜始まり ）
			
			//「オフィス」を「会場」に置換する
			$Moffice_office_nm = str_replace('オフィス','会場',$Moffice_office_nm );			
		}

		//営業時間マスタを読み込む（選択日の週の先頭以降）･･･９レコード１セット
		$Meigyojkn_cnt = 0;
		$query = 'select YOUBI_CD,TEIKYUBI_FLG,OFFICE_ST_TIME,OFFICE_ED_TIME,ST_DATE,ED_DATE from M_EIGYOJKN where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and YUKOU_FLG = 1 and ED_DATE >= "' . $select_ymd . '" order by YOUBI_CD,ST_DATE;';
		$result = mysql_query($query);
		if (!$result) {
			$err_flg = 4;
			//エラーメッセージ表示
			require( './zs_errgmn.php' );
				
			//**ログ出力**
			$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
			$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
			$log_office_cd = $office_cd;	//オフィスコード
			$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
			$log_naiyou = '営業時間マスタの参照に失敗しました。';	//内容
			$log_err_inf = $query;			//エラー情報
			require( './zs_log.php' );
			//************
			
		}else{
			while( $row = mysql_fetch_array($result) ){
				$Meigyojkn_youbi_cd[$Meigyojkn_cnt] = $row[0];		//曜日コード  0:日,1:月,2:火,3:水,4:木,5:金,6:土,7:土日祝の前日.8:祝日
				$Meigyojkn_teikyubi_flg[$Meigyojkn_cnt] = $row[1];	//定休日フラグ  0:営業日 1:定休日
				$Meigyojkn_st_time[$Meigyojkn_cnt] = $row[2];		//開始時刻
				$Meigyojkn_ed_time[$Meigyojkn_cnt] = $row[3];		//終了時刻
				$tmp_date = $row[4];
				$Meigyojkn_st_date[$Meigyojkn_cnt] = intval(substr($tmp_date,0,4) . substr($tmp_date,5,2) . substr($tmp_date,8,2));
				$tmp_date = $row[5];
				$Meigyojkn_ed_date[$Meigyojkn_cnt] = intval(substr($tmp_date,0,4) . substr($tmp_date,5,2) . substr($tmp_date,8,2));
				$Meigyojkn_cnt++;
			}
		}


		//営業日フラグを求める
		$select_eigyoubi_flg = 0;
		if( $err_flg == 0 ){
			$select_eigyoubi_flg = 0;	//対象日の営業日フラグ　0:営業日、1:祝日営業日、8:非営業日、9:祝日非営業日
			$query = 'select EIGYOUBI_FLG from M_CALENDAR where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and YMD  = "' . $select_ymd . '";';
			$result = mysql_query($query);
			if (!$result) {
				$err_flg = 4;
				//エラーメッセージ表示
				require( './zs_errgmn.php' );
			
				//**ログ出力**
				$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
				$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
				$log_office_cd = $office_cd;	//オフィスコード
				$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
				$log_naiyou = 'カレンダーマスタの参照に失敗しました。';	//内容
				$log_err_inf = $query;			//エラー情報
				require( './zs_log.php' );
				//************
				
			}else{
				while( $row = mysql_fetch_array($result) ){
					$select_eigyoubi_flg = $row[0];		//対象日の営業日フラグ　0:営業日、1:祝日営業日、8:非営業日、9:祝日非営業日
				}
			}
		}
				
				
		//営業時間と定休日フラグを求める
		$select_eigyou_st_time = 0;
		$select_eigyou_ed_time = 0;
		$select_teikyubi_flg = 0;	//定休日フラグ　0:営業日 1:定休日
		$find_flg = 0;
		if( $select_eigyoubi_flg == 1 || $select_eigyoubi_flg == 9 ){
			//祝日のみ対応（土日祝の前日は非対応）
			$a = 0;
			while( $a < $Meigyojkn_cnt && $find_flg == 0 ){
				if( $Meigyojkn_youbi_cd[$a] == 8 && $Meigyojkn_st_date[$a] <= $select_ymd && $select_ymd <= $Meigyojkn_ed_date[$a] ){
					if( $Meigyojkn_st_time[$a] != "" ){
						$select_eigyou_st_time = $Meigyojkn_st_time[$a];
						$select_eigyou_ed_time = $Meigyojkn_ed_time[$a];
						$select_teikyubi_flg = $Meigyojkn_teikyubi_flg[$a];
						$find_flg = 1;
						
					}else{
						if( $Meigyojkn_teikyubi_flg[$a] != 1 ){
							//曜日で検索しなおし
							$a = 0;
							while( $a < $Meigyojkn_cnt && $find_flg == 0 ){
								if( ( $Meigyojkn_youbi_cd[$a] == $target_youbi_cd ) &&
									( $Meigyojkn_st_date[$a] <= $select_ymd && $select_ymd <= $Meigyojkn_ed_date[$a] ) ){
									$select_eigyou_st_time = $Meigyojkn_st_time[$a];
									$select_eigyou_ed_time = $Meigyojkn_ed_time[$a];
									$select_teikyubi_flg = $Meigyojkn_teikyubi_flg[$a];
									$find_flg = 1;
								}
								$a++;
							}
						}else{
							$select_teikyubi_flg = $Meigyojkn_teikyubi_flg[$a];
							$find_flg = 1;
						}
					}
				}
						
				$a++;
			}
			
		}else{
			//非祝日
			$a = 0;
			while( $a < $Meigyojkn_cnt && $find_flg == 0 ){
				if( ( $Meigyojkn_youbi_cd[$a] == $target_youbi_cd ) &&
					( $Meigyojkn_st_date[$a] <= $select_ymd && $select_ymd <= $Meigyojkn_ed_date[$a] ) ){
					if( $Meigyojkn_teikyubi_flg[$a] != 1 ){
						$select_eigyou_st_time = $Meigyojkn_st_time[$a];
						$select_eigyou_ed_time = $Meigyojkn_ed_time[$a];
						$select_teikyubi_flg = $Meigyojkn_teikyubi_flg[$a];
						$find_flg = 1;
					}else{
						$select_teikyubi_flg = $Meigyojkn_teikyubi_flg[$a];
						$find_flg = 1;
					}
				}
				$a++;
			}
		}
				
		//定休日の場合、営業日個別に登録されていた場合は、営業日扱いとする
		if( $select_teikyubi_flg == 1 ){
			$query = 'select OFFICE_ST_TIME,OFFICE_ED_TIME from D_EIGYOJKN_KBT where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and YMD = "' . $select_ymd . '";';
			$result = mysql_query($query);
			if (!$result) {
				$err_flg = 4;
				//エラーメッセージ表示
				require( './zs_errgmn.php' );
						
				//**ログ出力**
				$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
				$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
				$log_office_cd = $office_cd;	//オフィスコード
				$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
				$log_naiyou = '営業時間個別の参照に失敗しました。';	//内容
				$log_err_inf = $query;			//エラー情報
				require( './zs_log.php' );
				//************
						
			}else{
				while( $row = mysql_fetch_array($result) ){
					if( $row[0] != "" ){
						$select_eigyou_st_time = $row[0];
					}
					if( $row[1] != "" ){
						$select_eigyou_ed_time = $row[1];
					}
					$select_teikyubi_flg = 0;
				}
			}
		}

		//時間区分に該当する時間を取得する
		$Mclass_st_time = 0;
		$Mclass_ed_time = 0;
		$query = 'select ST_TIME,ED_TIME from M_CLASS_JKNWR where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and CLASS_CD = "KBT" and JKN_KBN = "' . $select_jknkbn . '" and ST_DATE <= "' . $select_ymd . '" and ED_DATE >= "' . $select_ymd . '" and YUKOU_FLG = 1;';
		$result = mysql_query($query);
		if (!$result) {
			$err_flg = 4;
			//エラーメッセージ表示
			require( './zs_errgmn.php' );
					
			//**ログ出力**
			$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
			$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
			$log_office_cd = $office_cd;	//オフィスコード
			$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
			$log_naiyou = 'クラス時間割の参照に失敗しました。';	//内容
			$log_err_inf = $query;			//エラー情報
			require( './zs_log.php' );
			//************
					
		}else{
			while( $row = mysql_fetch_array($result) ){
				$Mclass_st_time = $row[0];	//開始時刻
				$Mclass_ed_time = $row[1];	//終了時刻
			}
		}

		//カウンセラー指定の場合、スタッフ名を求める
		$select_staff_nm = "";
		$select_open_staff_nm = "";
		
		if( $select_staff_cd != "" && $err_flg == 0 ){
			$query = 'select DECODE(STAFF_NM,"' . $ANGpw . '"),DECODE(OPEN_STAFF_NM,"' . $ANGpw . '") from M_STAFF where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and STAFF_CD = "' . $select_staff_cd . '";';
			$result = mysql_query($query);
			if (!$result) {
				$err_flg = 4;
				//エラーメッセージ表示
				require( './zs_errgmn.php' );
						
				//**ログ出力**
				$log_sbt = 'E';					//ログ種別    （ N:通常ログ  W:警告  E:エラー T:トランザクション ）
				$log_kkstaff_kbn = 'S';			//顧客店舗区分（ K:顧客サイト  S:スタッフサイト ）
				$log_office_cd = $office_cd;	//オフィスコード
				$log_kaiin_no = $staff_cd;		//会員番号 または スタッフコード
				$log_naiyou = 'スタッフマスタの参照に失敗しました。';	//内容
				$log_err_inf = $query;			//エラー情報
				require( './zs_log.php' );
				//************
						
			}else{
				while( $row = mysql_fetch_array($result) ){
					$select_staff_nm = $row[0];			//スタッフ名
					$select_open_staff_nm = $row[1];	//公開スタッフ名
				}
			}
		}


		if( $err_flg == 0 ){
			//エラーなし

			//ページ編集
			print('<center>');
			
			//ページ編集
			print('<table><tr>');
			print('<td width="950" bgcolor="lightblue"><img src="./img_' . $lang_cd . '/bar_kbtcounseling_menu.png" border="0"></td>');
			print('</tr></table>');
			
			print('<table border="0">');	//sub01
			print('<tr>');	//sub01
			print('<td width="85">&nbsp;</td>');	//sub01
			print('<td width="730" align="center">');	//sub01
			
			print('<table border="1">');
			//会場
			print('<tr>');
			print('<td width="110" align="center" valign="middle" background="../img_' . $lang_cd . '/bg_mizu_110x20.png"><img src="./img_' . $lang_cd . '/title_mini_kaijyou.png" border="0"></td>');
			print('<td width="565" align="center" valign="middle" background="../img_' . $lang_cd . '/bg_mizu_565x20.png"><font size="5">' . $Moffice_office_nm . '</font></td>');
			print('</tr>');
			//日時
			print('<tr>');
			print('<td align="center" valign="middle" background="../img_' . $lang_cd . '/bg_kimidori_110x20.png"><img src="./img_' . $lang_cd . '/title_mini_nichiji.png" border="0"></td>');
			if( $select_eigyoubi_flg == 1 || $select_youbi_cd == 0 ){
				$fontcolor = "red";	//祝日/日曜
			}else if( $select_youbi_cd == 6 ){
				$fontcolor = "blue";	//土曜
			}else{
				$fontcolor = "black";	//平日
			}
			print('<td align="center" valign="bottom" background="../img_' . $lang_cd . '/bg_kimidori_565x20.png">&nbsp;&nbsp;<font size="6" color="' . $fontcolor . '">' . substr($select_ymd,0,4) . '</font><font size="1" color="' . $fontcolor . '">&nbsp;年</font>&nbsp;<font size="6" color="' . $fontcolor . '">&nbsp;' . sprintf("%d",substr($select_ymd,4,2)) . '</font><font size="1" color="' . $fontcolor . '">&nbsp;月</font>&nbsp;<font size="6" color="' . $fontcolor . '">&nbsp;' . sprintf("%d",substr($select_ymd,6,2)) . '</font><font size="1" color="' . $fontcolor . '">&nbsp;日</font>&nbsp;<font size="5" color="' . $fontcolor . '">' . $week[$select_youbi_cd] . '</font><font size="1" color="' . $fontcolor . '">&nbsp;曜日</font>&nbsp;<font size="6">' . intval($Mclass_st_time / 100) . ':' . sprintf("%02d",($Mclass_st_time % 100)) . '&nbsp;-&nbsp;' . intval($Mclass_ed_time / 100) . ':' . sprintf("%02d",($Mclass_ed_time % 100)) . '</font></td>');
			print('</tr>');
			//カウンセラー
			print('<tr>');
			print('<td align="center" valign="middle" background="../img_' . $lang_cd . '/bg_pink_110x20.png"><img src="./img_' . $lang_cd . '/title_mini_counselor.png" border="0"></td>');
			print('<td align="center" valign="middle" background="../img_' . $lang_cd . '/bg_pink_565x20.png">');
			if( $select_staff_nm != "" ){
				print('<font size="5">' . $select_open_staff_nm . '</font>&nbsp;(' . $select_staff_nm . ')' );
			}else{
				print('(カウンセラー指定なし)');
			}
			print('</td>');
			print('</tr>');
			print('</table>');
			
			if( $select_eigyoubi_flg == 8 || $select_eigyoubi_flg == 9 ){
				//非営業日
				print('<font color="red">非営業日です。</font>');
			}else if( $select_ymd < $now_yyyymmdd ){
				//「過去日を選択しています」
				print('<img src="./img_' . $lang_cd . '/warning_kakobi.png" border="0">');
			}else if( $select_ymd == $now_yyyymmdd && $Mclass_ed_time <= ($now_hh * 100 + $now_ii) ){
				//「過去時刻を選択しています」
				print('<img src="./img_' . $lang_cd . '/warning_kakojikoku.png" border="0">');
			}
			
			print('</td>');	//sub01
			//戻るボタン
			print('<form method="post" action="kbtcounseling_trk_serch.php">');
			print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
			print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
			print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
			print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
			print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
			print('<input type="hidden" name="select_ymd" value="' . $select_ymd . '">');
			print('<input type="hidden" name="select_jknkbn" value="' . $select_jknkbn . '">');
			print('<input type="hidden" name="select_staff_cd" value="' . $select_staff_cd . '">');
			print('<td width="135" align="center" valign="top">');	//sub01
			$tabindex++;
			print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_modoru_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_modoru_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_modoru_1.png\';" onClick="kurukuru()" border="0">');
			print('</td>');	//sub01
			print('</form>');
			print('</tr>');	//sub01
			print('</table>');	//sub01
			
			//「会員情報を入力後、登録ボタンを押下してください。」
			print('<img src="./img_' . $lang_cd . '/title_kaiinnyuuryoku_go_trk.png" border="0"><br>');

			print('<table border="0">');
			print('<tr>');
			print('<td align="left">');

			print('<form method="post" action="kbtcounseling_trk_new1.php">');
			print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
			print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
			print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
			print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
			print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
			print('<input type="hidden" name="select_ymd" value="' . $select_ymd . '">');
			print('<input type="hidden" name="select_jknkbn" value="' . $select_jknkbn . '">');
			print('<input type="hidden" name="select_staff_cd" value="' . $select_staff_cd . '">');
		
			print('<table border="0">');
			print('<tr>');
			//会員名
			print('<td width="450" align="left" valign="top">');
			print('<img src="./img_' . $lang_cd . '/title_kaiinnm.png" border="0"><br>');
			print('<table border="0">');
			print('<tr>');
			print('<td align="left" valign="top">');
			print('<font size="2">(姓)</font><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_nm1" maxlength="20" size="9" value="' . $kaiin_nm1 . '" tabindex="' . $tabindex . '"  class="normal" style="font-size:20pt; ime-mode:active;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
			print('</td>');
			print('<td align="left" valign="top">');
			print('<font size="2">(名)</font><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_nm2" maxlength="20" size="9" value="' . $kaiin_nm2 . '" tabindex="' . $tabindex . '"  class="normal" style="font-size:20pt; ime-mode:active;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
			print('</td>');
			print('</tr>');
			print('</table>');
			print('</td>');
			print('</tr>');
			print('<tr>');
			//会員カナ
			print('<td width="450" valign="top">');
			print('<img src="./img_' . $lang_cd . '/title_kaiinnm_k.png" border="0"><br>');
			print('<table border="0">');
			print('<tr>');
			print('<td align="left" valign="top">');
			print('<font size="2">(セイ)</font><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_nm_k1" maxlength="20" size="9" value="' . $kaiin_nm_k1 . '" tabindex="' . $tabindex . '"  class="normal" style="font-size:20pt; ime-mode:active;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
			print('</td>');
			print('<td align="left" valign="top">');
			print('<font size="2">(メイ)</font><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_nm_k2" maxlength="20" size="9" value="' . $kaiin_nm_k2 . '" tabindex="' . $tabindex . '"  class="normal" style="font-size:20pt; ime-mode:active;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
			print('</td>');
			print('</tr>');
			print('</table>');
			print('</td>');
			print('</tr>');
			print('</table>');
		
			print('<table border="0">');
			print('<tr>');
			//会員電話番号
			print('<td width="300" valign="top">');
			print('<img src="./img_' . $lang_cd . '/title_kaiintel.png" border="0"><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_tel" maxlength="15" size="16" value="' . $kaiin_tel . '" tabindex="' . $tabindex . '" class="normal" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'"><br>');
			print('<font size="2">(半角英数字)<br></font>');
			print('</td>');
			//会員メールアドレス
			print('<td width="450" valign="top">');
			print('<img src="./img_' . $lang_cd . '/title_kaiinmail.png" border="0"><br>');
			$tabindex++;
			print('<input type="text" name="kaiin_mail" maxlength="60" size="30" value="' . $kaiin_mail . '" tabindex="' . $tabindex . '" class="normal" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'"><br>');
			print('<font size="2">(半角英数字)<br></font>');
			print('</td>');
			print('</tr>');
			print('</table>');

			//興味のある国
			print('<input type="hidden" name="kaiin_kyoumi" value="' . $kaiin_kyoumi . '" />');
//			print('<table border="0">');
//			print('<tr>');
//			print('<td width="750" align="left">');
//			print('<img src="./img_' . $lang_cd . '/title_kyoumi.png" border="0"><br>');
//			$tabindex++;
//			print('<input type="text" name="kaiin_kyoumi" maxlength="100" size="30" value="' . $kaiin_kyoumi . '" class="normal" tabindex="' . $tabindex . '" style="font-size:20pt; background-color: #E0FFFF; ime-mode:active; font-family: sans-serif;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
//			print('</td>');
//			print('</tr>');
//			print('</table>');

			//出発予定時期
			print('<input type="hidden" name="kaiin_jiki" value="' . $kaiin_jiki . '" />');
//			print('<table border="0">');
//			print('<tr>');
//			print('<td width="750" align="left">');
//			print('<img src="./img_' . $lang_cd . '/title_jiki.png" border="0"><br>');
//			$tabindex++;
//			print('<input type="text" name="kaiin_jiki" maxlength="100" size="30" value="' . $kaiin_jiki . '" class="normal" tabindex="' . $tabindex . '" style="font-size:20pt; background-color: #E0FFFF; ime-mode:active; font-family: sans-serif;" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">');
//			print('</td>');
//			print('</tr>');
//			print('</table>');

			//相談内容
			print('<table border="0">');
			print('<tr>');
			print('<td width="750" align="left">');
			print('<img src="./img_' . $lang_cd . '/title_soudan.png" border="0"><br>');
			$tabindex++;
			print('<textarea name="kaiin_soudan" rows="5"  style="ime-mode:active;" tabindex="' . $tabindex . '" class="normal" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">' . $kaiin_soudan . '</textarea>');
			print('</td>');
			print('</tr>');
			print('</table>');

			print('</td>');
			print('</tr>');
			print('</table>');
			
			
			print('<table border="0">');
			print('<tr>');
			print('<td width="90" rowspan="2">&nbsp;</td>');
			print('<td width="725" rowspan="2" align="left" valign="top">');
			//記入例
			print('<img src="./img_' . $lang_cd . '/title_kinyurei.png" border="0"><br>');
			print('</td>');
			print('<td width="135" align="right">');
			$tabindex++;
			print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_trk_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_trk_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_trk_1.png\';" onClick="kurukuru()" border="0">');
			print('</td>');
			print('</form>');
			print('</tr>');
			print('<tr>');
			print('<form method="post" action="kbtcounseling_trk_serch.php">');
			print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
			print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
			print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
			print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
			print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
			print('<input type="hidden" name="select_ymd" value="' . $select_ymd . '">');
			print('<input type="hidden" name="select_jknkbn" value="' . $select_jknkbn . '">');
			print('<input type="hidden" name="select_staff_cd" value="' . $select_staff_cd . '">');
			print('<td align="right">');
			$tabindex++;
			print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_modoru_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_modoru_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_modoru_1.png\';" onClick="kurukuru()" border="0">');
			print('</td>');
			print('</form>');
			print('</tr>');
			print('</table>');
			
			print('</center>');
		
			print('<hr>');
		
		}
	}

	mysql_close( $link );
?>
</body>
</html>