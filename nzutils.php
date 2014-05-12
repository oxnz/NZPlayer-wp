<?php

function nzfind($args = array( 'path'	=> '.', 'suffix' => 'php',
	'recursive'	=> true)) {
	$flist = scandir($args['path']);
	$cnt = count($flist);
	for ($i = 0; $i < $cnt; ++$i) {
		if ($flist[$i] == "." || $flist[$i] == ".."
			|| $flist[$i][0] == '.') {
			unset($flist[$i]);
			continue;
		}
		$fpath = join('/', array($args['path'], $flist[$i]));
		if (is_dir($fpath)) {
			unset($flist[$i]);
			if ($args['recursive']) {
				$flist[$fpath] = nzfind(array(
					'path' => $fpath,
					'suffix' => $args['suffix'],
					'recursive' => $args['recursive'])
				);
				if (empty($flist[$fpath])) {
					unset($flist[$fpath]);
				}
			}
		} else if (is_file($fpath)) {
			$pathinfo = pathinfo($fpath);
			if ($pathinfo['extension'] !== $args['suffix']) {
			/*
			if (pathinfo($fpath)['extension'] !== $args['suffix']) {
			 */
				unset($flist[$i]);
			} else {
				$flist[$i] = $fpath;
			}
		} else {
			return null;
		}
	}
	return $flist;
}

function nzlist($args = array(
	'ftree' => array(),
	'depth' => 1,
	'ignore'=> true)) {
	$flist = array();
	foreach ($args['ftree'] as $key => $value) {
		if (is_array($value)) {
		   if ($args['depth'] > 0) {
			   array_push($flist, nzlist(array('ftree' => $value,
				   'depth' => $args['depth']-1,
				   'ignore' => $args['ignore'])));
			   unset($flist[$key]);
		   } else {
			   if ($args['ignore']) {
			   } else { // merge to parent dir
				   $flist = array_merge($flist, $value);
			   }
		   }
		} else {
			array_push($flist, $value);
		}
	}
	return $flist;
}

/*
$flist = nzfind(array('path' => '../../../uploads', 'suffix' => 'mp3',
'recursive' => true));
print_r ($flist);
print_r(nzlist(array('ftree' => $flist, 'depth' => 2, 'ignore' => false)));
 */

