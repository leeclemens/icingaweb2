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
