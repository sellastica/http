<?php
namespace Core\Domain\Model\Http;

use Nette;

class Url extends Nette\Http\UrlScript
{
	/** @var bool */
	private $external = false;


	/**
	 * @param string|Nette\Http\Url $url
	 * @param bool $external
	 */
	public function __construct($url, $external = false)
	{
		parent::__construct($url);
		$this->external = (bool) $external;
	}

	/**
	 * @return string
	 */
	public function getRootRelativeUrl(): string
	{
		return $this->getPath()
			. (($tmp = $this->getQuery()) ? '?' . $tmp : '')
			. ($this->getFragment() === '' ? '' : '#' . $this->getFragment());
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->external === true ? $this->getAbsoluteUrl() : $this->getRootRelativeUrl();
	}
}