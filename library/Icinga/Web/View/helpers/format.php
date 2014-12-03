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

$this->addHelperFunction('timePointFuture', function ($timestamp) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timePointFuture();
    return $dtr->isAbsolute() ? $s : sprintf(
        '<span class="timeuntil">%s</span>',
        $s
    );
});

$this->addHelperFunction('timePointPast', function ($timestamp) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timePointPast();
    return $dtr->isAbsolute() ? $s : sprintf(
        '<span class="timesince">%s</span>',
        $s
    );
});

$this->addHelperFunction('timeSpanFuture', function ($timestamp) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timeSpanFuture();
    return $dtr->isAbsolute() ? $s : sprintf(
        '<span class="timeuntil">%s</span>',
        $s
    );
});

$this->addHelperFunction('timeSpanPast', function ($timestamp) {
    $dtr = new DateTimeRenderer($timestamp);
    $s = $dtr->timeSpanPast();
    return $dtr->isAbsolute() ? $s : sprintf(
        '<span class="timesince">%s</span>',
        $s
    );
});
