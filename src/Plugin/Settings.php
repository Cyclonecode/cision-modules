<?php

namespace CisionModules\Plugin;

class Settings
{

    /**
     * Name of configuration option.
     *
     * @var string
     */
    protected $optionName = '';

    /**
     * An array of settings.
     *
     * @var array $settings
     */
    protected $settings = [];

    /**
     * Version
     *
     * @var string
     */
    public $version = '1.0.3';

    /**
     * Settings constructor.
     *
     * @param string $optionName
     */
    public function __construct(string $optionName)
    {
        $this->optionName = $optionName;
        $this->load();
    }

    /**
     * @return array
     */
    public function toOptionsArray(): array
    {
        return $this->settings;
    }

    /**
     * @return null|string
     */
    public function toJSON(): ?string
    {
        return function_exists('json_encode') ? json_encode($this->toOptionsArray(), JSON_PRETTY_PRINT) : '';
    }

    /**
     * @return null|string
     */
    public function toYaml(): ?string
    {
        return function_exists('yaml_emit') ? yaml_emit($this->settings) : '';
    }

    /**
     * Returns the name of this option.
     *
     * @return string
     */
    public function getOptionName(): string
    {
        return $this->optionName;
    }

    /**
     * Returns the current version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Delete this setting from database.
     */
    public function delete(): void
    {
        delete_option($this->optionName);
        $this->settings = [];
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     *
     * @param mixed $value
     *   The value to set.
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Sets a configuration value.
     *
     * @param string $name
     *   Name of option to set.
     * @param mixed $value
     *   The value to set.
     */
    public function set(string $name, $value): void
    {
        $this->settings[$name] = $value;
    }

    /**
     * Sets configuration from array.
     *
     * @param array $settings
     */
    public function setFromArray(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Add a setting.
     *
     * @param string $name
     *   Name of setting to add.
     * @param mixed $value
     *   Value to add.
     */
    public function add(string $name, $value): void
    {
        if (!isset($this->settings[$name])) {
            $this->set($name, $value);
        }
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *   Name of option to get.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *   Name of option to get.
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return (isset($this->settings[$name]) ? $this->settings[$name] : null);
    }

    /**
     * Get a configuration value from array.
     *
     * @param string $name
     * @param int $index
     * @return mixed
     */
    public function getFromArray(string $name, int $index)
    {
        $value = $this->get($name);
        if (is_array($value) && count($value) >= $index) {
            return $value[$index];
        }
    }

    /**
     * Remove setting.
     *
     * @param string $name
     *   Name of setting to remove.
     */
    public function remove(string $name)
    {
        unset($this->settings[$name]);
    }

    /**
     * Rename setting.
     *
     * @param string $from
     *   Name of setting.
     * @param string $to
     *   New name for setting.
     */
    public function rename(string $from, string $to): void
    {
        if (isset($this->settings[$from])) {
            $this->settings[$to] = $this->settings[$from];
            $this->remove($from);
        }
    }

    /**
     * Load settings from database.
     */
    public function load(): void
    {
        $this->settings = get_option($this->optionName);
    }

    /**
     * Save setting to database.
     *
     * @return bool
     */
    public function save(): bool
    {
        ksort($this->settings);
        return update_option($this->optionName, $this->settings);
    }

    /**
     * Removes any settings that is not defined in $options.
     *
     * @param array $options
     *   An array which keys will be used to validate the current settings keys.
     */
    public function clean(array $options): void
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (!in_array($key, $this->settings)) {
                    unset($this->settings[$key]);
                }
            }
        }
    }
}
