<?php

namespace Recranet\TwigSpreadsheetBundle\Wrapper;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use Recranet\TwigSpreadsheetBundle\Helper\Filesystem;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\Filesystem\Exception\IOException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class DocumentWrapper.
 */
class DocumentWrapper extends BaseWrapper
{
    protected ?Spreadsheet $object;
    protected array $attributes;

    /**
     * DocumentWrapper constructor.
     *
     * @param array       $context
     * @param Environment $environment
     * @param array       $attributes
     */
    public function __construct(array $context, Environment $environment, array $attributes = [])
    {
        parent::__construct($context, $environment);

        $this->object = null;
        $this->attributes = $attributes;
    }

    /**
     * @param array $properties
     *
     * @throws \RuntimeException
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function start(array $properties = []): void
    {
        // load template
        if (isset($properties['template'])) {
            $templatePath = $this->expandPath($properties['template']);
            $reader = IOFactory::createReaderForFile($templatePath);
            $this->object = $reader->load($templatePath);
        }

        // create new
        else {
            $this->object = new Spreadsheet();
            $this->object->removeSheetByIndex(0);
        }

        $this->parameters['properties'] = $properties;

        $this->setProperties($properties);
    }

    /**
     * @throws \LogicException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws IOException
     */
    public function write(): string
    {
        if ($this->object === null) {
            throw new \LogicException('A document must be started before writing.');
        }

        $format = null;

        // try document property
        if (isset($this->parameters['format'])) {
            $format = $this->parameters['format'];
        }

        // try Symfony request
        elseif (isset($this->context['app'])) {
            /**
             * @var AppVariable $appVariable
             */
            $appVariable = $this->context['app'];
            if ($appVariable instanceof AppVariable && $appVariable->getRequest() !== null) {
                $format = $appVariable->getRequest()->getRequestFormat();
            }
        }

        // set default
        if ($format === null) {
            $format = 'xlsx';
        } else {
            $format = strtolower($format);
        }

        // set up PDF
        if ($format === 'pdf') {
            if (class_exists('\Dompdf\Dompdf')) {
                IOFactory::registerWriter('Pdf', Dompdf::class);
            } elseif (class_exists('\Mpdf\Mpdf')) {
                IOFactory::registerWriter('Pdf', Mpdf::class);
            } elseif (class_exists('\TCPDF')) {
                IOFactory::registerWriter('Pdf', Tcpdf::class);
            } else {
                throw new \RuntimeException('PDF rendering requires dompdf, mPDF or TCPDF');
            }
        }

        /**
         * @var BaseWriter $writer
         */
        $writer = IOFactory::createWriter($this->object, ucfirst($format));
        $writer->setPreCalculateFormulas($this->attributes['pre_calculate_formulas'] ?? true);

        // set up XML cache
        if ($this->attributes['cache']['xml'] !== false) {
            Filesystem::mkdir($this->attributes['cache']['xml']);
            $writer->setUseDiskCaching(true, $this->attributes['cache']['xml']);
        }

        // set special CSV writer attributes
        if ($writer instanceof Csv) {
            /*
             * @var Csv $writer
             */
            $writer->setDelimiter($this->attributes['csv_writer']['delimiter']);
            $writer->setEnclosure($this->attributes['csv_writer']['enclosure']);
            $writer->setExcelCompatibility($this->attributes['csv_writer']['excel_compatibility']);
            $writer->setIncludeSeparatorLine($this->attributes['csv_writer']['include_separator_line']);
            $writer->setLineEnding($this->attributes['csv_writer']['line_ending']);
            $writer->setSheetIndex($this->attributes['csv_writer']['sheet_index']);
            $writer->setUseBOM($this->attributes['csv_writer']['use_bom']);
        }

        ob_start();

        $writer->save('php://output');

        return ob_get_clean();
    }

    public function end(): void
    {
        if ($this->object === null) {
            throw new \LogicException('A document must be started before ending it.');
        }

        $this->object = null;
        $this->parameters = [];
    }

    public function getObject(): Spreadsheet
    {
        if ($this->object === null) {
            throw new \LogicException('Object is not initialized');
        }

        return $this->object;
    }

    public function hasObject(): bool
    {
        return $this->object !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function configureMappings(): array
    {
        return [
            'category' => function ($value) {
                $this->getObject()->getProperties()->setCategory($value);
            },
            'company' => function ($value) {
                $this->getObject()->getProperties()->setCompany($value);
            },
            'created' => function ($value) {
                $this->getObject()->getProperties()->setCreated($value);
            },
            'creator' => function ($value) {
                $this->getObject()->getProperties()->setCreator($value);
            },
            'defaultStyle' => function ($value) {
                $this->getObject()->getDefaultStyle()->applyFromArray($value);
            },
            'description' => function ($value) {
                $this->getObject()->getProperties()->setDescription($value);
            },
            'format' => function ($value) {
                $this->parameters['format'] = $value;
            },
            'keywords' => function ($value) {
                $this->getObject()->getProperties()->setKeywords($value);
            },
            'lastModifiedBy' => function ($value) {
                $this->getObject()->getProperties()->setLastModifiedBy($value);
            },
            'manager' => function ($value) {
                $this->getObject()->getProperties()->setManager($value);
            },
            'modified' => function ($value) {
                $this->getObject()->getProperties()->setModified($value);
            },
            'security' => [
                'lockRevision' => function ($value) {
                    $this->getObject()->getSecurity()->setLockRevision($value);
                },
                'lockStructure' => function ($value) {
                    $this->getObject()->getSecurity()->setLockStructure($value);
                },
                'lockWindows' => function ($value) {
                    $this->getObject()->getSecurity()->setLockWindows($value);
                },
                'revisionsPassword' => function ($value) {
                    $this->getObject()->getSecurity()->setRevisionsPassword($value);
                },
                'workbookPassword' => function ($value) {
                    $this->getObject()->getSecurity()->setWorkbookPassword($value);
                },
            ],
            'subject' => function ($value) {
                $this->getObject()->getProperties()->setSubject($value);
            },
            'template' => function ($value) {
                $this->parameters['template'] = $value;
            },
            'title' => function ($value) {
                $this->getObject()->getProperties()->setTitle($value);
            },
        ];
    }

    /**
     * Resolves paths using Twig namespaces.
     * The path must start with the namespace.
     * Namespaces are case-sensitive.
     *
     * @param string $path
     *
     * @return string
     */
    private function expandPath(string $path): string
    {
        $loader = $this->environment->getLoader();

        if ($loader instanceof FilesystemLoader && mb_strpos($path, '@') === 0) {
            foreach ($loader->getNamespaces() as $namespace) {
                if (mb_strpos($path, $namespace) === 1) {
                    foreach ($loader->getPaths($namespace) as $namespacePath) {
                        $expandedPathAttribute = str_replace('@'.$namespace, $namespacePath, $path);
                        if (Filesystem::exists($expandedPathAttribute)) {
                            return $expandedPathAttribute;
                        }
                    }
                }
            }
        }

        return $path;
    }
}
