<?php

namespace BushidoIO\PDFBundle\Service;

require_once __DIR__.'/../Lib/MPDF/mpdf.php';

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class PDFService extends \BushidoIO\PDFBundle\Lib\MPDF\mPDF implements ContainerAwareInterface
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
        
        $this->tmp = $options['tmp'];
        $this->ttfFontDataPath = $options['ttffontdatapath'];
        $mpdfCachePath = $this->container->getParameter("kernel.cache_dir") . DIRECTORY_SEPARATOR . 'mpdf';

        // If provided path is empty, doesn't exist or is not writable use cache_dir
        if (empty($this->tmp) || !is_writable($this->tmp)) {
            $this->tmp = $mpdfCachePath . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        }
        // Create it just in case
        if(!file_exists($this->tmp)){
            mkdir($this->tmp, 0755, true);
        }
        
        // If provided path is empty, doesn't exist or is not writable use cache_dir
        if (empty($this->ttfFontDataPath) || !is_writable($this->ttfFontDataPath)) {
            $this->ttfFontDataPath = $mpdfCachePath . DIRECTORY_SEPARATOR . 'ttffontdata' . DIRECTORY_SEPARATOR;
        }
        // Create it just in case
        if(!file_exists($this->ttfFontDataPath)){
            mkdir($this->ttfFontDataPath, 0755, true);
        }
        
        // Set those paths in mPDF constants
        if (!defined('_MPDF_TEMP_PATH')) {
            define('_MPDF_TEMP_PATH', $this->tmp);
        }
        if (!defined('_MPDF_TTFONTDATAPATH')) {
            define('_MPDF_TTFONTDATAPATH', $this->ttfFontDataPath);
        }
    }
    
    /**
     * Create PDF document from HTML
     *
     * @param String $html HTML content to create PDF from
     */
    public function createPDFFromHtml($html = '')
    {
        if ($html === '') {
            $html = '<html><head></head><body></body></html>';
        }

        /**
         * Destination of the PDF
         * I: Browser
         * D: Browser and force download
         * F: Save to $filename (may have full path)
         * S: Return document as string
         *
         * Only S is used at the momento
         */
        $destination = 'S';
        // This will be ignored if destination S is indicated
        $filename = 'output.pdf';

        $this->mPDF();
        $this->WriteHTML($html);

        return $this->Output($filename, $destination);
    }

    /**
     * Create a Response object with the PDF document from HTML
     *
     * @param String $html HTML content to create PDF from
     * @param String $filename Output filename of the PDF file
     */
    public function createResponse($html = '', $filename = 'output.pdf')
    {
        $content = $this->createPDFFromHtml($html);

        if (trim($filename) === '') {
            $filename = 'output-' . date('Ymd_His') . '.pdf';
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->setContent($content);
        
        return $response;
    }
}
