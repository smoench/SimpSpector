<?php
/**
 *
 */

namespace AppBundle;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
final class Events
{
    /**
     * @var string
     */
    const BEGIN = 'simpspector.begin';

    /**
     * @var string
     */
    const RESULT = 'simpspector.result';

    /**
     * @var string
     */
    const EXCEPTION = 'simpspector.exception';

    /**
     * @var string
     */
    const WEBHOOK = 'simpspector.webhook';
}