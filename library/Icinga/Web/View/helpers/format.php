<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web\View;

use Icinga\Web\Url;
use Icinga\Util\Format;

$this->addHelperFunction('format', function () {
    return Format::getInstance();
});

/* TODO:
 * drop the following 4 helpers
 * after replacing all their usages w/ dateTimeRenderer
 */

$this->addHelperFunction('timeSince', function ($timestamp) {
    return sprintf(
        '<span class="timesince" title="%s">%s</span>',
        date('Y-m-d H:i:s', $timestamp), // TODO: internationalized format
        Format::timeSince($timestamp)
    );
});

$this->addHelperFunction('prefixedTimeSince', function ($timestamp, $ucfirst = false) {
    return sprintf(
        '<span class="timesince" title="%s">%s</span>',
        date('Y-m-d H:i:s', $timestamp), // TODO: internationalized format
        Format::prefixedTimeSince($timestamp, $ucfirst)
    );
});

$this->addHelperFunction('timeUntil', function ($timestamp) {
    if (! $timestamp) return '';
    return sprintf(
        '<span class="timeuntil" title="%s">%s</span>',
        date('Y-m-d H:i:s', $timestamp), // TODO: internationalized format
        Format::timeUntil($timestamp)
    );
});

$this->addHelperFunction('prefixedTimeUntil', function ($timestamp, $ucfirst = false) {
    if (! $timestamp) return '';
    return sprintf(
        '<span class="timeuntil" title="%s">%s</span>',
        date('Y-m-d H:i:s', $timestamp), // TODO: internationalized format
        Format::prefixedTimeUntil($timestamp, $ucfirst)
    );
});

$this->addHelperFunction('dateTimeRenderer', function ($dateTimeOrTimestamp, $future = false) {
    return DateTimeRenderer::create($dateTimeOrTimestamp, $future);
});

$this->addHelperFunction('timePointFuture', function ($timestamp) {
    return sprintf(
        '<span class="timeuntil">%s</span>',
        DateTimeRenderer::timePointFuture($timestamp)
    );
});

$this->addHelperFunction('timePointPast', function ($timestamp) {
    return sprintf(
        '<span class="timesince">%s</span>',
        DateTimeRenderer::timePointPast($timestamp)
    );
});

$this->addHelperFunction('timeSpanFuture', function ($timestamp) {
    return sprintf(
        '<span class="timeuntil">%s</span>',
        DateTimeRenderer::timeSpanFuture($timestamp)
    );
});

$this->addHelperFunction('timeSpanPast', function ($timestamp) {
    return sprintf(
        '<span class="timesince">%s</span>',
        DateTimeRenderer::timeSpanPast($timestamp)
    );
});
