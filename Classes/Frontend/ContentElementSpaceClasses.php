<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Frontend;

use Maispace\MaiTheme\EventListener\ContentSpaceClassTcaListener;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Adds spacing classes for space_before_class and space_after_class onto the
 * root tag of a rendered content element. An empty value ("Keiner") adds nothing.
 */
final class ContentElementSpaceClasses
{
    #[AsAllowedCallable]
    public function apply(string $content, array $configuration, ServerRequestInterface $request): string
    {
        $contentObject = $request->getAttribute('currentContentObject');
        if (!$contentObject instanceof ContentObjectRenderer) {
            return $content;
        }

        return self::inject(
            $content,
            (string) ($contentObject->data['space_before_class'] ?? ''),
            (string) ($contentObject->data['space_after_class'] ?? ''),
        );
    }

    public static function inject(string $content, string $before, string $after): string
    {
        $classes = [];
        $beforeToken = self::token($before);
        $afterToken = self::token($after);
        if ($beforeToken !== '') {
            $classes[] = 'mai-ce-space-before-' . $beforeToken;
        }
        if ($afterToken !== '') {
            $classes[] = 'mai-ce-space-after-' . $afterToken;
        }
        if ($classes === []) {
            return $content;
        }

        return self::addClassesToFirstTag($content, $classes);
    }

    private static function token(string $value): string
    {
        return in_array($value, ContentSpaceClassTcaListener::TOKENS, true) ? $value : '';
    }

    /**
     * @param list<string> $classes
     */
    private static function addClassesToFirstTag(string $content, array $classes): string
    {
        $length = strlen($content);
        $offset = 0;
        while ($offset < $length) {
            $start = strpos($content, '<', $offset);
            if ($start === false) {
                return $content;
            }
            $next = $content[$start + 1] ?? '';
            if ($next === '' || $next === '/' || $next === '!' || $next === '?' || !ctype_alpha($next)) {
                $offset = $start + 1;
                continue;
            }
            $end = self::findTagEnd($content, $start);
            if ($end === null) {
                return $content;
            }
            $tag = substr($content, $start, $end - $start);
            $updated = self::insertClasses($tag, $classes);

            return substr($content, 0, $start) . $updated . substr($content, $end);
        }

        return $content;
    }

    private static function findTagEnd(string $content, int $start): ?int
    {
        $length = strlen($content);
        $quote = '';
        for ($index = $start + 1; $index < $length; $index++) {
            $character = $content[$index];
            if ($quote !== '') {
                if ($character === $quote) {
                    $quote = '';
                }
                continue;
            }
            if ($character === '"' || $character === "'") {
                $quote = $character;
                continue;
            }
            if ($character === '>') {
                return $index + 1;
            }
        }

        return null;
    }

    /**
     * @param list<string> $classes
     */
    private static function insertClasses(string $tag, array $classes): string
    {
        $addition = implode(' ', $classes);
        $updated = preg_replace(
            '/\sclass\s*=\s*(["\'])(.*?)\1/',
            ' class=$1$2 ' . $addition . '$1',
            $tag,
            1,
            $count,
        );
        if (!is_string($updated) || $count !== 1) {
            $closing = str_ends_with($tag, '/>') ? '/>' : '>';

            return substr($tag, 0, -strlen($closing)) . ' class="' . $addition . '"' . $closing;
        }

        return $updated;
    }
}
