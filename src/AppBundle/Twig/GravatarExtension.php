<?php

namespace AppBundle\Twig;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GravatarExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    const URL = '//www.gravatar.com/avatar/{hash}?d=mm&s={size}';

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('gravatar', [$this, 'gravatar'], ['is_safe' => ['html']])];
    }

    /**
     * @param string $email
     * @param int $size
     * @return string
     */
    public function gravatar($email, $size = 80)
    {
        return strtr(self::URL, [
            '{hash}' => $this->getHash($email),
            '{size}' => $size
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gravatar';
    }

    /**
     * @return string
     */
    private function getHash($email)
    {
        return md5(strtolower(trim($email)));
    }
}