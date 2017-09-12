<?php
WP_CLI::add_command('countFix', 'WP_CLI_Countfix');

class WP_CLI_Countfix extends WP_CLI_Command
{

	/**
	 * * Fix Term Counts
	 * *
	 * * ## EXAMPLES
	 * *
	 * * wp countFix fixTerms
	 * *
	 */
	public function fixTerms()
	{
		global $wpdb;
		$post_types = get_post_types('', 'objects');
		
		$sqlQuery = "SELECT term_taxonomy_id, taxonomy FROM " . $wpdb->term_taxonomy;
		$results = $wpdb->get_results($sqlQuery, ARRAY_A);
		$affected = 0;
		$taxs = array();
		foreach ($results as $row)
		{
			$tax = $row['taxonomy'];
			$term_taxonomy_id = $row['term_taxonomy_id'];
			if(!isset($taxs[$tax]))
			{
				$taxs[$tax] = $this->get_post_types_by_taxonomy($tax, 'string');
			}
			$types = $taxs[$tax];
			echo "term_taxonomy_id: " . $term_taxonomy_id . " count = ";
			$count = $wpdb->get_var(
					"SELECT count(*) FROM " . $wpdb->term_relationships.' as tr INNER JOIN '.
					$wpdb->posts.' as p on tr.object_id = p.ID '.
					" WHERE tr.term_taxonomy_id = '$term_taxonomy_id' AND post_type IN ($types) AND p.post_status IN ('publish', 'future') ");
			echo $count . "\n";
			$affected += $wpdb->query(
					"UPDATE " . $wpdb->term_taxonomy .
					" SET count = '$count' WHERE term_taxonomy_id = '$term_taxonomy_id'");
		}
		
		
		WP_CLI::success(__('affected rows:.'.$affected)."\n");
	}
	
	/**
	 * * Fix Comments Counts
	 * *
	 * * ## EXAMPLES
	 * *
	 * * wp countFix fixComments
	 * *
	 */
	public function fixComments()
	{
		//TODO create function, this is a example found in https://wordpress.org/support/topic/fix-comment-and-category-counts-after-import/
		/*
		$result = mysql_query("SELECT ID FROM " . $table_prefix . "posts");
		while($row = mysql_fetch_array($result))
		{
			$post_id = $row['ID'];
			echo "post_id: " . $post_id . " count = ";
			$countresult = mysql_query(
					"SELECT count(*) FROM " . $table_prefix .
					"comments WHERE comment_post_ID = '$post_id' AND comment_approved = 1");
			$countarray = mysql_fetch_array($countresult);
			$count = $countarray[0];
			echo $count . "<br />";
			mysql_query(
					"UPDATE " . $table_prefix .
					"posts SET comment_count = '$count' WHERE ID = '$post_id'");
		}*/
		WP_CLI::success(__('OK.'));
	}
	
	/**
	 * 
	 * @param int $term_taxonomy_id
	 * @return boolean|string|NULL
	 */
	protected function get_taxonomy_by_term_taxonomy_id( $term_taxonomy_id = null )
	{
		if(empty($term_taxonomy_id)) return false;
		global $wpdb;
		
		$res = $wpdb->get_var(
				$wpdb->prepare(
						'select taxonomy from '.$wpdb->term_taxonomy.' where term_taxonomy_id = %d LIMIT 1',
						(int) $term_taxonomy_id
						)
				);
		
		if( is_wp_error($res) ) return false;
		
		return $res;
	}
	
	/**
	 * Get all post types for a taxonomy.
	 *
	 * @author Kellen Mace, Jacson Passold
	 * @param  string $taxonomy The taxonomy slug to get post types for.
	 * @param  string $output The output format array or string (quoted string for sql query)
	 * @return array|string The post types associated with $taxonomy 
	 */
	protected function get_post_types_by_taxonomy( $taxonomy = '', $output = 'array' ) {
		global $wp_taxonomies;
		
		switch ($output)
		{
			case "string":
				if ( isset( $wp_taxonomies[ $taxonomy ] ) ) {
					return "'".implode("','", $wp_taxonomies[ $taxonomy ]->object_type)."'";
				}
				return '';
			break;
			case 'array':
			default:
				if ( isset( $wp_taxonomies[ $taxonomy ] ) ) {
					return $wp_taxonomies[ $taxonomy ]->object_type;
				}
				return array();
			break;
		}
	}
}
