<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Templates;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Every image the project renders through mai_assets has to say where it sits: a layout "slot"
 * (shared by all images of that kind) or an "elementUid". Without either, mai_assets cannot assign
 * the image to a measured size profile and falls back to the unmeasured default.
 */
final class ImageMeasurementSlotTest extends TestCase
{
    private const ATOMS = ['image', 'picture', 'figure'];

    private const IMAGE_VIEW_HELPER = '/<(mai:image\.(?:picture|responsiveImage)|[a-z]+:atom\.(?:image|picture|figure))\b/';

    #[Test]
    public function everyImageCallPassesASlotOrAnElementUid(): void
    {
        $missing = [];
        $checked = 0;

        foreach ($this->templateFiles() as $file) {
            $contents = (string) file_get_contents($file);
            if (preg_match_all(self::IMAGE_VIEW_HELPER, $contents, $matches, PREG_OFFSET_CAPTURE) < 1) {
                continue;
            }

            foreach ($matches[0] as [$match, $offset]) {
                ++$checked;
                $tag = substr($contents, $offset, $this->tagEnd($contents, $offset + strlen($match)) - $offset + 1);
                if (preg_match('/\s(?:slot|elementUid)=/', $tag) !== 1) {
                    $line = substr_count(substr($contents, 0, $offset), "\n") + 1;
                    $missing[] = sprintf('%s:%d %s', $this->relative($file), $line, $match);
                }
            }
        }

        self::assertGreaterThan(0, $checked, 'No image view helper calls found; the scan is broken.');
        self::assertSame([], $missing, "Image calls without slot or elementUid:\n" . implode("\n", $missing));
    }

    #[Test]
    public function imageAtomsRequireASlot(): void
    {
        foreach (self::ATOMS as $atom) {
            $path = __DIR__ . '/../../../Resources/Private/Components/Atom/' . ucfirst($atom) . '.fluid.html';
            self::assertFileExists($path);

            self::assertMatchesRegularExpression(
                '/<f:argument name="slot" type="string" optional="\{false\}"/',
                (string) file_get_contents($path),
                sprintf('atom.%s must declare "slot" as a required argument so callers cannot forget it.', $atom),
            );
        }
    }

    /**
     * Templates of this package and of the sibling extensions in the same monorepo.
     *
     * @return list<string>
     */
    private function templateFiles(): array
    {
        $roots = [dirname(__DIR__, 3) . '/Resources/Private'];
        foreach (glob(dirname(__DIR__, 4) . '/typo3-extension-*/Resources/Private') ?: [] as $dir) {
            $roots[] = $dir;
        }

        $files = [];
        foreach (array_unique(array_map('realpath', array_filter($roots, 'is_dir'))) as $root) {
            if ($root === false) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file instanceof \SplFileInfo && $file->getExtension() === 'html' && !str_contains($file->getPathname(), '/Design/')) {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return array_values(array_unique($files));
    }

    /**
     * Position of the closing ">" of a tag, ignoring ">" inside {...} expressions and quotes.
     */
    private function tagEnd(string $contents, int $from): int
    {
        $depth = 0;
        $quote = '';
        $length = strlen($contents);
        for ($i = $from; $i < $length; ++$i) {
            $char = $contents[$i];
            if ($quote !== '') {
                if ($char === $quote) {
                    $quote = '';
                }
            } elseif ($char === '"' || $char === "'") {
                $quote = $char;
            } elseif ($char === '{') {
                ++$depth;
            } elseif ($char === '}') {
                $depth = max(0, $depth - 1);
            } elseif ($char === '>' && $depth === 0) {
                return $i;
            }
        }

        return $length - 1;
    }

    private function relative(string $file): string
    {
        $position = strpos($file, 'typo3-extension-');

        return $position === false ? $file : substr($file, $position);
    }
}
