<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Morris Jobke <hey@morrisjobke.de>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\OCCTools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OCA\User_LDAP\User\DeletedUsersIndex;
use OCA\User_LDAP\Mapping\UserMapping;
use OCA\User_LDAP\Helper as LDAPHelper;
use OCA\User_LDAP\User_Proxy;
use OCA\User_LDAP\User\Manager;
use OCA\User_LDAP\Access;
use OCA\User_LDAP\Connection;

use OCP\IUser;
use OCP\IUserManager;

class SyncUsers extends Command {
	/** @var \OCA\User_LDAP\User_Proxy */
	protected $backend;

	/** @var \OCA\User_LDAP\Helper */
	protected $helper;

	/** @var \OCA\User_LDAP\User\DeletedUsersIndex */
	protected $dui;

	/** @var IUserManager */
	protected $userManager;

	/**
	 * @param User_Proxy $uBackend
	 * @param LDAPHelper $helper
	 * @param DeletedUsersIndex $dui
	 * @param IUserManager $userManager
	 */
	public function __construct(User_Proxy $uBackend, LDAPHelper $helper, DeletedUsersIndex $dui, IUserManager $userManager) {
		$this->backend = $uBackend;
		$this->helper = $helper;
		$this->dui = $dui;
		$this->userManager = $userManager;

		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('occtools:syncusers')
			->setDescription('Synchronise user data from LDAP.')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$table = new Table($output);
		$table->setHeaders(array('UID', 'DN', 'Exist'));
		$rows = array();

		$users = $this->userManager->search();

		foreach($users as $user) {
			// Attribut
			$uid=$user->getUID();
			$dn=$this->backend->getLDAPAccess()->getUserMapper()->getDNByName($uid);
			
			// User exist on LDAP ?
			$exists = $this->backend->userExistsOnLDAP($user->getUID());

			if($exists === false) {
				$this->dui->markUser($uid);
			}			
			else {
				$userldap=$this->backend->getLDAPAccess()->userManager->get($uid);
				$userldap->markLogin();
				$userldap->update();
			}

			// Output
			$rows[] = array('UID'      	=> $uid,			
							'DN'		=> $dn,
							'Exist'		=> ($exists?"yes":"no")
							);

		}

		$table->setRows($rows);
		$table->render($output);
	}

}
