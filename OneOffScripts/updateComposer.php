<?php

include 'commonbase.php';

class ComposerUpdater
	{

	private string $baseDir = '';

	private string $vendorDir = 'vendor/';

	private array $ignored = [];

	public function setBaseDirectory(string $baseDir) : self
		{
		$this->baseDir = $baseDir;

		return $this;
		}

	public function setVendorDirectory(string $vendorDir) : self
		{
		$this->vendorDir = $vendorDir;

		return $this;
		}

	public function setIgnoredRepos(array $ignored) : self
		{
		$this->ignored = $ignored;

		return $this;
		}

	public static function copyFiles(string $source, string $dest)
		{
		if (! file_exists($dest))
			{
			mkdir($dest, 0755, true);
			}
		$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $item)
			{
			$file = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
			$file = str_replace('/', '\\', $file);
			if ($item->isDir())
				{
				if (! file_exists($file))
					{
					mkdir($file, 755, true);
					}
				}
			else
				{
				$ext = strrchr($file, '.');
				if (in_array($ext, ['.php', '.md']))
					{
					copy($item, $file);
					}
				}
			}
		}

	public function copyPath(string $name, array $sources, string $destDir)
		{
		foreach ($sources as $sourceDir)
			{
			echo $name . ": destDir ->{$destDir}<- sourceDir ->{$sourceDir}<-\n";

			if ($destDir)
				{
				$destDir = str_replace('\\', '/', $this->baseDir.$destDir);
				$destDir = substr($destDir, 0, strlen($destDir) - 1);
				$sourceDir = $this->vendorDir.$name.'/'.$sourceDir;
				//echo "source $sourceDir to $destDir\n";
				$this->copyFiles($sourceDir, $destDir);
				}
			}
		}

	public function update() : void
		{
		$installed = json_decode(file_get_contents($this->vendorDir . '../composer.lock'), true);
		foreach ($installed['packages'] as $install)
			{
			$use = true;
			foreach ($this->ignored as $ignore)
				{
				if (stripos($install['name'], $ignore) !== false)
					{
					$use = false;
					break;
					}
				}

			if (! $use)
				{
				continue;
				}

			if (isset($install['autoload']))
				{
				$autoload = $install['autoload'];
				$destDir = $sourceDir = '';
				if (! empty($autoload['psr-4']))
					{
					foreach ($autoload['psr-4'] as $destDir => $sourceDir)
						{
						if (! $sourceDir)
							{
							$sourceDir = '.';
							}
						if (! is_array($sourceDir))
							{
							$sourceDir = [$sourceDir];
							}
						$this->copyPath($install['name'], $sourceDir, $destDir);
						}
					}
				else if (! empty($autoload['psr-0']))
					{
					foreach ($autoload['psr-0'] as $destDir => $sourceDir)
						{
						if ($sourceDir)
							{
							$destDir = '.\\';
							}
						if (! is_array($sourceDir))
							{
							$sourceDir = [$sourceDir];
							}
						$this->copyPath($install['name'], $sourceDir, $destDir);
						}
					}
				else if (! empty($autoload['classmap']))
					{
					foreach ($autoload['classmap'] as $file)
						{
						copy('vendor/' . $install['name'] . '/' . $file, 'NoNameSpace/' . $file);
						}
					}
				else
					{
					echo "No autoload for {$install['name']}\n";
					continue;
					}
				}
			else
				{
				echo "No autoloader for {$install['name']}\n";
				}
			}
		}
	}

$updater = new ComposerUpdater();

$updater->setIgnoredRepos([
	'components',
	'Composer',
	'doctrine',
	'GPBMetadata',
	'Jean85',
	'OndraM',
	'PackageVersions',
	'phar-io',
	'PHPStan',
	'PhpParser',
	'phpunit',
	'Prophecy',
	'ralouphie',
	'sebastian',
	'Symplify',
	'tecnickcom',
	'theseer',
	'tinify',
	'twig',
	]);

$updater->update();

