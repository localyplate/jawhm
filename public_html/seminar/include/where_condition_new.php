<?php
				$where_country .= '  and k_title1 not like  \'%英語圏%\' ';
				$where_country .= '  and k_desc1 not like  \'%英語圏%\' ';
				$where_country .= '  and k_desc2 not like  \'%英語圏%\' ) ';
				if(substr_count($checked_countryname, 6) == 1 || $checked_countryname == 'アメリカ' ||  $checked_countryname == 'usa' )
				{
					$val='アメリカ';
					$where_country .= ' or k_title1 like \'%'.$val.'%\' ';
					$where_country .= ' or k_desc1 like \'%'.$val.'%\' ';
					$where_country .= ' or k_desc2 like \'%'.$val.'%\' ';
				}
				
			if(substr_count($checked_know, 3) == 1 || $checked_know == 'プランニング' || $checked_know == 'plan' )
			if(substr_count($checked_know, 4) == 1 || $checked_know == '情報収集' || $checked_know == 'student' )
			{
				$data = '学校比較';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '看護師';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '休学';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '住まい仕事';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '学習法';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = 'トラブル';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '資格';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = 'セカンド';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '学生限定';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '都市比較';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
			}

			if(substr_count($checked_know, 3) == 1 || $checked_know == '懇談' || $checked_know == 'foot' )
			{
				$val='渡航相談会';
				$where_know .= ' or k_desc2 like \'%'.$val.'%\' ';
				$val='女性限定';
				$where_know .= ' or k_desc2 like \'%'.$val.'%\' ';
				$val='休職';
				$where_know .= ' or k_desc2 like \'%'.$val.'%\' ';
			}

			if(substr_count($checked_know, 7) == 1 || $checked_know == '体験談' || $checked_know == 'kouen' )
			{
				$val='体験談';
				$where_know .= ' or k_desc2 like \'%'.$val.'%\' ';
			}

			if(substr_count($checked_know, 5) == 1 || $checked_know == '語学学校' || $checked_know == 'school' )
				$where_know .= ' or k_desc2 like \'%'.$val.'%\' ';
			}
			if(substr_count($checked_know, 6) == 1 || $checked_know == 'chumoku' || $checked_know == 'abili' )
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = '学校懇談';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
				$data = 'パーティー';
				$where_know .= ' or k_desc2 like \'%'.$data.'%\' ';
			}