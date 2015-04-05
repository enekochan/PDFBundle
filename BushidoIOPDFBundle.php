<?php

namespace BushidoIO\PDFBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use BushidoIO\PDFBundle\DependencyInjection\BushidoIOPDFExtension;

class BushidoIOPDFBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BushidoIOPDFExtension();
        }

        return $this->extension;
    }
}
