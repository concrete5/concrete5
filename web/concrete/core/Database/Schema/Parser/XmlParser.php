<?php

namespace Concrete\Core\Database\Schema\Parser;

abstract class XmlParser {

	protected $rawXML;

	public function __construct(\SimpleXMLElement $xml) {
		$this->rawXML = $xml;
	}

	abstract public function parse(\Concrete\Core\Database\Connection $db);

}