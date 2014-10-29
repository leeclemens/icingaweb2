<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web\View;

use Icinga\Web\Url;
use Icinga\Util\Format;

$this->addHelperFunction('format', function () {
    return Format::getInstance();
});

$this->addHelperFunction('timeSince', function ($timestamp, $title = '') {
    if (! $timestamp) return '';

    if (! empty($title)) {
        $datetime = date('Y-m-d H:i:s', $timestamp); // TODO: internationalized format
        $title = sprintf($title, $datetime);
        return sprintf(
            '<span class="timesince" title="%s">%s</span>',
            $title,
            Format::timeSince($timestamp)
        );
    }

    return sprintf(
        '<span class="timesince">%s</span>',
        Format::timeSince($timestamp)
    );
});

$this->addHelperFunction('prefixedTimeSince', function ($timestamp, $ucfirst = false, $title = '') {
    if (! $timestamp) return '';

    if (! empty($title)) {
        $datetime = date('Y-m-d H:i:s', $timestamp); // TODO: internationalized format
        $title = sprintf($title, $datetime);
        return sprintf(
            '<span class="timesince" title="%s">%s</span>',
            $title,
            Format::prefixedTimeSince($timestamp, $ucfirst)
        );
    }

    return sprintf(
        '<span class="timesince">%s</span>',
        Format::prefixedTimeSince($timestamp, $ucfirst)
    );
});

$this->addHelperFunction('timeUntil', function ($timestamp, $title = '') {
    if (! $timestamp) return '';

    if (! empty($title)) {
        $datetime = date('Y-m-d H:i:s', $timestamp); // TODO: internationalized format
        $title = sprintf($title, $datetime);
        return sprintf(
            '<span class="timeuntil" title="%s">%s</span>',
            $title,
            Format::timeUntil($timestamp)
        );
    }

    return sprintf(
        '<span class="timeuntil">%s</span>',
        Format::timeUntil($timestamp)
    );
});

$this->addHelperFunction('prefixedTimeUntil', function ($timestamp, $ucfirst = false, $title = '') {
    if (! $timestamp) return '';

    if (! empty($title)) {
        $datetime = date('Y-m-d H:i:s', $timestamp); // TODO: internationalized format
        $title = sprintf($title, $datetime);
        return sprintf(
            '<span class="timeuntil" title="%s">%s</span>',
            $title,
            Format::prefixedTimeUntil($timestamp, $ucfirst)
        );
    }

    return sprintf(
        '<span class="timeuntil">%s</span>',
        Format::prefixedTimeUntil($timestamp, $ucfirst)
    );
});

$this->addHelperFunction('dateTimeRenderer', function ($dateTimeOrTimestamp, $future = false) {
    return DateTimeRenderer::create($dateTimeOrTimestamp, $future);
});
