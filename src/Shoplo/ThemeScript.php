<?php

namespace Shoplo;

class ThemeScript extends Resource
{
    public function retrieve($id = 0, $params = array(), $cache = false)
    {
        if ($id == 0) {
            if (!$cache || !isset($this->bucket['theme_script'])) {
                $params = $this->prepare_params($params);
                $result = empty($params) ? $this->send($this->prefix."theme_scripts") : $this->send(
                    $this->prefix."theme_scripts?".$params
                );
                $this->bucket['theme_script'] = $this->prepare_result($result);
            }

            return $this->bucket['theme_script'];
        } else {
            if (!$cache || !isset($this->bucket['theme_script'][$id])) {
                $result = $this->send($this->prefix."/theme_scripts/".$id);
                $this->bucket['theme_script'][$id] = $this->prepare_result($result);
            }

            return $this->bucket['theme_script'][$id];
        }
    }

    public function count($params = array())
    {
        $params = $this->prepare_params($params);

        return $this->send($this->prefix."theme_scripts/count".(!empty($params) ? '?'.$params : ''));
    }

    public function create($fields)
    {
        $fields = array('theme_script' => $fields);

        return $this->send("theme_scripts", 'POST', $fields);
    }

    public function modify($id, $fields)
    {
        $fields = array('theme_script' => $fields);

        return $this->send($this->prefix."theme_scripts/".$id, 'PUT', $fields);
    }

    public function remove($id, $params = array())
    {
        $params = $this->prepare_params($params);
        $result = empty($params) ? $this->send($this->prefix."theme_scripts/{$id}/", 'DELETE') : $this->send(
            $this->prefix."theme_scripts/{$id}/?".$params,
            'DELETE'
        );

        return $result;
    }
}
