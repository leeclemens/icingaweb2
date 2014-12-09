<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Util;

use DateTime;

class DateTimeRenderer
{
    const FORMAT_DATE = 0;
    const FORMAT_TIME = 1;
    const FORMAT_DATETIME = 2;

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
     * @param int $dateTime     timestamp accepted by DateTime::setTimestamp()
     */
    public function __construct($dateTime)
    {
        $this->now = time();
        $dt = new DateTime();
        $this->dateTime = $dt->setTimestamp($dateTime)->getTimestamp();
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
     * @param array $formatStr      printf-conform strings (absolute/relative) to insert date/time in
     *                              array('abs' => 'at %s', 'rel' => '%s ago')
     *
     * @return string
     */
    protected function render($future = false, $timePoint = false, $formatStr = array())
    {
        $formatStr = $formatStr[$this->absolute ? 'abs' : 'rel'] ?: null;
        if ($this->absolute) {
            if ($timePoint) {
                $grammar = t('at %s', 'time');
            } else {
                $grammar = $future ? t('until %s', 'time') : t('since %s', 'time');
            }
            return sprintf($formatStr ?: $grammar, self::format(
                (
                    date('Y-m-d', $this->now) ===
                    date('Y-m-d', $this->dateTime)
                )
                ? self::FORMAT_TIME
                : self::FORMAT_DATETIME,
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
                $grammar = $future ? t('in %s', 'time') : t('%s ago', 'time');
            } else {
                $grammar = t('for %s', 'time');
            }
            return sprintf($formatStr ?: $grammar, implode(' ', array_reverse($diffParts)));
        }
    }

    /**
     * Render given timestamp as a timepoint in the future
     *
     * @return string
     */
    public function timeUntil()
    {
        return $this->render(true, true);
    }

    /**
     * Render given timestamp as a timepoint in the past
     *
     * @return string
     */
    public function timeAgo()
    {
        return $this->render(false, true);
    }

    /**
     * Render given timestamp as the begin of a timespan in the past
     *
     * @return string
     */
    public function timeSince()
    {
        return $this->render(false, false);
    }

    /**
     * Format the given $timestamp according to the given $format
     *
     * @param int $format           valid values:
     *                              DateTimeRenderer::FORMAT_DATE
     *                              DateTimeRenderer::FORMAT_TIME
     *                              DateTimeRenderer::FORMAT_DATETIME
     * @param int $timestamp
     *
     * @return string
     *
     * @throws UnexpectedValueException     in case of an invalid value for $format
     */
    public static function format(int $format, int $timestamp = time())
    {
        switch ($format) {
            case static::FORMAT_DATE:
                $format = 'Y-m-d';
                break;
            case static::FORMAT_TIME:
                $format = 'H:i:s';
                break;
            case static::FORMAT_DATETIME:
                $format = 'Y-m-d H:i:s';
                break;
            default:
                throw new UnexpectedValueException('Invalid value `' . $format . '\' for $format');
        }
        return date($format, $timestamp);
    }
}
