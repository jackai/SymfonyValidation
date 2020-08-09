<?php

namespace App\Entity;

// TODO 可以加個白名單，在白名單內的才允許這樣呼叫
trait BaseEntityTrait
{
    /**
     * 動態呼叫方法
     *
     * @param $method
     * @param $params
     * @return $this|void
     */
    public function __call($method, $params)
    {
        $var = lcfirst(substr($method, 3));

        if (method_exists($this, $method)) {
            return;
        }

        if (strncasecmp($method, "get", 3) === 0) {
            return $this->$var;
        }

        if (strncasecmp($method, "set", 3) === 0) {
            $this->$var = $params[0];
            return $this;
        }
    }

    /**
     * 轉陣列
     *
     * @return array
     * @throws \ReflectionException
     */
    function toArray()
    {
        $ret = [];
        $reflection = new \ReflectionClass($this);
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        foreach ($vars as $var) {
            $ret[$var->name] = $this->{$var->name};
        }

        return $ret;
    }
}
