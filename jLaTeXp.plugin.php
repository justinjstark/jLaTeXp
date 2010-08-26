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
			//Make these happen late
			'filter_post_content_excerpt' => 14,
			'filter_post_content_out' => 14,
			'filter_comment_content_out' => 14,
		);
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
		
		//at least two returns
		$content = preg_replace ( "/(\n\r|\n){2,}/si", "</p><p>", $content );
		//only one returns
		$content = preg_replace ( "/(\n\r|\n)/si", "<br />", $content );
		//\newline
		$content = preg_replace ( "/\\\\newline/si", "<br />", $content );
		//\\
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
	

	/*
	 * function action_init_theme
	 * Called when the theme is initialized
	 */
	public function action_init_theme()
	{
		//Add the stylesheet
		Stack::add('template_stylesheet', array( URL::get_from_filesystem(__FILE__) . '/formatting.css', 'screen', 'screen' ), 'jLaTeXp-css');
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
