<?php

namespace MikeKBurke\ExperianAutocheck;

/**
 * Class ExperianAutocheckEntity
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/experian-autocheck
 */
class ExperianAutocheckEntity extends \ArrayObject implements \JsonSerializable
{
    public $CapAverage;
    public $Vrm;
    public $Vin;
    public $Make;
    public $Model;
    public $Colour;
    public $DateFirstRegistered;
    public $provider;
    public $created;

    /**
     * Constructor.
     *
     * @param array|null|object $inject Create
     */
    public function __construct($inject)
    {
        if ($inject) {
            if ($inject instanceof \stdClass) {
                $inject = get_object_vars($inject);
            }

            if (is_array($inject)) {
                foreach ($inject as $k => $v) {
                    if (property_exists($this, $k)) {
                        $this->$k = $v;
                    }
                }
            }
        }
    }

    /**
     * JSON array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            "CapAverage"            => $this->CapAverage,
            'Vrm'                   => $this->Vrm,
            'Vin'                   => $this->Vin,
            "Make"                  => $this->Make,
            "Model"                 => $this->Model,
            "Colour"                => $this->Colour,
            "DateFirstRegistered"   => $this->DateFirstRegistered,
            "provider"              => $this->provider,
            "created"               => $this->created
        ];
    }
}
