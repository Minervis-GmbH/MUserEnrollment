<?php

namespace srag\ActiveRecordConfig\SrUserEnrolment\Config;

use ilDateTime;
use ilDateTimeException;
use LogicException;
use srag\DIC\SrUserEnrolment\DICTrait;

/**
 * Class Repository
 *
 * @package srag\ActiveRecordConfig\SrUserEnrolment\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var string
     */
    protected $table_name = "";
    /**
     * @var array
     */
    protected $fields;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        self::dic()->database()->dropTable(Config::getTableName(), false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getField(string $name)
    {
        if (isset($this->getFields_()[$name])) {
            $field = $this->getFields_()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            $default_value = $field[1];

            switch ($type) {
                case Config::TYPE_STRING:
                    return $this->getStringValue($name, $default_value);

                case Config::TYPE_INTEGER:
                    return $this->getIntegerValue($name, $default_value);

                case Config::TYPE_DOUBLE:
                    return $this->getFloatValue($name, $default_value);

                case Config::TYPE_BOOLEAN:
                    return $this->getBooleanValue($name, $default_value);

                case Config::TYPE_TIMESTAMP:
                    return $this->getTimestampValue($name, $default_value);

                case Config::TYPE_DATETIME:
                    return $this->getDateTimeValue($name, $default_value);

                case Config::TYPE_JSON:
                    $assoc = boolval($field[2]);

                    return $this->getJsonValue($name, $assoc, $default_value);

                default:
                    throw new LogicException("Invalid type $type!");
                    break;
            }
        }

        throw new LogicException("Invalid field $name!");
    }


    /**
     * @return array
     */
    public function getFields() : array
    {
        $values = [];

        foreach ($this->getFields_() as $name) {
            $values[$name] = $this->getField($name);
        }

        return $values;
    }


    /**
     * @return array
     */
    protected function getFields_() : array
    {
        if (empty($this->fields)) {
            throw new LogicException("fields is empty - please call withFields earlier!");
        }

        return $this->fields;
    }


    /**
     * @return string
     */
    public function getTableName() : string
    {
        if (empty($this->table_name)) {
            throw new LogicException("table name is empty - please call withTableName earlier!");
        }

        return $this->table_name;
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        Config::updateDB();
    }


    /**
     * @param string $name Name
     */
    public function removeField(string $name)/*: void*/
    {
        $config = $this->getConfig($name, false);

        $this->deleteConfig($config);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setField(string $name, $value)/*: void*/
    {
        if (isset($this->getFields_()[$name])) {
            $field = $this->getFields_()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            switch ($type) {
                case Config::TYPE_STRING:
                    $this->setStringValue($name, $value);

                    return;

                case Config::TYPE_INTEGER:
                    $this->setIntegerValue($name, $value);

                    return;

                case Config::TYPE_DOUBLE:
                    $this->setFloatValue($name, $value);

                    return;

                case Config::TYPE_BOOLEAN:
                    $this->setBooleanValue($name, $value);

                    return;

                case Config::TYPE_TIMESTAMP:
                    $this->setTimestampValue($name, $value);

                    return;

                case Config::TYPE_DATETIME:
                    $this->setDateTimeValue($name, $value);

                    return;

                case Config::TYPE_JSON:
                    $this->setJsonValue($name, $value);

                    return;

                default:
                    throw new LogicException("Invalid type $type!");
                    break;
            }
        }

        throw new LogicException("Invalid field $name!");
    }


    /**
     * @param array $fields
     * @param bool  $remove_exists
     */
    public function setFields(array $fields, bool $remove_exists = false)/*: void*/
    {
        if ($remove_exists) {
            Config::truncateDB();
        }

        foreach ($fields as $name => $value) {
            $this->setField($name, $value);
        }
    }


    /**
     * @param array $fields
     *
     * @return self
     */
    public function withFields(array $fields) : self
    {
        $this->fields = $fields;

        return $this;
    }


    /**
     * @param string $table_name
     *
     * @return self
     */
    public function withTableName(string $table_name) : self
    {
        $this->table_name = $table_name;

        return $this;
    }


    /**
     * @param Config $config
     */
    protected function deleteConfig(Config $config)/*:void*/
    {
        $config->delete();
    }


    /**
     * @param Config $config
     */
    protected function storeConfig(Config $config)/*:void*/
    {
        $config->store();
    }


    /**
     * @param string $name
     * @param bool   $store_new
     *
     * @return Config
     */
    protected function getConfig(string $name, bool $store_new = true) : Config
    {
        /**
         * @var Config $config
         */

        $config = Config::where([
            "name" => $name
        ])->first();

        if ($config === null) {
            $config = $this->factory()->newInstance();

            $config->setName($name);

            if ($store_new) {
                $this->storeConfig($config);
            }
        }

        return $config;
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return mixed
     */
    protected function getXValue(string $name, $default_value = null)
    {
        $config = $this->getConfig($name);

        $value = $config->getValue();

        if ($value === null) {
            $value = $default_value;
        }

        return $value;
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setXValue(string $name, $value)/*: void*/
    {
        $config = $this->getConfig($name, false);

        $config->setValue($value);

        $this->storeConfig($config);
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return string
     */
    protected function getStringValue(string $name, $default_value = "") : string
    {
        return strval($this->getXValue($name, $default_value));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setStringValue(string $name, $value)/*: void*/
    {
        $this->setXValue($name, strval($value));
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return int
     */
    protected function getIntegerValue(string $name, $default_value = 0) : int
    {
        return intval($this->getXValue($name, $default_value));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setIntegerValue(string $name, $value)/*: void*/
    {
        $this->setXValue($name, intval($value));
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return float
     */
    protected function getFloatValue(string $name, $default_value = 0.0) : float
    {
        return floatval($this->getXValue($name, $default_value));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setFloatValue(string $name, $value)/*: void*/
    {
        $this->setXValue($name, floatval($value));
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return bool
     */
    protected function getBooleanValue(string $name, $default_value = false) : bool
    {
        return boolval(filter_var($this->getXValue($name, $default_value), FILTER_VALIDATE_BOOLEAN));
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setBooleanValue(string $name, $value)/*: void*/
    {
        $this->setXValue($name, json_encode(boolval(filter_var($value, FILTER_VALIDATE_BOOLEAN))));
    }


    /**
     * @param string $name
     * @param int    $default_value
     *
     * @return int
     */
    protected function getTimestampValue(string $name, int $default_value = 0) : int
    {
        $value = $this->getDateTimeValue($name);

        if ($value !== null) {
            return $value->getUnixTime();
        } else {
            return $default_value;
        }
    }


    /**
     * @param string $name
     * @param int    $value
     */
    protected function setTimestampValue(string $name, int $value)/*: void*/
    {
        if ($value !== null) {
            try {
                $this->setDateTimeValue($name, new ilDateTime(IL_CAL_UNIX, $value));
            } catch (ilDateTimeException $ex) {
            }
        } else {
            // Fix `@null`
            $this->setNullValue($name);
        }
    }


    /**
     * @param string          $name
     * @param ilDateTime|null $default_value
     *
     * @return ilDateTime|null
     */
    protected function getDateTimeValue(string $name, /*?*/ ilDateTime $default_value = null)/*:?ilDateTime*/
    {
        $value = $this->getXValue($name);

        if ($value !== null) {
            try {
                $value = new ilDateTime(IL_CAL_DATETIME, $value);
            } catch (ilDateTimeException $ex) {
                $value = $default_value;
            }
        } else {
            $value = $default_value;
        }

        return $value;
    }


    /**
     * @param string          $name
     * @param ilDateTime|null $value
     */
    protected function setDateTimeValue(string $name, /*?*/ ilDateTime $value = null)/*: void*/
    {
        if ($value !== null) {
            $this->setXValue($name, $value->get(IL_CAL_DATETIME));
        } else {
            $this->setNullValue($name);
        }
    }


    /**
     * @param string $name
     * @param bool   $assoc
     * @param mixed  $default_value
     *
     * @return mixed
     */
    protected function getJsonValue(string $name, bool $assoc = false, $default_value = null)
    {
        return json_decode($this->getXValue($name, json_encode($default_value)), $assoc);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setJsonValue(string $name, $value)/*: void*/
    {
        $this->setXValue($name, json_encode($value));
    }


    /**
     * @param string $name
     *
     * @return bool
     */
    protected function isNullValue(string $name) : bool
    {
        return ($this->getXValue($name) === null);
    }


    /**
     * @param string $name
     */
    protected function setNullValue(string $name)/*: void*/
    {
        $this->setXValue($name, null);
    }
}
