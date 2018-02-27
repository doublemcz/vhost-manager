<?php

namespace App\Commands;

use App\Core\DomainController;
use jc21\CliTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
			->setHelp('This command list domains');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$table = new CliTable();
		$table->addField('Domain', 'server_name');
		$table->addField('Listen', 'listen');
		$table->addField('Root', 'root');
		$table->addField('SSL', 'ssl');
		$table->addField('Proxy pass', 'proxy_pass');
		$table->addField('File', 'file');
		$table->injectData($this->domainController->getList());
		$table->display();
	}

}
