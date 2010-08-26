<?php

class latex_paragraph extends Plugin		// Extends the core Plugin class
{


	/************************** Actions & Filters **************************/
	

	/**
	 * function set_priorities
	 * Sets priorities of various actions and filters so they don't interfere with one another. (default priority is 8)
	 */
	function set_priorities()
	{
		return array
		(
			//Make this happen after the excerpt filter so images don't get stripped
			'filter_post_content_excerpt' => 14,
			//And push these back for good measure
			'filter_post_content_out' => 14,
			'filter_comment_content_out' => 14,
		);
	}
	
	
	public function callback_newparagraph( $match )
	{
		return "</p><p>" . substr( $match[0], -1 );
	}
	
	public function callback_newline( $match )
	{
		return "<br />" . substr( $match[0], -1 );
	}
	
	/**
	 * function filter_post_content
	 * Search for the LaTeX code so the tags can be replaced by images
	*/
	public function filter_post_content_out( $content, $post )
	{
		
		if ( ! preg_match ( '/^<p>/si', $content ) )
		{
			$content = "<p>" . $content . "</p>";
		}
		
		//XXX: \n\n[tex] is a problem.
		//$content = preg_replace_callback ( "/(\n\r|\n){2,}[^<]/si", array( $this, 'callback_newparagraph' ), $content );
		//$content = preg_replace_callback ( "/(\n\r|\n)[^<]/si", array( $this, 'callback_newline' ), $content );
		//$content = preg_replace ( "/(\n\r|\n){2,}/si", "</p><p>", $content );
		$content = preg_replace_callback ( "/(\n\r|\n){2,}/si", array( $this, 'callback_newparagraph' ), $content );
		$content = preg_replace ( "/(\n\r|\n)/si", "<br />", $content );
		$content = preg_replace ( "/\\\\newline/si", "<br />", $content );
		$content = preg_replace ( "/\\\\{2}/si", "<br />", $content );

		return $content;
	}
	
	
	/**
	 * function filter_post_content_excerpt
	 * Does the same thing as filter_post_content_out but for excerpts.
	 */
	public function filter_post_content_excerpt( $content, $post )
	{
		//why reinvent the wheel?
		return $this->filter_post_content_out( $content, null );
	}
	
	
	/**
	 * function filter_comment_content_out
	 * When a comment is displayed, search for tex.
	 * @return string $content
	*/
	public function filter_comment_content_out( $content, $comment )
	{
		//why reinvent the wheel?
		return $this->filter_post_content_out( $content, null );		
	}
	
	
	/************************** Admin Stuff **************************/


	/**
	 * function help
	 * Returns a quick bit of help
	 * @return string The help string
	*/
	public function help()		// Shows a text with basic usage instructions
	{
		$help = '<p>Forces LaTeX style paragraphs and newlines.  (one return = new line, two returns = new paragraph).</p>';
	}


}

?>
