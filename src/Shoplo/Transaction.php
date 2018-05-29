<?php

namespace Shoplo;

class Transaction extends Resource
{
    public function retrieve($id, $params = array(), $cache = false)
    {
        if (!$cache || !isset($this->bucket['transaction'][$id])) {
            $result = $this->send($this->prefix."/transactions/".$id);
            $this->bucket['transaction'][$id] = $this->prepare_result($result);
        }

        return $this->bucket['transaction'][$id];
    }
}