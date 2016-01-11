<?php

/**
 * (c) Bit24, http://studiobit.ru
 * User: Pavel Mashanov
 * Date: 11.01.16 17:24
 */
class Configuration {

	protected $raw;

	public function __construct($raw)
	{
		$this->raw = $raw;

		if(is_array($raw))
		{
			$this->raw = array_merge(static::getDefaults(), $raw);
		}
	}

	protected static function getDefaults()
	{
		return array(
			'master_repo_path' => '/var/opt/sulfur/repo',
			'master_branch' => 'master',
			'remote_repo' => null
		);
	}

	/**
	 * @param string $file
	 *
	 * @return static
	 */
	public static function loadFromFile($file)
	{
		$config = array();

		if(file_exists($file))
		{
			$config = parse_ini_file($file);

			if($config === FALSE)
			{
				die("Wrong config\n");
			}
		}
		else
		{
			die("No config found at $file\n");
		}

		return new Configuration($config);
	}

	/**
	 * @return string
	 */
	public function getMasterRepoPath()
	{
		return $this->raw['master_repo_path'];
	}

	/**
	 * @return string
	 */
	public function getMasterBranch()
	{
		return $this->raw['master_branch'];
	}

	/**
	 * @return string
	 */
	public function getRemote()
	{
		return $this->raw['remote_repo'];
	}

	/**
	 * @return string
	 */
	public function getBranchDomainPart()
	{
		return $this->raw['domain_part_for_branch'];
	}

	/**
	 * @return string
	 */
	public function getHostname()
	{
		return $this->raw['hostname'];
	}
}
