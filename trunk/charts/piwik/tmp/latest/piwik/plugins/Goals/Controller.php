<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Controller.php 2594 2010-07-20 18:21:39Z matt $
 * 
 * @category Piwik_Plugins
 * @package Piwik_Goals
 */

/**
 *
 * @package Piwik_Goals
 */
class Piwik_Goals_Controller extends Piwik_Controller 
{
	const CONVERSION_RATE_PRECISION = 1;
	
	protected $goalColumnNameToLabel = array(
		'nb_conversions' => 'Goals_ColumnConversions',
		'conversion_rate'=> 'General_ColumnConversionRate',
		'revenue' => 'Goals_ColumnRevenue',
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->idSite = Piwik_Common::getRequestVar('idSite');
		$this->goals = Piwik_Goals_API::getInstance()->getGoals($this->idSite);
	}
	
	public function widgetGoalReport()
	{
		$view = $this->getGoalReportView();
		$view->displayFullReport = false;
		echo $view->render();
	}
	
	public function goalReport()
	{
		$view = $this->getGoalReportView();
		$view->displayFullReport = true;
        $view->goalSegments = Piwik_Goals::getReportsWithGoalMetrics();
		echo $view->render();
	}
	
	protected function getGoalReportView()
	{
		$idGoal = Piwik_Common::getRequestVar('idGoal', null, 'int');
		if(!isset($this->goals[$idGoal]))
		{
			Piwik::redirectToModule('Goals', 'index', array('idGoal' => null));
		}
		$goalDefinition = $this->goals[$idGoal];
		
		$view = Piwik_View::factory('single_goal');
		$this->setGeneralVariablesView($view);
		$goal = $this->getMetricsForGoal($idGoal);
		foreach($goal as $name => $value)
		{
			$view->$name = $value;
		}
		$view->idGoal = $idGoal;
		$view->goalName = $goalDefinition['name'];
		$view->graphEvolution = $this->getEvolutionGraph(true, array('nb_conversions'), $idGoal);
		$view->nameGraphEvolution = 'GoalsgetEvolutionGraph'.$idGoal;
		$view->topSegments = $this->getTopSegments($idGoal);
		
		// conversion rate for new and returning visitors
		$request = new Piwik_API_Request("method=Goals.getConversionRateReturningVisitors&format=original");
		$view->conversion_rate_returning = round( $request->process(), self::CONVERSION_RATE_PRECISION );
		$request = new Piwik_API_Request("method=Goals.getConversionRateNewVisitors&format=original");
		$view->conversion_rate_new = round( $request->process(), self::CONVERSION_RATE_PRECISION );
		return $view;
	}
	
	public function index()
	{
		$view = $this->getOverviewView();
		$view->goalsJSON = json_encode($this->goals);
        $view->goalSegments = Piwik_Goals::getReportsWithGoalMetrics();
		$view->userCanEditGoals = Piwik::isUserHasAdminAccess($this->idSite);
		$view->displayFullReport = true;
		echo $view->render();
	}
	
	public function widgetGoalsOverview( )
	{
		$view = $this->getOverviewView();
		$view->displayFullReport = false;
		echo $view->render();
	}
	
