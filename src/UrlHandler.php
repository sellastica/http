<?php
namespace Sellastica\Http;

use Nette;

class UrlHandler
{
	/** @var Nette\Application\IRouter */
	private $router;
	/** @var Nette\Http\UrlScript */
	private $url;
	/** @var Nette\Application\Request|NULL */
	private $appRequest;


	/**
	 * @param Nette\Http\UrlScript $url
	 * @param Nette\Application\IRouter $router
	 */
	public function __construct(
		Nette\Http\UrlScript $url,
		Nette\Application\IRouter $router
	)
	{
		$this->url = $url;
		$this->router = $router;
		$this->appRequest = $this->getAppRequest();
	}

	/**
	 * @return Nette\Http\UrlScript
	 */
	public function getUrl(): Nette\Http\UrlScript
	{
		return $this->url;
	}

	/**
	 * @param string $parameter
	 * @param null $value
	 * @return $this
	 */
	public function removeQueryParameter(string $parameter, $value = null)
	{
		if ($queryParameter = $this->url->getQueryParameter($parameter)) {
			//remove query parameter
			if (!isset($value) || $value === $queryParameter) {
				$this->url->setQueryParameter($parameter, null);
			}
		}

		return $this;
	}

	/**
	 * @param string $parameter
	 * @param null $value
	 * @return $this
	 */
	public function addRouterParameter(string $parameter, $value)
	{
		if (!isset($this->appRequest)) {
			return $this;
		}

		$appRequestParameter = $this->appRequest->getParameter($parameter);
		if (!isset($appRequestParameter)) {
			$appRequestParameter = [];
		}

		if (is_array($appRequestParameter)) {
			if (!in_array($value, $appRequestParameter)) {
				//add new value to the array
				$appRequestParameter[] = $value;
				sort($appRequestParameter);
				$this->setAppRequestParameter($parameter, $appRequestParameter);

				$this->constructUrl();
			}
		} elseif (is_scalar($appRequestParameter)) {
			//set whole parameter with $value
			$this->setRouterParameter($parameter, $value);
		}

		return $this;
	}

	/**
	 * @param string $parameter
	 * @param $value
	 * @return $this
	 */
	public function setRouterParameter(string $parameter, $value)
	{
		if (!isset($this->appRequest)) {
			return $this;
		}

		$this->setAppRequestParameter($parameter, $value);
		$this->constructUrl();

		return $this;
	}

	/**
	 * @param string $parameter
	 * @param null $value
	 * @return $this
	 */
	public function removeRouterParameter(string $parameter, $value = null)
	{
		if (!isset($this->appRequest)) {
			return $this;
		}

		$appRequestParameter = $this->appRequest->getParameter($parameter);
		if (is_array($appRequestParameter) && isset($value)) {
			if (($key = array_search($value, $appRequestParameter)) !== false) {
				unset($appRequestParameter[$key]);
				$this->setAppRequestParameter($parameter, $appRequestParameter);
			}
		} else {
			//remove whole parameter
			$this->setAppRequestParameter($parameter, null);
		}

		$this->constructUrl();

		return $this;
	}

	/**
	 * @param string $parameter
	 * @param $value
	 */
	private function setAppRequestParameter(string $parameter, $value)
	{
		$this->appRequest->setParameters(
			array_merge($this->appRequest->getParameters(), [$parameter => $value])
		);
	}

	private function constructUrl()
	{
		$url = $this->router->constructUrl($this->appRequest, $this->url);
		$this->url = new Nette\Http\UrlScript($url);
	}

	/**
	 * @return Nette\Application\Request|NULL
	 */
	private function getAppRequest()
	{
		$this->url->setScriptPath('/');
		//must clone otherwise every time the same instance is returned!
		return clone $this->router->match(new Nette\Http\Request($this->url));
	}
}


interface IUrlHandlerFactory
{
	/**
	 * @param Nette\Http\UrlScript $url
	 * @return UrlHandler
	 */
	public function create(Nette\Http\UrlScript $url);
}