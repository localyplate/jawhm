<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow">
<title>管理画面－スタッフ（新規登録）確認</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery.activity-indicator-1.0.0.min.js"></script> 
<style type="text/css">
input.err,select.err,textarea.err {
	background-color: #FF0000;
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
	$gmn_id = 'kanri_staff_trk1.php';
	//遷移ＯＫとする遷移元の画面ＩＤ
	$ok_gmn = array('kanri_staff_trk0.php','kanri_staff_trk1.php');

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
	
	//固有引数の取得
	$select_office_cd = $_POST['select_office_cd'];

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
			if ( $lang_cd == "" || $office_cd == "" || $staff_cd == "" || $select_office_cd == "" ){
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

		//店舗メニューボタン表示
		require( './zs_menu_button.php' );

		//画像事前読み込み
		print('<img src="./img_' . $lang_cd . '/btn_modoru_2.png" width="0" height="0" style="visibility:hidden;">');
		print('<img src="./img_' . $lang_cd . '/btn_trk_2.png" width="0" height="0" style="visibility:hidden;">');


		//ページ編集
		//固有引数の取得
		$stf_cd = $_POST['stf_cd'];			//登録対象のスタッフコード
		$stf_nm = $_POST['stf_nm'];			//登録対象のスタッフ名
		$open_stf_nm = $_POST['open_stf_nm'];	//登録対象の公開スタッフ名
		$kanri_flg = $_POST['kanri_flg'];	//管理者フラグ
		$stf_tel = $_POST['stf_tel'];		//電話番号
		$stf_mail = $_POST['stf_mail'];		//メールアドレス
		$class_cd1 = $_POST['class_cd1'];	//クラス１
		$class_cd2 = $_POST['class_cd2'];	//クラス２
		$class_cd3 = $_POST['class_cd3'];	//クラス３
		$class_cd4 = $_POST['class_cd4'];	//クラス４
		$class_cd5 = $_POST['class_cd5'];	//クラス５
		$ope_auth = $_POST['ope_auth'];		//業務権限
		$st_year = $_POST['st_year'];		//開始年
		$st_month = $_POST['st_month'];		//開始月
		$st_day = $_POST['st_day'];			//開始日
		$ed_year = $_POST['ed_year'];		//終了年
		$ed_month = $_POST['ed_month'];		//終了月
		$ed_day = $_POST['ed_day'];			//終了日
		$yukou_flg = $_POST['yukou_flg'];	//有効・無効フラグ
		
		$err_cnt = 0;	//エラー件数

		//固有引数チェック
		//スタッフコード
		$err_stf_cd	=0;
		$strcng_bf = $stf_cd;
		require( '../zz_strdel.php' );	// ”と’を削除する
		$stf_cd = $strcng_af;
		if( strlen( $stf_cd ) == 0 ){
			$err_stf_cd = 1;
			$err_cnt++;
		}else{
			//存在チェック
			$query = 'select count(*) from M_STAFF where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and STAFF_CD = "' . $stf_cd . '";';
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
				$row = mysql_fetch_array($result);
		
				if( $row[0] > 0 ){
					//データが既に存在する場合
					$err_stf_cd = 2;
					$err_cnt++;
				}
			}
		}

		//クラスマスタの取得
		$Mclass_cnt = 0;
		$query = 'select CLASS_CD,CLASS_NM from M_CLASS where KG_CD = "' . $DEF_kg_cd . '" and OFFICE_CD = "' . $select_office_cd . '" and YUKOU_FLG = 1 order by ST_DATE,CLASS_CD;';
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
			$log_naiyou = 'クラスマスタの参照に失敗しました。';	//内容
			$log_err_inf = $query;			//エラー情報
			require( './zs_log.php' );
			//************
			
		}else{
			while( $row = mysql_fetch_array($result) ){
				$Mclass_class_cd[$Mclass_cnt] = $row[0];	//クラスコード
				$Mclass_class_nm[$Mclass_cnt] = $row[1];	//クラス名
				$Mclass_cnt++;
			}
		}


		//スタッフ名
		$err_stf_nm	= 0;
		$strcng_bf = $stf_nm;
		require( '../zz_strdel.php' );	// ”と’を削除する
		$stf_nm = $strcng_af;
		if( strlen( $stf_nm ) == 0 ){
			$err_stf_nm = 1;
			$err_cnt++;
		}
		
		//公開スタッフ名
		$err_open_stf_nm = 0;
		if( $open_stf_nm == "" ){
			$open_stf_nm = $stf_nm;
		}
		
		//電話番号
		$err_stf_tel = 0;
		if( $stf_tel != '' ){
			$stf_tel = mb_convert_kana( $stf_tel , k , "shift_jis");
			$stf_tel = mb_convert_kana( $stf_tel , h , "shift_jis");
			$stf_tel = mb_convert_kana( $stf_tel , a , "shift_jis");
			$strcng_bf = $stf_tel;
			require( '../zz_strdel.php' );	// ”と’を削除する
			$stf_tel = $strcng_af;
			//（電話番号チェック：数字のみの文字列を取得する）
			$telchk_bf = $stf_tel;
			require( '../zz_telchk.php' );	//$telchk_af に数字のみが設定されてくる
			$stf_tel_af = $telchk_af;
			$err_stf_tel = $telchk_rtncd;
			if( $err_stf_tel != 0 ){
				$err_cnt++;
			}
		}

		//メールアドレス
		$err_stf_mail = 0;
   		if( $stf_mail != '' ){
			if( strlen( $stf_mail ) < 7 ){
				$err_stf_mail = 1;
				$err_cnt++;
			}
		}
		
		//クラス１～５
		//並べなおしと重複分を削除
		//今回はマスタのチェックはしない。
		$tmp_class_cd_cnt = 0;
		$tmp_class_cd[0] = '';
		$tmp_class_cd[1] = '';
		$tmp_class_cd[2] = '';
		$tmp_class_cd[3] = '';
		$tmp_class_cd[4] = '';
				
		if( $class_cd1 != '' ){
			$tmp_class_cd[$tmp_class_cd_cnt] = $class_cd1;
			$tmp_class_cd_cnt++;
		}
				
		if( $class_cd2 != '' ){
			$find = 0;
			$z = 0;
			while( $z < $tmp_class_cd_cnt && $find == 0 ){
				if( $class_cd2 == $tmp_class_cd[$z] ){
					$find = 1;
				}else{
					$z++;
				}
			}
			$tmp_class_cd[$z] = $class_cd2;
			if( $z == $tmp_class_cd_cnt ){
				$tmp_class_cd_cnt++;
			}
		}
				
		if( $class_cd3 != '' ){
			$find = 0;
			$z = 0;
			while( $z < $tmp_class_cd_cnt && $find == 0 ){
				if( $class_cd3 == $tmp_class_cd[$z] ){
					$find = 1;
				}else{
					$z++;
				}
			}
			$tmp_class_cd[$z] = $class_cd3;
			if( $z == $tmp_class_cd_cnt ){
				$tmp_class_cd_cnt++;
			}
		}
				
		if( $class_cd4 != '' ){
			$find = 0;
			$z = 0;
			while( $z < $tmp_class_cd_cnt && $find == 0 ){
				if( $class_cd4 == $tmp_class_cd[$z] ){
					$find = 1;
				}else{
					$z++;
				}
			}
			$tmp_class_cd[$z] = $class_cd4;
			if( $z == $tmp_class_cd_cnt ){
				$tmp_class_cd_cnt++;
			}
		}
					
		if( $class_cd5 != '' ){
			$find = 0;
			$z = 0;
			while( $z < $tmp_class_cd_cnt && $find == 0 ){
				if( $class_cd5 == $tmp_class_cd[$z] ){
					$find = 1;
				}else{
					$z++;
				}
			}
			$tmp_class_cd[$z] = $class_cd5;
			if( $z == $tmp_class_cd_cnt ){
				$tmp_class_cd_cnt++;
			}
		}
				
		$class_cd1 = $tmp_class_cd[0];
		$class_cd2 = $tmp_class_cd[1];
		$class_cd3 = $tmp_class_cd[2];
		$class_cd4 = $tmp_class_cd[3];
		$class_cd5 = $tmp_class_cd[4];
		
		//業務権限
		$err_ope_auth = 0;
		if( $ope_auth == "" ){
			$ope_auth = 0;
		}else{
			if( !is_numeric($ope_auth) ){
				//数字ではない
				$err_ope_auth = 1;
				$err_cnt++;
			}else{
				if( $ope_auth < 0 || $ope_auth > 999 ){
					//範囲外[0-999]
					$err_ope_auth = 2;
					$err_cnt++;
				}
			}
		}
		
		//有効期間（開始年月日）
		$err_st_year = 0;
		if($st_year == ''){
			$err_st_year = 1;
			$err_cnt++;
		}else if(!ereg('[0-9]{4}',$st_year)){
			$err_st_year = 1;
			$err_cnt++;
		}else if( $st_year < 2012 ){
			$err_st_year = 1;
			$err_cnt++;
		}

		$err_st_month = 0;
		if($st_month == ''){
			$err_st_month = 1;
			$err_cnt++;
		}elseif(!ereg('[1-9]',$st_month) or $st_month < 1 or $st_month > 12){
			$err_st_month = 1;
			$err_cnt++;
		}

		$err_st_day = 0;
		if( $err_st_year == 0 && $err_st_month == 0 ){
			//該当年月の日数を求める
			$DFmaxdd = cal_days_in_month(CAL_GREGORIAN, $st_month , $st_year );
			if($st_day == ''){
				$err_st_day = 1;
				$err_cnt++;
			}elseif(!ereg('[1-9]',$st_day) or $st_day < 1 or $st_day > $DFmaxdd ){
				$err_st_day = 1;
				$err_cnt++;
			}
		}else{
			$err_st_day = 1;
		}
		
		//有効期間（終了年月日）
		$err_ed_year = 0;
		if($ed_year == ''){
			$err_ed_year = 1;
			$err_cnt++;
		}else if(!ereg('[0-9]{4}',$ed_year)){
			$err_ed_year = 1;
			$err_cnt++;
		}else if( $ed_year < $now_yyyy || $ed_year >= 2038 ){
			$err_ed_year = 1;
			$err_cnt++;
		}
		
		$err_ed_month = 0;
		if($ed_month == ''){
			$err_ed_month = 1;
			$err_cnt++;
		}else if(!ereg('[1-9]',$ed_month) or $ed_month < 1 or $ed_month > 12){
			$err_ed_month = 1;
			$err_cnt++;
		}else if( $ed_year == $now_yyyy && $ed_month < $now_mm ){
			$err_ed_month = 1;
			$err_cnt++;
		}

		$err_ed_day = 0;
		if( $err_ed_year == 0 && $err_ed_month == 0 ){
			//該当年月の日数を求める
			$DFmaxdd = cal_days_in_month(CAL_GREGORIAN, $ed_month , $ed_year );
			if($ed_day == ''){
				$err_ed_day = 1;
				$err_cnt++;
			}else if(!ereg('[1-9]',$ed_day) or $ed_day < 1 or $ed_day > $DFmaxdd ){
				$err_ed_day = 1;
				$err_cnt++;
			}else if( $ed_year == $now_yyyy && $ed_month == $now_mm && $ed_day < $now_dd ){
				$err_ed_day = 1;
				$err_cnt++;
			}
		}else{
			$err_ed_day = 1;
			$err_cnt++;
		}
		
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
		}
		
		//クラス名の編集
		$class_nm1 = '';
		$class_nm2 = '';
		$class_nm3 = '';
		$class_nm4 = '';
		$class_nm5 = '';
		
		if( $class_cd1 != '' ){
			$find = 0;
			$i = 0;
			while( $i < $Mclass_cnt && $find == 0 ){
				if( $class_cd1 == $Mclass_class_cd[$i] ){
					$class_nm1 = $Mclass_class_nm[$i];
					$find = 1;
				}else{
					$i++;
				}
			}
		}

		if( $class_cd2 != '' ){
			$find = 0;
			$i = 0;
			while( $i < $Mclass_cnt && $find == 0 ){
				if( $class_cd2 == $Mclass_class_cd[$i] ){
					$class_nm2 = $Mclass_class_nm[$i];
					$find = 1;
				}else{
					$i++;
				}
			}
		}

		if( $class_cd3 != '' ){
			$find = 0;
			$i = 0;
			while( $i < $Mclass_cnt && $find == 0 ){
				if( $class_cd3 == $Mclass_class_cd[$i] ){
					$class_nm3 = $Mclass_class_nm[$i];
					$find = 1;
				}else{
					$i++;
				}
			}
		}

		if( $class_cd4 != '' ){
			$find = 0;
			$i = 0;
			while( $i < $Mclass_cnt && $find == 0 ){
				if( $class_cd4 == $Mclass_class_cd[$i] ){
					$class_nm4 = $Mclass_class_nm[$i];
					$find = 1;
				}else{
					$i++;
				}
			}
		}
			
		if( $class_cd5 != '' ){
			$find = 0;
			$i = 0;
			while( $i < $Mclass_cnt && $find == 0 ){
				if( $class_cd5 == $Mclass_class_cd[$i] ){
					$class_nm5 = $Mclass_class_nm[$i];
					$find = 1;
				}else{
					$i++;
				}
			}
		}


		if( $err_flg == 0 ){
			
			//明細データにエラーがあるか？
			if( $err_cnt == 0 ){
				//エラーなし
			
				print('<center>');
			
				//ページ編集
				print('<table bgcolor="pink"><tr><td width="950">');
				print('<img src="./img_' . $lang_cd . '/bar_kanri_staff.png" border="0">');
				print('</td></tr></table>');
		
				print('<table border="0">');
				print('<tr>');
				print('<td width="815" align="left"><img src="./img_' . $lang_cd . '/bar_officenm2.png"><br><font size="5" color="blue">' . $Moffice_office_nm . '</font></td>');
				print('<form method="post" action="kanri_staff_trk0.php">');
				print('<td align="right">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
				print('<input type="hidden" name="stf_cd" value="' . $stf_cd . '">');
				print('<input type="hidden" name="stf_nm" value="' . $stf_nm . '">');
				print('<input type="hidden" name="kanri_flg" value="' . $kanri_flg . '">');
				print('<input type="hidden" name="stf_tel" value="' . $stf_tel . '">');
				print('<input type="hidden" name="stf_mail" value="' . $stf_mail . '">');
				print('<input type="hidden" name="class_cd1" value="' . $class_cd1 . '">');
				print('<input type="hidden" name="class_cd2" value="' . $class_cd2 . '">');
				print('<input type="hidden" name="class_cd3" value="' . $class_cd3 . '">');
				print('<input type="hidden" name="class_cd4" value="' . $class_cd4 . '">');
				print('<input type="hidden" name="class_cd5" value="' . $class_cd5 . '">');
				print('<input type="hidden" name="st_year" value="' . $st_year . '">');
				print('<input type="hidden" name="st_month" value="' . $st_month . '">');
				print('<input type="hidden" name="st_day" value="' . $st_day . '">');
				print('<input type="hidden" name="ed_year" value="' . $ed_year . '">');
				print('<input type="hidden" name="ed_month" value="' . $ed_month . '">');
				print('<input type="hidden" name="ed_day" value="' . $ed_day . '">');
				print('<input type="hidden" name="yukou_flg" value="' . $yukou_flg . '">');
				$tabindex++;
				print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_modoru_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_modoru_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_modoru_1.png\';" onClick="kurukuru()" border="0">');
				print('</td>');
				print('</form>');
				print('</tr>');
				print('</table>');

				print('<hr>');
				
				print('<font color="blue">※以下の内容でよろしければ、登録ボタンを押下してください。</font><br>');
			
				print('<table border="0">');
				print('<tr>');
				print('<td align="left">');
				
				//スタッフコード・スタッフ名・管理者フラグ
				print('<table border="0">');
				print('<tr>');
				print('<td><b>スタッフコード(*)</b>&nbsp;&nbsp;<br>');
				print('<font size="5" color="blue">' . $stf_cd . '</font>&nbsp;&nbsp;</font>');
				print('</td>');
				print('<td><b>スタッフ名(*)</b>&nbsp;&nbsp;&nbsp;&nbsp;<br>');
				print('<font size="5" color="blue">' . $stf_nm . '</font>&nbsp;&nbsp;&nbsp;&nbsp;</font>');
				print('</td>');
				print('<td><b>管理者フラグ</b><br>');
				if( $kanri_flg == 0 ){
					print('<font size="5" color="gray">管理者ではない</font>');
				}else{
					print('<font size="5" color="red">管理者</font>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');

				//公開スタッフ名
				print('<table border="0">');
				print('<tr>');
				print('<td><b>会員サイトに表示するスタッフ名</b><br>');
				if( $open_stf_nm == '' ){
					print('<font size="5" color="gray">（未登録）</font>');
				}else{
					print('<font size="5" color="blue">' . $open_stf_nm . '</font>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');

				//電話番号
				print('<table border="0">');
				print('<tr>');
				print('<td align="left"><b>電話番号</b>&nbsp;&nbsp;<br>');
				if( $stf_tel == '' ){
					print('<font size="5" color="gray">（未登録）</font>');
				}else{
					print('<font size="5" color="blue">' . $stf_tel . '</font>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');
				
				//メールアドレス
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" valign="top"><b>メールアドレス</b>&nbsp;&nbsp;<br>');
				if( $stf_mail == '' ){
					print('<font size="5" color="gray">（未登録）</font>');
				}else{
					print('<font size="4" color="blue">' . $stf_mail . '</font>&nbsp;&nbsp;');
				}
				print('</td>');
				print('</tr>');
				print('</table>');	

				//予約種別１～５
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" colspan="5"><b>予約種別</b>･･･カウンセラー／講師の場合は担当する予約種別を選択して下さい。</td>');
				print('</tr>');
				
				if( $class_cd1 == '' ){
					print('<tr>');
					print('<td colspan="5">');
					print('<font size="5" color="gray">（カウンセラー／講師ではない）</font>');
					print('</td>');
					print('</tr>');
					
				}else{
					print('<tr>');
					//予約種別（１）
					print('<td align="left">予約種別（１）<br>');
					if( $class_cd1 == '' ){
						print('<font size="2" color="gray">（未選択）</font>');
					}else{
						print('<font size="4" color="blue">' . $class_nm1 . '</font>&nbsp;&nbsp;');
					}
					print('</td>');
					
					//予約種別（２）
					print('<td align="left">予約種別（２）<br>');
					if( $class_cd2 == '' ){
						print('<font size="2" color="gray">（未選択）</font>');
					}else{
						print('<font size="4" color="blue">' . $class_nm2 . '</font>&nbsp;&nbsp;');
					}
					print('</td>');
		
					//予約種別（３）
					print('<td align="left">予約種別（３）<br>');
					if( $class_cd3 == '' ){
						print('<font size="2" color="gray">（未選択）</font>');
					}else{
						print('<font size="4" color="blue">' . $class_nm3 . '</font>&nbsp;&nbsp;');
					}
					print('</td>');
		
					//予約種別（４）
					print('<td align="left">予約種別（４）<br>');
					if( $class_cd4 == '' ){
						print('<font size="2" color="gray">（未選択）</font>');
					}else{
						print('<font size="4" color="blue">' . $class_nm4 . '</font>&nbsp;&nbsp;');
					}
					print('</td>');
		
					//予約種別（５）
					print('<td align="left">予約種別（５）<br>');
					if( $class_cd5 == '' ){
						print('<font size="2" color="gray">（未選択）</font>');
					}else{
						print('<font size="4" color="blue">' . $class_nm5 . '</font>&nbsp;&nbsp;');
					}
					print('</td>');
					
					print('</tr>');
				}
				print('</table>');

				//業務権限
				print('<table border="0">');
				print('<tr>');
				print('<td align="left"><b>業務権限</b>&nbsp;&nbsp;<br>');
				print('<font size="5" color="blue">' . sprintf("%03d",$ope_auth) . '</font>');
				print('</td>');
				print('</tr>');
				print('</table>');

				print('<br>');
				
				//有効期間
				print('<b>有効期間(*)</b>・・・上記スタッフの有効期間<br>');
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" valign="top">');
				print('開始日<br>');
				print('<font color="blue" size="5">&nbsp;<b>' . $st_year . '</b></font>');
				print('年');
				print('<font color="blue" size="5">&nbsp;<b>' . $st_month . '</b></font>');
				print('月');
				print('<font color="blue" size="5">&nbsp;<b>' . $st_day . '</b></font>');
				print('日 から');
				print('</td>');
				print('<td align="left" valign="top">');
				print('終了日<font size="2">&nbsp;(現在の最大日は2037/12/31)</font><br>');
				print('<font color="blue" size="5">&nbsp;<b>' . $ed_year . '</b></font>');
				print('年');
				print('<font color="blue" size="5">&nbsp;<b>' . $ed_month . '</b></font>');
				print('月');
				print('<font color="blue" size="5">&nbsp;<b>' . $ed_day . '</b></font>');
				print('日 まで');
				print('</td>');
				print('</tr>');
				print('</table>');
	
				print('</td>');
				print('</tr>');
				print('</table>');
	
				//有効無効／登録ボタン／戻るボタン
				print('<table border="0">');
				print('<tr>');
				print('<td width="815" align="left">');
				print('<b>有効／無効(*)</b><br>');
				if( $yukou_flg == 1 ){
					print('<font color="blue" size="5"><b>有効</b></font>');
				}else{
					print('<font color="red" size="5"><b>無効</b></font>');
				}
				print('<br><font size="2" color="red">&nbsp;無効&nbsp;</font><font size="2">にすると予約システム上には表示されません。</font>');
				print('</td>');
				print('<td align="right">');
				print('<form method="post" action="kanri_staff_trk2.php">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
				print('<input type="hidden" name="stf_cd" value="' . $stf_cd . '">');
				print('<input type="hidden" name="stf_nm" value="' . $stf_nm . '">');
				print('<input type="hidden" name="open_stf_nm" value="' . $open_stf_nm . '">');
				print('<input type="hidden" name="kanri_flg" value="' . $kanri_flg . '">');
				print('<input type="hidden" name="stf_tel" value="' . $stf_tel . '">');
				print('<input type="hidden" name="stf_mail" value="' . $stf_mail . '">');
				print('<input type="hidden" name="class_cd1" value="' . $class_cd1 . '">');
				print('<input type="hidden" name="class_cd2" value="' . $class_cd2 . '">');
				print('<input type="hidden" name="class_cd3" value="' . $class_cd3 . '">');
				print('<input type="hidden" name="class_cd4" value="' . $class_cd4 . '">');
				print('<input type="hidden" name="class_cd5" value="' . $class_cd5 . '">');
				print('<input type="hidden" name="ope_auth" value="' . $ope_auth . '">');
				print('<input type="hidden" name="st_year" value="' . $st_year . '">');
				print('<input type="hidden" name="st_month" value="' . $st_month . '">');
				print('<input type="hidden" name="st_day" value="' . $st_day . '">');
				print('<input type="hidden" name="ed_year" value="' . $ed_year . '">');
				print('<input type="hidden" name="ed_month" value="' . $ed_month . '">');
				print('<input type="hidden" name="ed_day" value="' . $ed_day . '">');
				print('<input type="hidden" name="yukou_flg" value="' . $yukou_flg . '">');
				$tabindex++;
				print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_trk_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_trk_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_trk_1.png\';" onClick="kurukuru()" border="0">');
				print('</td>');
				print('</form>');
				print('</tr>');
				print('<tr>');
				print('<td width="815" align="left">&nbsp;</td>');
				print('<form method="post" action="kanri_staff_trk0.php">');
				print('<td align="right">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
				print('<input type="hidden" name="stf_cd" value="' . $stf_cd . '">');
				print('<input type="hidden" name="stf_nm" value="' . $stf_nm . '">');
				print('<input type="hidden" name="open_stf_nm" value="' . $open_stf_nm . '">');
				print('<input type="hidden" name="kanri_flg" value="' . $kanri_flg . '">');
				print('<input type="hidden" name="stf_tel" value="' . $stf_tel . '">');
				print('<input type="hidden" name="stf_mail" value="' . $stf_mail . '">');
				print('<input type="hidden" name="class_cd1" value="' . $class_cd1 . '">');
				print('<input type="hidden" name="class_cd2" value="' . $class_cd2 . '">');
				print('<input type="hidden" name="class_cd3" value="' . $class_cd3 . '">');
				print('<input type="hidden" name="class_cd4" value="' . $class_cd4 . '">');
				print('<input type="hidden" name="class_cd5" value="' . $class_cd5 . '">');
				print('<input type="hidden" name="ope_auth" value="' . $ope_auth . '">');
				print('<input type="hidden" name="st_year" value="' . $st_year . '">');
				print('<input type="hidden" name="st_month" value="' . $st_month . '">');
				print('<input type="hidden" name="st_day" value="' . $st_day . '">');
				print('<input type="hidden" name="ed_year" value="' . $ed_year . '">');
				print('<input type="hidden" name="ed_month" value="' . $ed_month . '">');
				print('<input type="hidden" name="ed_day" value="' . $ed_day . '">');
				print('<input type="hidden" name="yukou_flg" value="' . $yukou_flg . '">');
				$tabindex++;
				print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_modoru_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_modoru_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_modoru_1.png\';" onClick="kurukuru()" border="0">');
				print('</td>');
				print('</form>');
				print('</tr>');
				print('</table>');
		
				print('</center>');
				
				print('<hr>');
			
			
			}else if( $err_cnt > 0 ){
				//エラーがある場合
				
				print('<center>');
				
				//ページ編集
				print('<table bgcolor="pink"><tr><td width="950">');
				print('<img src="./img_' . $lang_cd . '/bar_kanri_staff.png" border="0">');
				print('</td></tr></table>');
		
				print('<table border="0">');
				print('<tr>');
				print('<td width="815" align="left"><img src="./img_' . $lang_cd . '/bar_officenm2.png"><br><font size="5" color="blue">' . $Moffice_office_nm . '</font></td>');
				print('<form method="post" action="kanri_staff_select.php">');
				print('<td align="right">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
				$tabindex++;
				print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_modoru_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_modoru_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_modoru_1.png\';" onClick="kurukuru()" border="0">');
				print('</td>');
				print('</form>');
				print('</tr>');
				print('</table>');
	
				print('<hr>');
				
				print('<font color="red">※エラーがあります。</font><br>');
			
				print('<form method="post" action="kanri_staff_trk1.php">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
				
				print('<table border="0">');
				print('<tr>');
				print('<td align="left">');
				
				//スタッフコード・スタッフ名・管理者フラグ
				print('<table border="0">');
				print('<tr>');
				print('<td valign="top"><b>スタッフコード(*)</b><br>');
				$tabindex++;
				print('<input type="text" name="stf_cd" maxlength="20" size="20" tabindex="' . $tabindex . '" value="' . $stf_cd . '"class="');
				if( $err_stf_cd == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>');
				if( $err_stf_cd == 2 ){
					print('<font size="2" color="red">エラー：既に登録されています。&nbsp;</font>');
				}else if( $err_stf_cd != 0 ){
					print('<font size="2" color="red">エラー：入力してください。&nbsp;</font>');
				}
				print('<font size="2">(半角英数字:最大20桁)&nbsp;&nbsp;</font>');
				print('</td>');
				print('<td valign="top"><b>スタッフ名(*)</b><br>');
				$tabindex++;
				print('<input type="text" name="stf_nm" maxlength="40" size="25" tabindex="' . $tabindex . '" value="' . $stf_nm . '"style="font-size:20pt;ime-mode:active;" class="');
				if( $err_stf_nm == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>');
				print('<font size="2">(全角・半角英数字)&nbsp;&nbsp;</font>');
				print('</td>');
				print('<td valign="top"><b>管理者フラグ</b><br>');
				$tabindex++;
				print('<select name="kanri_flg" class="normal" tabindex="' . $tabindex . '" style="font-size:20pt;">');
				if( $kanri_flg == 0 ){
					print('<option value="0" selected></option>');
					print('<option value="1" class="color2">管理者</option>');
				}else{
					print('<option value="0"></option>');
					print('<option value="1" class="color2" selected>管理者</option>');
				}
				print('</select>');
				print('<br><font size="2">&nbsp;</font>');
				print('</td>');
				
				print('</tr>');
				print('</table>');

				//公開スタッフ名
				print('<table border="0">');
				print('<tr>');
				print('<td><b>会員サイトに表示するスタッフ名</b><br>');
				$tabindex++;
				print('<input type="text" name="open_stf_nm" maxlength="40" size="25" tabindex="' . $tabindex . '" value="' . $open_stf_nm . '"style="font-size:20pt;ime-mode:active;" class="');
				if( $err_open_stf_nm == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>');
				print('<font size="2">(全角・半角英数字)&nbsp;&nbsp;</font>');
				print('</td>');
				print('</tr>');
				print('</table>');

				//電話番号
				print('<table border="0">');
				print('<tr>');
				print('<td align="left"><b>電話番号</b><br>');
				$tabindex++;
				print('<input type="text" name="stf_tel" maxlength="15" size="16" tabindex="' . $tabindex . '" value="' . $stf_tel . '"class="');
				if( $err_stf_tel == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>')	;
				if( $err_stf_tel == 0 ){
					print('<font size="2">(半角英数字)&nbsp;&nbsp;</font>');
				}else{
					print('<font size="2" color="red">エラー：桁数を確認してください。</font><br>');
					print('<font size="2" color="red">（固定電話の場合は市外局番から入力してください。）</font><br>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');
			
				//メールアドレス
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" valign="top"><b>メールアドレス</b>･･･予約情報を受け取るアドレス<br>');
				$tabindex++;
				print('<input type="text" name="stf_mail" maxlength="60" size="30" tabindex="' . $tabindex . '" value="' . $stf_mail . '"class="');
				if( $err_stf_mail == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>');
				if( $err_stf_mail == 0 ){
					print('<font size="2">(半角英数字)&nbsp;&nbsp;</font>');
				}else if( $err_stf_mail == 1){
					print('<font size="2" color="red">エラー</font>');
				}else if( $err_stf_mail == 2){
					print('<font size="2" color="red">クラス予約をメール連絡するため、入力してください。</font>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');

				//予約種別１～５
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" colspan="5"><b>予約種別</b>･･･カウンセラー／講師の場合は担当する予約種別を選択して下さい。</td>');
				print('</tr>');
				
				print('<tr>');
				//予約種別（１）
				print('<td align="left">予約種別（１）<br>');
				$tabindex++;
				print('<select name="class_cd1" class="normal" tabindex="' . $tabindex . '" style="font-size:10pt">');
				if( $class_cd1 == '' ){
					print('<option value="" selected>&nbsp;</option>');
				}else{
					print('<option value="">&nbsp;</option>');
				}
				$i = 0;
				while( $i < $Mclass_cnt ){
					print('<option value="' . $Mclass_class_cd[$i] . '"');
					if( $Mclass_class_cd[$i] == $class_cd1 ){
						print(' selected>');
					}else{
						print('>');
					}
					print( $Mclass_class_cd[$i] . '&nbsp;' . $Mclass_class_nm[$i] . '</option>');
					
					$i++;
				}
				print('</select>');
				print('</td>');
				
				//予約種別（２）
				print('<td align="left">予約種別（２）<br>');
				$tabindex++;
				print('<select name="class_cd2" class="normal" tabindex="' . $tabindex . '" style="font-size:10pt">');
				if( $class_cd2 == '' ){
					print('<option value="" selected>&nbsp;</option>');
				}else{
					print('<option value="">&nbsp;</option>');
				}
				$i = 0;
				while( $i < $Mclass_cnt ){
					print('<option value="' . $Mclass_class_cd[$i] . '"');
					if( $Mclass_class_cd[$i] == $class_cd2 ){
						print(' selected>');
					}else{
						print('>');
					}
					print( $Mclass_class_cd[$i] . '&nbsp;' . $Mclass_class_nm[$i] . '</option>');
					
					$i++;
				}
				print('</select>');
				print('</td>');
	
				//予約種別（３）
				print('<td align="left">予約種別（３）<br>');
				$tabindex++;
				print('<select name="class_cd3" class="normal" tabindex="' . $tabindex . '" style="font-size:10pt">');
				if( $class_cd3 == '' ){
					print('<option value="" selected>&nbsp;</option>');
				}else{
					print('<option value="">&nbsp;</option>');
				}
				$i = 0;
				while( $i < $Mclass_cnt ){
					print('<option value="' . $Mclass_class_cd[$i] . '"');
					if( $Mclass_class_cd[$i] == $class_cd3 ){
						print(' selected>');
					}else{
						print('>');
					}
					print( $Mclass_class_cd[$i] . '&nbsp;' . $Mclass_class_nm[$i] . '</option>');
					
					$i++;
				}
				print('</select>');
				print('</td>');
	
				//予約種別（４）
				print('<td align="left">予約種別（４）<br>');
				$tabindex++;
				print('<select name="class_cd4" class="normal" tabindex="' . $tabindex . '" style="font-size:10pt">');
				if( $class_cd4 == '' ){
					print('<option value="" selected>&nbsp;</option>');
				}else{
					print('<option value="">&nbsp;</option>');
				}
				$i = 0;
				while( $i < $Mclass_cnt ){
					print('<option value="' . $Mclass_class_cd[$i] . '"');
					if( $Mclass_class_cd[$i] == $class_cd4 ){
						print(' selected>');
					}else{
						print('>');
					}
					print( $Mclass_class_cd[$i] . '&nbsp;' . $Mclass_class_nm[$i] . '</option>');
					
					$i++;
				}
				print('</select>');
				print('</td>');
	
				//予約種別（５）
				print('<td align="left">予約種別（５）<br>');
				$tabindex++;
				print('<select name="class_cd5" class="normal" tabindex="' . $tabindex . '" style="font-size:10pt">');
				if( $class_cd5 == '' ){
					print('<option value="" selected>&nbsp;</option>');
				}else{
					print('<option value="">&nbsp;</option>');
				}
				$i = 0;
				while( $i < $Mclass_cnt ){
					print('<option value="' . $Mclass_class_cd[$i] . '"');
					if( $Mclass_class_cd[$i] == $class_cd5 ){
						print(' selected>');
					}else{
						print('>');
					}
					print( $Mclass_class_cd[$i] . '&nbsp;' . $Mclass_class_nm[$i] . '</option>');
					
					$i++;
				}
				print('</select>');
				print('</td>');
				
				print('</tr>');
				print('</table>');

				//業務権限
				print('<table border="0">');
				print('<tr>');
				print('<td align="left"><b>業務権限</b><br>');
				$tabindex++;
				print('<input type="text" name="ope_auth" maxlength="3" size="3" tabindex="' . $tabindex . '" value="' . $ope_auth . '"class="');
				if( $err_ope_auth == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;ime-mode:disabled" onfocus="this.style.backgroundColor=\'#cccccc\'" onblur="this.style.backgroundColor=\'#E0FFFF\'">&nbsp;&nbsp;<br>')	;
				if( $err_ope_auth == 0 ){
					print('<font size="2">(半角英数字)&nbsp;&nbsp;</font>');
				}else if( $err_ope_auth == 1 ){
					print('<font size="2" color="red">エラー：数字で入力してください。</font><br>');
				}else if( $err_ope_auth == 2 ){
					print('<font size="2" color="red">エラー：範囲内（０から９９９まで）で入力してください。</font><br>');
				}
				print('</td>');
				print('</tr>');
				print('</table>');



				print('<br>');	//調整

				//有効期間
				print('<b>有効期間(*)</b>・・・上記スタッフの有効期間<br>');
				print('<table border="0">');
				print('<tr>');
				print('<td align="left" valign="top">');
				print('開始日<br>');
				$tabindex++;
				print('<select name="st_year" class="');
				if( $err_st_year == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 2012;
				while( $i < 2038 ){
					print('<option value="' . $i . '" ');
					if( $i == $st_year ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('年');
				$tabindex++;
				print('<select name="st_month" class="');
				if( $err_st_month == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 1;
				while( $i < 13 ){
					print('<option value="' . $i . '" ');
					if( $i == $st_month ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('月');
				$tabindex++;
				print('<select name="st_day" class="');
				if( $err_st_day == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 1;
				while( $i < 32 ){
					print('<option value="' . $i . '" ');
					if( $i == $st_day ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('日 から');
				print('</td>');
				print('<td align="left" valign="top">');
				print('終了日<font size="2">&nbsp;(現在の最大日は2037/12/31)</font><br>');
				$tabindex++;
				print('<select name="ed_year" class="');
				if( $err_ed_year == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 2012;
				while( $i < 2038 ){
					print('<option value="' . $i . '" ');
					if( $i == $ed_year ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('年');
				$tabindex++;
				print('<select name="ed_month" class="');
				if( $err_ed_month == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 1;
				while( $i < 13 ){
					print('<option value="' . $i . '" ');
					if( $i == $ed_month ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('月');
				$tabindex++;
				print('<select name="ed_day" class="');
				if( $err_ed_day == 0 ){
					print('normal');
				}else{
					print('err');
				}
				print('" style="font-size:20pt;" tabindex="' . $tabindex . '">');
				$i = 1;
				while( $i < 32 ){
					print('<option value="' . $i . '" ');
					if( $i == $ed_day ){
						print('selected');
					}
					print('>' . $i. '</option>');
				
					$i++;
				}
				print('</select>');
				print('日 まで');
				print('</td>');
				print('</tr>');
				print('</table>');
	
				print('</td>');
				print('</tr>');
				print('</table>');
	
				//有効無効／登録ボタン／戻るボタン
				print('<table border="0">');
				print('<tr>');
				print('<td width="815" align="left">');
				print('<b>有効／無効(*)</b><br>');
				$tabindex++;
				print('<select name="yukou_flg" class="normal" style="font-size:20pt;" tabindex="' . $tabindex . '" >');
				if( $yukou_flg == 0 ){
					print('<option value="0" class="color2" selected>無効</option>');
					print('<option value="1" class="color1" >有効</option>');
				}else{
					print('<option value="0" class="color2" >無効</option>');
					print('<option value="1" class="color1" selected>有効</option>');
				}
				print('</select>');
				print('<font size="2" color="red">&nbsp;無効&nbsp;</font><font size="2">にすると予約システム上には表示されません。</font>');
				print('</td>');
				print('<td align="right">');
				$tabindex++;
				print('<input type="image" tabindex="' . $tabindex . '" src="./img_' . $lang_cd . '/btn_trk_1.png" onmouseover="this.src=\'./img_' . $lang_cd . '/btn_trk_2.png\';" onmouseout="this.src=\'./img_' . $lang_cd . '/btn_trk_1.png\';" onClick="kurukuru()" border="0">');
				print('</td>');
				print('</form>');
				print('</tr>');
				print('<tr>');
				print('<td width="815" align="left">&nbsp;</td>');
				print('<form method="post" action="kanri_staff_select.php">');
				print('<td align="right">');
				print('<input type="hidden" name="prc_gmn" value="' . $gmn_id . '">');
				print('<input type="hidden" name="lang_cd" value="' . $lang_cd . '" />');
				print('<input type="hidden" name="office_cd" value="' . $office_cd . '" />');
				print('<input type="hidden" name="staff_cd" value="' . $staff_cd . '">');
				print('<input type="hidden" name="select_office_cd" value="' . $select_office_cd . '">');
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
	}

	mysql_close( $link );
?>
</body>
</html>