<?php

namespace App\Tools;

class CSVWriter
	{
	private string $delimiter = ',';

	private bool $download = true;

	private bool $headerRow = false;

	/**
	 * @var closed-resource|false|resource
	 */
	private $out;

	public function __construct(string $filename, string $delimiter = ',', bool $download = true)
		{
		$this->delimiter = $delimiter;
		$this->download = $download;

		if ($download)
			{
			\header('Content-Type: application/csv');
			\header('Content-Disposition: inline; filename="' . $filename . '"');
			\header('Cache-Control: private, max-age=0, must-revalidate');
			\header('Pragma: public');
			$this->out = \fopen('php://output', 'w');
			}
		else
			{
			$this->out = \fopen($filename, 'w');
			}
		}

	public function __destruct()
		{
		\fclose($this->out);

		if ($this->download)
			{
			exit;
			}
		}

	public function addHeaderRow(bool $headerRow = true) : static
		{
		$this->headerRow = true;

		return $this;
		}

	public function outputRow(array $row) : static
		{
		if ($this->headerRow)
			{
			$this->headerRow = false;
			\fputcsv($this->out, \array_keys($row), $this->delimiter);
			}

		\fputcsv($this->out, $row, $this->delimiter);

		return $this;
		}
	}
