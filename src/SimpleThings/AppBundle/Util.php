<?php
/**
 * (c) SimpleThings GmbH
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Commit;

/**
 * @author David Badura <badura@simplethings.de>
 */
class Util
{
    /**
     * @param Commit $commit
     * @return string
     */
    public static function getUniqueIdByCommit(Commit $commit)
    {
        return sprintf(
            "%s_%s",
            $commit->getProject()->getId(),
            $commit->getRevision()
        );
    }
} 