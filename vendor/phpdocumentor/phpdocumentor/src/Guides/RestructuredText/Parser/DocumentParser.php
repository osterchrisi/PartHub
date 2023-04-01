<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Doctrine\Common\EventManager;
use Exception;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\BlockNode;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Event\PostParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Event\PreParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Directive as ParserDirective;
use RuntimeException;
use Throwable;

use function array_search;
use function chr;
use function explode;
use function md5;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function strlen;
use function substr;
use function trim;

class DocumentParser
{
    /** @var Parser */
    private $parser;

    /** @var Environment */
    private $environment;

    /** @var EventManager */
    private $eventManager;

    /** @var Directive[] */
    private $directives;

    /** @var DocumentNode */
    private $document;

    /** @var false|string|null */
    private $specialLetter;

    /** @var ParserDirective|null */
    private $directive;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var TableParser */
    private $tableParser;

    /** @var Buffer */
    private $buffer;

    /** @var Node|null */
    private $nodeBuffer;

    /** @var bool */
    private $isCode = false;

    /** @var Lines */
    private $lines;

    /** @var string */
    private $state;

    /** @var ListLine|null */
    private $listLine;

    /** @var bool */
    private $listFlow = false;

    /** @var TitleNode */
    private $lastTitleNode;

    /** @var TitleNode[] */
    private $openTitleNodes = [];

