<?php
/**
 * User: Pavel Mashanov
 */

require_once(__DIR__.'/git.php');
require_once(__DIR__.'/configuration.php');

class Sulfur {

	protected $config;

	/** @var GitRepo */
	protected $master = null;

	/**
	 * Sulfur constructor.
	 *
	 * @param string $configPath
	 */
	public function __construct($configPath = '/etc/sulfur/sulfur.ini')
	{
		$this->config = Configuration::loadFromFile($configPath);
	}

	/**
	 * Обновляет все и вся
	 */
	public function update()
	{
		if(!$this->isMasterCorrect())
		{
			$this->createMasterRepo();
		}

		$this->master = Git::open($this->config->getMasterRepoPath());

		// Обновить мастер-репозиторий
		$this->updateMasterRepo();

		$this->updateSandboxList();
		//TODO: Получить список веток
			//TODO: На новые ветки создать песочницы
			//TODO: На удаленные ветки песочницы удалить
		//TODO: Обновить репозитории в каждой ветке с мастера
	}

	/**
	 * Создает/удаляет песочницы для веток
	 */
	protected function updateSandboxList()
	{
		$branches = $this->getRemoteBranchesList();
		$sandboxes = $this->getSandboxesForBranches($branches);

		foreach ($sandboxes as $sandbox)
		{
			if(file_exists('/home/bitrix/ext_www/' . $sandbox))
			{

			}
		}
	}

	protected function getSandboxesForBranches($branches)
	{
		$branchpart = $this->config->getBranchDomainPart();
		$domain = $this->config->getHostname();

		return array_map(function($branch) use ($branchpart, $domain) { return "$branch.$branchpart.$domain";}, $branches );
	}

	protected function updateMasterRepo()
	{
		$this->master->fetchAll();
	}

	protected function getRemoteBranchesList()
	{
		$b = $this->master->list_remote_branches();

		$b = array_map(function($r) { return str_replace('origin/', '', $r);}, $b);

		return $b;
	}

	protected function createMasterRepo()
	{
		$master = GitRepo::create_new($this->config->getMasterRepoPath(), false, false);
		$master->add_remote('origin', $this->config->getRemote());
		$master->fetchAll();
		$master->pull('origin', $this->config->getMasterBranch());
	}

	protected function isMasterCorrect()
	{
		try
		{
			$master = Git::open($this->config->getMasterRepoPath().'/');

			$s = $master->list_remotes();

			return (in_array('origin', $s));
		} catch(Exception $e)
		{
			return false;
		}
	}
}


$sulfur = new Sulfur();
$sulfur->update();