function mediainfo($fpath) {
	$genrelist = array(
		0 => 'Blues',
		1 => 'Classic Rock',
		2 => 'Country',
		3 => 'Dance',
		4 => 'Disco',
		5 => 'Funk',
		6 => 'Grunge',
		7 => 'Hip-Hop',
		8 => 'Jazz',
		9 => 'Metal',
		10 =>'New Age',
		11 =>'Oldies',
		12 =>'Other',
		13 =>'Pop',
		14 =>'R&B',
		15 =>'Rap',
		16 =>'Reggae',
		17 =>'Rock',
		18 =>'Techno',
		19 =>'Industrial',
		20 =>'Alternative',
		21 =>'Ska',
		22 =>'Death Metal',
		23 =>'Pranks',
		24 =>'Soundtrack',
		25 =>'Euro-Techno',
		26 =>'Ambient',
		27 =>'Trip-Hop',
		28 =>'Vocal',
		29 =>'Jazz+Funk',
		30 =>'Fusion',
		31 =>'Trance',
		32 =>'Classical',
		33 =>'Instrumental',
		34 =>'Acid',
		35 =>'House',
		36 =>'Game',
		37 =>'Sound Clip',
		38 =>'Gospel',
		39 =>'Noise',
		40 =>'Alternative Rock',
		41 =>'Bass',
		42 =>'Soul',
		43 =>'Punk',
		44 =>'Space',
		45 =>'Meditative',
		46 =>'Instrumental Pop',
		47 =>'Instrumental Rock',
		48 =>'Ethnic',
		49 =>'Gothic',
		50 =>'Darkwave',
		51 =>'Techno-Industrial',
		52 =>'Electronic',
		53 =>'Pop-Folk',
		54 =>'Eurodance',
		55 =>'Dream',
		56 =>'Southern Rock',
		57 =>'Comedy',
		58 =>'Cult',
		59 =>'Gangsta',
		60 =>'Top 40',
		61 =>'Christian Rap',
		62 =>'Pop/Funk',
		63 =>'Jungle',
		64 =>'Native US',
		65 =>'Cabaret',
		66 =>'New Wave',
		67 =>'Psychadelic',
		68 =>'Rave',
		69 =>'Showtunes',
		70 =>'Trailer',
		71 =>'Lo-Fi',
		72 =>'Tribal',
		73 =>'Acid Punk',
		74 =>'Acid Jazz',
		75 =>'Polka',
		76 =>'Retro',
		77 =>'Musical',
		78 =>'Rock & Roll',
		79 =>'Hard Rock',
		80 =>'Folk',
		81 =>'Folk-Rock',
		82 =>'National Folk',
		83 =>'Swing',
		84 =>'Fast Fusion',
		85 =>'Bebob',
		86 =>'Latin',
		87 =>'Revival',
		88 =>'Celtic',
		89 =>'Bluegrass',
		90 =>'Avantgarde',
		91 =>'Gothic Rock',
		92 =>'Progressive Rock',
		93 =>'Psychedelic Rock',
		94 =>'Symphonic Rock',
		95 =>'Slow Rock',
		96 =>'Big Band',
		97 =>'Chorus',
		98 =>'Easy Listening',
		99 =>'Acoustic',
		100 =>'Humour',
		101 =>'Speech',
		102 =>'Chanson',
		103 =>'Opera',
		104 => 'Chamber Music',
		105 => 'Sonata',
		106 => 'Symphony',
		107 => 'Booty Bass',
		108 => 'Primus',
		109 => 'Porn Groove',
		110 => 'Satire',
		111 => 'Slow Jam',
		112 => 'Club',
		113 => 'Tango',
		114 => 'Samba',
		115 => 'Folklore',
		116 => 'Ballad',
		117 => 'Power Ballad',
		118 => 'Rhytmic Soul',
		119 => 'Freestyle',
		120 => 'Duet',
		121 => 'Punk Rock',
		122 => 'Drum Solo',
		123 => 'Acapella',
		124 => 'Euro-House',
		125 => 'Dance Hall',
		126 => 'Goa',
		127 => 'Drum & Bass',
		128 => 'Club-House',
		129 => 'Hardcore',
		130 => 'Terror',
		131 => 'Indie',
		132 => 'BritPop',
		133 => 'Negerpunk',
		134 => 'Polsk Punk',
		135 => 'Beat',
		136 => 'Christian Gangsta',
		137 => 'Heavy Metal',
		138 => 'Black Metal',
		139 => 'Crossover',
		140 => 'Contemporary C',
		141 => 'Christian Rock',
		142 => 'Merengue',
		143 => 'Salsa',
		144 => 'Thrash Metal',
		145 => 'Anime',
		146 => 'JPop',
		147 => 'SynthPop',
		148	=> 'Unknown', // add by nz
	);

	if (file_exists($fpath)) {
		$cover = substr($fpath, 0, -3);
		foreach (array("png", "jpg", "jpeg", "gif") as $suffix) {
			if (file_exists($cover . $suffix)) {
				$cover .= $suffix;
			}
		}
		if ('.' == substr($cover, -1)) { // cover not exist
			foreach (array("png", "jpg", "jpeg", "gif") as $suffix) {
				$candidate = dirname($fpath) . '/default-cover.' . $suffix;
				if (file_exists($candidate)) {
					$cover = $candidate;
				}
			}
		}
		$fp = fopen($fpath, "r");
		fseek($fp, filesize($fpath)-128);
		$tag = fread($fp, 128);
		fclose($fp);
		if ("TAG" == substr($tag, 0, 3)) {
			$title = substr($tag, 3, 30);
			$artist = substr($tag, 33, 30);
			$album = substr($tag, 63, 30);
			$year = (int)substr($tag, 93, 4);
			$comment = substr($tag, 97, 28);
			$trackid = substr($tag, 125, 2);
			if (0 == (int)$trackid[0]) {
				$trackid = (int)$trackid[1];
			} else {
				$trackid = -1;
				$comment .= $trackid;
			}
			$genre = (int)substr($tag, 127, 1);
			$genre = $genrelist[$genre > 147 ? 148 : $genre];
		}
		if (NULL == $title) {
			$pathinfo = pathinfo($fpath);
			$title = $pathinfo['filename'];
		}
		return array(
			'cover'		=> $cover,
			'title'		=> $title,
			'artist'	=> $artist ? $artist : 'Unknown',
			'album'		=> $album ? $album : 'Unknown',
			'year'		=> $year ? $year : '2014',
			'comment'	=> $comment,
			'trackid'	=> -1 == $trackid ? '007' : $trackid,
			'genre'		=> $genre
		);
	}
}

/*
print_r(mediainfo('/Users/oxnz/Sites/wp-content/uploads/2014/05/Here Comes The Flood.mp3'));
print "HELLO\n";
print_r(mediainfo('/Users/oxnz/Sites/wp-content/uploads/2014/05/dream.mp3'));
*/
