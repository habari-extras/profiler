<?php

class ProfilerPlugin extends Plugin
{
	
	/**
	 * Display profiler information on dshboard
	 **/	 
	function filter_dashboard_status($statuses)
	{
		$statuses[_t('Average Page Creation Time')] = _t('%f seconds', array(Options::get('profiler_avg')));
		return $statuses;
	}
	
	/**
	 * Log page creation times
	 **/	 	
	function filter_final_output($output)
	{
		global $profile_start;
		
		$pagetime = microtime(true) - $profile_start;
		
		$avgtime = Options::get('profiler_avg');
		$hits = Options::get('profiler_hits');

		$newavg = ($avgtime * $hits + $pagetime) / ($hits + 1);
		$hits++;
		
		Options::set('profiler_avg', $newavg);
		Options::set('profiler_hits', $hits);
	
		return $output;
	}
	
	/**
	 * Add an option to reset the profiler stats
	 **/	 
	public function filter_plugin_config( $actions, $plugin_id )
	{
		if ( $plugin_id == $this->plugin_id() ) {
			$actions['reset']= _t( 'Reset Stats' );
		}
		return $actions;
	}
	
	/**
	 * Reset the stats
	 **/	 	
	public function action_plugin_ui( $plugin_id, $action )
	{
		if ( $plugin_id == $this->plugin_id() ) {
			switch ( $action ) {
				case 'reset':
					echo '<b>' . _t('Profiler stats have been reset') . '</b>';
					Options::set('profiler_avg', 0);
					Options::set('profiler_hits', 0);
					break;
			}
		}
	}

}
?>