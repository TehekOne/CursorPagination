<?php

namespace TehekOne\CursorPagination\Pagination;

/**
 * Class Cursor
 *
 * @package TehekOne\CursorPagination\Pagination
 */
class Cursor
{
    /**
     * @var null
     */
    protected $after;

    /**
     * @var null
     */
    protected $before;

    /**
     * Cursor constructor.
     *
     * @param null $before
     * @param null $after
     */
    public function __construct($before = null, $after = null)
    {
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * @return null
     */
    public function afterCursor()
    {
        return $this->decode($this->after);
    }

    /**
     * @param string $data
     *
     * @return bool|string
     */
    public function decode(string $data)
    {
        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    /**
     * @return null
     */
    public function beforeCursor()
    {
        return $this->decode($this->before);
    }

    /**
     * @return bool
     */
    public function isAfter(): bool
    {
        return $this->after !== null;
    }

    /**
     * @return bool
     */
    public function isBefore(): bool
    {
        return $this->before !== null;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function encode(string $data)
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');

        return rtrim($encoded, '=');
    }
}
