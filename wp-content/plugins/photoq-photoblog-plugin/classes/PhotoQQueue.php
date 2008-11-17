<?php
class PhotoQQueue
{
	
	/**
	 * The list of queued photos
	 *
	 * @var array
	 * @access private
	 */
	var $_queuedPhotos;
	
	var $_db;
	
	//var $_oc;
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQQueue()
	{
		$this->__construct();
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct()
	{
		
		$this->_db =& PhotoQSingleton::getInstance('PhotoQDB');
		//$this->_oc =& PhotoQSingleton::getInstance('PhotoQOptionController');
		
		//get Queue from DB
		$this->load();
	}
	
	function load()
	{
		$this->_queuedPhotos = array();
	
		if($results = $this->_db->getQueueByPosition()){
			foreach ($results as $qEntry) {
				$this->addPhoto(
					new PhotoQQueuedPhoto( $qEntry->q_img_id,
						$qEntry->q_imgname, $qEntry->q_title, 
						$qEntry->q_descr, $qEntry->q_slug, 
						$qEntry->q_tags, $qEntry->q_exif, $qEntry->q_edited
					)
				);
			}
		}
	}
	
	function addPhoto($photo)
	{
		array_push($this->_queuedPhotos, $photo);
	}
	
	/**
	 * Delete a photo from the queue.
	 *
	 * @param int $id the id of the photo to delete
	 * @return object PhotoQStatusMessage
	 */
	function deletePhotoById($id)
	{
		foreach($this->_queuedPhotos as $position => $photo) {
    		if($photo->id == $id){
    			//remove from database
				$this->_db->deleteQueueEntry($id, $position);
        		//remove from queue
    			unset($this->_queuedPhotos[$position]);
        		//remove from server
    			return $photo->delete();
    		}
    	}
    	return new PhotoQErrorMessage(__("Could not find photo to delete: $id"));
	}
	
	function deleteAll()
	{
		foreach($this->_queuedPhotos as $position => $photo)
    		$this->deletePhotoById($photo->id);
	}
	
	
	/**
	 * Returns the length of the queue.
	 *
	 * @return integer	The length of the queue.
	 * @access public
	 */
	function getLength()
	{
		return count($this->_queuedPhotos);
	}
	
	
	/**
	 * Publish the top of the queue.
	 *
	 * @return object PhotoQStatusMessage
	 */
	function publishTop()
	{
		if($this->getLength() == 0){
			return new PhotoQErrorMessage(__('Queue is empty, nothing to post.'));
		}
		$topPhoto = $this->_queuedPhotos[0];
		if($postID = $topPhoto->publish()){
			$this->_db->deleteQueueEntry($topPhoto->id, 1);
			$statusMsg = '<strong>'.__('Your post has been saved.').'</strong> <a href="'. get_permalink( $postID ).'">'.__('View post').'</a> | <a href="post.php?action=edit&amp;post='.$postID.'">'.__('Edit post').'</a>';
			return new PhotoQStatusMessage($statusMsg);
		}else
			return new PhotoQErrorMessage(__("Publishing Photo did not succeed."));
	}
	
	/**
	 * Publish several photos from queue at once.
	 *
	 * @param $num2Post the number of photos to post.
	 * @return object PhotoQStatusMessage
	 */
	function publishMulti($num2Post)
	{
		if($this->getLength() == 0){
			return new PhotoQErrorMessage(__('Queue is empty, nothing to post.'));
		}
		$num2Post = min($this->getLength(), $num2Post);
		
		//we'll increase this timestamp from one post to the next to make sure 
		//that posts are at least spaced by one second otherwise wordpress doesn't 
		//know how to deal with it.
		$postDateFirst = current_time('timestamp');
		
		for ($i = 0; $i<$num2Post; $i++){
			$topPhoto = $this->_queuedPhotos[$i];
			if($postID = $topPhoto->publish($postDateFirst + $i))
				$this->_db->deleteQueueEntry($topPhoto->id, 1);
			else
				return new PhotoQErrorMessage(__("Publishing Photo did not succeed."));
			
		}
		$statusMsg = '<strong>'.__('Your posts have been saved.').'</strong>';
		return new PhotoQStatusMessage($statusMsg);
	}
	
	
	/**
	 * Temporary function while refactoring
	 *
	 * @return unknown
	 */
	function getQueue()
	{
		$queue = array();
		$count = 0;
		foreach ($this->_queuedPhotos as $qEntry) {
			$queue[$count]->q_img_id = $qEntry->id;
			$queue[$count]->q_imgname = $qEntry->imgname;
			$queue[$count]->q_position = $count + 1;
			$queue[$count]->q_title = $qEntry->title;
			$queue[$count]->q_slug = $qEntry->slug;
			$queue[$count]->q_descr = $qEntry->descr;
			$queue[$count]->q_tags = $qEntry->tags;
			$queue[$count]->q_edited = $qEntry->edited;
			$count++;
		}
		return $queue;
	}
	

	
}
?>