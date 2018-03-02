<?php

namespace App\Commands;

use App\Core\DomainController;
use jc21\CliTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DomainListCommand extends Command
{

	/** @var DomainController */
	private $domainController;

	/**
	 * DomainListCommand constructor.
	 * @param DomainController $domainController
	 */
	public function __construct(DomainController $domainController)
	{
		parent::__construct();
		$this->domainController = $domainController;
	}

	/**
	 * @inheritdoc
	 */
	protected function configure()
	{
		$this
			->setName('ls')
			->setDescription('List domains')
			->setHelp('This command list domains')
			->addOption(
				'filter',
				null,
				InputOption::VALUE_OPTIONAL,
				'Filter domain names. Can be string or regexp (start and closing character is already present)'
			);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$data = $this->domainController->getList();
		$data = $this->filterData($input->getOption('filter'), $data);

		$table = new CliTable();
		$table->addField('Domain', 'server_name');
		$table->addField('Listen', 'listen');
		$table->addField('Root', 'root');
		$table->addField('SSL', 'ssl');
		$table->addField('Proxy pass', 'proxy_pass');
		$table->addField('File', 'file');
		$table->injectData($data);
		$table->display();
	}

	/**
	 * @param string $filter
	 * @param array $data
	 * @return array
	 */
	private function filterData($filter, $data)
	{
		if (empty($filter) || !is_array($data)) {
			return $data;
		}

		$result = [];
		foreach ($data as $domain) {
			if (@preg_match("~$filter~", $domain['server_name'])) {
				$result[] = $domain;
			}
		}

		return $result;
	}

}
