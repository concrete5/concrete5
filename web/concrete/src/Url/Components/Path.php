<?php
namespace Concrete\Core\Url\Components;

/**
 * c5 specific path component for league/url
 */
class Path extends \League\Url\Components\Path
{

    protected $trail = false;

    /**
     * @param \League\Url\Components\Path $old_path
     * @param bool                        $trailing_slash
     */
    public function __construct($data, $trailing_slash = false) {
        $this->set($data);
        $this->trail = !!$trailing_slash;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $res = array();
        foreach (array_values($this->data) as $value) {
            $res[] = rawurlencode($value);
        }
        if (!$res) {
            return null;
        }

        return implode($this->delimiter, $res) . (!!$this->trail ? '/' : '');
    }

    public function withoutDispatcher()
    {
        $path = clone $this;
        if ($path[0] == DISPATCHER_FILENAME) {
            $path->offsetUnset(0);
        }

        return $path;
    }

}
