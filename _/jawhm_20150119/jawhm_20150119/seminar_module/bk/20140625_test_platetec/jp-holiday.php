<?php

//  ========================================================
//  jp-holiday.php ---- 日本国祝日取得のための date_jp_holiday 関数群
//  Copyright 2008 Kiyoshi Sakai <sakai [アット] d4k [ドット] net>
//  version: 0.0.2
//  ========================================================


function date_jp_holiday_getNationalFoundationDay($y) {
	return 1967 <= $y ? '02-11' : null;
}

function date_jp_holiday_getEquinoxDateCalc($y, $va) {

	//	1太陽年は（西暦2000年において）365.24219日（365.24219040日）となっており、100年間で約0.53秒ずつ短くなっている。
	//	2000年の太陽の春分点通過日
	//	3月20.69115日
	//	2000年の太陽の秋分点通過日
	//	9月23.09日

	switch($va) {
	case 'vernal':
		$equinox = 953537715.36;	// strtotime('2000-03-20') + (60 * 60 * 24 * 0.69115)
		break;
	case 'autumnal':
		$equinox = 969642576.00;	// strtotime('2000-09-23') + (60 * 60 * 24 * 0.09)
		break;
	}

	return date('m-d', 
		$equinox 											// 2000年の春(秋)分点
		+ (3155692525056 * ($y - 2000) / 100000) 			// 2000年から太陽年の計算
		+ (53 * (array_sum(range(0, $y - 2000))) / 10000)	// 太陽年の誤差修正（100年間で約0.53短くなりつつある）
	);

}

function date_jp_holiday_isVernalEquinoxDay($y, $m_d) {

	static $jp_hol_vernal_equinox_days;

	if(!$jp_hol_vernal_equinox_days) {
		$jp_hol_vernal_equinox_days = array(
			1925 => '03-21',
			1926 => '03-21',
			1927 => '03-21',
			1928 => '03-21',
			1929 => '03-21',
			1930 => '03-21',
			1931 => '03-21',
			1932 => '03-21',
			1933 => '03-21',
			1934 => '03-21',
			1935 => '03-21',
			1936 => '03-21',
			1937 => '03-21',
			1938 => '03-21',
			1939 => '03-21',
			1940 => '03-21',
			1941 => '03-21',
			1942 => '03-21',
			1943 => '03-21',
			1944 => '03-21',
			1945 => '03-21',
			1946 => '03-21',
			1947 => '03-21',
			1948 => '03-21',
			1949 => '03-21',
			1950 => '03-21',
			1951 => '03-21',
			1952 => '03-21',
			1953 => '03-21',
			1954 => '03-21',
			1955 => '03-21',
			1956 => '03-21',
			1957 => '03-21',
			1958 => '03-21',
			1959 => '03-21',
			1960 => '03-20',
			1961 => '03-21',
			1962 => '03-21',
			1963 => '03-21',
			1964 => '03-20',
			1965 => '03-21',
			1966 => '03-21',
			1967 => '03-21',
			1968 => '03-20',
			1969 => '03-21',
			1970 => '03-21',
			1971 => '03-21',
			1972 => '03-20',
			1973 => '03-21',
			1974 => '03-21',
			1975 => '03-21',
			1976 => '03-20',
			1977 => '03-21',
			1978 => '03-21',
			1979 => '03-21',
			1980 => '03-20',
			1981 => '03-21',
			1982 => '03-21',
			1983 => '03-21',
			1984 => '03-20',
			1985 => '03-21',
			1986 => '03-21',
			1987 => '03-21',
			1988 => '03-20',
			1989 => '03-21',
			1990 => '03-21',
			1991 => '03-21',
			1992 => '03-20',
			1993 => '03-20',
			1994 => '03-21',
			1995 => '03-21',
			1996 => '03-20',
			1997 => '03-20',
			1998 => '03-21',
			1999 => '03-21',

			2000 => '03-20',
			2001 => '03-20',
			2002 => '03-21',
			2003 => '03-21',
			2004 => '03-20',
			2005 => '03-20',
			2006 => '03-21',
			2007 => '03-21',
			2008 => '03-20',
			2009 => '03-20',
			2010 => '03-21',
			2011 => '03-21',
			2012 => '03-20',
			2013 => '03-20',
			2014 => '03-21',
			2015 => '03-21'
		);
	}

	if(isset($jp_hol_vernal_equinox_days[$y])) {
		$ve_m_d = $jp_hol_vernal_equinox_days[$y];
	} else {
		$ve_m_d = date_jp_holiday_getEquinoxDateCalc($y, 'vernal');
		$jp_hol_vernal_equinox_days[$y] = $ve_m_d;
	}
	return $m_d == $ve_m_d ? true : false;
}

