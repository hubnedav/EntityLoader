<?php

declare(strict_types=1);

namespace Tests\Functional\Fixtures;

use Arachne\EntityLoader\FilterInInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ArticleFilterIn implements FilterInInterface
{
    public function supports(string $type): bool
    {
        return $type === Article::class;
    }

    /**
     * {@inheritdoc}
     */
    public function filterIn($value, string $type): Article
    {
        return new Article($value);
    }
}
