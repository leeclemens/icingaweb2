<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web\View;

use DateTime;

class DateTimeRenderer
{
    /**
     * The current timestamp
     *
     * @var int
     */
    protected $now;

    /**
     * The given timestamp
     *
     * @var int
     */
    protected $dateTime;

    /**
     * @param int $dateTime     timestamp accepted by date_timestamp_set()
     */
    public function __construct($dateTime)
    {
        $this->now = time();
        $this->dateTime = date_timestamp_get(
            date_timestamp_set(new DateTime(), $dateTime)
        );
    }

    /**
     * Creates a new DateTimeRenderer
     *
     * @param int $dateTime
     *
     * @return DateTimeRenderer
     */
    public static function create($dateTime)
    {
        return new static($dateTime);
    }

    /**
     * Render given timestamp (or relative timespan)
     *
     * @param bool $future          Future or past?
     * @param bool $timePoint       Did an event (just) happen once
     *                              or is a state ongoing?
     *
     * @return string
     */
    public function render($future = false, $timePoint = false)
    {
        $diff = abs($this->now - $this->dateTime);
        $diffParts = array();
        if ($diff < 60) {
            $diffParts['s'] = $diff;
        } elseif ($diff < 3600) {
            $diffParts['s'] = $diff % 60;
            $diffParts['i'] = (int) ($diff / 60);
        } elseif ($diff < 21600) {
            $diffParts['s'] = $diff % 60;
            $diff = (int) ($diff / 60);
            $diffParts['i'] = $diff % 60;
            $diffParts['h'] = (int) ($diff / 60);
        } else {
            if ($timePoint) {
                $grammar = 'at %s';
            } else {
                $grammar = $future ? 'until %s' : 'since %s';
            }
            return sprintf($grammar, date(
                (
                    date('Y-m-d', $this->now) ===
                    date('Y-m-d', $this->dateTime)
                ) ? 'H:i:s' : 'Y-m-d H:i:s',
                $this->dateTime
            ));
        }
        foreach ($diffParts as $key => $value) {
            if (0 === $value) {
                unset($diffParts[$key]);
            }
        }
        if (0 === count($diffParts)) {
            $diffParts['s'] = 0;
        }
        $formats = array(
            's' => '%ss',
            'i' => '%sm',
            'h' => '%sh'
        );
        foreach ($diffParts as $key => $value) {
            $diffParts[$key] = sprintf($formats[$key], $value);
        }
        if ($timePoint) {
            $grammar = $future ? 'in %s' : '%s ago';
        } else {
            $grammar = 'for %s';
        }
        return sprintf($grammar, implode(' ', array_reverse($diffParts)));
    }
}
