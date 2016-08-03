<?php
namespace TriviWars\Entity;

use Doctrine\ORM\EntityManager;

class BaseEntity
{
    protected $toString = null;

    private function formatVar($name)
    {
        return lcfirst($name);
    }

    public function __toString()
    {
        if ($this->toString !== null) {
            return $this->{$this->toString};
        }
        return $this->getResourceId() . ' #' . $this->getId();
    }

    /**
     * @param array $data
     * @param EntityManager $om
     * @param array $repos
     * @return $this
     * @throws \Exception
     */
    public function fill($data, $om = null, $repos = array())
    {
        foreach ($data as $var => $val) {
            if ($var != 'submit' && !is_array($val)) {
                if (isset($repos[$var])) {
                    if (empty($val)) {
                        $val = null;
                    } elseif (is_numeric($val)) {
                        $val = $om->getRepository($repos[$var])->findOneBy(array('id' => intval($val)));
                    }
                } else {
                    if (is_string($val) && preg_match('`\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}`', $val)) {
                        $val = new \DateTime($val);
                    }
                }

                $this->_set($var, $val, true);
            }
        }
        return $this;
    }

    public function has($v)
    {
        $var = $this->formatVar($v);
        $getMethod = 'get' . $var;
        return (method_exists($this, $getMethod) || property_exists($this, $var));
    }

    private function _get($v)
    {
        $var = $this->formatVar($v);
        $getMethod = 'get' . $var;
        if (method_exists($this, $getMethod)) {
            return $this->$getMethod();
        }

        if (property_exists($this, $var)) {
            if (is_object($this->$var) && get_class($this->$var) == 'Doctrine\ORM\PersistentCollection') {
                return $this->$var->getValues();
            } else {
                return $this->$var;
            }
        } else {
            $class_info = new \ReflectionClass($this);
            throw new \Exception('Member `' . $var . '` does not exists (get - ' . $class_info->getFileName() . ').');
        }
    }

    private function _set($v, $val, $continueOnFailure = false)
    {
        $var = $this->formatVar($v);
        $setMethod = 'set' . $var;
        if (method_exists($this, $setMethod)) {
            $this->$setMethod($val);
            return;
        }

        if (property_exists($this, $var)) {
            $this->$var = $val;
        } elseif (!$continueOnFailure) {
            throw new \Exception('Member `' . $var . '` does not exists (set).');
        }
    }

    private function _add($var, $val)
    {
        if (is_object($this->$var) && get_class($this->$var) == 'Doctrine\ORM\PersistentCollection') {
            $this->$var->add($val);
        } elseif (is_array($this->$var)) {
            array_push($this->$var, $val);
        } else {
            throw new \Exception('Can\'t add to this kind of variable');
        }
    }

    public function __call($method, $args)
    {
        $gset = substr($method, 0, 3);
        $var = lcfirst(substr($method, 3));
        $gset2 = substr($method, 0, 2);
        $var2 = lcfirst(substr($method, 2));

        if ($gset == 'get') {
            return $this->_get($var);
        } elseif ($gset == 'set') {
            if (count($args) == 1) {
                $this->_set($var, $args[0]);
                return $this;
            } else {
                throw new \Exception('Need arguments to set a value (`' . implode('`, `', $args) . '`).');
            }
        } elseif ($gset == 'add' && count($args) == 1) {
            if (property_exists($this, $var)) {
                $this->_add($var, $args[0]);
            } elseif (property_exists($this, $var . 's')) {
                $this->_add($var.'s', $args[0]);
            } else {
                throw new \Exception('Member `' . $var . '` does not exists (add).');
            }
        } elseif ($gset2 == 'is') {
            return !empty($this->_get($var2));
        } else {
            throw new \Exception('Method `' . $method . '` does not exists (`' . $gset . '` - ' . count($args) . ').');
        }
    }
}