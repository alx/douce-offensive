<?php

/**
 * This class deals with EXIF meta data embedded in the photos.
 *
 */
class PhotoQExif
{
	
	/**
	 * Get associative array with exif info from a photo
	 *
	 * @param string $path	Path to the photo.
	 * @return array		Exif info in associative array.
	 */
	function readExif($path)
	{
		//include and call the exifixer script
		require_once realpath(PHOTOQ_PATH.'lib/exif/exif.php');
		$fullexif = read_exif_data_raw($path, 0);
		//we now retain only the useful (whatever it means ;-) ) info
		$ifd0 = PhotoQExif::_filterUseless($fullexif['IFD0']);
		$subIfd = PhotoQExif::_filterUseless($fullexif['SubIFD']);
		$makerNote = $subIfd['MakerNote'];
		unset($subIfd['MakerNote']);
		$gps = PhotoQExif::_filterUseless($fullexif['GPS']);
		
		//bring all the arrays to single dimension
		$ifd0 = PhotoQHelper::flatten($ifd0);
		$subIfd = PhotoQHelper::flatten($subIfd);
		$makerNote = PhotoQHelper::flatten($makerNote);
		$gps = PhotoQHelper::flatten($gps);
		
		//and finally merge them into a single array
		$exif = array_merge($ifd0, $subIfd, $makerNote, $gps);
		
		//update discovered tags
		PhotoQExif::_discoverTags($exif);
		
		
		return $exif;
	}
	
	function getFormattedExif($exif, $tags){
		
		if(!is_array($tags) || count($tags) < 1 ){
			$result = '';
		}else{
			$result = '<ul class="photoQExifInfo">';
			$foundOne = false; //we don't want to print <ul> if there is no exif in the photo
			foreach($tags as $tag){
				if(array_key_exists($tag, $exif)){
					$foundOne = true;
					$result .= '<li class="photoQExifInfoItem">
					<span class="photoQExifTag">'.$tag.':</span> <span class="photoQExifValue">'.$exif[$tag].'</span></li>';
				}
			}
			$result .= '</ul>';
			if(!$foundOne)
				$result = '';
		}
		return $result;
	}


	function _discoverTags($newTags){
		$oldTags = get_option( "wimpq_exif_tags" );
		if($oldTags !== false){
			$discovered = array_merge($oldTags, $newTags);
			ksort($discovered, SORT_STRING);
			update_option( "wimpq_exif_tags", $discovered);
		}else
			add_option("wimpq_exif_tags", $newTags);
	}
	
	/**
	 * Recursively removes entries containing ':unknown' in key from input array.
	 *
	 * @param array $in the input array
	 * @return array	the filtered array
	 */
	function _filterUseless($in){
		$out = array();
		if(is_array($in)){
			foreach ($in as $key => $value){
				if(strpos($key,'unknown:') === false && !in_array($key,PhotoQExif::_getUselessTagNames()))
					if(is_array($value))
						$out[$key] = PhotoQExif::_filterUseless($value);
					else
						$out[$key] = $value;
			}
		}
		return $out;
	}

	/**
	 * This return a list of tags that are either not implemented correctly in exifixer,
	 * that are added by exifixer and not needed or that contain no useful information (e.g. 
	 * only offsets inside the TIFF header or info i deem unlikely to be useful to my users).
	 *
	 * @return unknown
	 */
	function _getUselessTagNames()
	{
		return array(
		'Bytes',
		'CFAPattern',
		'ComponentsConfiguration',				
		'ExifInteroperabilityOffset',
		'ExifOffset',
		'GPSInfo',
		'KnownMaker',
		'MakerNoteNumTags',
		'OwnerName',
		'RAWDATA',
		'Unknown',
		'UserCommentOld',
		'VerboseOutput',
		'YCbCrPositioning'
		);
	}

	
	
}
?>