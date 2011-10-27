<?php
class ConsolidatedReportJob extends AppModel
{
	public $name = 'ConsolidatedReportJob';
	public $useTable = 'consolidated_report_jobs';
	public $primaryKey = 'id';
	public $hasMany = array(
		'ConsolidatedReportJobsClients' => array(
			'foreignKey' => 'consolidated_report_job_id',
		)
	);
	
	/**
	 *
	 */
	public function getScheduled() {
		$params = array(
			'conditions' => array(
				'ConsolidatedReportJob.status' => 'scheduled',

			)
		);
		
		return $this->find('all', $params);
	}
	
	/**
	 *
	 */
	public function setJobInProgress($job_id) {
		$sql = "UPDATE consolidated_report_jobs SET status='in_progress' WHERE id=$job_id";
		$this->query($sql);
		
		$sql = "UPDATE consolidated_report_jobs_clients SET status='in_progress' WHERE consolidated_report_job_id=$job_id";
		$this->query($sql);
	}	
	
	/**
	 *
	 */
	public function setJobCompleted($job_id) {
		$sql = "UPDATE consolidated_report_jobs SET status='completed' WHERE id=$job_id";
		$this->query($sql);
	}	
	
	/**
	 *
	 */
	public function setTaskCompleted($task_id) {
		$sql = "UPDATE consolidated_report_jobs_clients SET status='completed' WHERE id=$task_id";
		$this->query($sql);
	}
}
?>