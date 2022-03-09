<?php

declare(strict_types=1);

namespace Kernel\Helpers;

class Collection implements \ArrayAccess
{
    protected $data;

    public function __construct($data)
    {
        if ($data instanceof Collection) {
            $this->data = $data->get();
        } else {
            $this->data = $data;
        }
    }

    public function has($key): bool
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return false;
                }
            }
            return true;
        }
        return isset($this->data[$key]);
    }

    public function get($key = null, $default = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return null;
                }
            }
            return $id;
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    public function set($key, $value): void
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return;
                }
            }
            $id = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    public function first(): ?mixed
    {
        if (isset($this->data[0])) {
            return $this->data[0];
        }
        return null;
    }

    public function filter($call): mixed
    {
        $result = [];
        foreach ($this->data as $k => $v) {
            if ($call($v, $k)) {
                $result[] = $v;
            }
        }
        return $result;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}
