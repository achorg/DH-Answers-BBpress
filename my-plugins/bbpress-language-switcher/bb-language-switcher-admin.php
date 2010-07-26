<?php

function bb_language_switcher_update() {		// reads the .mo files and looks for the formal language name automagically
	if (!bb_current_user_can('administrate')) {return;}			

$bb_language_switcher_iso639=array(	// _ck_ converted from public domain masterlist 2009-Mar-29, free for re-use  (needs reduction, some duplicates)
'aa'=>'Afar','ab'=>'Abkhazian','ae'=>'Avestan','af'=>'Afrikaans','ak'=>'Akan','am'=>'Amharic','an'=>'Aragonese','ar'=>'Arabic','as'=>'Assamese','av'=>'Avaric','ay'=>'Aymara','az'=>'Azerbaijani',
'ba'=>'Bashkir','be'=>'Belarusian','bg'=>'Bulgarian','bh'=>'Bihari','bi'=>'Bislama','bm'=>'Bambara','bn'=>'Bengali','bo'=>'Tibetan','br'=>'Breton','bs'=>'Bosnian',
'ca'=>'Catalan','ca'=>'Valencian','ce'=>'Chechen','ch'=>'Chamorro','co'=>'Corsican','cr'=>'Cree','cs'=>'Czech','cu'=>'Church Slavic','cv'=>'Chuvash','cy'=>'Welsh','da'=>'Danish',
'de'=>'German','dv'=>'Dhivehi','dv'=>'Divehi','dv'=>'Maldivian','dz'=>'Dzongkha',
'ee'=>'Ewe','el'=>'Greek','en'=>'English','eo'=>'Esperanto','es'=>'Castilian','es'=>'Spanish','et'=>'Estonian','eu'=>'Basque',
'fa'=>'Persian','ff'=>'Fulah','fi'=>'Finnish','fj'=>'Fijian','fo'=>'Faroese','fr'=>'French','fy'=>'Western Frisian',
'ga'=>'Irish','gd'=>'Gaelic','gd'=>'Scottish Gaelic','gl'=>'Galician','gn'=>'Guarani','gu'=>'Gujarati','gv'=>'Manx',
'ha'=>'Hausa','he'=>'Hebrew','hi'=>'Hindi','ho'=>'Hiri Motu','hr'=>'Croatian','ht'=>'Haitian Creole','hu'=>'Hungarian','hy'=>'Armenian','hz'=>'Herero',
'id'=>'Indonesian','ie'=>'Interlingue','ie'=>'Occidental','ig'=>'Igbo','ii'=>'Nuosu','ii'=>'Sichuan Yi','ik'=>'Inupiaq','io'=>'Ido','is'=>'Icelandic','it'=>'Italian','iu'=>'Inuktitut',
'ja'=>'Japanese','jv'=>'Javanese',
'ka'=>'Georgian','kg'=>'Kongo','ki'=>'Gikuyu','ki'=>'Kikuyu','kj'=>'Kuanyama','kk'=>'Kazakh','kl'=>'Greenlandic','kl'=>'Kalaallisut','km'=>'Central Khmer',
'kn'=>'Kannada','ko'=>'Korean','kr'=>'Kanuri','ks'=>'Kashmiri','ku'=>'Kurdish','kv'=>'Komi','kw'=>'Cornish','ky'=>'Kirghiz','ky'=>'Kyrgyz',
'la'=>'Latin','lb'=>'Letzeburgesch','lb'=>'Luxembourgish','lg'=>'Ganda','li'=>'Limburgan','li'=>'Limburger','li'=>'Limburgish','ln'=>'Lingala','lo'=>'Lao','lt'=>'Lithuanian','lu'=>'Luba-Katanga','lv'=>'Latvian',
'mg'=>'Malagasy','mh'=>'Marshallese','mi'=>'Maori','mk'=>'Macedonian','ml'=>'Malayalam','mn'=>'Mongolian','mr'=>'Marathi','ms'=>'Malay','mt'=>'Maltese','my'=>'Burmese',
'na'=>'Nauru','nb'=>'Norwegian Bokmål','nd'=>'North Ndebele','ne'=>'Nepali','ng'=>'Ndonga','nl'=>'Dutch','nl'=>'Flemish','nn'=>'Norwegian Nynorsk',
'no'=>'Norwegian','nr'=>'South Ndebele','nv'=>'Navajo','ny'=>'Chewa','ny'=>'Chichewa','ny'=>'Nyanja',
'oc'=>'Occitan','oj'=>'Ojibwa','om'=>'Oromo','or'=>'Oriya','os'=>'Ossetian','os'=>'Ossetic',
'pa'=>'Panjabi','pi'=>'Pali','pl'=>'Polish','ps'=>'Pushto','pt'=>'Portuguese','qu'=>'Quechua',
'rm'=>'Romansh','rn'=>'Rundi','ro'=>'Moldavian','ro'=>'Moldovan','ro'=>'Romanian','ru'=>'Russian','rw'=>'Kinyarwanda',
'sa'=>'Sanskrit','sc'=>'Sardinian','sd'=>'Sindhi','se'=>'Northern Sami','sg'=>'Sango','si'=>'Sinhala','si'=>'Sinhalese','sk'=>'Slovak','sl'=>'Slovenian',
'sm'=>'Samoan','sn'=>'Shona','so'=>'Somali','sq'=>'Albanian','sr'=>'Serbian','ss'=>'Swati','st'=>'Sotho, Southern','su'=>'Sundanese','sv'=>'Swedish','sw'=>'Swahili',
'ta'=>'Tamil','te'=>'Telugu','tg'=>'Tajik','th'=>'Thai','ti'=>'Tigrinya','tk'=>'Turkmen','tl'=>'Tagalog','tn'=>'Tswana','to'=>'Tonga','tr'=>'Turkish','ts'=>'Tsonga','tt'=>'Tatar','tw'=>'Twi','ty'=>'Tahitian',
'ug'=>'Uighur','ug'=>'Uyghur','uk'=>'Ukrainian','ur'=>'Urdu','uz'=>'Uzbek','ve'=>'Venda','vi'=>'Vietnamese','vo'=>'Volapük',
'wa'=>'Walloon','wo'=>'Wolof','xh'=>'Xhosa','yi'=>'Yiddish','yo'=>'Yoruba','za'=>'Chuang','za'=>'Zhuang','zh'=>'Chinese','zu'=>'Zulu'
);

	$languages=bb_glob(BB_LANG_DIR.'*.mo');	// might have problem in safe mode
	if (empty($languages)) {return;}			// no .mo files?
	foreach ($languages as $language) {	
		if (BB_LANG_USE_FILE_META) {
		unset($match); $content="";
		$handle = fopen($language, "rb");		// should work even under windows
		while (!feof($handle)) {
			$content.=fread($handle, 8192);
			if (preg_match("/X\-Poedit\-Language\:(.+?)\n/i",$content,$match)) {continue;} // language name in English from poedit meta data
			if (strlen($content)>81920) {$content=substr($content,-8192);}			// trim buffer size
		}		
		unset($content);
		fclose($handle);
		}
		preg_match("/.*[\/](.+?)\.mo$/i",$language,$lang);		// filename becomes key
		$lang=trim($lang[1]); if (strlen($lang)==5) {$cn=" (".strtoupper(substr($lang,-2)).") ";} else {$cn="";}
		if (!empty($match[1])) {
			$list[$lang]=ucwords(trim($match[1])).$cn;
		} elseif (BB_LANG_USE_FILE_META!='only' && BB_LANG_USE_FILE_META!='force') {			
			$list[$lang]=utf8_encode($bb_language_switcher_iso639[strtolower(substr($lang,0,2))]).$cn;	// fallback if name can't be found in meta
		}
	}	
	if (!empty($list)) {
		$list[' ']="English";	// add default
		asort($list); 		// make alphabetical
		bb_update_option('bb_language_switcher',$list);
	}	
}

function bb_language_switcher_debug() {	
	if (!bb_current_user_can('administrate')) {return;}
	bb_language_switcher_update(); $bb_language_switcher=bb_get_option('bb_language_switcher');
	$url=bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),BB_LANG_DIR),' /\\').'/';  $count=0;
	echo "<html><table border='0' cellpadding='1' cellspacing='1' style='font-family:monospace;'>";
	foreach ($bb_language_switcher as $value=>$description) {if ($value) {$count++; echo "<tr><td>$description</td><td><a href='$url$value.mo'>$value.mo</a></td></tr>";}}
	echo "</table>\n<br />$count language files total"; 	 exit;
}

?>