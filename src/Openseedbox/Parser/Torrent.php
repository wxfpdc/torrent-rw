<?php

namespace Openseedbox\Parser;
use Bhutanio\BEncode\BEncode;

class Torrent implements TorrentInterface, TorrentParserInterface {

	private $parsed;
	private $encoder;
	private $data;

	/**
	 * @inheritdoc
	 */
	public function parse($data) {
		$this->encoder = new BEncode();
		$this->parsed = $this->encoder->bdecode($data);		
		$this->data = $data;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getInfoHash() {
		return sha1($this->encoder->bencode($this->parsed["info"]));
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return $this->parsed["info"]["name"];
	}

	/**
	 * @inheritdoc
	 */
	public function isFromMagnet() {
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function getBase64Metadata() {
		return base64_encode($this->data);
	}

	/**
	 * @inheritdoc
	 */
	public function getTotalSizeBytes() {
		return $this->parsed["info"]["length"];
	}

	/**
	 * @inheritdoc
	 */
	public function getTrackerUrls() {
		$urls = array();
		foreach ($this->parsed["announce-list"] as $trackers) {
			foreach ($trackers as $tracker) {
				$urls[] = $tracker;
			}
		}
		return $urls;
	}

	/**
	 * @inheritdoc
	 */
	public function getMagnetUri() {
		$magnet = new Magnet();
		return $magnet->create($this->getInfoHash(), $this->getName(), $this->getTrackerUrls());
	}
}