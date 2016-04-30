<?php

namespace AppBundle\Tests\CommonMark;

use AppBundle\CommonMark\CommonMarkFactory;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class FencedCodeBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testHtmlOutput()
    {
        $markdown = <<<TXT
```php line:5 file:foo.php offset:1
echo \$foo;
```
TXT;

        $output = <<<TXT
<pre class="prettyprint lang-php line-numbers" data-line="5" data-file="foo.php" data-start="1" data-line-offset="0"><code>echo \$foo;
</code></pre>
TXT;

        $html = CommonMarkFactory::create()->convertToHtml($markdown);

        $this->assertEquals($output, trim($html));
    }
}
