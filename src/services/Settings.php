<?php

namespace rethink\hrouter\services;

use blink\core\Object;

/**
 * Class Settings
 *
 * @package rethink\hrouter\services
 */
class Settings extends Object
{
    /**
     * Gets all available settings.
     *
     * @return array
     */
    public function all()
    {
        $rows = $this->getTable()->get();

        return array_pluck($rows, 'value', 'name');
    }

    /**
     * Checks whether the specified setting exists or not.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return $this->getTable()->where('name', $name)->exists();
    }

    /**
     * Gets a setting by name.
     *
     * @param string $name
     * @param string|null $default
     * @return string
     */
    public function get(string $name, string $default = null)
    {
        $row = $this->getTable()->where('name', $name)->first();

        return $row ? $row->value : $default;
    }

    /**
     * Gets multiple settings in a bulk.
     *
     * @param array $names
     * @return array
     */
    public function getMultiple(array $names)
    {
        $results = [];

        foreach ($names as $name) {
            $results[$name] = $this->get($name);
        }

        return $results;
    }

    /**
     * Sets a setting by name.
     *
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value)
    {
        $this->getTable()->updateOrInsert(['name' => $name], ['value' => $value]);
    }

    /**
     * Sets multiple settings in a bulk.
     *
     * @param array $values
     */
    public function setMultiple(array $values)
    {
        foreach ($values as $name => $value) {
            $this->set($name, $value);
        }
    }

    /**
     * Removes a setting.
     *
     * @param string $name
     */
    public function remove(string $name)
    {
        $this->getTable()->where('name', $name)->delete();
    }

    protected function getTable()
    {
        return capsule_conn()->table('settings');
    }
}