function date_jp_holiday_isAutumnalEquinoxDay($y, $m_d) {
	static $jp_hol_autumnal_equinox_days;
	if(!$jp_hol_autumnal_equinox_days) {
		$jp_hol_autumnal_equinox_days = array(
			1925 => '09-23',
			1926 => '09-24',
			1927 => '09-24',
			1928 => '09-23',
			1929 => '09-23',
			1930 => '09-24',
			1931 => '09-24',
			1932 => '09-23',
			1933 => '09-23',
			1934 => '09-24',
			1935 => '09-24',
			1936 => '09-23',
			1937 => '09-23',
			1938 => '09-24',
			1939 => '09-24',
			1940 => '09-23',
			1941 => '09-23',
			1942 => '09-24',
			1943 => '09-24',
			1944 => '09-23',
			1945 => '09-23',
			1946 => '09-24',
			1947 => '09-24',
			1948 => '09-23',
			1949 => '09-23',
			1950 => '09-23',
			1951 => '09-24',
			1952 => '09-23',
			1953 => '09-23',
			1954 => '09-23',
			1955 => '09-24',
			1956 => '09-23',
			1957 => '09-23',
			1958 => '09-23',
			1959 => '09-24',
			1960 => '09-23',
			1961 => '09-23',
			1962 => '09-23',
			1963 => '09-24',
			1964 => '09-23',
			1965 => '09-23',
			1966 => '09-23',
			1967 => '09-24',
			1968 => '09-23',
			1969 => '09-23',
			1970 => '09-23',
			1971 => '09-24',
			1972 => '09-23',
			1973 => '09-23',
			1974 => '09-23',
			1975 => '09-24',
			1976 => '09-23',
			1977 => '09-23',
			1978 => '09-23',
			1979 => '09-24',
			1980 => '09-23',
			1981 => '09-23',
			1982 => '09-23',
			1983 => '09-23',
			1984 => '09-23',
			1985 => '09-23',
			1986 => '09-23',
			1987 => '09-23',
			1988 => '09-23',
			1989 => '09-23',
			1990 => '09-23',
			1991 => '09-23',
			1992 => '09-23',
			1993 => '09-23',
			1994 => '09-23',
			1995 => '09-23',
			1996 => '09-23',
			1997 => '09-23',
			1998 => '09-23',
			1999 => '09-23',

			2000 => '09-23',
			2001 => '09-23',
			2002 => '09-23',
			2003 => '09-23',
			2004 => '09-23',
			2005 => '09-23',
			2006 => '09-23',
			2007 => '09-23',
			2008 => '09-23',
			2009 => '09-23',
			2010 => '09-23',
			2011 => '09-23',
			2012 => '09-22',
			2013 => '09-23',
			2014 => '09-23',
			2015 => '09-23'
		);
	}

	if(isset($jp_hol_autumnal_equinox_days[$y])) {
		$va_m_d = $jp_hol_autumnal_equinox_days[$y];
	} else {
		$va_m_d = date_jp_holiday_getEquinoxDateCalc($y, 'autumnal');
		$jp_hol_autumnal_equinox_days[$y] = $va_m_d;
	}
	return $m_d == $va_m_d ? true : false;
}

