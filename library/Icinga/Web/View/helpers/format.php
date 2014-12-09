<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Web\View;

use Icinga\Web\Url;
use Icinga\Util\Format;
use Icinga\Util\DateTimeRenderer;

$this->addHelperFunction('format', function () {
    return Format::getInstance();
});

$this->addHelperFunction('timeUntil', function ($timestamp, $formatStr = array(), $forceAbs = false) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timeUntil($formatStr, $forceAbs);
    return ($forceAbs || $dtr->isAbsolute()) ? $s : sprintf(
        '<span class="timeuntil">%s</span>',
        $s
    );
});

$this->addHelperFunction('timeAgo', function ($timestamp, $formatStr = array(), $forceAbs = false) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timeAgo($formatStr, $forceAbs);
    return ($forceAbs || $dtr->isAbsolute()) ? $s : sprintf(
        '<span class="timesince">%s</span>',
        $s
    );
});

$this->addHelperFunction('timeSince', function ($timestamp, $formatStr = array(), $forceAbs = false) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timeSince($formatStr, $forceAbs);
    return ($forceAbs || $dtr->isAbsolute()) ? $s : sprintf(
        '<span class="timesince">%s</span>',
        $s
    );
});

$this->addHelperFunction('formatDate', function ($timestamp) {
    return DateTimeRenderer::format(
        DateTimeRenderer::FORMAT_DATE,
        $timestamp
    );
});

$this->addHelperFunction('formatTime', function ($timestamp) {
    return DateTimeRenderer::format(
        DateTimeRenderer::FORMAT_TIME,
        $timestamp
    );
});

$this->addHelperFunction('formatDateTime', function ($timestamp) {
    return DateTimeRenderer::format(
        DateTimeRenderer::FORMAT_DATETIME,
        $timestamp
    );
});
