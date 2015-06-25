<?php

namespace AppBundle\Tests\Ciconia\Extension;

use Ciconia\Common\Text;
use Ciconia\Renderer\HtmlRenderer;
use AppBundle\Ciconia\Extension\FencedCodeBlockExtension;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class FencedCodeBlockExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FencedCodeBlockExtension
     */
    protected $extension;

    /**
     *
     */
    public function setUp()
    {
        $this->extension = new FencedCodeBlockExtension();
        $markdown = $this->getMockBuilder('Ciconia\Markdown')
            ->disableOriginalConstructor()
            ->getMock();

        $renderer = new HtmlRenderer();

        $this->extension->register($markdown);
        $this->extension->setRenderer($renderer);
    }

    /**
     *
     */
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

        $text = new Text($markdown);
        $this->extension->processFencedCodeBlock($text);

        $this->assertEquals($output, trim($text->getString()));
    }
}
