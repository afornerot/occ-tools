<?php
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\OCCTools\Command;

use OCA\Activity\MailQueueHandler;
use OCA\Activity\UserSettings;
use OCP\IConfig;
use OCP\ILogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command {

	/** @var MailQueueHandler */
	protected $queueHandler;

	/** @var IConfig */
	protected $config;

	/** @var ILogger */
	protected $logger;

	/**
	 * @param MailQueueHandler $queueHandler
	 * @param IConfig $config
	 * @param ILogger $logger
	 */
	public function __construct(MailQueueHandler $queueHandler,
								IConfig $config,
								ILogger $logger) {
		parent::__construct();

		$this->queueHandler = $queueHandler;
		$this->config = $config;
		$this->logger = $logger;
	}

	protected function configure() {
		$this
			->setName('occtools:test')
			->setDescription('Skelete Command = do nothing')
		;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln("Skelete Command = do nothing");	

		return 0;
	}
}
