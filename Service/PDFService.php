<?php

namespace BushidoIO\PDFBundle\Service;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PDFService implements ContainerAwareInterface
{
    protected $container;
    protected $tmp;
    protected $ttfFontDataPath;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->readConfiguration();
    }

    private function readConfiguration()
    {
        $options = $this->container->getParameter('bushidoio_pdf');

        if (!empty($options['tmp']) && is_writable($options['tmp'])) {
            $this->tmp = $options['tmp'];
            // Create it just in case
            if(!is_null($this->tmp) && !file_exists($this->tmp)){
                mkdir($this->tmp, 0755, true);
            }
        }

        if (!empty($options['ttffontdatapath']) && is_writable($options['ttffontdatapath'])) {
            $this->tmp = $options['ttffontdatapath'];
            // Create it just in case
            if(!is_null($this->ttfFontDataPath) && !file_exists($this->ttfFontDataPath)){
                mkdir($this->ttfFontDataPath, 0755, true);
            }
        }
    }

    /**
     * Create a PDF document from an HTML string
     *
     * @param string $html HTML content to create the PDF document from
     * @param string $filename Filename for the created PDF document
     *
     * @return string PDF document
     *
     * @throws MpdfException
     */
    public function createPDFFromHtml($html = '', $filename = 'output.pdf')
    {
        if ($html === '') {
            $html = '<html><head></head><body></body></html>';
        }

        /**
         * Destination of the PDF
         * Destination::INLINE ('I'): Browser
         * Destination::DOWNLOAD ('D'): Browser and force download
         * Destination::FILE ('F'): Save to $filename (may have full path)
         * Destination::STRING_RETURN ('S'): Return document as string
         *
         * Only Destination::STRING_RETURN is used at the moment
         */
        $destination = Destination::STRING_RETURN;

        $config = array();
        if (!is_null($this->tmp)) {
            $config['tempDir'] = $this->tmp;
        }
        if (!is_null($this->ttfFontDataPath)) {
            $config['fontDir'] = $this->tmp;
        }

        $mpdf = new Mpdf($config);
        $mpdf->WriteHTML($html);

        return $mpdf->Output($filename, $destination);
    }

    /**
     * Create a Response object with the PDF document from HTML
     *
     * @param string $html HTML content to create PDF from
     * @param string $filename Output filename of the PDF file
     *
     * @return Response A Response instance
     */
    public function createResponse($html = '', $filename = 'output.pdf')
    {
        try {
            $content = $this->createPDFFromHtml($html);
        } catch(MpdfException $e) {
            throw new HttpException(500, $e->getMessage(), $e);
        }

        if (trim($filename) === '') {
            $filename = 'output-' . date('Ymd_His') . '.pdf';
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $filename));
        $response->setContent($content);

        return $response;
    }
}