	protected function getOverviewView()
	{
		$view = Piwik_View::factory('overview');
		$this->setGeneralVariablesView($view);
		
		$view->graphEvolution = $this->getEvolutionGraph(true, array('nb_conversions'));
		$view->nameGraphEvolution = 'GoalsgetEvolutionGraph'; 

		// sparkline for the historical data of the above values
		$view->urlSparklineConversions		= $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('nb_conversions')));
		$view->urlSparklineConversionRate 	= $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('conversion_rate')));
		$view->urlSparklineRevenue 			= $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('revenue')));

		$request = new Piwik_API_Request("method=Goals.get&format=original&idGoal=0");
		$datatable = $request->process();
		$dataRow = $datatable->getFirstRow();
		$view->nb_conversions = $dataRow->getColumn('nb_conversions');
		$view->conversion_rate = $dataRow->getColumn('conversion_rate');
		$view->revenue = $dataRow->getColumn('revenue');
		
		$goalMetrics = array();
		foreach($this->goals as $idGoal => $goal)
		{
			$goalMetrics[$idGoal] = $this->getMetricsForGoal($idGoal);
			$goalMetrics[$idGoal]['name'] = $goal['name'];
		}
		
		$view->goalMetrics = $goalMetrics;
		$view->goals = $this->goals;
		return $view;
	}

	public function getLastNbConversionsGraph( $fetch = false )
	{
		$view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, 'Goals.getConversions');
		return $this->renderView($view, $fetch);
	}
	
	public function getLastConversionRateGraph( $fetch = false )
	{
		$view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, 'Goals.getConversionRate');
		return $this->renderView($view, $fetch); 
	}

	public function getLastRevenueGraph( $fetch = false )
	{
		$view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, 'Goals.getRevenue');
		return $this->renderView($view, $fetch);
	}
	
	public function addNewGoal()
	{
		$view = Piwik_View::factory('add_new_goal');
		$this->setGeneralVariablesView($view);
		$view->userCanEditGoals = Piwik::isUserHasAdminAccess($this->idSite);
		$view->onlyShowAddNewGoal = true;
		echo $view->render();
	}

	public function getEvolutionGraph( $fetch = false, $columns = false, $idGoal = false)
	{
		if(empty($columns))
		{
			$columns = Piwik_Common::getRequestVar('columns');
		}

		$columns = !is_array($columns) ? array($columns) : $columns;

		if(empty($idGoal))
		{
			$idGoal = Piwik_Common::getRequestVar('idGoal', false);
		}
		$view = $this->getLastUnitGraph($this->pluginName, __FUNCTION__, 'Goals.get');
		$view->setParametersToModify(array('idGoal' => $idGoal));
		
		foreach($columns as $columnName)
		{
			// find the right translation for this column, eg. find 'revenue' if column is Goal_1_revenue
			foreach($this->goalColumnNameToLabel as $metric => $metricTranslation)
			{
				if(strpos($columnName, $metric) !== false)
				{
					$columnTranslation = Piwik_Translate($metricTranslation);
					break;
				}
			}
			
			if(!empty($idGoal) && isset($this->goals[$idGoal]))
			{
				$goalName = $this->goals[$idGoal]['name'];
				$columnTranslation = "$columnTranslation (".Piwik_Translate('Goals_GoalX', "$goalName").")";
			}
			$view->setColumnTranslation($columnName, $columnTranslation);
		}
		$view->setColumnsToDisplay($columns);
		return $this->renderView($view, $fetch);
	}
	
	
	protected function getTopSegments($idGoal)
	{ 
		$columnNbConversions = 'goal_'.$idGoal.'_nb_conversions';
		$columnConversionRate = 'goal_'.$idGoal.'_conversion_rate';
		
		$topSegmentsToLoad = array(
			'country' => 'UserCountry.getCountry',
			'keyword' => 'Referers.getKeywords',
			'website' => 'Referers.getWebsites',
		);
		
		$topSegments = array();
		foreach($topSegmentsToLoad as $segmentName => $apiMethod)
		{
			$request = new Piwik_API_Request("method=$apiMethod
												&format=original
												&filter_update_columns_when_show_all_goals=1
												&filter_only_display_idgoal=". Piwik_DataTable_Filter_AddColumnsProcessedMetricsGoal::GOALS_FULL_TABLE ."
												&filter_sort_order=desc
												&filter_sort_column=$columnNbConversions
												&filter_limit=3");
			$datatable = $request->process();
			$topSegment = array();
			foreach($datatable->getRows() as $row)
			{
				$conversions = $row->getColumn($columnNbConversions);
				if($conversions > 0)
				{
    				$topSegment[] = array (
    					'name' => $row->getColumn('label'),
    					'nb_conversions' => $conversions,
    					'conversion_rate' => $row->getColumn($columnConversionRate),
    					'metadata' => $row->getMetadata(),
    				);
				}
			}
			$topSegments[$segmentName] = $topSegment;
		}
		return $topSegments;
	}
	
	protected function getMetricsForGoal($idGoal)
	{
		$request = new Piwik_API_Request("method=Goals.get&format=original&idGoal=$idGoal");
		$datatable = $request->process();
		$dataRow = $datatable->getFirstRow();
		return array (
				'id'				=> $idGoal,
				'nb_conversions' 	=> $dataRow->getColumn('nb_conversions'),
				'conversion_rate'	=> round($dataRow->getColumn('conversion_rate'), 1),
				'revenue'			=> $dataRow->getColumn('revenue'),
				'urlSparklineConversions' 		=> $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('nb_conversions'), 'idGoal' => $idGoal)),
				'urlSparklineConversionRate' 	=> $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('conversion_rate'), 'idGoal' => $idGoal)),
				'urlSparklineRevenue' 			=> $this->getUrlSparkline('getEvolutionGraph', array('columns' => array('revenue'), 'idGoal' => $idGoal)),
		);
	}
}
