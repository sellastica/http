<?php
namespace Sellastica\Http;

interface IUrlHandlerFactory
{
	/**
	 * @param \Nette\Http\UrlScript $url
	 * @return UrlHandler
	 */
	public function create(\Nette\Http\UrlScript $url): UrlHandler;
}