function date_jp_holiday_isHoliday($dt, $by_date_itself = false) {

	$y = date_format($dt, 'Y');
	$m_d = date_format($dt, 'm-d');
	$y_m_d = $y + '-' + $m_d;

	if(isset($dt->is_jp_holiday) && is_bool($dt->is_jp_holiday) && isset($dt->y_m_d) && $dt->y_m_d == $y_m_d) {
		return $dt->is_jp_holiday;
	}

	$dt->y_m_d = $y_m_d;

	switch($m_d) {
	case '01-01':
		if(1949 <= $y) {
			$dt->jp_hol_name = '元日';
			$dt->jp_hol_engl = 'New Year\'s Day';
			$dt->jp_hol_desc = '年のはじめを祝う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '01-15':
		if(1949 <= $y && $y <= 1999) {
			$dt->jp_hol_name = '成人の日';
			$dt->jp_hol_engl = 'Coming of Age Day';
			$dt->jp_hol_desc = 'おとなになったことを自覚し、みずから生き抜こうとする青年を祝いはげます。';
			$dt->is_jp_holiday = true;
		}
		break;
	case '04-29':
		if(1927 <= $y && $y <= 1988) {
			$dt->jp_hol_name = '天皇誕生日';
			$dt->jp_hol_engl = 'Emperor\'s Birthday';
			$dt->jp_hol_desc = '天皇の誕生日を祝う。';
			$dt->is_jp_holiday = true;
			return true;
		} else if(1989 <= $y && $y <= 2006) {
			$dt->jp_hol_name = 'みどりの日';
			$dt->jp_hol_engl = 'Greenery Day';
			$dt->jp_hol_desc = '自然に親しむとともにその恩恵に感謝し、豊かな心をはぐくむ。';
			$dt->is_jp_holiday = true;
			return true;
		} else if(2007 <= $y) {
			$dt->jp_hol_name = '昭和の日';
			$dt->jp_hol_engl = 'Showa Day';	// 正式な発表ではありません…
			$dt->jp_hol_desc = '激動の日々を経て、復興を遂げた昭和の時代を顧み、国の将来に思いをいたす。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '05-03':
		if(1949 <= $y) {
			$dt->jp_hol_name = '憲法記念日';
			$dt->jp_hol_engl = 'Constitution Memorial Day';
			$dt->jp_hol_desc = '日本国憲法の施行を記念し、国の成長を期する。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '05-04':
		if(2007 <= $y) {
			$dt->jp_hol_name = 'みどりの日';
			$dt->jp_hol_engl = 'Greenery Day';
			$dt->jp_hol_desc = '自然に親しむとともにその恩恵に感謝し、豊かな心をはぐくむ。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '05-05':
		if(1949 <= $y) {
			$dt->jp_hol_name = 'こどもの日';
			$dt->jp_hol_engl = 'Children\'s Day';
			$dt->jp_hol_desc = 'こどもの人格を重んじ、こどもの幸福をはかるとともに、母に感謝する。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '07-20':
		if(1996 <= $y && $y <= 2002) {
			$dt->jp_hol_name = '海の日';
			$dt->jp_hol_engl = 'Marine Day';
			$dt->jp_hol_desc = '海の恩恵に感謝するとともに、海洋国日本の繁栄を願う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '09-15':
		if(1967 <= $y && $y <= 2002) {
			$dt->jp_hol_name = '敬老の日';
			$dt->jp_hol_engl = 'Respect for the Aged Day';
			$dt->jp_hol_desc = '多年にわたり社会につくしてきた老人を敬愛し、長寿を祝う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '10-10':
		if(1966 <= $y && $y <= 1999) {
			$dt->jp_hol_name = '体育の日';
			$dt->jp_hol_engl = 'Health and Sports Day';
			$dt->jp_hol_desc = 'スポーツにしたしみ、健康な心身をつちかう。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '11-03':
		if(1948 <= $y) {
			$dt->jp_hol_name = '文化の日';
			$dt->jp_hol_engl = 'National Culture Day';
			$dt->jp_hol_desc = '自由と平和を愛し、文化をすすめる。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '11-23':
		if(1948 <= $y) {
			$dt->jp_hol_name = '勤労感謝の日';
			$dt->jp_hol_engl = 'Labor Thanksgiving Day';
			$dt->jp_hol_desc = '勤労をたっとび、生産を祝い、国民たがいに感謝しあう。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '12-23':
		if(1989 <= $y) {
			$dt->jp_hol_name = '天皇誕生日';
			$dt->jp_hol_engl = 'Emperor\'s Birthday';
			$dt->jp_hol_desc = '天皇の誕生日を祝う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case date_jp_holiday_getNationalFoundationDay($y):	// 政令で定める日
		if(1967 <= $y) {
			$dt->jp_hol_name = '建国記念の日';
			$dt->jp_hol_engl = 'National Foundation Day';
			$dt->jp_hol_desc = '建国をしのび、国を愛する心を養う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	}


	switch(date_format($dt, 'n') . '-' . ceil(date_format($dt, 'j') / 7) . '-' . date_format($dt, 'w')) {
	case '1-2-1':	// １月の第２月曜日
		if(2000 <= $y) {
			$dt->jp_hol_name = '成人の日';
			$dt->jp_hol_engl = 'Coming of Age Day';
			$dt->jp_hol_desc = 'おとなになったことを自覚し、みずから生き抜こうとする青年を祝いはげます。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '7-3-1':	// ７月の第３月曜日
		if(2003 <= $y) {
			$dt->jp_hol_name = '海の日';
			$dt->jp_hol_engl = 'Marine Day';
			$dt->jp_hol_desc = '海の恩恵に感謝するとともに、海洋国日本の繁栄を願う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '9-3-1':	// ９月の第３月曜日
		if(2003 <= $y) {
			$dt->jp_hol_name = '敬老の日';
			$dt->jp_hol_engl = 'Respect for the Aged Day';
			$dt->jp_hol_desc = '多年にわたり社会につくしてきた老人を敬愛し、長寿を祝う。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	case '10-2-1':	// １０月の第２月曜日
		if(2000 <= $y) {
			$dt->jp_hol_name = '体育の日';
			$dt->jp_hol_engl = 'Health and Sports Day';
			$dt->jp_hol_desc = 'スポーツにしたしみ、健康な心身をつちかう。';
			$dt->is_jp_holiday = true;
			return true;
		}
		break;
	}

	if(date_jp_holiday_isVernalEquinoxDay($y, $m_d)) {
		if(1949 <= $y) {
			$dt->jp_hol_name = '春分の日';
			$dt->jp_hol_engl = 'Vernal Equinox';
			$dt->jp_hol_desc = '自然をたたえ、生物をいつくしむ。';
			$dt->is_jp_holiday = true;
			return true;
		}
	}
	if(date_jp_holiday_isAutumnalEquinoxDay($y, $m_d)) {
		if(1948 <= $y) {
			$dt->jp_hol_name = '秋分の日';
			$dt->jp_hol_engl = 'Autumnal Equinox';
			$dt->jp_hol_desc = '祖先をうやまい、なくなった人々をしのぶ。';
			$dt->is_jp_holiday = true;
			return true;
		}
	}

	if($by_date_itself) {
		$dt->jp_hol_name = null;
		$dt->jp_hol_engl = null;
		$dt->jp_hol_desc = null;
		$dt->is_jp_holiday = false;
		return false;
	}

	$dt_before = clone $dt;
	date_modify($dt_before, '-1 day');						// $dt_before は前日
	if(date_jp_holiday_isHoliday($dt_before, true)) {		// 前日が祝日

		if(date_format($dt_before, 'w') == 0) {				// 前日が祝日かつ日曜ならば
			$dt->jp_hol_name = '振替休日';
			$dt->jp_hol_engl = 'Substitute Holiday'; 
			$dt->jp_hol_desc = null;
			$dt->is_jp_holiday = true;
			return true;
		}

		if(1986 <= $y) {
			$dt_after = clone $dt;
			date_modify($dt_after, '+1 day');					// $dt_after は翌日
			if(date_jp_holiday_isHoliday($dt_after, true)) {	// 前日も翌日も祝日ならば
				$dt->jp_hol_name = '国民の休日';
				$dt->jp_hol_engl = 'Holiday for a Nation';
				$dt->jp_hol_desc = null;
				$dt->is_jp_holiday = true;
				return true;
			}
		}

		if(2007 <= $y) {
			date_modify($dt_before, '-1 day');						// $dt_before は前日の前日
			while(date_jp_holiday_isHoliday($dt_before, true)) {	// 前日以前が祝日の連続になっている場合
				if($dt_before->format('w') == 0) {					// 前日以前が祝日の連続で、日曜が重なる場合
					$dt->jp_hol_name = '振替休日';
					$dt->jp_hol_engl = 'Substitute Holiday'; 
					$dt->jp_hol_desc = null;
					$dt->is_jp_holiday = true;
					return true;
				}
				date_modify($dt_before, '-1 day');					// $dt_before は更に前日
			}
		}

	}


	$dt->jp_hol_name = null;
	$dt->jp_hol_engl = null;
	$dt->jp_hol_desc = null;
	$dt->is_jp_holiday = false;
	return false;

}

?>
