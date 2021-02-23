<?php

ini_set('memory_limit', -1);

include 'commonbase.php';

class PettyParser
	{
	private \App\Tools\CSVWriter $songWriter;
	private \App\Tools\CSVWriter $showWriter;
	private array $badParts = ['Click here', '(You must be', 'https://www', 'Buried Treasure show'];
	private array $months = [];

	public function __construct()
		{
		$this->songWriter = new \App\Tools\CSVWriter('TomPettyShowSongs.csv', ',', false);
		$this->songWriter->outputRow(['showId', 'artist', 'title', 'album']);
		$this->showWriter = new \App\Tools\CSVWriter('TomPettyShows.csv', ',', false);
		$this->showWriter->addHeaderRow();
		for ($i = 1; $i <= 12; ++$i)
			{
			$this->months[strtoupper(date('F', strtotime("2020-{$i}-28")))] = $i;
			$this->months[strtoupper(date('M', strtotime("2020-{$i}-28")))] = $i;
			}
		}

	public function run(string $fileName) : void
		{
		$dom = new \PHPHtmlParser\Dom();
		$dom->loadStr(file_get_contents($fileName));
		foreach ($dom->find('div') as $node)
			{
			$class = $node->getAttribute('class');
			if ($class == 'songTitle')
				{
				$parts = explode('#', $node->text);
				$showId = (int)array_pop($parts);
				}
			else if ($class == 'lyricsDisplay')
				{
				$song = [];
				foreach ($this->getTextFromNode($node) as $text)
					{
					$text = trim(html_entity_decode($text));

					if ($this->badText($text))
						{
						if (stripos($text, 'Buried Treasure Show') !== false)
							{
							$show = $this->parseShow($text);
							if (! empty($show['showId']))
								{
								$this->showWriter->outputRow($show);
								}
							}
						continue;
						}
					if (strlen($text) > 2)
						{
						$song[] = $text;
						}
					else if (count($song) > 2)
						{
						$album = array_pop($song);
						$artist = array_shift($song);
						$this->songWriter->outputRow([$showId, $artist, implode(', ', $song), $album]);
						$song = [];
						}
					}
				}
			}
		}

	private function getTextFromNode($p, array $previous = []) : array
		{
		if ($p->isTextNode())
			{
			$parts = explode('<br />', $p->text);
			foreach ($parts as $part)
				{
				$previous[] = trim(html_entity_decode($part));
				}
			}
		elseif ($p->hasChildren())
			{
			foreach ($p->getChildren() as $node)
				{
				$previous = $this->getTextFromNode($node, $previous);
				}
			}

		return $previous;
		}

	private function badText(string $text) : bool
		{
		foreach ($this->badParts as $search)
			{
			if (stripos($text, $search) !== false)
				{
				return true;
				}
			}

		return false;
		}

	private function parseShow(string $text) : array
		{
		$text = html_entity_decode(preg_replace('/[^\x20-\x7E]/',' ', $text));
		$text = str_replace(['(', ')', ',', '-', '.', '/', 'Thursday'], ' ', $text);
		$text = str_replace('  ', ' ', trim($text));
		$text = str_replace('  ', ' ', $text);
		$text = str_replace('  ', ' ', $text);
		$fields = [
			'showId' => 0,
			'season' => 0,
			'airDate' => '',
			'repeat' => 0,
			'episode' => 0,
			];

		$text = strtoupper($text);
		$words = explode(' ', $text);
		$count = count($words);
		for ($i = 0; $i < $count; ++$i)
			{
			switch ($words[$i])
				{
				case 'SHOW':
					{
					$show = (int)trim($words[$i + 1], '#');
					if (empty($fields['showId']))
						{
						$fields['showId'] = $show;
						}
					else
						{
						$fields['episode'] = $show;
						}
					break;
					}
				case 'SEASON':
					{
					$fields['season'] = (int)trim($words[$i + 1], '#');
					break;
					}
				case 'REPEAT':
					{
					$fields['repeat'] = 1;
					break;
					}
				case 'AIRING':
				case 'AIRS':
				case 'BROADCAST':
					{
					if (isset($words[$i + 1]))
						{
						$month = $words[$i + 1];
						$day = (int)$words[$i + 2];
						$year = $words[$count - 1];
						$fields['airDate'] = $year . '-' . $this->months[$month] . '-' . $day;
						}
					break;
					}
				}
			}

		return $fields;
		}
	}

$parser = new PettyParser();
$parser->run('tompettyFull.html');

