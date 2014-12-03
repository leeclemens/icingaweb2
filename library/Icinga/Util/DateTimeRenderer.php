<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Util;

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
     * Difference between given and current timestamp
     *
     * @var int
     */
    protected $diff;

    /**
     * Whether time is absolute
     *
     * @var bool
     */
    protected $absolute;

    /**
     * @param int $dateTime     timestamp accepted by date_timestamp_set()
     */
    public function __construct($dateTime)
    {
        $this->now = time();
        $this->dateTime = date_timestamp_get(
            date_timestamp_set(new DateTime(), $dateTime)
        );
        $this->absolute = 21600 < (
            $this->diff = abs($this->now - $this->dateTime)
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
     * Check whether time is absolute
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->absolute;
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
        if ($this->absolute) {
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
        } else {
            $diffParts = array();
            if ($this->diff < 60) {
                $diffParts['s'] = $this->diff;
            } elseif ($this->diff < 3600) {
                $diffParts['s'] = $this->diff % 60;
                $diffParts['i'] = (int) ($this->diff / 60);
            } else {
                $diffParts['s'] = $this->diff % 60;
                $diff = (int) ($this->diff / 60);
                $diffParts['i'] = $diff % 60;
                $diffParts['h'] = (int) ($diff / 60);
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

    /**
     * Creates a new DateTimeRenderer and return it, rendered
     *
     * @param int $dateTime
     *
     * @return string
     */
    public static function timePointFuture($dateTime)
    {
        return static::create($dateTime)->render(true, true);
    }

    /**
     * Creates a new DateTimeRenderer and return it, rendered
     *
     * @param int $dateTime
     *
     * @return string
     */
    public static function timePointPast($dateTime)
    {
        return static::create($dateTime)->render(false, true);
    }

    /**
     * Creates a new DateTimeRenderer and return it, rendered
     *
     * @param int $dateTime
     *
     * @return string
     */
    public static function timeSpanFuture($dateTime)
    {
        return static::create($dateTime)->render(true, false);
    }

    /**
     * Creates a new DateTimeRenderer and return it, rendered
     *
     * @param int $dateTime
     *
     * @return string
     */
    public static function timeSpanPast($dateTime)
    {
        return static::create($dateTime)->render(false, false);
    }
}
