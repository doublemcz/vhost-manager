<?php

namespace App\Core;

use App\Application\Configuration;
use gitstream\parser\nginx\Block;
use gitstream\parser\nginx\Parser;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class DomainController
{

	/** @var Configuration */
	private $configuration;

	/**
	 * @param Configuration $configuration
	 */
	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * @return array
	 */
	public function getList()
	{
		if (empty($this->configuration['vhostDir'])) {
			return [];
		}

		$sitesEnabled = $this->configuration['vhostDir'] . '/sites-enabled';
		if (!is_dir($sitesEnabled)) {
			echo "[ERROR] Dir '$sitesEnabled'' does not exist\n";
			exit;
		}

		$result = [];
		/** @var \SplFileInfo $path */
		foreach (Finder::findFiles('*.conf')->from($sitesEnabled) as $path) {
			$this->getDomainInfo($result, (string)$path);
		}

		return $result;
	}

	/**
	 * @param $result
	 * @param string $absolutePath
	 * @return array
	 */
	private function getDomainInfo(&$result, $absolutePath)
	{
		$parser = new Parser();
		$root = $parser->load($absolutePath);
		$upstreams = $this->parseUpstreams($root);

		foreach ($root->children as $server) {
			if ($server->name !== 'server') {
				continue;
			}

			$row = [
				'file' => pathinfo($absolutePath, PATHINFO_BASENAME),
				'ssl' => '',
				'proxy_pass' => '',
			];

			$defaultRoot = "";
			foreach ($server->children as $property) {
				if ($property->name === 'root') {
					$defaultRoot = $property->value;
				}
			}

			foreach ($server->children as $property) {
				switch ($property->name) {
					case 'location' :
						if ($property->value === '/') {
							$proxyPass = "";
							$root = "";
							foreach ($property->children as $locationProperty) {
								if ($locationProperty->name === 'proxy_pass') {
									$proxyPass = $locationProperty->value;
								}

								if ($locationProperty->name === 'root') {
									$root = $locationProperty->value;
								}
							}

							$row['root'] = $root ?: $defaultRoot;
							$row['proxy_pass'] = $this->replaceUpstream($proxyPass, $upstreams);
						}

						break;
					case 'listen':
						$ports = array_filter((array)$property->value, function($value) {
							return preg_match('/[0-9]+/', $value);
						});

						$row['listen'] = implode(', ', $ports);

						break;
					default:
						$row[$property->name] = $property->value;

						break;

				}
			}

			// Compose key due to ksort
			$key = $row['server_name'] . $row['listen'] . $row['file'] . count($result);
			$result[$key] = $row;
		}

		ksort($result);

		return $result;
	}

	/**
	 * @param Block $root
	 * @return array
	 */
	private function parseUpstreams(Block $root)
	{
		$result = [];

		foreach ($root->children as $upstream) {
			if ($upstream->name !== 'upstream') {
				continue;
			}

			$servers = [];
			foreach ($upstream->children as $upstreamServer) {
				if ($upstreamServer->name !== 'server') {
					continue;
				}

				$servers[] = $upstreamServer->value;
			}

			$result[$upstream->value] = implode(', ', $servers);
		}

		return $result;
	}

	/**
	 * @param string $proxyPass
	 * @param array $upstreams
	 * @return string
	 */
	private function replaceUpstream($proxyPass, $upstreams = [])
	{
		if (empty($upstreams)) {
			return $proxyPass;
		}

		$protocol = Strings::before($proxyPass, '://');
		$proxyPassClean = str_replace('https://', '', $proxyPass);
		$proxyPassClean = str_replace('http://', '', $proxyPassClean);
		if (array_key_exists($proxyPassClean, $upstreams)) {
			return $protocol . '://' . $upstreams[$proxyPassClean];
		}

		return $proxyPass;
	}

}