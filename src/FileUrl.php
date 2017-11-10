<?php
namespace Core\Domain\Model\Http;

class FileUrl extends Url
{
	const TIMESTAMP = 'm';


	/**
	 * @return string
	 */
	public function getUrlFromServerRoot(): string
	{
		return ROOT_DIR . $this->getPath();
	}

	/**
	 * @return string
	 */
	public function getUrlFromDocumentRoot(): string
	{
		return WWW_DIR . $this->getPath();
	}
}