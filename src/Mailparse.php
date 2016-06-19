<?php

namespace OOPHP\Mailparse;

use OOPHP\Mailparse\Exception\NonReadableStreamException;

/**
 * Class Parse
 *
 * @package OOPHP\Mailparse
 */
class Mailparse
{
    /**
     * @var resource $resource mailparse handle
     */
    protected $resource;

    /**
     * @var string $text The actual message's text
     */
    protected $text;

    /**
     * if $this->resource is still active, close it
     */
    public function __destruct()
    {
        if (is_resource($this->resource)) {
            mailparse_msg_free($this->resource);
        }
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $text = file_get_contents($path);

        return $this->setText($text);
    }

    /**
     * @param resource $stream
     *
     * @return $this
     * @throws NonReadableStreamException
     */
    public function setStream($stream, bool $closeStream = false)
    {
        $meta = @stream_get_meta_data($stream);
        if (!$meta || !$meta['mode'] || $meta['mode'][0] != 'r' || $meta['eof']) {
            throw new NonReadableStreamException();
        }

        $text = '';
        while (!feof($stream)) {
            $text .= fread($stream, 2082);
        }

        if ($closeStream) {
            fclose($stream);
        }

        return $this->setText($text);
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text)
    {
        $this->resource = mailparse_msg_create();
        $eaten = 0;
        $text_length = strlen($text);
        while ($eaten < $text_length) {
            mailparse_msg_parse($this->resource, substr($text, $eaten, 2082));
            $eaten += 2082;
        }

        $this->text = $text;

        return $this;
    }

    /**
     * @return string Returns the actual message text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed    $filename
     * @param callable $callback [optional]
     *
     * @return string
     */
    public function extractPartFile($filename, callable $callback = null)
    {
        return mailparse_msg_extract_part_file($this->resource, $filename, $callback);
    }

    /**
     * @param string   $msgBody
     * @param callable $callback
     *
     * @return void
     */
    public function extractPart(string $msgBody, callable $callback)
    {
        mailparse_msg_extract_part($this->resource, $msgBody, $callback);
    }

    /**
     * @param string   $filename
     * @param callable $callback
     *
     * @return string
     */
    public function extractWholePartFile(string $filename, callable $callback)
    {
        return mailparse_msg_extract_whole_part_file($this->resource, $filename, $callback);
    }

    /**
     * @param resource|null $part
     *
     * @return array
     */
    public function getPartData($part = null)
    {
        if ($part === null) {
            $part = $this->resource;
        }

        return mailparse_msg_get_part_data($part);
    }

    /**
     * @param $partId
     *
     * @return Mailparse
     */
    public function getPartObject(string $partId): Mailparse
    {
        $object = new static;
        $object->text = &$this->text;
        $object->resource = $this->getPart($partId);

        return $object;
    }

    /**
     * @param string $section
     *
     * @return resource
     */
    public function getPart(string $section)
    {
        return mailparse_msg_get_part($this->resource, $section);
    }

    /**
     * @return array
     */
    public function getStructure()
    {
        return mailparse_msg_get_structure($this->resource);
    }
}
