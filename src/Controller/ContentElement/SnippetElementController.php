<?php

declare(strict_types=1);

namespace Futura\ContaoModularisBuilder\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\File\Metadata;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Contao\Validator;
use Doctrine\DBAL\Connection;
use Futura\ContaoModularisBuilder\Snippet\Placeholder;
use Futura\ContaoModularisBuilder\Snippet\SnippetParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[AsContentElement(type: 'modularis_element', category: 'includes', template: 'content_element/modularis_element')]
class SnippetElementController extends AbstractContentElementController
{
    public const TYPE = 'modularis_element';

    public function __construct(
        private readonly Connection $connection,
        private readonly SnippetParser $parser,
        private readonly Studio $studio,
        private readonly Environment $twig,
        private readonly InsertTagParser $insertTagParser,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $snippet = $this->connection->fetchAssociative(
            'SELECT id, name, alias, html, imgSize FROM tl_modularis_template WHERE id = ?',
            [(int) $model->modularisTemplate],
        );

        if (!$snippet) {
            return new Response();
        }

        $data = json_decode((string) $model->modularisData, true);
        $data = \is_array($data) ? $data : [];

        $rendered = [];
        $isEmpty = [];

        foreach ($this->parser->parse((string) $snippet['html']) as $name => $placeholder) {
            $value = (string) ($data[$name] ?? '');
            $isEmpty[$name] = '' === trim($value);

            $rendered[$name] = 'image' === $placeholder->type
                ? $this->renderImage($placeholder, $data, $snippet['imgSize'])
                : $this->renderValue($placeholder, $value);
        }

        $html = $this->parser->render((string) $snippet['html'], $rendered, $isEmpty);
        $html = $this->insertTagParser->replaceInline($html);

        $template->set('html', $html);
        $template->set('snippet', [
            'id' => (int) $snippet['id'],
            'name' => $snippet['name'],
            'alias' => $snippet['alias'],
        ]);
        $template->set('values', $data);

        $this->tagResponse(['contao.db.tl_modularis_template.'.$snippet['id']]);

        return $template->getResponse();
    }

    private function renderValue(Placeholder $placeholder, string $value): string
    {
        if ('' === $value) {
            return '';
        }

        return match ($placeholder->type) {
            'html', 'rte' => $value,
            'textarea' => nl2br(StringUtil::specialchars($value)),
            default => StringUtil::specialchars($value),
        };
    }

    /**
     * @param array<string, mixed> $data
     */
    private function renderImage(Placeholder $placeholder, array $data, mixed $templateImgSize): string
    {
        $uuid = (string) ($data[$placeholder->name] ?? '');

        if (!Validator::isStringUuid($uuid)) {
            return '';
        }

        $builder = $this->studio
            ->createFigureBuilder()
            ->fromUuid($uuid)
            ->setSize($this->resolveImageSize($placeholder, $data, $templateImgSize))
        ;

        $overwrite = array_filter([
            Metadata::VALUE_ALT => (string) ($data[$placeholder->name.'_alt'] ?? ''),
            Metadata::VALUE_TITLE => (string) ($data[$placeholder->name.'_title'] ?? ''),
            Metadata::VALUE_CAPTION => (string) ($data[$placeholder->name.'_caption'] ?? ''),
            Metadata::VALUE_URL => (string) ($data[$placeholder->name.'_url'] ?? ''),
        ], static fn (string $value): bool => '' !== $value);

        if ([] !== $overwrite) {
            $builder->setOverwriteMetadata(new Metadata($overwrite));
        }

        $fullsize = $data[$placeholder->name.'_fullsize'] ?? ($placeholder->lightbox ? '1' : '');

        if ($fullsize) {
            $builder->enableLightbox(true);
        }

        $figure = $builder->buildIfResourceExists();

        if (null === $figure) {
            return '';
        }

        return $this->twig->render('@Contao/component/_figure.html.twig', ['figure' => $figure]);
    }

    /**
     * Eigene Bildgröße des Redakteurs geht vor der im Platzhalter definierten
     * Standardgröße, die wiederum vor der Bildgröße der Vorlage geht.
     *
     * @param array<string, mixed> $data
     *
     * @return array{0:int,1:int,2:string}|int|string|null
     */
    private function resolveImageSize(Placeholder $placeholder, array $data, mixed $templateImgSize): array|int|string|null
    {
        $own = $data[$placeholder->name.'_size'] ?? '';

        if (\is_string($own) && '' !== trim($own)) {
            return StringUtil::deserialize($own);
        }

        return $placeholder->imageSize ?? (StringUtil::deserialize($templateImgSize) ?: null);
    }
}
