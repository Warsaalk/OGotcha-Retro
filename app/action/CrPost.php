<?php
use Plinth\Request\ActionType;
use Plinth\Validation\Validator;
use Plinth\Common\Info;

use Kokx\Service\CombatReport;
use Kokx\Renderer\Helper;

class CrPost extends ActionType {
/*
$vl = $this->Main()->getValidator();

$vl->addVariable( 'report', array('min=1'), 'string' );
$vl->addVariable( 'theme', array('select'), 'string' );
$vl->addVariable( 'middletext', array(), 'string', false );
$vl->addVariable( 'merge', array(), 'integer', false );
$vl->addVariable( 'hidetime', array(), 'integer', false ); // Use integer type for this checkbox as it doesn't exits in a group
$vl->addVariable( 'advanced', array(), 'integer', false );
$vl->addVariable( 'spoiler', array(), 'integer', false );
$vl->addVariable( 'raids', array(), 'string', false );
$vl->addVariable( 'attacker_harvest', array(), 'string', false );
$vl->addVariable( 'attacker_deuterium', array(), 'string', false );
$vl->addVariable( 'defender_harvest', array(), 'string', false );
$vl->addVariable( 'defender_deuterium', array(), 'string', false );

$vl->validate( $form );

if( $vl->isValid() ){

	//$this->Main()->addInfo( new Info( 'Valid Cr', Info::SUCCESS ) );

}else{

	$this->Main()->addInfo( new Info( $this->Main()->getDict()->getVal('Invalid post'), Info::ERROR ) );

}
	*/
	/**
	 * (non-PHPdoc)
	 * @see \Plinth\Request\ActionType::getSettings()
	 */
	public function getSettings() {
		
		return array(
			'variables' => array(
				'report' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'message' => new Info('Please paste a correct combat report!', Info::ERROR)
				),
				'theme' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'message' => new Info('!', Info::ERROR)
				),
				'middletext' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'merge' => array(
					'type' => Validator::PARAM_INTEGER,
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'hidetime' => array(
					'type' => Validator::PARAM_INTEGER,
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'advanced' => array(
					'type' => Validator::PARAM_INTEGER,
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'spoiler' => array(
					'type' => Validator::PARAM_INTEGER,
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'raids' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'attacker_harvest' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'attacker_deuterium' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'defender_harvest' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				),
				'defender_deuterium' => array(
					'type' => Validator::PARAM_STRING,
					'rules' => array(
						Validator::RULE_MIN_LENGTH => 1
					),
					'required' => false,
					'message' => new Info('!', Info::ERROR)
				)
			)
		);
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Plinth\Request\ActionType::onFinish()
	 */
	public function onFinish(array $variables, array $files) {

		$crService = new CombatReport($this->main);

		try {

			$settings 	= $crService->getSettings();
			$report		= $crService->getReport();

			$renderer = $crService->getRenderer($settings, $report);

			$renderedReport = $renderer->renderReport();
			$renderedTitle  = $renderer->renderTitle();
			$renderedPreview= Helper::parseBBCode($renderedReport);

			$response = $this->main->getResponse();
			$response->addData('renderedReport', $renderedReport);
			$response->addData('renderedTitle', $renderedTitle);
			$response->addData('renderedPreview', $renderedPreview);

		} catch (Exception $e) {

			$this->main->addInfo(new Info($this->main->getDict()->get('Bad Cr'), Info::ERROR));

		}

	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Plinth\Request\ActionType::onError()
	 */
	public function onError() {}
	
}