    /**
     * @param Directive[] $directives
     */
    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        array $directives
    ) {
        $this->parser = $parser;
        $this->environment = $parser->getEnvironment();
        $this->eventManager = $eventManager;
        $this->directives = $directives;
        $this->lineDataParser = new LineDataParser($this->parser, $eventManager);
        $this->lineChecker = new LineChecker($this->lineDataParser);
        $this->tableParser = new TableParser();
        $this->buffer = new Buffer();
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    public function parse(string $contents): DocumentNode
    {
        $preParseDocumentEvent = new PreParseDocumentEvent($this->parser, $contents);

        $this->eventManager->dispatchEvent(
            PreParseDocumentEvent::PRE_PARSE_DOCUMENT,
            $preParseDocumentEvent
        );

        $this->document = new DocumentNode(md5($contents));

        $this->init();

        $this->parseLines(trim($preParseDocumentEvent->getContents()));

        foreach ($this->directives as $directive) {
            $directive->finalize($this->document);
        }

        $this->eventManager->dispatchEvent(
            PostParseDocumentEvent::POST_PARSE_DOCUMENT,
            new PostParseDocumentEvent($this->document)
        );

        return $this->document;
    }

    private function init(): void
    {
        $this->specialLetter = false;
        $this->buffer = new Buffer();
        $this->nodeBuffer = null;
    }

    private function setState(string $state): void
    {
        $this->state = $state;
    }

    private function prepareDocument(string $document): string
    {
        $document = str_replace("\r\n", "\n", $document);
        $document = sprintf("\n%s\n", $document);

        $document = $this->mergeIncludedFiles($document);

        // Removing UTF-8 BOM
        $document = str_replace("\xef\xbb\xbf", '', $document);

        // Replace \u00a0 with " "
        $document = str_replace(chr(194) . chr(160), ' ', $document);

        return $document;
    }

    private function createLines(string $document): Lines
    {
        return new Lines(explode("\n", $document));
    }

    private function parseLines(string $document): void
    {
        $document = $this->prepareDocument($document);

        $this->lines = $this->createLines($document);
        $this->setState(State::BEGIN);

        foreach ($this->lines as $line) {
            while (true) {
                if ($this->parseLine($line)) {
                    break;
                }
            }
        }

        // DocumentNode is flushed twice to trigger the directives
        $this->flush();
        $this->flush();

        foreach ($this->openTitleNodes as $titleNode) {
            $this->endOpenSection($titleNode);
        }
    }

    private function parseLine(string $line): bool
    {
        switch ($this->state) {
            case State::BEGIN:
                if (trim($line) !== '') {
                    if ($this->lineChecker->isListLine($line, $this->isCode)) {
                        $this->setState(State::LIST);

                        $listNode = new ListNode();

                        $this->nodeBuffer = $listNode;

                        $this->listLine = null;
                        $this->listFlow = true;

                        return false;
                    }

                    if ($this->lineChecker->isBlockLine($line)) {
                        if ($this->isCode) {
                            $this->setState(State::CODE);
                        } else {
                            $this->setState(State::BLOCK);
                        }

                        return false;
                    }

                    if ($this->parseLink($line)) {
                        return true;
                    }

                    if ($this->lineChecker->isDirective($line)) {
                        $this->setState(State::DIRECTIVE);
                        $this->buffer = new Buffer();
                        $this->flush();
                        $this->initDirective($line);
                    } elseif ($this->lineChecker->isDefinitionList($this->lines->getNextLine())) {
                        $this->setState(State::DEFINITION_LIST);
                        $this->buffer->push($line);

                        return true;
                    } else {
                        $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

                        if ($separatorLineConfig === null) {
                            $this->setState(State::NORMAL);

                            return false;
                        }

                        $this->setState(State::TABLE);

                        $tableNode = new TableNode(
                            $separatorLineConfig,
                            $this->tableParser->guessTableType($line)
                        );

                        $this->nodeBuffer = $tableNode;
                    }
                }

                break;

            case State::LIST:
                if (!$this->parseListLine($line)) {
                    $this->flush();
                    $this->setState(State::BEGIN);

                    return false;
                }

                break;

            case State::DEFINITION_LIST:
                if ($this->lineChecker->isDefinitionListEnded($line, $this->lines->getNextLine())) {
                    $this->flush();
                    $this->setState(State::BEGIN);

                    return false;
                }

                $this->buffer->push($line);

                break;

            case State::TABLE:
                if (trim($line) === '') {
                    $this->flush();
                    $this->setState(State::BEGIN);
                } else {
                    $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

                    // not sure if this is possible, being cautious
                    if (!$this->nodeBuffer instanceof TableNode) {
                        throw new Exception('Node Buffer should be a TableNode instance');
                    }

                    // push the separator or content line onto the TableNode
                    if ($separatorLineConfig !== null) {
                        $this->nodeBuffer->pushSeparatorLine($separatorLineConfig);
                    } else {
                        $this->nodeBuffer->pushContentLine($line);
                    }
                }

                break;

            case State::NORMAL:
                if (trim($line) !== '') {
                    $specialLetter = $this->lineChecker->isSpecialLine($line);

                    if ($specialLetter !== null) {
                        $this->specialLetter = $specialLetter;

                        $lastLine = $this->buffer->pop();

                        if ($lastLine !== null) {
                            $this->buffer = new Buffer([$lastLine]);
                            $this->setState(State::TITLE);
                        } else {
                            $this->buffer->push($line);
                            $this->setState(State::SEPARATOR);
                        }

                        $this->flush();
                        $this->setState(State::BEGIN);
                    } elseif ($this->lineChecker->isDirective($line)) {
                        $this->flush();
                        $this->setState(State::BEGIN);

                        return false;
                    } elseif ($this->lineChecker->isComment($line)) {
                        $this->flush();
                        $this->setState(State::COMMENT);
                    } else {
                        $this->buffer->push($line);
                    }
                } else {
                    $this->flush();
                    $this->setState(State::BEGIN);
                }

                break;

            case State::COMMENT:
                if (!$this->lineChecker->isComment($line) && (trim($line) === '' || $line[0] !== ' ')) {
                    $this->setState(State::BEGIN);

                    return false;
                }

                break;

            case State::BLOCK:
            case State::CODE:
                if (!$this->lineChecker->isBlockLine($line)) {
                    $this->flush();
                    $this->setState(State::BEGIN);

                    return false;
                }

                $this->buffer->push($line);

                break;

            case State::DIRECTIVE:
                if (!$this->isDirectiveOption($line)) {
                    if (!$this->lineChecker->isDirective($line)) {
                        $directive = $this->getCurrentDirective();
                        $this->isCode = $directive !== null ? $directive->wantCode() : false;
                        $this->setState(State::BEGIN);

                        return false;
                    }

                    $this->flush();
                    $this->initDirective($line);
                }

                break;

            default:
                $this->environment->addError('Parser ended in an unexcepted state');
        }

        return true;
    }

    private function flush(): void
    {
        $node = null;

        $this->isCode = false;

        if ($this->hasBuffer()) {
            switch ($this->state) {
                case State::TITLE:
                    $data = $this->buffer->getLinesString();

                    $level = $this->environment->getLevel((string) $this->specialLetter);
                    $level = $this->environment->getInitialHeaderLevel() + $level - 1;

                    $node = new TitleNode(
                        new SpanNode($this->environment, $data),
                        $level
                    );

                    if ($this->lastTitleNode !== null) {
                        // current level is less than previous so we need to end all open sections
                        if ($node->getLevel() < $this->lastTitleNode->getLevel()) {
                            foreach ($this->openTitleNodes as $titleNode) {
                                $this->endOpenSection($titleNode);
                            }

                            // same level as the last so just close the last open section
                        } elseif ($node->getLevel() === $this->lastTitleNode->getLevel()) {
                            $this->endOpenSection($this->lastTitleNode);
                        }
                    }

                    $this->lastTitleNode = $node;

                    $this->document->addNode(new SectionBeginNode($node));

                    $this->openTitleNodes[] = $node;

                    break;

                case State::SEPARATOR:
                    $level = $this->environment->getLevel((string) $this->specialLetter);

                    $node = new SeparatorNode($level);

                    break;

                case State::CODE:
                    /** @var string[] $buffer */
                    $buffer = $this->buffer->getLines();

                    $node = new CodeNode($buffer);

                    break;

                case State::BLOCK:
                    /** @var string[] $lines */
                    $lines = $this->buffer->getLines();

                    $blockNode = new BlockNode($lines);

                    $document = $this->parser->getSubParser()->parseLocal($blockNode->getValue());

                    $node = new QuoteNode($document);

                    break;

                case State::LIST:
                    $this->parseListLine(null, true);

                    /** @var ListNode $node */
                    $node = $this->nodeBuffer;

                    break;

                case State::DEFINITION_LIST:
                    $definitionList = $this->lineDataParser->parseDefinitionList(
                        $this->buffer->getLines()
                    );

                    $node = new DefinitionListNode($definitionList);

                    break;

                case State::TABLE:
                    /** @var TableNode $node */
                    $node = $this->nodeBuffer;

                    $node->finalize($this->parser, $this->lineChecker);

                    break;

                case State::NORMAL:
                    $this->isCode = $this->prepareCode();

                    $buffer = $this->buffer->getLinesString();

                    $node = new ParagraphNode(new SpanNode($this->environment, $buffer));

                    break;
            }
        }

        if ($this->directive !== null) {
            $currentDirective = $this->getCurrentDirective();

            if ($currentDirective !== null) {
                try {
                    $currentDirective->process(
                        $this->parser,
                        $node,
                        $this->directive->getVariable(),
                        $this->directive->getData(),
                        $this->directive->getOptions()
                    );
                } catch (Throwable $e) {
                    $message = sprintf(
                        'Error while processing "%s" directive%s: %s',
                        $currentDirective->getName(),
                        $this->environment->getCurrentFileName() !== '' ? sprintf(
                            ' in "%s"',
                            $this->environment->getCurrentFileName()
                        ) : '',
                        $e->getMessage()
                    );

                    $this->environment->addError($message);
                }
            }

            $node = null;
        }

        $this->directive = null;

        if ($node !== null) {
            $this->document->addNode($node);
        }

        $this->init();
    }

    private function hasBuffer(): bool
    {
        return !$this->buffer->isEmpty() || $this->nodeBuffer !== null;
    }

    private function getCurrentDirective(): ?Directive
    {
        if ($this->directive === null) {
            return null;
        }

        $name = $this->directive->getName();

        return $this->directives[$name];
    }

    private function isDirectiveOption(string $line): bool
    {
        if ($this->directive === null) {
            return false;
        }

        $directiveOption = $this->lineDataParser->parseDirectiveOption($line);

        if ($directiveOption === null) {
            return false;
        }

        $this->directive->setOption($directiveOption->getName(), $directiveOption->getValue());

        return true;
    }

    private function initDirective(string $line): bool
    {
        $parserDirective = $this->lineDataParser->parseDirective($line);

        if ($parserDirective === null) {
            return false;
        }

        if (!isset($this->directives[$parserDirective->getName()])) {
            $message = sprintf(
                'Unknown directive: "%s" %sfor line "%s"',
                $parserDirective->getName(),
                $this->environment->getCurrentFileName() !== '' ? sprintf(
                    'in "%s" ',
                    $this->environment->getCurrentFileName()
                ) : '',
                $line
            );

            $this->environment->addError($message);

            return false;
        }

        $this->directive = $parserDirective;

        return true;
    }

    private function prepareCode(): bool
    {
        $lastLine = $this->buffer->getLastLine();

        if ($lastLine === null) {
            return false;
        }

        $trimmedLastLine = trim($lastLine);

        if (strlen($trimmedLastLine) >= 2) {
            if (substr($trimmedLastLine, -2) === '::') {
                if (trim($trimmedLastLine) === '::') {
                    $this->buffer->pop();
                } else {
                    $this->buffer->set($this->buffer->count() - 1, substr($trimmedLastLine, 0, -1));
                }

                return true;
            }
        }

        return false;
    }

    private function parseLink(string $line): bool
    {
        $link = $this->lineDataParser->parseLink($line);

        if ($link === null) {
            return false;
        }

        if ($link->getType() === Link::TYPE_ANCHOR) {
            $anchorNode = new AnchorNode($link->getName());

            $this->document->addNode($anchorNode);
        }

        $this->environment->setLink($link->getName(), $link->getUrl());

        return true;
    }

    private function parseListLine(?string $line, bool $flush = false): bool
    {
        if ($line !== null && trim($line) !== '') {
            $listLine = $this->lineDataParser->parseListLine($line);

            if ($listLine !== null) {
                if ($this->listLine instanceof ListLine) {
                    $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

                    /** @var ListNode $listNode */
                    $listNode = $this->nodeBuffer;

                    $listNode->addLine($this->listLine->toArray());
                }

                $this->listLine = $listLine;
            } else {
                if ($this->listLine instanceof ListLine && ($this->listFlow || $line[0] === ' ')) {
                    $this->listLine->addText($line);
                } else {
                    $flush = true;
                }
            }

            $this->listFlow = true;
        } else {
            $this->listFlow = false;
        }

        if ($flush) {
            if ($this->listLine instanceof ListLine) {
                $this->listLine->setText(new SpanNode($this->environment, $this->listLine->getText()));

                /** @var ListNode $listNode */
                $listNode = $this->nodeBuffer;

                $listNode->addLine($this->listLine->toArray());

                $this->listLine = null;
            }

            return false;
        }

        return true;
    }

    private function endOpenSection(TitleNode $titleNode): void
    {
        $this->document->addNode(new SectionEndNode($titleNode));

        $key = array_search($titleNode, $this->openTitleNodes, true);

        if ($key === false) {
            return;
        }

        unset($this->openTitleNodes[$key]);
    }

    public function mergeIncludedFiles(string $document): string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) {
                $path = $this->environment->absoluteRelativePath($match[1]);

                $origin = $this->environment->getOrigin();
                if (!$origin->has($path)) {
                    throw new RuntimeException(
                        sprintf('Include "%s" (%s) does not exist or is not readable.', $match[0], $path)
                    );
                }

                $contents = $origin->read($path);

                if ($contents === false) {
                    throw new RuntimeException(sprintf('Could not load file from path %s', $path));
                }

                return $this->mergeIncludedFiles($contents);
            },
            $document
        );
    }
}
