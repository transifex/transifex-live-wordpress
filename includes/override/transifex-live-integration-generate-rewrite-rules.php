<?php

/**
 * Language rewrites
 * @package TransifexLiveIntegration
 */

/**
 * Static class for settings defaults
 * Experimental turned off in production
 */
class Transifex_Live_Integration_Generate_Rewrite_Rules {

	/**
	 * Generates rewrite rules from a permalink structure.
	 *
	 * The main WP_Rewrite function for building the rewrite rule list. The
	 * contents of the function is a mix of black magic and regular expressions,
	 * so best just ignore the contents and move to the parameters.
	 *
	 * @since  1.5.0
	 * @access public
	 *
	 * @param  string $permalink_structure The permalink structure.
	 * @param  int    $ep_mask             Optional. Endpoint mask defining what endpoints are added to the structure.
	 *                                    Accepts `EP_NONE`, `EP_PERMALINK`, `EP_ATTACHMENT`, `EP_DATE`, `EP_YEAR`,
	 *                                    `EP_MONTH`, `EP_DAY`, `EP_ROOT`, `EP_COMMENTS`, `EP_SEARCH`, `EP_CATEGORIES`,
	 *                                    `EP_TAGS`, `EP_AUTHORS`, `EP_PAGES`, `EP_ALL_ARCHIVES`, and `EP_ALL`.
	 *                                    Default `EP_NONE`.
	 * @param  bool   $paged               Optional. Whether archive pagination rules should be added for the structure.
	 *                                    Default true.
	 * @param  bool   $feed                Optional Whether feed rewrite rules should be added for the structure.
	 *                                    Default true.
	 * @param  bool   $forcomments         Optional. Whether the feed rules should be a query for a comments feed.
	 *                                    Default false.
	 * @param  bool   $walk_dirs           Optional. Whether the 'directories' making up the structure should be walked
	 *                                    over and rewrite rules built for each in-turn. Default true.
	 * @param  bool   $endpoints           Optional. Whether endpoints should be applied to the generated rewrite rules.
	 *                                    Default true.
	 * @return array Rewrite rule list.
	 */
	static public function generate_rewrite_rules( $permalink_structure,
			$ep_mask = EP_NONE, $paged = true, $feed = true, $forcomments = false,
			$walk_dirs = true, $endpoints = true
	) {
		global $wp_rewrite;
		// Build a regex to match the feed section of URLs, something like (feed|atom|rss|rss2)/?
		$feedregex2 = '';
		foreach ((array) $wp_rewrite->feeds as $feed_name) {
			$feedregex2 .= $feed_name . '|';
		}
		$feedregex2 = '(' . trim( $feedregex2, '|' ) . ')/?$';
		/*
		 * $feedregex is identical but with /feed/ added on as well, so URLs like <permalink>/feed/atom
		 * and <permalink>/atom are both possible
		 */
		$feedregex = $wp_rewrite->feed_base . '/' . $feedregex2;
		// Build a regex to match the trackback and page/xx parts of URLs.
		$trackbackregex = 'trackback/?$';
		$pageregex = $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$';
		$commentregex = $wp_rewrite->comments_pagination_base . '-([0-9]{1,})/?$';
		$embedregex = 'embed/?$';
		// Build up an array of endpoint regexes to append => queries to append.
		if ( $endpoints ) {
			$ep_query_append = array();
			foreach ((array) $wp_rewrite->endpoints as $endpoint) {
				// Match everything after the endpoint name, but allow for nothing to appear there.
				$epmatch = $endpoint[1] . '(/(.*))?/?$';
				// This will be appended on to the rest of the query for each dir.
				$epquery = '&' . $endpoint[2] . '=';
				$ep_query_append[$epmatch] = array( $endpoint[0], $epquery );
			}
		}
		// Get everything up to the first rewrite tag.
		$front = substr( $permalink_structure, 0, strpos( $permalink_structure, '%' ) );
		// Build an array of the tags (note that said array ends up being in $tokens[0]).
		preg_match_all( '/%.+?%/', $permalink_structure, $tokens );
		$num_tokens = count( $tokens[0] );
		$index = $wp_rewrite->index; //probably 'index.php'
		$feedindex = $index;
		$trackbackindex = $index;
		$embedindex = $index;
		/*
		 * Build a list from the rewritecode and queryreplace arrays, that will look something
		 * like tagname=$matches[i] where i is the current $i.
		 */
		$queries = array();
		for ($i = 0; $i < $num_tokens; ++$i) {
			if ( 0 < $i ) {
				$queries[$i] = $queries[$i - 1] . '&';
			} else {
				$queries[$i] = '';
			}
			$query_token = str_replace( $wp_rewrite->rewritecode, $wp_rewrite->queryreplace, $tokens[0][$i] ) . $wp_rewrite->preg_index( $i + 1 );
			$queries[$i] .= $query_token;
		}
		// Get the structure, minus any cruft (stuff that isn't tags) at the front.
		$structure = $permalink_structure;
		if ( $front != '/' ) {
			$structure = str_replace( $front, '', $structure );
		}
		/*
		 * Create a list of dirs to walk over, making rewrite rules for each level
		 * so for example, a $structure of /%year%/%monthnum%/%postname% would create
		 * rewrite rules for /%year%/, /%year%/%monthnum%/ and /%year%/%monthnum%/%postname%
		 */
		$structure = trim( $structure, '/' );
		$dirs = $walk_dirs ? explode( '/', $structure ) : array( $structure );
		$num_dirs = count( $dirs );
		// Strip slashes from the front of $front.
		$front = preg_replace( '|^/+|', '', $front );
		// The main workhorse loop.
		$post_rewrite = array();
		$struct = $front;
		for ($j = 0; $j < $num_dirs; ++$j) {

			$struct = str_replace( '%lang%', '', $struct );
			// Get the struct for this dir, and trim slashes off the front.
			$struct .= $dirs[$j] . '/'; // Accumulate. see comment near explode('/', $structure) above.
			$struct = ltrim( $struct, '/' );
			if ( $walk_dirs ) {
				if ( strpos( $struct, '%lang' ) === false ) {
					$struct = '%lang%/' . $struct;
				}
			}
			Plugin_Debug::logTrace( $struct );
			// Replace tags with regexes.
			$match = str_replace( $wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $struct );
			Plugin_Debug::logTrace( $match );
			// Make a list of tags, and store how many there are in $num_toks.
			$num_toks = preg_match_all( '/%.+?%/', $struct, $toks );
			Plugin_Debug::logTrace( $num_toks );
			// Get the 'tagname=$matches[i]'.
			$query = (!empty( $num_toks ) && isset( $queries[$num_toks - 1] ) ) ? $queries[$num_toks - 1] : '';
			Plugin_Debug::logTrace( $query );
			// Set up $ep_mask_specific which is used to match more specific URL types.
			switch ($dirs[$j]) {
				case '%year%':
					$ep_mask_specific = EP_YEAR;
					break;
				case '%monthnum%':
					$ep_mask_specific = EP_MONTH;
					break;
				case '%day%':
					$ep_mask_specific = EP_DAY;
					break;
				default:
					$ep_mask_specific = EP_NONE;
			}
			// Create query for /page/xx.
			$pagematch = $match . $pageregex;
			$pagequery = $index . '?' . $query . '&paged=' . $wp_rewrite->preg_index( $num_toks + 1 );
			// Create query for /comment-page-xx.
			$commentmatch = $match . $commentregex;
			$commentquery = $index . '?' . $query . '&cpage=' . $wp_rewrite->preg_index( $num_toks + 1 );
			if ( get_option( 'page_on_front' ) ) {
				// Create query for Root /comment-page-xx.
				$rootcommentmatch = $match . $commentregex;
				$rootcommentquery = $index . '?' . $query . '&page_id=' . get_option( 'page_on_front' ) . '&cpage=' . $wp_rewrite->preg_index( $num_toks + 1 );
			}
			// Create query for /feed/(feed|atom|rss|rss2|rdf).
			$feedmatch = $match . $feedregex;
			$feedquery = $feedindex . '?' . $query . '&feed=' . $wp_rewrite->preg_index( $num_toks + 1 );
			// Create query for /(feed|atom|rss|rss2|rdf) (see comment near creation of $feedregex).
			$feedmatch2 = $match . $feedregex2;
			$feedquery2 = $feedindex . '?' . $query . '&feed=' . $wp_rewrite->preg_index( $num_toks + 1 );
			// If asked to, turn the feed queries into comment feed ones.
			if ( $forcomments ) {
				$feedquery .= '&withcomments=1';
				$feedquery2 .= '&withcomments=1';
			}
			// Start creating the array of rewrites for this dir.
			$rewrite = array();
			// ...adding on /feed/ regexes => queries
			if ( $feed ) {
				$rewrite = array( $feedmatch => $feedquery, $feedmatch2 => $feedquery2 );
			}
			//...and /page/xx ones
			if ( $paged ) {
				$rewrite = array_merge( $rewrite, array( $pagematch => $pagequery ) );
			}
			// Only on pages with comments add ../comment-page-xx/.
			if ( EP_PAGES & $ep_mask || EP_PERMALINK & $ep_mask ) {
				$rewrite = array_merge( $rewrite, array( $commentmatch => $commentquery ) );
			} elseif ( EP_ROOT & $ep_mask && get_option( 'page_on_front' ) ) {
				$rewrite = array_merge( $rewrite, array( $rootcommentmatch => $rootcommentquery ) );
			}
			// Do endpoints.
			if ( $endpoints ) {
				foreach ((array) $ep_query_append as $regex => $ep) {
					// Add the endpoints on if the mask fits.
					if ( $ep[0] & $ep_mask || $ep[0] & $ep_mask_specific ) {
						$rewrite[$match . $regex] = $index . '?' . $query . $ep[1] . $wp_rewrite->preg_index( $num_toks + 2 );
					}
				}
			}
			// If we've got some tags in this dir.
			if ( $num_toks ) {
				$post = false;
				$page = false;
				/*
				 * Check to see if this dir is permalink-level: i.e. the structure specifies an
				 * individual post. Do this by checking it contains at least one of 1) post name,
				 * 2) post ID, 3) page name, 4) timestamp (year, month, day, hour, second and
				 * minute all present). Set these flags now as we need them for the endpoints.
				 */
				if ( strpos( $struct, '%postname%' ) !== false || strpos( $struct, '%post_id%' ) !== false || strpos( $struct, '%pagename%' ) !== false || (strpos( $struct, '%year%' ) !== false && strpos( $struct, '%monthnum%' ) !== false && strpos( $struct, '%day%' ) !== false && strpos( $struct, '%hour%' ) !== false && strpos( $struct, '%minute%' ) !== false && strpos( $struct, '%second%' ) !== false)
				) {
					$post = true;
					if ( strpos( $struct, '%pagename%' ) !== false ) {
						$page = true;
					}
				}
				if ( !$post ) {
					// For custom post types, we need to add on endpoints as well.
					foreach (get_post_types( array( '_builtin' => false ) ) as $ptype) {
						if ( strpos( $struct, "%$ptype%" ) !== false ) {
							$post = true;
							// This is for page style attachment URLs.
							$page = is_post_type_hierarchical( $ptype );
							break;
						}
					}
				}
				Plugin_Debug::logTrace( 'post check' );
				Plugin_Debug::logTrace( $post );
				// If creating rules for a permalink, do all the endpoints like attachments etc.
				if ( $post ) {
					// Create query and regex for trackback.
					$trackbackmatch = $match . $trackbackregex;
					$trackbackquery = $trackbackindex . '?' . $query . '&tb=1';
					// Create query and regex for embeds.
					$embedmatch = $match . $embedregex;
					$embedquery = $embedindex . '?' . $query . '&embed=true';
					// Trim slashes from the end of the regex for this dir.
					$match = rtrim( $match, '/' );
					// Get rid of brackets.
					$submatchbase_org = str_replace( array( '(', ')' ), '', $match );
					$submatchbase = str_replace( '(', '(?:', $match ); // Replace with non capturing group
					Plugin_Debug::logTrace( $submatchbase_org );
					Plugin_Debug::logTrace( $submatchbase );
					// Add a rule for at attachments, which take the form of <permalink>/some-text.
					$sub1 = $submatchbase . '/([^/]+)/';
					// Add trackback regex <permalink>/trackback/...
					$sub1tb = $sub1 . $trackbackregex;
					// And <permalink>/feed/(atom|...)
					$sub1feed = $sub1 . $feedregex;
					// And <permalink>/(feed|atom...)
					$sub1feed2 = $sub1 . $feedregex2;
					// And <permalink>/comment-page-xx
					$sub1comment = $sub1 . $commentregex;
					// And <permalink>/embed/...
					$sub1embed = $sub1 . $embedregex;
					/*
					 * Add another rule to match attachments in the explicit form:
					 * <permalink>/attachment/some-text
					 */
					$sub2 = $submatchbase . '/attachment/([^/]+)/';
					// And add trackbacks <permalink>/attachment/trackback.
					$sub2tb = $sub2 . $trackbackregex;
					// Feeds, <permalink>/attachment/feed/(atom|...)
					$sub2feed = $sub2 . $feedregex;
					// And feeds again on to this <permalink>/attachment/(feed|atom...)
					$sub2feed2 = $sub2 . $feedregex2;
					// And <permalink>/comment-page-xx
					$sub2comment = $sub2 . $commentregex;
					// And <permalink>/embed/...
					$sub2embed = $sub2 . $embedregex;
					// Create queries for these extra tag-ons we've just dealt with.
					$subquery = $index . '?attachment=' . $wp_rewrite->preg_index( 1 );
					$subtbquery = $subquery . '&tb=1';
					$subfeedquery = $subquery . '&feed=' . $wp_rewrite->preg_index( 2 );
					$subcommentquery = $subquery . '&cpage=' . $wp_rewrite->preg_index( 2 );
					$subembedquery = $subquery . '&embed=true';
					// Do endpoints for attachments.
					if ( !empty( $endpoints ) ) {
						foreach ((array) $ep_query_append as $regex => $ep) {
							if ( $ep[0] & EP_ATTACHMENT ) {
								$rewrite[$sub1 . $regex] = $subquery . $ep[1] . $wp_rewrite->preg_index( 3 );
								$rewrite[$sub2 . $regex] = $subquery . $ep[1] . $wp_rewrite->preg_index( 3 );
							}
						}
					}
					/*
					 * Now we've finished with endpoints, finish off the $sub1 and $sub2 matches
					 * add a ? as we don't have to match that last slash, and finally a $ so we
					 * match to the end of the URL
					 */
					$sub1 .= '?$';
					$sub2 .= '?$';
					/*
					 * Post pagination, e.g. <permalink>/2/
					 * Previously: '(/[0-9]+)?/?$', which produced '/2' for page.
					 * When cast to int, returned 0.
					 */
					$match = $match . '(?:/([0-9]+))?/?$';
					$query = $index . '?' . $query . '&page=' . $wp_rewrite->preg_index( $num_toks + 1 );
					// Not matching a permalink so this is a lot simpler.
				} else {
					// Close the match and finalise the query.
					$match .= '?$';
					$query = $index . '?' . $query;
				}
				/*
				 * Create the final array for this dir by joining the $rewrite array (which currently
				 * only contains rules/queries for trackback, pages etc) to the main regex/query for
				 * this dir
				 */
				$rewrite = array_merge( $rewrite, array( $match => $query ) );
				// If we're matching a permalink, add those extras (attachments etc) on.
				if ( $post ) {
					// Add trackback.
					$rewrite = array_merge( array( $trackbackmatch => $trackbackquery ), $rewrite );
					// Add embed.
					$rewrite = array_merge( array( $embedmatch => $embedquery ), $rewrite );
					// Add regexes/queries for attachments, attachment trackbacks and so on.
					if ( !$page ) {
						// Require <permalink>/attachment/stuff form for pages because of confusion with subpages.
						$rewrite = array_merge(
								$rewrite, array(
							$sub1 => $subquery,
							$sub1tb => $subtbquery,
							$sub1feed => $subfeedquery,
							$sub1feed2 => $subfeedquery,
							$sub1comment => $subcommentquery,
							$sub1embed => $subembedquery
								)
						);
					}
					$rewrite = array_merge( array( $sub2 => $subquery, $sub2tb => $subtbquery, $sub2feed => $subfeedquery, $sub2feed2 => $subfeedquery, $sub2comment => $subcommentquery, $sub2embed => $subembedquery ), $rewrite );
				}
			}
			// Add the rules for this dir to the accumulating $post_rewrite.
			$post_rewrite = array_merge( $rewrite, $post_rewrite );
		}
		// The finished rules. phew!
		return $post_rewrite;
	}

}
